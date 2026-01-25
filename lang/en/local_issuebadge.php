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
 * English language strings for IssueBadge plugin
 *
 * @package    local_issuebadge
 * @copyright  2026 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// General.
$string['pluginname'] = 'IssueBadge';
$string['settings'] = 'IssueBadge Settings';

// Getting Started.
$string['gettingstarted'] = 'Getting Started';
$string['setupsteps'] = 'Setup Steps';
$string['step1_signup'] = 'Sign up for IssueBadge';
$string['step2_createbadge'] = 'Create your badges';
$string['step2_createbadge_desc'] = 'Design and create badges in your IssueBadge dashboard';
$string['step3_getapikey'] = 'Get your API Key';
$string['getapikey'] = 'Get API Key';
$string['videotutorials'] = 'Video Tutorials';
$string['videotutorials_desc'] = 'Watch step-by-step video guides on how to use IssueBadge.';
$string['watchtutorials'] = 'Watch on YouTube';
$string['needhelp'] = 'Need Help?';
$string['documentation'] = 'Documentation';
$string['moodleguide'] = 'Moodle Setup Guide';
$string['contactsupport'] = 'Contact Us';

// Capabilities.
$string['issuebadge:issue'] = 'Issue badges to users';
$string['issuebadge:manage'] = 'Manage IssueBadge settings';
$string['issuebadge:view'] = 'View issued badges';

// API Settings.
$string['apikey'] = 'API Bearer Token';
$string['apikey_desc'] = 'Enter your IssueBadge API Bearer Token from https://app.issuebadge.com';
$string['apisettings'] = 'API Configuration';
$string['apiurl'] = 'API Base URL';
$string['apiurl_desc'] = 'IssueBadge API endpoint URL';

// Badge management.
$string['badge'] = 'Badge';
$string['badges'] = 'Badges';
$string['badgeid'] = 'Badge ID';
$string['badgename'] = 'Badge Name';
$string['badgedescription'] = 'Description';
$string['managebadges'] = 'Manage Badges';
$string['managebadges_desc'] = 'Manage badge issuance and view issued badges from this dashboard.';
$string['nobadges'] = 'No badges available. Please click "Refresh from API" to import badges.';
$string['nobadgescached'] = 'No badges cached. Please refresh from API first.';
$string['refreshbadges'] = 'Refresh from API';
$string['refreshsuccess'] = 'Successfully imported {$a} badges from API.';
$string['refresherror'] = 'Failed to refresh badges from API: {$a}';
$string['lastrefreshed'] = 'Last refreshed';
$string['never'] = 'Never';
$string['addbadgemanually'] = 'Add Badge Manually';
$string['addbadgemanually_help'] = 'Use this only if "Refresh from API" is not working. Enter the exact Badge ID from your IssueBadge.com dashboard. The Badge ID must match a real badge in your account, otherwise issuing will fail.';
$string['addbadge'] = 'Add Badge';
$string['deletebadge'] = 'Delete Badge';
$string['confirmdelete'] = 'Are you sure you want to delete this badge from cache?';
$string['badgedeleted'] = 'Badge deleted from cache.';
$string['badgeadded'] = 'Badge added successfully.';
$string['badgeupdated'] = 'Badge updated successfully.';

// Issue badges.
$string['issuebadge'] = 'Issue Badge';
$string['issuebadgemanagement'] = 'IssueBadge Management';
$string['issuemanual'] = 'Issue Badge Manually';
$string['issueto'] = 'Issue to';
$string['issuetouser'] = 'Moodle User';
$string['issuetoexternal'] = 'External Recipient (Name & Email)';
$string['selectbadge'] = 'Select a badge';
$string['selectuser'] = 'Select user';
$string['selectusers'] = 'Select users';
$string['recipientname'] = 'Recipient Name';
$string['recipientemail'] = 'Recipient Email';
$string['enterrecipientname'] = 'Enter recipient name';
$string['enterrecipientemail'] = 'Enter recipient email';
$string['issuebadgebtn'] = 'Issue Badge';
$string['testissue'] = 'Test Issue';
$string['testmode'] = 'Test Mode';
$string['testmode_desc'] = 'Use this to test badge issuance with custom name and email.';

// Issue results.
$string['badgeissued'] = 'Badge issued successfully';
$string['badgeissued_desc'] = 'Badge issued to {$a->name}. Issue ID: {$a->issueid}';
$string['badgesissued'] = '{$a} badges issued successfully';
$string['error_issuefailed'] = 'Failed to issue badge: {$a}';
$string['issuedate'] = 'Issue date';
$string['publicurl'] = 'Public URL';
$string['issueid'] = 'Issue ID';
$string['viewbadge'] = 'View Badge';

// View issued.
$string['viewissued'] = 'View Issued Badges';
$string['noissuedbadges'] = 'No badges have been issued yet.';
$string['bulkissue'] = 'Bulk Issue Badges';

// Course configuration.
$string['configurebadge'] = 'Configure Badge';
$string['courseconfiguration'] = 'Course Badge Configuration';
$string['courseconfig_desc'] = 'Configure automatic badge issuance for this course.';
$string['badgenotconfigured'] = 'No badge configured for this course';
$string['course'] = 'Course';
$string['coursecompletion'] = 'Course Completion Badge';
$string['coursecompletion_desc'] = 'Badge to issue upon course completion';
$string['enableautoissue'] = 'Enable automatic badge issuance';
$string['enableautoissue_desc'] = 'Automatically issue badges when students complete courses or pass quizzes';
$string['configsaved'] = 'Configuration saved successfully.';

// Trigger types.
$string['triggertype'] = 'Trigger Type';
$string['triggertype_desc'] = 'When should the badge be automatically issued?';
$string['trigger_manual'] = 'Manual only (no auto-issue)';
$string['trigger_completion'] = 'Course Completion';
$string['trigger_quiz'] = 'Quiz Grade Threshold';

// Quiz settings.
$string['selectquiz'] = 'Select Quiz';
$string['selectquiz_desc'] = 'Select the quiz that triggers badge issuance';
$string['passinggrade'] = 'Passing Grade (%)';
$string['passinggrade_desc'] = 'Minimum grade percentage required to earn the badge';
$string['passinggrade_desc_all'] = 'Minimum grade percentage required (0 = no grade check). For course completion, checks overall course grade. For quiz, checks quiz grade.';
$string['noquizzes'] = 'No quizzes found in this course';

// Auto-issue messages.
$string['autoissuedquiz'] = 'Badge automatically issued for passing quiz with {$a}%';
$string['autoissuedcompletion'] = 'Badge automatically issued for course completion';
$string['alreadyissued'] = 'User has already been issued this badge';

// Errors.
$string['error_api'] = 'Failed to communicate with IssueBadge API';
$string['error_invalidresponse'] = 'Invalid response from IssueBadge API';
$string['error_missingdata'] = 'Missing required data';
$string['error_noapi'] = 'API key not configured. Please configure in site administration.';
$string['error_nobadgeselected'] = 'Please select a badge';
$string['error_norecipient'] = 'Please select a user or enter recipient details';
$string['error_invalidemail'] = 'Please enter a valid email address';
$string['nousersselected'] = 'No users selected';

// Events.
$string['eventbadgeissued'] = 'Badge issued';

// Navigation.
$string['dashboard'] = 'Dashboard';
$string['configuration'] = 'Configuration';
$string['issuing'] = 'Issue Badges';

// Privacy.
$string['privacy:metadata:issuebadge_api'] = 'Data sent to the IssueBadge API service';
$string['privacy:metadata:issuebadge_api:email'] = 'User email sent to IssueBadge';
$string['privacy:metadata:issuebadge_api:name'] = 'User name sent to IssueBadge';
$string['privacy:metadata:local_issuebadge_issues'] = 'Information about issued badges';
$string['privacy:metadata:local_issuebadge_issues:badge_id'] = 'The badge ID from IssueBadge';
$string['privacy:metadata:local_issuebadge_issues:courseid'] = 'The course ID if issued in a course context';
$string['privacy:metadata:local_issuebadge_issues:issue_id'] = 'The unique issue ID from IssueBadge';
$string['privacy:metadata:local_issuebadge_issues:public_url'] = 'The public URL to view the issued badge';
$string['privacy:metadata:local_issuebadge_issues:timecreated'] = 'When the badge was issued';
$string['privacy:metadata:local_issuebadge_issues:userid'] = 'The user who received the badge';

// Misc.
$string['loadingbadges'] = 'Loading badges...';
$string['actions'] = 'Actions';
$string['status'] = 'Status';
$string['enabled'] = 'Enabled';
$string['disabled'] = 'Disabled';
$string['save'] = 'Save';
$string['cancel'] = 'Cancel';
$string['back'] = 'Back';
$string['or'] = 'or';
$string['configure'] = 'Configure';

// Dashboard.
$string['badgesissuedtotal'] = 'Badges Issued';
$string['coursesconfigured'] = 'Courses Configured';
$string['configuredcourses'] = 'Configured Courses';
$string['issuemanual_desc'] = 'Manually issue badges to Moodle users or external recipients.';
$string['viewissued_desc'] = 'View all badges that have been issued.';
$string['settings_desc'] = 'Configure API credentials and plugin settings.';

// How it works.
$string['howitworks'] = 'How It Works';
$string['step1_title'] = 'Configure API';
$string['step1_desc'] = 'Enter your IssueBadge.com API key in Settings.';
$string['step2_title'] = 'Import Badges';
$string['step2_desc'] = 'Click "Refresh from API" in Manage Badges to import your badges.';
$string['step3_title'] = 'Configure Courses';
$string['step3_desc'] = 'Go to configure.php?courseid=X to set up auto-issue for each course.';
$string['step4_title'] = 'Issue Badges';
$string['step4_desc'] = 'Badges are issued automatically or manually to learners.';

// Trigger options help.
$string['triggeroptions'] = 'Auto-Issue Trigger Options';
$string['triggeroptions_manual'] = 'No automatic issuance. You must issue badges manually from the Issue Badge page.';
$string['triggeroptions_completion'] = 'Badge is automatically issued when a learner completes the course.';
$string['triggeroptions_quiz'] = 'Badge is automatically issued when a learner passes the selected quiz with the minimum grade percentage.';

// Course setup tip.
$string['coursesetuptip'] = 'How to Configure a Course';
$string['coursesetuptip_desc'] = 'To set up automatic badge issuance for a course, go to the course and visit:';
$string['coursesetuptip_url'] = '/local/issuebadge/configure.php?courseid=YOUR_COURSE_ID';
