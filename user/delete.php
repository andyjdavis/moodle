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
 * Allows a user to delete their own account
 *
 * @copyright 2014 Andrew Davis
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

require_once('../config.php');


$confirm = optional_param('confirm', 0, PARAM_BOOL);

$PAGE->set_url('/user/delete.php');

$context = context_system::instance();
$PAGE->set_context($context);

require_login();

if (isguestuser($USER)) {
    print_error('userguestnodelete');
}

if (is_siteadmin($USER)) {
    print_error('useradminodelete');
}

//$context = context_user::instance($USER->id);
require_capability('moodle/user:deleteaccount', $context);

$PAGE->set_pagelayout('admin');
$PAGE->set_title('a');
$PAGE->set_heading(get_string('confirmation', 'admin'));

echo $OUTPUT->header();

if ($confirm and confirm_sesskey()) {
    $notifications = '';
    if (!delete_user($USER)) {
        $notifications .= $OUTPUT->notification(get_string('usernotdeletederror', 'error'));
    }
    echo $OUTPUT->box_start('generalbox', 'notice');
    if (!empty($notifications)) {
        echo $notifications;
    } else {
        echo $OUTPUT->notification(get_string('changessaved'), 'notifysuccess');
        require_logout();
    }
    $continue = new single_button(new moodle_url($CFG->wwwroot), get_string('continue'), 'post');
    echo $OUTPUT->render($continue);
    echo $OUTPUT->box_end();
} else {
    echo $OUTPUT->heading("fullname() are you sure you want to delete your account?");
    $formcontinue = new single_button(new moodle_url('delete.php', array('confirm' => 1)), get_string('yes'));

    $formcancel = new single_button(new moodle_url('profile.php', array('id' => $USER->id)), get_string('cancel'), 'get');
    echo $OUTPUT->confirm(get_string('deletecheckaccount', ''), $formcontinue, $formcancel);
}

echo $OUTPUT->footer();
