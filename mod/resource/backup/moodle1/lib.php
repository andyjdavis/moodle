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
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 *
 * @package    mod
 * @subpackage forum
 * @copyright  2011 Mark Nielsen <mark@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Forum conversion handler
 */
class moodle1_mod_resource_handler extends moodle1_mod_handler {

    /**
     * Declare the paths in moodle.xml we are able to convert
     *
     * The method returns list of {@link convert_path} instances.
     * For each path returned, the corresponding conversion method must be
     * defined.
     *
     * Note that the paths /MOODLE_BACKUP/COURSE/MODULES/MOD/FORUM do not
     * actually exist in the file. The last element with the module name was
     * appended by the moodle1_converter class.
     *
     * @return array of {@link convert_path} instances
     */
    public function get_paths() {
        return array(
            new convert_path(
                'resource', '/MOODLE_BACKUP/COURSE/MODULES/MOD/RESOURCE',
                array(
                    'renamefields' => array(
                        'summary' => 'intro',
                    ),
                    'newfields' => array(
                        'introformat' => 0,
                    ),
                )
            )
        );
    }

    /**
     * Converts /MOODLE_BACKUP/COURSE/MODULES/MOD/RESOURCE data
     */
    public function process_resource($data) {
        // get the course module id and context id
        $instanceid = $data['id'];
        $moduleid   = $this->get_moduleid($instanceid);
        $contextid  = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        //resources have been split into several different activity types
        //doing field renaming here rather than in get_paths() as different activity types have had different modifications made
        $activitytype = null;

        switch ($data['type']) {
            case 'text':
                $data['intro']       = text_to_html($data['intro'], false, false, true);
                $data['introformat'] = FORMAT_HTML;
            case 'html':
                $activitytype = 'page';
                break;
            case 'file':
                $activitytype = 'resource';

                unset($data['type']);
                unset($data['reference']);
                unset($data['alltext']);
                unset($data['popup']);
                unset($data['options']);

                $data['tobemigrated'] = 0;
                $data['mainfile'] = null;
                $data['legacyfiles'] = 0;
                $data['legacyfileslast'] = null;
                $data['display'] = 0;
                $data['displayoptions'] = null;
                $data['filterfiles'] = 0;
                $data['revision'] = 0;
                unset($data['mainfile']);
                break;
            case 'directory':
                $activitytype = 'folder';
                break;
            case 'ims':
                $activitytype = 'imscp';
                break;
            default:
                echo 'an unhandled type was received '.$data['type'];
        }

        // we now have all information needed to start writing into the file
        $this->open_xml_writer("activities/{$activitytype}_{$moduleid}/{$activitytype}.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => $activitytype, 'contextid' => $contextid));
        $this->xmlwriter->begin_tag($activitytype, array('id' => $instanceid));

        unset($data['id']); // we already write it as attribute, do not repeat it as child element
        foreach ($data as $field => $value) {
            $this->xmlwriter->full_tag($field, $value);
        }

        $this->xmlwriter->end_tag($activitytype);
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();
    }

    public function on_resource_end() {
        
    }
}