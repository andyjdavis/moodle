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
 * Unit tests for lib/modinfolib.php.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Andrew Davis
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Unit tests for messagelib.php
 *
 * @copyright 2012 Andrew Davis
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class messagelib_testcase extends advanced_testcase {
    /**
     * Test message_get_providers_for_user()
     */
    public function test_message_get_providers_for_user() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        // It would probably be better to use a quiz instance as it has capability controlled messages
        // however mod_quiz doesn't have a data generator
        // Instead we're going to use backup notifications and give and take away the capability at various levels
        $assign = $this->getDataGenerator()->create_module('assign', array('course'=>$course->id));
        $modulecontext = context_module::instance($assign->id);

        // Create and enrol a teacher
        $teacherrole = $DB->get_record('role', array('shortname'=>'editingteacher'), '*', MUST_EXIST);
        $teacher = $this->getDataGenerator()->create_user();
        role_assign($teacherrole->id, $teacher->id, $coursecontext);
        $enrolplugin = enrol_get_plugin('manual');
        $enrolplugin->add_instance($course);
        $enrolinstances = enrol_get_instances($course->id, false);
        foreach ($enrolinstances as $enrolinstance) {
            if ($enrolinstance->enrol === 'manual') {
                break;
            }
        }
        $enrolplugin->enrol_user($enrolinstance, $teacher->id);

        // Make the teacher the current user
        $this->setUser($teacher);

        // Teacher shouldn't have the required capability so they shouldn't be able to see the backup message
        $this->assertFalse(has_capability('moodle/site:config', $modulecontext));
        $providers = message_get_providers_for_user($teacher->id);
        foreach($providers as $provider) {
            if ($provider->name == 'backup') {
                $this->fail('User has access to backup notifications and they should not.');
            }
        }

        // Give the user the required capability in an activity module
        // They should now be able to see the backup message
        assign_capability('moodle/site:config', CAP_ALLOW, $teacherrole->id, $modulecontext->id, true);
        accesslib_clear_all_caches_for_unit_testing();
        $modulecontext = context_module::instance($assign->id);
        $this->assertTrue(has_capability('moodle/site:config', $modulecontext));

        $providers = message_get_providers_for_user($teacher->id);
        $foundprovider = null;
        foreach($providers as $provider) {
            if ($provider->name == 'backup') {
                $foundprovider = $provider;
                break;
            }
        }
        $this->assertTrue(!empty($foundprovider));

        // Prohibit the capability for the user at the course level
        // This overrules the CAP_ALLOW at the module level
        // They should not be able to see the backup message
        assign_capability('moodle/site:config', CAP_PROHIBIT, $teacherrole->id, $coursecontext->id, true);
        accesslib_clear_all_caches_for_unit_testing();
        $modulecontext = context_module::instance($assign->id);
        $this->assertFalse(has_capability('moodle/site:config', $modulecontext));

        $providers = message_get_providers_for_user($teacher->id);
        foreach($providers as $provider) {
            if ($provider->name == 'backup') {
                $this->fail('User has access to backup notifications and they should not.');
            }
        }
    }
}
