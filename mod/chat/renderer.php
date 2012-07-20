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
 * Workshop module renderering methods are defined here
 *
 * @package    mod_chat
 * @copyright  2012 Andrew Davis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Chat module renderer class
 *
 * @copyright 2012 Andrew Davis
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_chat_renderer extends plugin_renderer_base {

    protected function render_event_message(event_message $eventmessage) {
        return <<<TEMPLATE
<div class="chat-event">
<span class="time">{$eventmessage->time}</span>
<a target='_blank' href="{$eventmessage->senderprofile}">{$eventmessage->sendername}</a>
<span class="event">{$eventmessage->event}</span>
</div>
TEMPLATE;
    }

    /**
     * Render a user message
     *
     * @param user_message $usermessage the message to display
     * @return string html code
     */
    protected function render_user_message(user_message $usermessage) {

        $messageclass = 'left';
        if (!empty($usermessage->ismymessage)) {
            $messageclass = 'right';
        }

        $output = html_writer::start_tag('div', array('class' => 'chat-message', 'align' => $messageclass));
        $output .= html_writer::tag('span', $usermessage->message, array('class'=>"triangle-isosceles $messageclass"));
        $output .= html_writer::tag('span', $usermessage->avatar, array('class'=>'picture'));
        $output .= html_writer::end_tag('div');

        $output .= html_writer::start_tag('div', array('class' => 'sendertime', 'align' => $messageclass));
        $output .= html_writer::tag('span', $usermessage->time, array('class'=>'time'));
        $output .= html_writer::tag('span', $usermessage->sendername, array('class'=>'user'));
        $output .= html_writer::end_tag('div');

        return $output;
    }
}
