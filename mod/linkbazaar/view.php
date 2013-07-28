<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints a particular instance of linkbazaar
 *
 * @package    mod_linkbazaar
 * @copyright  2013 Andrew Davis <andrew@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/addlink_form.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('l', 0, PARAM_INT);  // linkbazaar instance ID

if ($id) {
    $cm         = get_coursemodule_from_id('linkbazaar', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $linkbazaar = $DB->get_record('linkbazaar', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $linkbazaar = $DB->get_record('linkbazaar', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $linkbazaar->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('linkbazaar', $linkbazaar->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/linkbazaar:view', $context);

$linkbazaar = new linkbazaar($linkbazaar, $cm, $course);

add_to_log($course->id, 'linkbazaar', 'view', "view.php?id={$cm->id}", $linkbazaar->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/linkbazaar/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($linkbazaar->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

echo $OUTPUT->header();
echo $OUTPUT->heading($linkbazaar->name, 2);

if ($linkbazaar->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('linkbazaar', $linkbazaar, $cm->id), 'generalbox mod_introbox', 'linkbazaarintro');
}

$table = new html_table();

$votes = $linkbazaar->get_user_votes($USER->id);
$canvote = has_capability('mod/linkbazaar:votelink', $context);
$canmoderate = has_capability('mod/linkbazaar:removelink', $context);

foreach ($linkbazaar->links as $link) {
    
    $votelink = null;
    $deletelink = null;
    if ($USER->id != $link->userid && $canvote) {
        if (!array_key_exists($link->id, $votes)) {
            $votelink = html_writer::link(
                new moodle_url('vote.php', array('contextid' => $context->id, 'linkid' => $link->id)),
                html_writer::empty_tag('img', array('src' => 'pix/arrow.png',
                                                    'alt' => get_string('upvote', 'linkbazaar'),
                                                    'title' => get_string('upvotetitle', 'linkbazaar')))
            );
        } else {
            $votelink = html_writer::link(
                new moodle_url('vote.php', array('contextid' => $context->id,
                                                      'linkid' => $link->id,
                                                      'delete' => 1)),
                html_writer::empty_tag('img', array('src' => 'pix/selectedarrow.png',
                                                    'alt' => get_string('upvoteremove', 'linkbazaar'),
                                                    'title' => get_string('upvoteremovetitle', 'linkbazaar')))
            );
        }
    }

    if ($USER->id == $link->userid || $canmoderate) {
        $deletelink = html_writer::link(
            new moodle_url('link.php', array('id' => $linkbazaar->id, 'linkid' => $link->id, 'delete' => 1)),
            format_string('delete', true)
        );
    }

    $htmllink = html_writer::link(
        new moodle_url($link->url),
        format_string($link->name, true),
        array('target' => '_blank'));

    $table->data[] = array($votelink, $deletelink, $htmllink, $link->votes);
}

echo html_writer::table($table);

if (has_capability('mod/linkbazaar:addlink', $context)) {

    $form = new linkbazaar_link_form(
        new moodle_url('/mod/linkbazaar/link.php', array('id' => $linkbazaar->id)),
        array(
            'user' => $USER,
        ),
        'post', '', array('class' => 'linkform'));

    $form->set_data(array(
        'user'  => $USER->id
    ));

    $form->display();
}

echo $OUTPUT->footer();
