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

$contextid = required_param('contextid', PARAM_INT);
$linkid = required_param('linkid', PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);

list($context, $course, $cm) = get_context_info_array($contextid);
require_login($course, false, $cm);

require_capability('mod/linkbazaar:votelink', $context);
if (isguestuser()) {
    print_error('guestsarenotallowed');
}

$link = $DB->get_record('linkbazaar_link', array('id' => $linkid), '*', MUST_EXIST);

if ($USER->id == $link->userid) {
    throw new moodle_exception('votingownlink', 'linkbazaar');
}

add_to_log($course->id, 'linkbazaar', 'vote', "view.php?id={$cm->id}", $link->name . ' ' . $link->url, $cm->id);
if ($delete) {
    $DB->delete_records('linkbazaar_upvote', array(
        'linkid' => $link->id,
        'userid' => $USER->id)
    );
} else if (!$DB->record_exists('linkbazaar_upvote', array('linkid' => $link->id, 'userid' => $USER->id))) {
    $DB->insert_record('linkbazaar_upvote', array(
        'linkid' => $link->id,
        'userid' => $USER->id,
        'timecreated' => time())
    );
} else {
    debugging('up vote already exists');
}

redirect(new moodle_url('/mod/linkbazaar/view.php', array('id' => $cm->id, 'view' => 'all')));
