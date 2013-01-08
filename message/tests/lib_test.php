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
 * Tests for messagelib.php.
 *
 * @package    core_message
 * @copyright  2013 Andrew Davis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/message/lib.php');

/**
 * @copyright  2013 Andrew Davis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class core_message_lib_testcase extends advanced_testcase {

    public function test_message_count_unread_messages() {
        $this->resetAfterTest(true);

        $userfrom = $this->getDataGenerator()->create_user();
        $userto = $this->getDataGenerator()->create_user();
        $uninvolveduser = $this->getDataGenerator()->create_user();

        $message = $this->insert_test_message($userfrom, $userto);

        $this->assertTrue(message_count_unread_messages($userfrom) == 0);
        $this->assertTrue(message_count_unread_messages($userto) == 1);
        $this->assertTrue(message_count_unread_messages($userto, $uninvolveduser) == 0);
    }

    public function test_message_mark_message_read() {
        $this->resetAfterTest(true);

        $userfrom = $this->getDataGenerator()->create_user();
        $userto = $this->getDataGenerator()->create_user();

        $message = $this->insert_test_message($userfrom, $userto);
        $this->assertTrue(message_count_unread_messages($userto) == 1);

        $returnedid = message_mark_message_read($message, time());
        $this->assertTrue($returnedid == $message->id);
        $this->assertTrue(message_count_unread_messages($userto) == 0);
    }

    public function test_message_get_history() {
        $this->resetAfterTest(true);

        $userfrom = $this->getDataGenerator()->create_user();
        $userto = $this->getDataGenerator()->create_user();
        $uninvolveduser = $this->getDataGenerator()->create_user();

        $message = $this->insert_test_message($userfrom, $userto);
        $this->assertTrue(message_count_unread_messages($userto) == 1);

        $history = message_get_history($userfrom, $userto);
        $this->assertTrue(count($history) == 1);
        $this->assertTrue( isset($history[$message->id]) );
        
        $history = message_get_history($userfrom, $uninvolveduser);
        $this->assertTrue(count($history) == 0);
    }

    private function insert_test_message($userfrom, $userto) {
        global $DB;

        $savemessage = new stdClass();
        $savemessage->useridfrom        = $userfrom->id;
        $savemessage->useridto          = $userto->id;
        $savemessage->subject           = "Message from {$userfrom->id} to {$userto->id}";
        $savemessage->fullmessage       = "Full message from {$userfrom->id} to {$userto->id}";
        $savemessage->fullmessageformat = FORMAT_HTML;
        $savemessage->fullmessagehtml   = "<p>Full message from {$userfrom->id} to {$userto->id}</p>";
        $savemessage->smallmessage      = "Small message from {$userfrom->id} to {$userto->id}";
        $savemessage->notification = 0;
        $savemessage->contexturl = null;
        $savemessage->contexturlname = null;
        $savemessage->timecreated = time();
        
        // Not using message_send() as the recipient message settings can cause the message to be marked read immediately.
        $savemessage->id = $DB->insert_record('message', $savemessage);
        return $savemessage;
    }
}
