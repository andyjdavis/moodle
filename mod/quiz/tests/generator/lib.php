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

defined('MOODLE_INTERNAL') || die();

/**
 * Quiz module test data generator class
 *
 * @package mod_quiz
 * @copyright 2012 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_generator extends phpunit_module_generator {

    /**
     * Create new quiz module instance.
     * @param array|stdClass $record
     * @param array $options (mostly course_module properties)
     * @return stdClass activity record with extra cmid field
     */
    public function create_instance($record = null, array $options = null) {
        global $CFG;
        require_once("$CFG->dirroot/mod/quiz/lib.php");

        $this->instancecount++;
        $i = $this->instancecount;

        $record = (object)(array)$record;
        $options = (array)$options;

        if (empty($record->course)) {
            throw new coding_exception('module generator requires $record->course');
        }
        if (isset($options['idnumber'])) {
            $record->cmidnumber = $options['idnumber'];
        } else {
            $record->cmidnumber = '';
        }

        $defaultquizsettings = array(
            'name'                   => get_string('pluginname', 'data').' '.$i,
            'intro'                  => 'Test quiz ' . $i,
            'introformat'            => FORMAT_MOODLE,
            'timeopen'               => 0,
            'timeclose'              => 0,
            'preferredbehaviour'     => 'deferredfeedback',
            'attempts'               => 0,
            'attemptonlast'          => 0,
            'grademethod'            => QUIZ_GRADEHIGHEST,
            'decimalpoints'          => 2,
            'questiondecimalpoints'  => -1,
            'reviewattempt'          => DURING | IMMEDIATELY_AFTER | LATER_WHILE_OPEN | AFTER_CLOSE,
            'reviewcorrectness'      => DURING | IMMEDIATELY_AFTER | LATER_WHILE_OPEN | AFTER_CLOSE,
            'reviewmarks'            => DURING | IMMEDIATELY_AFTER | LATER_WHILE_OPEN | AFTER_CLOSE,
            'reviewspecificfeedback' => DURING | IMMEDIATELY_AFTER | LATER_WHILE_OPEN | AFTER_CLOSE,
            'reviewgeneralfeedback'  => DURING | IMMEDIATELY_AFTER | LATER_WHILE_OPEN | AFTER_CLOSE,
            'reviewrightanswer'      => DURING | IMMEDIATELY_AFTER | LATER_WHILE_OPEN | AFTER_CLOSE,
            'reviewoverallfeedback'  => DURING | IMMEDIATELY_AFTER | LATER_WHILE_OPEN | AFTER_CLOSE,
            'questionsperpage'       => 1,
            'shufflequestions'       => 0,
            'shuffleanswers'         => 1,
            'questions'              => '',
            'sumgrades'              => 0,
            'grade'                  => 0,
            'timecreated'            => time(),
            'timemodified'           => time(),
            'timelimit'              => 0,
            'overduehandling'        => 'autoabandon',
            'graceperiod'            => 86400,
            'password'               => '',
            'subnet'                 => '',
            'browsersecurity'        => '',
            'delay1'                 => 0,
            'delay2'                 => 0,
            'showuserpicture'        => 0,
            'showblocks'             => 0,
            'navmethod'              => QUIZ_NAVMETHOD_FREE,
        );

        foreach ($defaultquizsettings as $name => $value) {
            if (!isset($record->{$name})) {
                $record->{$name} = $value;
            }
        }

        $record->coursemodule = $this->precreate_course_module($record->course, $options);
        $id = assign_add_instance($record, null);
        return $this->post_add_instance($id, $record->coursemodule);
    }
}
