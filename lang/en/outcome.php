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
 * Strings for core subsystem 'outcome', language 'en'
 *
 * @package   core_outcome
 * @category  outcome
 * @copyright Copyright (c) 2013 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Mark Nielsen
 */

$string['outcomedescriptionrequired'] = 'Outcome description is required.';
$string['outcomesetidrequired'] = 'Outcome set ID is required.';
$string['outcomeidnumberrequired'] = 'Outcome Unique ID is required.';
$string['nooutcomesetsmapped'] = 'There are no outcome sets currently in use in this course. You may identify outcome set usage in the <a href="{$a}" title="Edit course settings">Course Settings</a> page.';
$string['disabled'] = 'Disabled';
$string['legacyoutcomes'] = 'Legacy Outcomes';
$string['newoutcomes'] = 'New Outcomes';
$string['bothlegacynew'] = 'Both Legacy and New';
$string['enableoutcomes_help'] = 'Choose which version of Outcomes to enable (also known as Competencies, Goals, Standards or Criteria).
The New Outcomes system supports association of site-wide Outcomes to content, which students can be measured against and marked as having
met the competency in their personal profile.  With Legacy Outcomes, activities can explicitly be graded using one or more scales that are
tied to Legacy Outcome statements.';
$string['outcomesetsforx'] = 'Outcome sets for {$a}';
$string['outcomesets'] = 'Outcome sets';
$string['completionmarkingforx'] = 'Completion marking for {$a}';
$string['editingoutcomeset'] = 'Editing outcome set';
$string['fullname'] = 'Full name';
$string['mappedcourses'] = 'Mapped courses';
$string['editdeleteexport'] = 'Edit/Delete/Export';
$string['reports'] = 'Reports';
$string['addnewoutcomeset'] = 'Add new outcome set';
$string['outcomes'] = 'Outcomes';
$string['general'] = 'General';
$string['name'] = 'Name';
$string['ok'] = 'OK';
$string['idnumber'] = 'Unique ID';
$string['idnumber_help'] = 'The Unique ID should be a globally unique value.  Meaning it should even be unique across Moodle sites.';
$string['description'] = 'Description';
$string['provider'] = 'Provider';
$string['provider_help'] = 'Keep track of the organization that created this outcome set.';
$string['region'] = 'Region';
$string['region_help'] = 'The region to which this outcome set applies.';
$string['outcomes_help'] = 'Outcomes can be added in a flat form or in a hierarchical format.  Outcomes can be nested to allow better organization.';
$string['add'] = 'Add';
$string['addoutcome'] = 'Add outcome';
$string['edit'] = 'Edit';
$string['delete'] = 'Delete';
$string['deletex'] = 'Delete {$a}';
$string['educationlevels'] = 'Education levels';
$string['educationlevel'] = 'Education level';
$string['educationlevels_help'] = 'A comma separated list of education levels that are related to this outcome.  Example value: 9, 10, 11';
$string['subjects'] = 'Subjects';
$string['subject'] = 'Subject';
$string['subjects_help'] = 'A comma separated list of educational subjects that are related to this outcome.  Example value: Math, English, Biology';
$string['assessable'] = 'Assessable';
$string['assessable_help'] = 'If an outcome is assessable, then the outcome can be mapped to content and a user can earn the outcome.  If an outcome is not assessable, then it is used only for visual purposes.';
$string['docnum'] = 'Doc number';
$string['docnum_help'] = 'Organizational value, like 1.2.3 or A.B.C';
$string['failedtosavereasonparent'] = 'Failed to save outcome "{$a}" because the parent outcome could not be found.';
$string['editx'] = 'Edit {$a}';
$string['addchildoutcome'] = 'Add an outcome underneath {$a}';
$string['changessavedtox'] = 'Changes saved to {$a}';
$string['idnumbernotunique'] = 'The value entered is not unique.';
$string['outcomesetdeleted'] = 'The "{$a->name}" outcome set has successfully been deleted ({$a->undo}).';
$string['close'] = 'Close';
$string['undo'] = 'Undo';
$string['outcomesetrestored'] = 'The "{$a} outcome set has been successfully restored.';
$string['selectoutcomesets'] = 'Select outcome sets';
$string['selectoutcomesets_help'] = 'Select the outcome sets that you wish to associate to this course.  When an outcome set is associated to a course, then its outcomes can be mapped to content and it is used in reporting.';
$string['chooseoutcomeset'] = 'Choose an outcome set...';
$string['outcomeset'] = 'Outcome set';
$string['selectoutcomes'] = 'Select outcomes';
$string['selectoutcome'] = 'Select outcome';
$string['removeoutcome'] = 'Remove outcome';
$string['noselectedoutcomes'] = 'No outcomes are currently mapped.  To map this item against outcomes, please click above.';
$string['selectoutcomes_help'] = 'Select outcomes to associate to this content.';
$string['nooutcomesetsfound'] = 'No available outcome sets were found.  Please ask a site administrator to create an outcome set.';
$string['nooutcomesfound'] = 'No available outcomes were found.  Please ask a site administrator to create an outcome set with assessable outcomes and map the outcome set to this course.';
$string['closex'] = 'Close {$a}';
$string['openx'] = 'Open {$a}';
$string['outcomesforx'] = 'Outcomes for {$a}';
$string['alleducationlevels'] = 'All education levels';
$string['allsubjects'] = 'All subjects';
$string['outcome_placement'] = 'Place outcome';
$string['outcome_reference'] = 'Reference outcome';
$string['asfirstchild'] = 'as first child';
$string['before'] = 'before';
$string['after'] = 'after';
$string['moveoutcome'] = 'Move outcome';
$string['movex'] = 'Move {$a}';
$string['move'] = 'Move';
$string['outcomemodified'] = 'The modified outcomes have not been saved.  Press <em>Save changes</em> or use the <em>Cancel</em> button to revert all changes.';
$string['mappedcoursesforx'] = 'View mapped courses for {$a}';
$string['viewcoursex'] = 'View course {$a}';
$string['coursesmappedtox'] = 'Courses mapped to {$a}';
$string['coverage'] = 'Coverage';
$string['report:marking'] = 'Completion Marking';
$string['report:markingx'] = 'Completion Marking report for {$a} outcome set';
$string['user'] = 'User';
$string['filters'] = 'Filters';
$string['filter'] = 'Filter';
$string['viewitems'] = 'View items';
$string['id'] = 'ID';
$string['outcome'] = 'Outcome';
$string['completion'] = 'Completion';
$string['averagegrade'] = 'Average Grade';
$string['scaleitems'] = 'Scale Items';
$string['complete'] = 'Complete';
$string['markasearned'] = 'Mark as an earned outcome';
$string['activitycompletion'] = 'Activity Completion';
$string['type'] = 'Type';
$string['scalevalue'] = 'Scale value';
$string['unknown'] = '<em>Unknown</em>';
$string['content'] = 'Content';
$string['pointvalue'] = 'Point value';
$string['importoutcomeset'] = 'Import Outcome Set';
$string['importformat'] = 'Import format';
$string['importfile'] = 'Import file';
$string['fileuploadfailed'] = 'The file failed to upload, please try again.';
$string['importcomplete'] = 'Successfully imported <em>{$a}</em> outcome set.';
$string['export'] = 'Export';
$string['exportx'] = 'Export {$a}';
$string['outcomeidnumbernotunique'] = 'Outcome <em>{$a->description}</em> cannot be created because the Unique ID of <em>{$a->idnumber}</em> is already in use by <em>{$a->conflict}</em> outcome.';
$string['outcomesetidnumbererror'] = 'Outcome set <em>{$a->name}</em> cannot be created because the Unique ID of <em>{$a->idnumber}</em> is already in use by <em>{$a->conflict}</em> outcome set.';
$string['nogradebookusers'] = 'This report cannot be run because there are no gradable users enrolled in the course.';
$string['nothingimported'] = 'Nothing was imported, perhaps the wrong import format was selected.  Please verify the file contents, file format and try again.';
$string['group'] = 'Group';
$string['allgroups'] = 'All groups';
$string['associatedcontent'] = 'Associated content';
$string['associatedcontentx'] = 'Associated content ({$a})';
$string['report:course_performance'] = 'Activity and Performance';
$string['report:course_performancex'] = 'Activity and Performance for {$a}';
$string['outcomeperformanceforx'] = 'Activity and Performance for {$a}';
$string['report:course_coverage'] = 'Coverage';
$string['report:course_coveragex'] = 'Coverage for {$a}';
$string['outcomecoverageforx'] = 'Coverage for {$a}';
$string['report:course_unmapped'] = 'Unmapped Content Items and Quiz Questions';
$string['report:course_unmappedx'] = 'Unmapped Content Items and Quiz Questions';
$string['outcomeunmappedforx'] = 'Unmapped Content Items and Quiz Questions}';
$string['unmappedactivitiesandresources'] = 'Unmapped Activities and Resources';
$string['unmappedquestions'] = 'Unmapped Quiz Questions';
$string['title'] = 'Title';
$string['quizzes'] = 'Quizzes';
$string['map'] = 'Map';
$string['resources'] = 'Resources';
$string['activities'] = 'Activities';
$string['questions'] = 'Questions';
$string['questionbanknote'] = 'Includes some items in question bank which may not be in a quiz.';
$string['pushaddwarning'] = 'You have selected an outcome set but have not clicked <em>Add</em>.  Please check the subject and education level and click <em>Add</em> or unselect the outcome set.';
$string['warning'] = 'Warning';
$string['uniqueidchangewarning'] = 'Changing the Unique ID is not advisable as it can cause failures in linking outcome data with backup and restores.';
$string['nonassessablewarning'] = 'The associated outcome has been marked as (deleted or non-assessable). You may want to update this rubric to match the updated standard.';
$string['totalpoints'] = 'Total points';
$string['outcomeawardwarning'] = 'This student will retain the <em>{$a}</em> outcome as met at the site level even though it is no longer met in this course.  Please contact the site administrator if you think the outcome should be revoked for the student at the site level.';
$string['notmappedwarning'] = 'Additional information';
$string['notmappedwarning_help'] = 'This activity does not have outcomes directly mapped to it, but contains an advanced grading method or questions that are mapped to outcomes.';
$string['moveoutcomeoptslegend'] = 'Choose new position and reference outcome:';
