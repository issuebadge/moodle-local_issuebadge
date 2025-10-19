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
 * Admin settings for IssueBadge plugin
 *
 * @package    local_issuebadge
 * @copyright  2025 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_issuebadge', get_string('pluginname', 'local_issuebadge'));

    // API Settings heading.
    $settings->add(new admin_setting_heading(
        'local_issuebadge/apiheading',
        get_string('apisettings', 'local_issuebadge'),
        ''
    ));

    // API Base URL.
    $settings->add(new admin_setting_configtext(
        'local_issuebadge/apiurl',
        get_string('apiurl', 'local_issuebadge'),
        get_string('apiurl_desc', 'local_issuebadge'),
        'https://app.issuebadge.com/api/v1',
        PARAM_URL
    ));

    // API Bearer Token.
    $settings->add(new admin_setting_configtext(
        'local_issuebadge/apikey',
        get_string('apikey', 'local_issuebadge'),
        get_string('apikey_desc', 'local_issuebadge'),
        '',
        PARAM_TEXT,
        60
    ));

    // Enable automatic badge issuance on course completion.
    $settings->add(new admin_setting_configcheckbox(
        'local_issuebadge/enableautoissue',
        get_string('enableautoissue', 'local_issuebadge'),
        get_string('enableautoissue_desc', 'local_issuebadge'),
        0
    ));

    $ADMIN->add('localplugins', $settings);

    // Add a link to the management page.
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_issuebadge_manage',
        get_string('managebadges', 'local_issuebadge'),
        new moodle_url('/local/issuebadge/index.php'),
        'local/issuebadge:manage'
    ));
}
