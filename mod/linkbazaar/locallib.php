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
 * Internal library of functions for mod_linkbazaar
 *
 * @package    mod_linkbazaar
 * @copyright  2013 Andrew Davis <andrew@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class representing a linkbazaar instance
 *
 * @package   mod_linkbazaar
 * @copyright 2013 Andrew Davis <andrew@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class linkbazaar {
    public $cm;

    /** @var stdClass course record */
    public $course;

    /** @var stdClass context object */
    public $context;

    /** @var int linkbazaar instance identifier */
    public $id;

    /** @var string linkbazaar activity name */
    public $name;

    /** @var string introduction to the activity */
    public $intro;

    /** @var int format of the {@link $intro} */
    public $introformat;

    /** @var int the last modification time stamp */
    public $timemodified;
    
    /** @var array The links within this Link Bazaar insance */
    public $links;
    
    /**
     * Constructor.
     *
     * @param int $id The linkbazaar ID
     */
    public function __construct(stdClass $dbrecord, stdClass $cm, stdClass $course, stdClass $context=null) {
        global $DB;
        
        foreach ($dbrecord as $field => $value) {
            if (property_exists('linkbazaar', $field)) {
                $this->{$field} = $value;
            }
        }
        $this->cm = $cm;
        $this->course = $course;
        
        if (is_null($context)) {
            $this->context = context_module::instance($this->cm->id);
        } else {
            $this->context = $context;
        }
        
        $sql = "SELECT link.id, link.url, link.name, link.userid, count(vote.id) as votes
                  FROM {linkbazaar_link} link
             LEFT JOIN {linkbazaar_upvote} vote ON vote.linkid = link.id
                 WHERE link.linkbazaarid = :linkbazaarid
              GROUP BY link.id, vote.linkid
              ORDER BY votes DESC";

        $params = array('linkbazaarid' => $this->id);
        $this->links = $DB->get_records_sql($sql, $params);
    }
    
    public function get_user_votes($userid) {
        global $USER, $DB;

        $sql = "SELECT vote.linkid
                  FROM {linkbazaar_upvote} vote
                  JOIN {linkbazaar_link} link ON link.id = vote.linkid
                  JOIN {linkbazaar} bazaar ON bazaar.id = link.linkbazaarid
                 WHERE vote.userid = :user AND bazaar.course = :course";

        $params = array('user' => $USER->id, 'course' => $this->course->id);
        return $DB->get_records_sql($sql, $params);
    }
}

function linkbazaar_delete_link($linkid) {
    global $DB;
    $DB->delete_records('linkbazaar_upvote', array('linkid' => $linkid));
    $DB->delete_records('linkbazaar_link', array('id' => $linkid));
}
