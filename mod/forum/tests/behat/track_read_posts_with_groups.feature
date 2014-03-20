@mod @mod_forum @WIP
Feature: Students can be informed about unread forum posts based on group membership
  In order to ease the forum posts follow up
  As a user
  I need to distinguish the unread posts from the read ones taking into account group membership

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | trackforums |
      | student1 | Student | 1 | student1@asd.com | 1 |
      | student2 | Student | 2 | student2@asd.com | 1 |
    And the following "courses" exist:
      | fullname | shortname | category | Group mode | Separate groups |
      | Course 1 | C1 | 0 | Force group mode | Yes |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "groups" exist:
      | name | description | course | idnumber |
      | Group 1 | G1 description | C1 | G1 |
      | Group 2 | G2 description | C1 | G2 |
    And the following "group members" exist:
      | user | group |
      | student1 | G1 |
      | student2 | G2 |
    And I log in as "admin"
    And I follow "Course 1"
    And I turn editing mode on
    And I set the following administration settings values:
      | Allow forced read tracking | 1 |

  Scenario: Correct number of unread forum posts is displayed with separate groups
    Given I follow "Home"
    And I follow "Course 1"
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Forum type | Standard forum for general use |
      | Description | Test forum description |
      | Read tracking | Force |
      | Group mode | Separate groups |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
      | Group | Group 1 |
    And I log out
    When I log in as "student1"
    And I follow "My home"
    Then I should see "There are new forum posts"
    And I follow "Course 1"
    Then I should see "1 unread post"
    And I log out
    When I log in as "student2"
    And I follow "My home"
    Then I should not see "There are new forum posts"
    And I follow "Course 1"
    Then I should not see "1 unread post"

  Scenario: Correct number of unread forum posts is displayed with visible groups
    Given I follow "Home"
    And I follow "Course 1"
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Forum type | Standard forum for general use |
      | Description | Test forum description |
      | Read tracking | Force |
      | Group mode | Visible groups |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
      | Group | Group 1 |
    And I log out
    When I log in as "student1"
    And I follow "My home"
    Then I should see "There are new forum posts"
    And I follow "Course 1"
    Then I should see "1 unread post"
    And I log out
    When I log in as "student2"
    And I follow "My home"
    Then I should see "There are new forum posts"
    And I follow "Course 1"
    Then I should see "1 unread post"
    
  Scenario: Correct number of unread forum posts is displayed with no groups
    Given I follow "Home"
    And I follow "Course 1"
    Given I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Forum type | Standard forum for general use |
      | Description | Test forum description |
      | Read tracking | Force |
      | Group mode | No groups |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Test post subject |
      | Message | Test post message |
    And I log out
    When I log in as "student1"
    And I follow "My home"
    Then I should see "There are new forum posts"
    And I follow "Course 1"
    Then I should see "1 unread post"
    And I log out
    When I log in as "student2"
    And I follow "My home"
    Then I should see "There are new forum posts"
    And I follow "Course 1"
    Then I should see "1 unread post"
