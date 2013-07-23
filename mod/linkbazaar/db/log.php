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
 * Definition of log events
 *
 * @package    mod_linkbazaar
 * @copyright  2013 Andrew Davis <andrew@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $DB;

$logs = array(
    array('module' => 'linkbazaar', 'action' => 'view', 'mtable' => 'linkbazaar', 'field' => 'name'),
    array('module' => 'linkbazaar', 'action' => 'update', 'mtable' => 'linkbazaar', 'field' => 'name'),
    array('module' => 'linkbazaar', 'action' => 'add', 'mtable' => 'linkbazaar', 'field' => 'name'),
    array('module' => 'linkbazaar', 'action' => 'add link', 'mtable' => 'user', 'field' => $DB->sql_concat('firstname', "' '", 'lastname')),
    array('module' => 'linkbazaar', 'action' => 'delete link', 'mtable' => 'user', 'field' => $DB->sql_concat('firstname', "' '", 'lastname')),
    array('module' => 'linkbazaar', 'action' => 'vote', 'mtable' => 'linkbazaar_link', 'field' => $DB->sql_concat('name', "' '", 'url')),
);
