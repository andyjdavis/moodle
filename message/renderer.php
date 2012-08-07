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
 * Contains renderer objects for messaging
 *
 * @package    core_message
 * @copyright  2011 Lancaster University Network Services Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * message Renderer
 *
 * Class for rendering various message objects
 *
 * @package    core_message
 * @subpackage message
 * @copyright  2011 Lancaster University Network Services Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_message_renderer extends plugin_renderer_base {

    /**
     * Display the interface to manage message outputs
     *
     * @param  array  $processors array of objects containing message processors
     * @return string The text to render
     */
    public function manage_messageoutputs($processors) {
        global $CFG;
        // Display the current workflows
        $table = new html_table();
        $table->attributes['class'] = 'generaltable';
        $table->data        = array();
        $table->head        = array(
            get_string('name'),
            get_string('enable'),
            get_string('settings'),
        );
        $table->colclasses = array(
            'displayname', 'availability', 'settings',
        );

        foreach ($processors as $processor) {
            $row = new html_table_row();
            $row->attributes['class'] = 'messageoutputs';

            // Name
            $name = new html_table_cell(get_string('pluginname', 'message_'.$processor->name));

            // Enable
            $enable = new html_table_cell();
            $enable->attributes['class'] = 'mdl-align';
            if (!$processor->available) {
                $enable->text = html_writer::nonempty_tag('span', get_string('outputnotavailable', 'message'), array('class' => 'error'));
            } else if (!$processor->configured) {
                $enable->text = html_writer::nonempty_tag('span', get_string('outputnotconfigured', 'message'), array('class' => 'error'));
            } else if ($processor->enabled) {
                $url = new moodle_url('/admin/message.php', array('disable' => $processor->id, 'sesskey' => sesskey()));
                $enable->text = html_writer::link($url, html_writer::empty_tag('img',
                    array('src'   => $this->output->pix_url('i/hide'),
                          'class' => 'icon',
                          'title' => get_string('outputenabled', 'message'),
                          'alt'   => get_string('outputenabled', 'message'),
                    )
                ));
            } else {
                $name->attributes['class'] = 'dimmed_text';
                $url = new moodle_url('/admin/message.php', array('enable' => $processor->id, 'sesskey' => sesskey()));
                $enable->text = html_writer::link($url, html_writer::empty_tag('img',
                    array('src'   => $this->output->pix_url('i/show'),
                          'class' => 'icon',
                          'title' => get_string('outputdisabled', 'message'),
                          'alt'   => get_string('outputdisabled', 'message'),
                    )
                ));
            }
            // Settings
            $settings = new html_table_cell();
            if ($processor->available && $processor->hassettings) {
                $settingsurl = new moodle_url('settings.php', array('section' => 'messagesetting'.$processor->name));
                $settings->text = html_writer::link($settingsurl, get_string('settings', 'message'));
            }

            $row->cells = array($name, $enable, $settings);
            $table->data[] = $row;
        }
        return html_writer::table($table);
    }

    /**
     * Display the interface to manage default message outputs
     *
     * @param  array $processors  array of objects containing message processors
     * @param  array $providers   array of objects containing message providers
     * @param  array $preferences array of objects containing current preferences
     * @return string The text to render
     */
    public function manage_defaultmessageoutputs($processors, $providers, $preferences) {
        global $CFG;

        // Prepare list of options for dropdown menu
        $options = array();
        foreach (array('disallowed', 'permitted', 'forced') as $setting) {
            $options[$setting] = get_string($setting, 'message');
        }

        $output = html_writer::start_tag('form', array('id'=>'defaultmessageoutputs', 'method'=>'post'));
        $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'sesskey', 'value'=>sesskey()));

        // Display users outputs table
        $table = new html_table();
        $table->attributes['class'] = 'generaltable';
        $table->data        = array();
        $table->head        = array('');

        // Populate the header row
        foreach ($processors as $processor) {
            $table->head[]  = get_string('pluginname', 'message_'.$processor->name);
        }
        // Generate the matrix of settings for each provider and processor
        foreach ($providers as $provider) {
            $row = new html_table_row();
            $row->attributes['class'] = 'defaultmessageoutputs';
            $row->cells = array();

            // Provider Name
            $providername = get_string('messageprovider:'.$provider->name, $provider->component);
            $row->cells[] = new html_table_cell($providername);

            // Settings for each processor
            foreach ($processors as $processor) {
                $cellcontent = '';
                foreach (array('permitted', 'loggedin', 'loggedoff') as $setting) {
                    // pepare element and preference names
                    $elementname = $provider->component.'_'.$provider->name.'_'.$setting.'['.$processor->name.']';
                    $preferencebase = $provider->component.'_'.$provider->name.'_'.$setting;
                    // prepare language bits
                    $processorname = get_string('pluginname', 'message_'.$processor->name);
                    $statename = get_string($setting, 'message');
                    $labelparams = array(
                        'provider'  => $providername,
                        'processor' => $processorname,
                        'state'     => $statename
                    );
                    if ($setting == 'permitted') {
                        $label = get_string('sendingvia', 'message', $labelparams);
                        // determine the current setting or use default
                        $select = MESSAGE_DEFAULT_PERMITTED;
                        $preference = $processor->name.'_provider_'.$preferencebase;
                        if (array_key_exists($preference, $preferences)) {
                            $select = $preferences->{$preference};
                        }
                        // dropdown menu
                        $cellcontent = html_writer::label($label, $elementname, true, array('class' => 'accesshide'));
                        $cellcontent .= html_writer::select($options, $elementname, $select, false, array('id' => $elementname));
                        $cellcontent .= html_writer::tag('div', get_string('defaults', 'message'));
                    } else {
                        $label = get_string('sendingviawhen', 'message', $labelparams);
                        // determine the current setting based on the 'permitted' setting above
                        $checked = false;
                        if ($select == 'forced') {
                            $checked = true;
                        } else if ($select == 'permitted') {
                            $preference = 'message_provider_'.$preferencebase;
                            if (array_key_exists($preference, $preferences)) {
                                $checked = (int)in_array($processor->name, explode(',', $preferences->{$preference}));
                            }
                        }
                        // generate content
                        $cellcontent .= html_writer::start_tag('div');
                        $cellcontent .= html_writer::label($label, $elementname, true, array('class' => 'accesshide'));
                        $cellcontent .= html_writer::checkbox($elementname, 1, $checked, '', array('id' => $elementname));
                        $cellcontent .= $statename;
                        $cellcontent .= html_writer::end_tag('div');
                    }
                }
                $row->cells[] = new html_table_cell($cellcontent);
            }
            $table->data[] = $row;
        }

        $output .= html_writer::table($table);
        $output .= html_writer::start_tag('div', array('class' => 'form-buttons'));
        $output .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('savechanges','admin'), 'class' => 'form-submit'));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('form');
        return $output;
    }

    /**
     * Display the interface for messaging options
     *
     * @param array $processors Array of objects containing message processors
     * @param array $providers Array of objects containing message providers
     * @param array $preferences Array of objects containing current preferences
     * @param array $defaultpreferences Array of objects containing site default preferences
     * @param bool $notificationsdisabled Indicate if the user's "emailstop" flag is set (shouldn't receive any non-forced notifications)
     * @return string The text to render
     */
    public function manage_messagingoptions($processors, $providers, $preferences, $defaultpreferences, $notificationsdisabled = false) {
        // Filter out enabled, available system_configured and user_configured processors only.
        $readyprocessors = array_filter($processors, create_function('$a', 'return $a->enabled && $a->configured && $a->object->is_user_configured();'));

        // Start the form.  We're not using mform here because of our special formatting needs ...
        $output = html_writer::start_tag('form', array('method'=>'post', 'class' => 'mform'));
        $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'sesskey', 'value'=>sesskey()));

        /// Settings table...
        $output .= html_writer::start_tag('fieldset', array('id' => 'providers', 'class' => 'clearfix'));
        $output .= html_writer::nonempty_tag('legend', get_string('providers_config', 'message'), array('class' => 'ftoggler'));

        // Display the messging options table
        $table = new html_table();
        $table->attributes['class'] = 'generaltable';
        $table->data        = array();
        $table->head        = array('');

        foreach ($readyprocessors as $processor) {
            $table->head[]  = get_string('pluginname', 'message_'.$processor->name);
        }

        $number_procs = count($processors);
        // Populate the table with rows
        foreach ( $providers as $provider) {
            $preferencebase = $provider->component.'_'.$provider->name;

            $headerrow = new html_table_row();
            $providername = get_string('messageprovider:'.$provider->name, $provider->component);
            $providercell = new html_table_cell($providername);
            $providercell->header = true;
            $providercell->colspan = $number_procs + 1;
            $providercell->attributes['class'] = 'c0';
            $headerrow->cells = array($providercell);
            $table->data[] = $headerrow;

            foreach (array('loggedin', 'loggedoff') as $state) {
                $optionrow = new html_table_row();
                $optionname = new html_table_cell(get_string($state.'description', 'message'));
                $optionname->attributes['class'] = 'c0';
                $optionrow->cells = array($optionname);
                foreach ($readyprocessors as $processor) {
                    // determine the default setting
                    $permitted = MESSAGE_DEFAULT_PERMITTED;
                    $defaultpreference = $processor->name.'_provider_'.$preferencebase.'_permitted';
                    if (isset($defaultpreferences->{$defaultpreference})) {
                        $permitted = $defaultpreferences->{$defaultpreference};
                    }
                    // If settings are disallowed or forced, just display the
                    // corresponding message, if not use user settings.
                    if (in_array($permitted, array('disallowed', 'forced'))) {
                        if ($state == 'loggedoff') {
                            // skip if we are rendering the second line
                            continue;
                        }
                        $cellcontent = html_writer::nonempty_tag('div', get_string($permitted, 'message'), array('class' => 'dimmed_text'));
                        $optioncell = new html_table_cell($cellcontent);
                        $optioncell->rowspan = 2;
                        $optioncell->attributes['class'] = 'disallowed';
                    } else {
                        // determine user preferences and use them.
                        $disabled = array();
                        $checked = false;
                        if ($notificationsdisabled) {
                            $disabled['disabled'] = 1;
                        }
                        // See if user has touched this preference
                        if (isset($preferences->{$preferencebase.'_'.$state})) {
                            // User have some preferneces for this state in the database, use them
                            $checked = isset($preferences->{$preferencebase.'_'.$state}[$processor->name]);
                        } else {
                            // User has not set this preference yet, using site default preferences set by admin
                            $defaultpreference = 'message_provider_'.$preferencebase.'_'.$state;
                            if (isset($defaultpreferences->{$defaultpreference})) {
                                $checked = (int)in_array($processor->name, explode(',', $defaultpreferences->{$defaultpreference}));
                            }
                        }
                        $elementname = $preferencebase.'_'.$state.'['.$processor->name.']';
                        // prepare language bits
                        $processorname = get_string('pluginname', 'message_'.$processor->name);
                        $statename = get_string($state, 'message');
                        $labelparams = array(
                            'provider'  => $providername,
                            'processor' => $processorname,
                            'state'     => $statename
                        );
                        $label = get_string('sendingviawhen', 'message', $labelparams);
                        $cellcontent = html_writer::label($label, $elementname, true, array('class' => 'accesshide'));
                        $cellcontent .= html_writer::checkbox($elementname, 1, $checked, '', array_merge(array('id' => $elementname, 'class' => 'notificationpreference'), $disabled));
                        $optioncell = new html_table_cell($cellcontent);
                        $optioncell->attributes['class'] = 'mdl-align';
                    }
                    $optionrow->cells[] = $optioncell;
                }
                $table->data[] = $optionrow;
            }
        }
        $output .= html_writer::start_tag('div');
        $output .= html_writer::table($table);
        $output .= html_writer::end_tag('div');

        $disableallcheckbox = $this->output->help_icon('disableall', 'message') . get_string('disableall', 'message') . html_writer::checkbox('disableall', 1, $notificationsdisabled, '', array('class'=>'disableallcheckbox'));
        $output .= html_writer::nonempty_tag('div', $disableallcheckbox, array('class'=>'disableall'));

        $output .= html_writer::end_tag('fieldset');

        foreach ($processors as $processor) {
            if (($processorconfigform = $processor->object->config_form($preferences)) && $processor->enabled) {
                $output .= html_writer::start_tag('fieldset', array('id' => 'messageprocessor_'.$processor->name, 'class' => 'clearfix'));
                $output .= html_writer::nonempty_tag('legend', get_string('pluginname', 'message_'.$processor->name), array('class' => 'ftoggler'));
                $output .= html_writer::start_tag('div');
                $output .= $processorconfigform;
                $output .= html_writer::end_tag('div');
                $output .= html_writer::end_tag('fieldset');
            }
        }

        $output .= html_writer::start_tag('fieldset', array('id' => 'messageprocessor_general', 'class' => 'clearfix'));
        $output .= html_writer::nonempty_tag('legend', get_string('generalsettings','admin'), array('class' => 'ftoggler'));
        $output .= html_writer::start_tag('div');
        $output .= get_string('blocknoncontacts', 'message').': ';
        $output .= html_writer::checkbox('blocknoncontacts', 1, $preferences->blocknoncontacts, '');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('fieldset');
        $output .= html_writer::start_tag('div', array('class' => 'mdl-align'));
        $output .= html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('updatemyprofile'), 'class' => 'form-submit'));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('form');
        return $output;
    }

    public function render_message_contact_selector(message_contact_selector $contactselector) {
        global $PAGE;

        $output =  html_writer::start_tag('div', array('class' => 'contactselector mdl-align'));

        $output .= $this->usergroup_selector($contactselector);

        if ($contactselector->viewing == MESSAGE_VIEW_UNREAD_MESSAGES) {
            $output .= $this->contacts($contactselector, 1);
        } else if ($contactselector->viewing == MESSAGE_VIEW_CONTACTS || $contactselector->viewing == MESSAGE_VIEW_SEARCH || $contactselector->viewing == MESSAGE_VIEW_RECENT_CONVERSATIONS || $contactselector->viewing == MESSAGE_VIEW_RECENT_NOTIFICATIONS) {
            $output .= $this->contacts($contactselector, 0);
        } else if ($contactselector->viewing == MESSAGE_VIEW_BLOCKED) {
            $output .= $this->blocked_users($contactselector);
        } else if (substr($contactselector->viewing, 0, 7) == MESSAGE_VIEW_COURSE) {
            $courseidtoshow = intval(substr($contactselector->viewing, 7));

            if (!empty($courseidtoshow)
                && array_key_exists($courseidtoshow, $contactselector->coursecontexts)
                && has_capability('moodle/course:viewparticipants', $contactselector->coursecontexts[$courseidtoshow])) {

                $output .= $this->participants($contactselector, $courseidtoshow);
            } else {
                //shouldn't get here. User trying to access a course they're not in perhaps.
                add_to_log(SITEID, 'message', 'view', 'index.php', $viewing);
            }
        }

        $output .=  html_writer::start_tag('form', array('action' => 'index.php','method' => 'GET'));
        $output .=  html_writer::start_tag('fieldset');
        $managebuttonclass = 'visible';
        if ($contactselector->viewing == MESSAGE_VIEW_SEARCH) {
            $managebuttonclass = 'hiddenelement';
        }
        $strmanagecontacts = get_string('search','message');
        $output .=  html_writer::empty_tag('input', array('type' => 'hidden','name' => 'viewing','value' => MESSAGE_VIEW_SEARCH));
        $output .=  html_writer::empty_tag('input', array('type' => 'submit','value' => $strmanagecontacts,'class' => $managebuttonclass));
        $output .=  html_writer::end_tag('fieldset');
        $output .=  html_writer::end_tag('form');

        $output .=  html_writer::end_tag('div');
        return $output;
    }

    /**
     * Create a select box allowing the user to choose to view new messages, course participants etc.
     *
     * Called by render_message_contact_selector()
     * @param message_contact_selector $contactselector The message_contact_selector that will the navigation drop down
     * @return void
     */
    public function usergroup_selector(message_contact_selector $contactselector) {
        $options = array();

        if ($contactselector->unreadcount > 0) {
            $options[MESSAGE_VIEW_UNREAD_MESSAGES] = $contactselector->strunreadmessages;
        }

        $str = get_string('mycontacts', 'message');
        $options[MESSAGE_VIEW_CONTACTS] = $str;

        $options[MESSAGE_VIEW_RECENT_CONVERSATIONS] = get_string('mostrecentconversations', 'message');
        $options[MESSAGE_VIEW_RECENT_NOTIFICATIONS] = get_string('mostrecentnotifications', 'message');

        if (!empty($contactselector->courses)) {
            $courses_options = array();

            foreach($contactselector->courses as $course) {
                if (has_capability('moodle/course:viewparticipants', $contactselector->coursecontexts[$course->id])) {
                    //Not using short_text() as we want the end of the course name. Not the beginning.
                    $shortname = format_string($course->shortname, true, array('context' => $contactselector->coursecontexts[$course->id]));
                    if (textlib::strlen($shortname) > MESSAGE_MAX_COURSE_NAME_LENGTH) {
                        $courses_options[MESSAGE_VIEW_COURSE.$course->id] = '...'.textlib::substr($shortname, -MESSAGE_MAX_COURSE_NAME_LENGTH);
                    } else {
                        $courses_options[MESSAGE_VIEW_COURSE.$course->id] = $shortname;
                    }
                }
            }

            if (!empty($courses_options)) {
                $options[] = array(get_string('courses') => $courses_options);
            }
        }

        $countblocked = count($contactselector->blockedusers);
        if ($countblocked>0) {
            $str = get_string('blockedusers','message', $countblocked);
            $options[MESSAGE_VIEW_BLOCKED] = $str;
        }

        $output = html_writer::start_tag('form', array('id' => 'usergroupform','method' => 'get','action' => ''));
            $output .= html_writer::start_tag('fieldset');
                $output .= html_writer::select($options, 'viewing', $contactselector->viewing, false, array('id' => 'viewing','onchange' => 'this.form.submit()'));
            $output .=html_writer::end_tag('fieldset');
        $output .=html_writer::end_tag('form');

        return $output;
    }

    /**
     * Print $user1's contacts. Called by render_message_contact_selector()
     *
     * @param array $onlinecontacts $user1's contacts which are online
     * @param array $offlinecontacts $user1's contacts which are offline
     * @param array $strangers users which are not contacts but who have messaged $user1
     * @param string $contactselecturl the url to send the user to when a contact's name is clicked
     * @param int $minmessages The minimum number of unread messages required from a user for them to be displayed
     *                         Typically 0 (show all contacts) or 1 (only show contacts from whom we have a new message)
     * @param bool $showactionlinks show action links (add/remove contact etc) next to the users
     * @param string $titletodisplay Optionally specify a title to display above the participants
     * @param object $user2 the user $user1 is talking to. They will be highlighted if they appear in the list of contacts
     * @return void
     */
    public function contacts(message_contact_selector $contactselector, $minmessages = 0) {
        global $CFG, $PAGE, $OUTPUT;

        $countonlinecontacts  = count($contactselector->onlinecontacts);
        $countofflinecontacts = count($contactselector->offlinecontacts);
        $countstrangers       = count($contactselector->strangers);
        $isuserblocked = null;

        $output = '';

        if ($countonlinecontacts + $countofflinecontacts == 0) {
            $output .= html_writer::tag('div', get_string('contactlistempty', 'message'), array('class' => 'heading'));
        }

        $output .= html_writer::start_tag('table', array('id' => 'message_contacts', 'class' => 'boxaligncenter'));

        if (!empty($contactselector->strunreadmessages)) {
            $output .= $this->heading($contactselector->strunreadmessages);
        }

        if($countonlinecontacts) {
            /// print out list of online contacts

            if (empty($contactselector->strunreadmessages)) {
                $output .= $this->heading(get_string('onlinecontacts', 'message', $countonlinecontacts));
            }

            $isuserblocked = false;
            $isusercontact = true;
            foreach ($contactselector->onlinecontacts as $contact) {
                if ($minmessages == 0 || $contact->messagecount >= $minmessages) {
                    $user = new message_contactlist_user($contact, $isusercontact, $isuserblocked, $contactselector->showcontactactionlinks, $contactselector->user2);
                    $output .= $this->render($user);
                }
            }
        }

        if ($countofflinecontacts) {
            /// print out list of offline contacts

            if (empty($titletodisplay)) {
                $output .= $this->heading(get_string('offlinecontacts', 'message', $countofflinecontacts));
            }

            $isuserblocked = false;
            $isusercontact = true;
            foreach ($contactselector->offlinecontacts as $contact) {
                if ($minmessages == 0 || $contact->messagecount >= $minmessages) {
                    $user = new message_contactlist_user($contact, $isusercontact, $isuserblocked, $contactselector->showcontactactionlinks, $contactselector->user2);
                    $output .= $this->render($user);
                }
            }

        }

        /// print out list of incoming contacts
        if ($countstrangers) {
            $output .= $this->heading(get_string('incomingcontacts', 'message', $countstrangers));

            $isuserblocked = false;
            $isusercontact = false;
            foreach ($strangers as $stranger) {
                if ($minmessages == 0 || $stranger->messagecount >= $minmessages) {
                    $user = new message_contactlist_user($stranger, $isusercontact, $isuserblocked, $contactselector->showcontactactionlinks, $contactselector->user2);
                    $output .= $this->render($user);
                }
            }
        }

        $output .= html_writer::end_tag('table');

        if ($countstrangers && ($countonlinecontacts + $countofflinecontacts == 0)) {  // Extra help
            $output .= html_writer::tag('div','('.get_string('addsomecontactsincoming', 'message').')',array('class' => 'note'));
        }
        return $output;
    }

    /**
     * Print users blocked by $user1. Called by render_message_contact_selector()
     *
     * @param array $blockedusers the users blocked by $user1
     * @param string $contactselecturl the url to send the user to when a contact's name is clicked
     * @param bool $showactionlinks show action links (add/remove contact etc) next to the users
     * @param string $titletodisplay Optionally specify a title to display above the participants
     * @param object $user2 the user $user1 is talking to. They will be highlighted if they appear in the list of blocked users
     * @return void
     */
    function blocked_users(message_contact_selector $contactselector) {
        global $DB, $USER;

        $countblocked = count($contactselector->blockedusers);

        $output = html_writer::start_tag('table', array('id' => 'message_contacts', 'class' => 'boxaligncenter'));

        /*if (!empty($titletodisplay)) {
            $output .= html_writer::start_tag('tr');
            $output .= html_writer::tag('td', $titletodisplay, array('colspan' => 3, 'class' => 'heading'));
            $output .= html_writer::end_tag('tr');
        }*/

        if ($countblocked) {
            $output .= html_writer::start_tag('tr');
            $output .= html_writer::tag('td', get_string('blockedusers', 'message', $countblocked), array('colspan' => 3, 'class' => 'heading'));
            $output .= html_writer::end_tag('tr');

            $isuserblocked = true;
            $isusercontact = false;
            foreach ($contactselector->blockedusers as $blockeduser) {
                $user = new message_contactlist_user($blockeduser, false, true, $contactselector->showcontactactionlinks, $contactselector->user2);
                $output .= $this->render($user);
            }
        }

        $output .= html_writer::end_tag('table');
        return $output;
    }

    /**
     * Print a row of contactlist displaying user picture, messages waiting and
     * block links etc
     *
     * @param object $contact contact object containing all fields required for $OUTPUT->user_picture()
     * @param bool $incontactlist is the user a contact of ours?
    * @param bool $isblocked is the user blocked?
    * @param string $selectcontacturl the url to send the user to when a contact's name is clicked
    * @param bool $showactionlinks display action links next to the other users (add contact, block user etc)
    * @param object $selecteduser the user the current user is viewing (if any). They will be highlighted.
    * @return void
    */
    function render_message_contactlist_user(message_contactlist_user $user) {
        global $OUTPUT, $USER, $PAGE;

        $fullname  = fullname($user->contact);
        $fullnamelink  = $fullname;

        $linkclass = '';
        if (!empty($user->selecteduser) && $user->contact->id == $user->user2->id) {
            $linkclass = 'messageselecteduser';
        }

        /// are there any unread messages for this contact?
        if ($user->contact->messagecount > 0 ){
            $fullnamelink = '<strong>'.$fullnamelink.' ('.$user->contact->messagecount.')</strong>';
        }

        $strcontact = $strblock = $strhistory = null;

        if ($user->showactionlinks) {
            $strcontact = message_get_contact_add_remove_link($user->isusercontact, $user->isuserblocked, $user->contact);
            $strblock   = message_get_contact_block_link($user->isusercontact, $user->isuserblocked, $user->contact);
            $strhistory = message_history_link($USER->id, $user->contact->id, true, '', '', 'icon');
        }

        $output = html_writer::start_tag('tr');
        $output .= html_writer::start_tag('td', array('class' => 'pix'));
        $output .= $OUTPUT->user_picture($user->contact, array('size' => 20, 'courseid' => SITEID));
        $output .= html_writer::end_tag('td');

        $output .= html_writer::start_tag('td', array('class' => 'contact'));

        $popupoptions = array(
                'height' => MESSAGE_DISCUSSION_HEIGHT,
                'width' => MESSAGE_DISCUSSION_WIDTH,
                'menubar' => false,
                'location' => false,
                'status' => true,
                'scrollbars' => true,
                'resizable' => true);

        $link = $action = null;
        
        $link = new moodle_url($PAGE->url.'&user2='.$user->contact->id);
        /*if (!empty($selectcontacturl)) {
            $link = new moodle_url($selectcontacturl.'&user2='.$contact->id);
        } else {
            //can $selectcontacturl be removed and maybe the be removed and hardcoded?
            $link = new moodle_url("/message/index.php?id={$user->contact->id}");
            $action = new popup_action('click', $link, "message_{$user->contact->id}", $popupoptions);
        }*/
        $output .= $OUTPUT->action_link($link, $fullnamelink, $action, array('class' => $linkclass,'title' => get_string('sendmessageto', 'message', $fullname)));
        $output .= html_writer::end_tag('td');
        $output .= html_writer::tag('td', '&nbsp;'.$strcontact.$strblock.'&nbsp;'.$strhistory, array('class' => 'link'));
        $output .= html_writer::end_tag('tr');
        return $output;
    }

    /**
     * Print the message history between two users
     *
     * @param object $user1 the current user
     * @param object $user2 the other user
     * @param string $search search terms to highlight
     * @param int $messagelimit maximum number of messages to return
     * @param string $messagehistorylink the html for the message history link or false
     * @param bool $viewingnewmessages are we currently viewing new messages?
     */
    function render_message_history(message_history $messagehistory) {
        global $CFG, $OUTPUT;

        $output = $OUTPUT->box_start('center');
        $output .= html_writer::start_tag('table', array('cellpadding' => '10', 'class' => 'message_user_pictures'));
        $output .= html_writer::start_tag('tr');

        $output .= html_writer::start_tag('td', array('align' => 'center', 'id' => 'user1'));
        $output .= $OUTPUT->user_picture($messagehistory->user1, array('size' => 100, 'courseid' => SITEID));
        $output .= html_writer::tag('div', fullname($messagehistory->user1), array('class' => 'heading'));
        $output .= html_writer::end_tag('td');

        $output .= html_writer::start_tag('td', array('align' => 'center'));
        $output .= html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('t/left'), 'alt' => get_string('from')));
        $output .= html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('t/right'), 'alt' => get_string('to')));
        $output .= html_writer::end_tag('td');

        $output .= html_writer::start_tag('td', array('align' => 'center', 'id' => 'user2'));
        $output .= $OUTPUT->user_picture($messagehistory->user2, array('size' => 100, 'courseid' => SITEID));
        $output .= html_writer::tag('div', fullname($messagehistory->user2), array('class' => 'heading'));

        if (isset($messagehistory->user2->iscontact) && isset($messagehistory->user2->isblocked)) {
            $incontactlist = $messagehistory->user2->iscontact;
            $isblocked = $messagehistory->user2->isblocked;

            $script = null;
            $text = true;
            $icon = false;

            $strcontact = message_get_contact_add_remove_link($incontactlist, $isblocked, $messagehistory->user2, $script, $text, $icon);
            $strblock   = message_get_contact_block_link($incontactlist, $isblocked, $messagehistory->user2, $script, $text, $icon);
            $useractionlinks = $strcontact.'&nbsp;|'.$strblock;

            $output .= html_writer::tag('div', $useractionlinks, array('class' => 'useractionlinks'));
        }

        $output .= html_writer::end_tag('td');
        $output .= html_writer::end_tag('tr');
        $output .= html_writer::end_tag('table');
        $output .= $OUTPUT->box_end();

        if (!empty($messagehistorylink)) {
            $output .= $messagehistorylink;
        }

        /// Get all the messages and print them
        if ($messages = message_get_history($messagehistory->user1, $messagehistory->user2, $messagehistory->messagelimit, $messagehistory->viewingnewmessages)) {
            $tablecontents = '';

            $current = new stdClass();
            $current->mday = '';
            $current->month = '';
            $current->year = '';
            $messagedate = get_string('strftimetime');
            $blockdate   = get_string('strftimedaydate');
            foreach ($messages as $message) {
                if ($message->notification) {
                    $notificationclass = ' notification';
                } else {
                    $notificationclass = null;
                }
                $date = usergetdate($message->timecreated);
                if ($current->mday != $date['mday'] | $current->month != $date['month'] | $current->year != $date['year']) {
                    $current->mday = $date['mday'];
                    $current->month = $date['month'];
                    $current->year = $date['year'];

                    $datestring = html_writer::empty_tag('a', array('name' => $date['year'].$date['mon'].$date['mday']));
                    $tablecontents .= html_writer::tag('div', $datestring, array('class' => 'mdl-align heading'));

                    $tablecontents .= $OUTPUT->heading(userdate($message->timecreated, $blockdate), 4, 'mdl-align');
                }

                $formatted_message = $side = null;
                if ($message->useridfrom == $messagehistory->user1->id) {
                    $messageinstance = new message_message($message, $messagedate, $messagehistory->search, 'me');
                    $formatted_message = $this->render($messageinstance);
                    $side = 'left';
                } else {
                    $messageinstance = new message_message($message, $messagedate, $messagehistory->search, 'other');
                    $formatted_message = $this->render($messageinstance);
                    $side = 'right';
                }
                $tablecontents .= html_writer::tag('div', $formatted_message, array('class' => "mdl-left $side $notificationclass"));
            }

            $output .= html_writer::nonempty_tag('div', $tablecontents, array('class' => 'mdl-left messagehistory'));
        } else {
            $output .= html_writer::nonempty_tag('div', '('.get_string('nomessagesfound', 'message').')', array('class' => 'mdl-align messagehistory'));
        }

        return $output;
    }

    /**
     * Format a message for display in the message history
     *
     * @param object $message the message object
     * @param string $format optional date format
     * @param string $keywords keywords to highlight
     * @param string $class CSS class to apply to the div around the message
     * @return string the formatted message
     */
    function render_message_message(message_message $message) {

        static $dateformat;

        //if we haven't previously set the date format or they've supplied a new one
        if ( empty($dateformat) || (!empty($message->format) && $dateformat != $message->format) ) {
            if ($message->format) {
                $dateformat = $message->format;
            } else {
                $dateformat = get_string('strftimedatetimeshort');
            }
        }
        $time = userdate($message->messagedata->timecreated, $dateformat);
        $options = new stdClass();
        $options->para = false;

        //if supplied display small messages as fullmessage may contain boilerplate text that shouldnt appear in the messaging UI
        if (!empty($message->messagedata->smallmessage)) {
            $messagetext = $message->messagedata->smallmessage;
        } else {
            $messagetext = $message->messagedata->fullmessage;
        }
        if ($message->messagedata->fullmessageformat == FORMAT_HTML) {
            //dont escape html tags by calling s() if html format or they will display in the UI
            $messagetext = html_to_text(format_text($messagetext, $message->messagedata->fullmessageformat, $options));
        } else {
            $messagetext = format_text(s($messagetext), $message->messagedata->fullmessageformat, $options);
        }

        $messagetext .= message_format_contexturl($message);

        if ($message->keywords) {
            $messagetext = highlight($message->keywords, $messagetext);
        }

        return '<div class="message '.$message->cssclass.'"><a name="m'.$message->messagedata->id.'"></a> <span class="time">'.$time.'</span>: <span class="content">'.$messagetext.'</span></div>';
    }

    /**
     * A helper function that returns a formatted heading
     *
     * @param string $title the heading to display
     * @param int $colspan
     * @return void
     */
    function heading($title, $colspan=3) {
        $output = html_writer::start_tag('tr');
        $output .= html_writer::tag('td', $title, array('colspan' => $colspan, 'class' => 'heading'));
        $output .= html_writer::end_tag('tr');
        return $output;
    }

    /**
     * Print course participants. Called by render_message_contact_selector()
     *
     * @param object $context the course context
     * @param int $courseid the course ID
     * @param string $contactselecturl the url to send the user to when a contact's name is clicked
     * @param bool $showactionlinks show action links (add/remove contact etc) next to the users
     * @param string $titletodisplay Optionally specify a title to display above the participants
     * @param int $page if there are so many users listed that they have to be split into pages what page are we viewing
     * @param object $user2 the user $user1 is talking to. They will be highlighted if they appear in the list of participants
     * @return void
     */
    function participants(message_contact_selector $contactselector, $courseidtoshow) {
        global $DB, $USER, $PAGE, $OUTPUT;

        if (!empty($contactselector->strunreadmessages)) {
            $titletodisplay = $contactselector->strunreadmessages;
        } else {
            $titletodisplay = get_string('participants');
        }

        $context = $contactselector->coursecontexts[$courseidtoshow];
        $countparticipants = count_enrolled_users($context);
        $participants = get_enrolled_users($context, '', 0, 'u.*', '', $contactselector->page*MESSAGE_CONTACTS_PER_PAGE, MESSAGE_CONTACTS_PER_PAGE);

        $pagingbar = new paging_bar($countparticipants, $contactselector->page, MESSAGE_CONTACTS_PER_PAGE, $PAGE->url, 'page');
        $output = $OUTPUT->render($pagingbar);

        $output .= html_writer::start_tag('table', array('id' => 'message_participants', 'class' => 'boxaligncenter', 'cellspacing' => '2', 'cellpadding' => '0', 'border' => '0'));

        $output .= html_writer::start_tag('tr');
        $output .= html_writer::tag('td', $titletodisplay, array('colspan' => 3, 'class' => 'heading'));
        $output .= html_writer::end_tag('tr');

        //todo these need to come from somewhere if the course participants list is to show users with unread messages
        $iscontact = true;
        $isblocked = false;
        foreach ($participants as $participant) {
            if ($participant->id != $USER->id) {
                $participant->messagecount = 0;//todo it would be nice if the course participant could report new messages
                $user = new message_contactlist_user($participant, $iscontact, $isblocked, $contactselector->showcontactactionlinks, $contactselector->user2);
                $output .= $this->render($user);
            }
        }

        $output .= html_writer::end_tag('table');
        return $output;
    }

    
    
    /**
     * Print information on a user. Used when printing search results.
     *
     * @param object/bool $user the user to display or false if you just want $USER
     * @param bool $iscontact is the user being displayed a contact?
     * @param bool $isblocked is the user being displayed blocked?
     * @param bool $includeicontext include text next to the action icons?
     * @return void
     */
    function user ($user=false, $iscontact=false, $isblocked=false, $includeicontext=false) {
        global $USER, $OUTPUT;

        if ($user === false) {
            $output = $OUTPUT->user_picture($USER, array('size' => 20, 'courseid' => SITEID));
        } else {
            $output = $OUTPUT->user_picture($user, array('size' => 20, 'courseid' => SITEID));
            $output .= '&nbsp;';

            $return = false;
            $script = null;
            if ($iscontact) {
                $output .= $this->contact_link($user->id, 'remove', $return, $script, $includeicontext);
            } else {
                $output .= $this->contact_link($user->id, 'add', $return, $script, $includeicontext);
            }
            $output .= '&nbsp;';
            if ($isblocked) {
                $output .= $this->contact_link($user->id, 'unblock', $return, $script, $includeicontext);
            } else {
                $output .= $this->contact_link($user->id, 'block', $return, $script, $includeicontext);
            }

            $popupoptions = array(
                    'height' => MESSAGE_DISCUSSION_HEIGHT,
                    'width' => MESSAGE_DISCUSSION_WIDTH,
                    'menubar' => false,
                    'location' => false,
                    'status' => true,
                    'scrollbars' => true,
                    'resizable' => true);

            $link = new moodle_url("/message/index.php?id=$user->id");
            //$action = new popup_action('click', $link, "message_$user->id", $popupoptions);
            $action = null;
            $output .= $OUTPUT->action_link($link, fullname($user), $action, array('title' => get_string('sendmessageto', 'message', fullname($user))));
        }
        return $output;
    }
    
    /**
     * Print the user's recent conversations
     *
     * @param stdClass $user the current user
     * @param bool $showicontext flag indicating whether or not to show text next to the action icons
     */
    function recent_conversations($user=null, $showicontext=false) {
        global $USER;

        echo html_writer::start_tag('p', array('class' => 'heading'));
        echo get_string('mostrecentconversations', 'message');
        echo html_writer::end_tag('p');

        if (empty($user)) {
            $user = $USER;
        }

        $conversations = message_get_recent_conversations($user);

        // Attach context url information to create the "View this conversation" type links
        foreach($conversations as $conversation) {
            $conversation->contexturl = new moodle_url("/message/index.php?user2={$conversation->id}");
            $conversation->contexturlname = get_string('thisconversation', 'message');
        }

        $showotheruser = true;
        return $this->recent_messages_table($conversations, $user, $showotheruser, $showicontext);
    }

    /**
     * Print the user's recent notifications
     *
     * @param stdClass $user the current user
     */
    function recent_notifications($user=null) {
        global $USER;

        echo html_writer::start_tag('p', array('class' => 'heading'));
        echo get_string('mostrecentnotifications', 'message');
        echo html_writer::end_tag('p');

        if (empty($user)) {
            $user = $USER;
        }

        $notifications = message_get_recent_notifications($user);

        $showicontext = false;
        $showotheruser = false;
        return $this->recent_messages_table($notifications, $user, $showotheruser, $showicontext);
    }
    
    /**
     * Print a list of recent messages
     *
     * @param array $messages the messages to display
     * @param object $user the current user
     * @param bool $showotheruser display information on the other user?
     * @param bool $showicontext show text next to the action icons?
     * @return void
     */
    function recent_messages_table($messages, $user=null, $showotheruser=true, $showicontext=false) {
        global $OUTPUT;
        static $dateformat;

        if (empty($dateformat)) {
            $dateformat = get_string('strftimedatetimeshort');
        }

        $output = html_writer::start_tag('div', array('class' => 'messagerecent'));
        foreach ($messages as $message) {
            $output .= html_writer::start_tag('div', array('class' => 'singlemessage'));

            if ($showotheruser) {
                if ( $message->contactlistid )  {
                    if ($message->blocked == 0) { /// not blocked
                        $strcontact = message_contact_link($message->id, 'remove', true, null, $showicontext);
                        $strblock   = message_contact_link($message->id, 'block', true, null, $showicontext);
                    } else { // blocked
                        $strcontact = message_contact_link($message->id, 'add', true, null, $showicontext);
                        $strblock   = message_contact_link($message->id, 'unblock', true, null, $showicontext);
                    }
                } else {
                    $strcontact = message_contact_link($message->id, 'add', true, null, $showicontext);
                    $strblock   = message_contact_link($message->id, 'block', true, null, $showicontext);
                }

                //should we show just the icon or icon and text?
                $histicontext = 'icon';
                if ($showicontext) {
                    $histicontext = 'both';
                }
                $strhistory = message_history_link($user->id, $message->id, true, '', '', $histicontext);

                $output .= html_writer::start_tag('span', array('class' => 'otheruser'));

                $output .= html_writer::start_tag('span', array('class' => 'pix'));
                $output .= $OUTPUT->user_picture($message, array('size' => 20, 'courseid' => SITEID));
                $output .= html_writer::end_tag('span');

                $output .= html_writer::start_tag('span', array('class' => 'contact'));

                $link = new moodle_url("/message/index.php?id=$message->id");
                $action = null;
                $output .= $OUTPUT->action_link($link, fullname($message), $action, array('title' => get_string('sendmessageto', 'message', fullname($message))));

                $output .= html_writer::end_tag('span');//end contact

                $output .= $strcontact.$strblock.$strhistory;
                $output .= html_writer::end_tag('span');//end otheruser
            }
            $messagetoprint = null;
            if (!empty($message->smallmessage)) {
                $messagetoprint = $message->smallmessage;
            } else {
                $messagetoprint = $message->fullmessage;
            }

            $output .= html_writer::tag('span', userdate($message->timecreated, $dateformat), array('class' => 'messagedate'));
            $output .= html_writer::tag('span', format_text($messagetoprint, FORMAT_HTML), array('class' => 'themessage'));
            $output .= message_format_contexturl($message);
            $output .= html_writer::end_tag('div');//end singlemessage
        }
        $output .= html_writer::end_tag('div');//end messagerecent
        return $output;
    }



}
