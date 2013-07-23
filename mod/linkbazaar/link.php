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
 * @package    mod_linkbazaar
 * @copyright  2013 Andrew Davis <andrew@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/addlink_form.php');

$id = required_param('id', PARAM_INT);  // Link Bazaar ID

// If deleting a link.
$linkid = optional_param('linkid', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);

$linkbazaar = $DB->get_record('linkbazaar', array('id' => $id), '*', MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $linkbazaar->course), '*', MUST_EXIST);
$cm         = get_coursemodule_from_instance('linkbazaar', $linkbazaar->id, $course->id, false, MUST_EXIST);

require_login($course, false, $cm);

$linkbazaar = new linkbazaar($linkbazaar, $cm, $course);

if (isguestuser()) {
    print_error('guestsarenotallowed');
}

if ($linkid && $delete) {
    $link = $DB->get_record('linkbazaar_link', array('id' => $linkid), '*', MUST_EXIST);

    if ($USER->id != $link->userid) {
        require_capability('mod/linkbazaar:removelink', $linkbazaar->context);
    }

    add_to_log($course->id, 'linkbazaar', 'delete link', 'view.php?id='.$cm->id, $link->name . ' ' . $link->url, $cm->id);

    linkbazaar_delete_link($link->id);
    
} else {
    require_capability('mod/linkbazaar:addlink', $linkbazaar->context);

    $form = new linkbazaar_link_form();

    if ($data = $form->get_data()) {

        if ($data->user != $USER->id) {
            throw new moodle_exception('invaliduserid', 'error');
        }

        add_to_log($course->id, 'linkbazaar', 'add link', 'view.php?id='.$cm->id, $data->name . ' ' . $data->link, $cm->id);

        $DB->insert_record('linkbazaar_link', array(
            'linkbazaarid' => $linkbazaar->id,
            'userid'       => $data->user,
            'url'          => $data->link,
            'name'         => $data->name,
            'timecreated'  => time())
        );
    }
}

redirect(new moodle_url('/mod/linkbazaar/view.php', array('id' => $cm->id)));

