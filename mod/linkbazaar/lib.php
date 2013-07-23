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
 * Library of interface functions and constants for mod_linkbazaar
 * *
 * @package    mod_linkbazaar
 * @copyright  2013 Andrew Davis <andrew@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function linkbazaar_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:         return true;
        default:                        return null;
    }
}

/**
 * Saves a new instance of the mod_bazaar into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $newmodule An object from the form in mod_form.php
 * @param mod_linkbazaar_mod_form $mform
 * @return int The id of the newly inserted linkbazaar record
 */
function linkbazaar_add_instance(stdClass $newmodule, mod_linkbazaar_mod_form $mform = null) {
    global $DB;

    $newmodule->timecreated = time();

    return $DB->insert_record('linkbazaar', $newmodule);
}

/**
 * Updates an instance of the linkbazaar in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $newmodule An object from the form in mod_form.php
 * @param mod_linkbazaar_mod_form $mform
 * @return boolean Success/Fail
 */
function linkbazaar_update_instance(stdClass $linkbazaar, mod_newmodule_mod_form $mform = null) {
    global $DB;

    $linkbazaar->timemodified = time();
    $linkbazaar->id = $newmodule->instance;

    return $DB->update_record('linkbazaar', $linkbazaar);
}

/**
 * Removes an instance of the linkbazaar from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function linkbazaar_delete_instance($id) {
    global $DB, $CFG;

    if (! $linkbazaar = $DB->get_record('linkbazaar', array('id' => $id))) {
        return false;
    }

    if ($links = $DB->get_records('linkbazaar_link', array('linkbazaarid' => $linkbazaar->id))) {
        require_once($CFG->dirroot.'/mod/linkbazaar/locallib.php');
        foreach ($links as $link) {
            linkbazaar_delete_link($link->id);
        }
    }

    $DB->delete_records('linkbazaar', array('id' => $linkbazaar->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdClass|null
 */
/*function newmodule_user_outline($course, $user, $mod, $linkbazaar) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}*/

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $newmodule the module instance record
 * @return void, is supposed to echp directly
 */
/*function newmodule_user_complete($course, $user, $mod, $newmodule) {
}*/

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in newmodule activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function linkbazaar_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link newmodule_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function linkbazaar_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@see newmodule_get_recent_mod_activity()}

 * @return void
 */
function linkbazaar_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function linkbazaar_cron () {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function linkbazaar_get_extra_capabilities() {
    return array();
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding newmodule nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the newmodule module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
/*function newmodule_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm) {
}*/

/**
 * Extends the settings navigation with the newmodule settings
 *
 * This function is called when the context for the page is a newmodule module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $newmodulenode {@link navigation_node}
 */
/*function newmodule_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $newmodulenode=null) {
}*/
