@mod @mod_lesson
Feature: A lesson's settings can be edited
  In order to edit an existing lesson
  As a teacher
  I need to edit an existing lesson
  
  Background:
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Lesson" to section "1" and I fill the form with:
      | Name | Test lesson name |
    And I follow "Test lesson name"

  @javascript
  Scenario: Edit a lesson
    When I follow "Edit settings"
    Then I should not see "Notice"
    And I should not see "Call Stack"
