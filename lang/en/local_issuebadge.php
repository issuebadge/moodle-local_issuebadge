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
 * @copyright  2025 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin name.
$string['pluginname'] = 'IssueBadge';

// Capabilities.
$string['issuebadge:issue'] = 'Issue badges to users';
$string['issuebadge:manage'] = 'Manage IssueBadge settings';
$string['issuebadge:view'] = 'View issued badges';

// Settings.
$string['settings'] = 'IssueBadge Settings';
$string['apisettings'] = 'API Configuration';
$string['apikey'] = 'API Bearer Token';
$string['apikey_desc'] = 'Enter your IssueBadge API Bearer Token from https://app.issuebadge.com';
$string['apiurl'] = 'API Base URL';
$string['apiurl_desc'] = 'IssueBadge API endpoint URL';
$string['enableautoissue'] = 'Enable automatic badge issuance';
$string['enableautoissue_desc'] = 'Automatically issue badges when students complete courses';

// General strings.
$string['issuebadge'] = 'IssueBadge';
$string['issuebadgemanagement'] = 'IssueBadge Management';
$string['badge'] = 'Badge';
$string['selectbadge'] = 'Select a badge';
$string['recipientname'] = 'Recipient name';
$string['recipientemail'] = 'Recipient email';
$string['issuebadge'] = 'Issue Badge';
$string['bulkissue'] = 'Bulk Issue Badges';
$string['viewissued'] = 'View Issued Badges';

// Form strings.
$string['selectuser'] = 'Select user';
$string['selectusers'] = 'Select users';
$string['course'] = 'Course';
$string['issuedate'] = 'Issue date';
$string['publicurl'] = 'Public URL';

// Messages.
$string['badgeissued'] = 'Badge issued successfully';
$string['badgeissued_desc'] = 'Badge issued to {$a->name}. Issue ID: {$a->issueid}';
$string['badgesissued'] = '{$a} badges issued successfully';
$string['nobadges'] = 'No badges available. Please check your API configuration.';
$string['nousersselected'] = 'No users selected';
$string['loadingbadges'] = 'Loading badges...';

// Errors.
$string['error_api'] = 'Failed to communicate with IssueBadge API';
$string['error_noapi'] = 'API key not configured. Please configure in site administration.';
$string['error_invalidresponse'] = 'Invalid response from IssueBadge API';
$string['error_missingdata'] = 'Missing required data';
$string['error_issuefailed'] = 'Failed to issue badge: {$a}';

// Privacy API.
$string['privacy:metadata:local_issuebadge_issues'] = 'Information about issued badges';
$string['privacy:metadata:local_issuebadge_issues:userid'] = 'The user who received the badge';
$string['privacy:metadata:local_issuebadge_issues:badge_id'] = 'The badge ID from IssueBadge';
$string['privacy:metadata:local_issuebadge_issues:issue_id'] = 'The unique issue ID from IssueBadge';
$string['privacy:metadata:local_issuebadge_issues:public_url'] = 'The public URL to view the issued badge';
$string['privacy:metadata:local_issuebadge_issues:courseid'] = 'The course ID if issued in a course context';
$string['privacy:metadata:local_issuebadge_issues:timecreated'] = 'When the badge was issued';
$string['privacy:metadata:issuebadge_api'] = 'Data sent to the IssueBadge API service';
$string['privacy:metadata:issuebadge_api:name'] = 'User name sent to IssueBadge';
$string['privacy:metadata:issuebadge_api:email'] = 'User email sent to IssueBadge';

// Navigation.
$string['managebadges'] = 'Manage Badges';
$string['issuemanual'] = 'Issue Badge Manually';

// Events.
$string['eventbadgeissued'] = 'Badge issued';

// Course completion.
$string['coursecompletion'] = 'Course Completion Badge';
$string['coursecompletion_desc'] = 'Badge to issue upon course completion';
$string['configurebadge'] = 'Configure Badge';
$string['badgenotconfigured'] = 'No badge configured for this course';
