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
 * Defines a form to add a stamp
 *
 * @package    mod_linkbazaar
 * @copyright  2013 Andrew Davis <andrew@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Defines a form to add a link
 */
class linkbazaar_link_form extends moodleform {

    /**
     * Defines the form elements
     */
    public function definition() {
        global $OUTPUT;

        $mform = $this->_form;
        $data  = $this->_customdata;

        $mform->addElement('header', 'linkform', get_string('addlink', 'linkbazaar'));
        
        $mform->addElement('text', 'name', get_string('linkname', 'linkbazaar'), 'size="48"');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $mform->addElement('text', 'link', get_string('linkurl', 'linkbazaar'), 'size="48"');
        $mform->setType('link', PARAM_TEXT);
        $mform->addRule('link', get_string('required'), 'required', null, 'client');
        $mform->addRule('link', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        //----------------------------------------------------------------------
        $mform->addGroup(array(
            $mform->createElement('submit', 'submit', get_string('addstampbutton', 'stampcoll')),
            ), 'controlbuttons', '&nbsp;', array(' '), false);

        //----------------------------------------------------------------------
        $mform->addElement('hidden', 'user');
        $mform->setType('user', PARAM_INT);
    }
}
