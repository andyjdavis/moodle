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
     * Note that the paths /MOODLE_BACKUP/COURSE/MODULES/MOD/RESOURCE do not
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
        global $CFG;

        switch ($data['type']) {
            case 'text':
            case 'html':
                $handler = new moodle1_mod_page_handler($this->converter, $this->plugintype, $this->pluginname);
                return $handler->process_page($data);
            case 'directory':
                $handler = new moodle1_mod_folder_handler($this->converter, $this->plugintype, $this->pluginname);
                return $handler->process_folder($data);
            case 'ims':
                $handler = new moodle1_mod_imscp_handler($this->converter, $this->plugintype, $this->pluginname);
                return $handler->process_imscp($data);
        }

        //only $data['type'] == "file" should get to here

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

        // get the course module id and context id
        $instanceid = $data['id'];
        $moduleid   = $this->get_moduleid($instanceid);
        $contextid  = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        // we now have all information needed to start writing into the file
        $this->open_xml_writer("activities/resource_{$moduleid}/resource.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => 'resource', 'contextid' => $contextid));
        $this->xmlwriter->begin_tag('resource', array('id' => $instanceid));

        unset($data['id']); // we already write it as attribute, do not repeat it as child element
        foreach ($data as $field => $value) {
            $this->xmlwriter->full_tag($field, $value);
        }

        //doing this here at $this->xmlwriter is null by the time we reach on_resource_end()
        $this->xmlwriter->end_tag('resource');
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();
    }

    public function on_resource_end() {
    }
}