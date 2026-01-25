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
 * @copyright  2026 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_issuebadge', get_string('pluginname', 'local_issuebadge'));

    // Getting Started help section - two column layout.
    $guideurl = 'https://issuebadge.com/h/blog/' .
        'how-to-sent-customized-digital-badge-and-qr-code-based-verifiable-credentials-from-moodle';
    $gettingstarted = '
<div class="card mb-4 border-info">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fa fa-info-circle mr-2"></i>' . get_string('gettingstarted', 'local_issuebadge') . '</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="mb-3"><i class="fa fa-list-ol mr-2"></i>' . get_string('setupsteps', 'local_issuebadge') . '</h6>
                <ol class="mb-0">
                    <li class="mb-3">
                        <strong>' . get_string('step1_signup', 'local_issuebadge') . '</strong><br>
                        <a href="https://issuebadge.com" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                            <i class="fa fa-external-link mr-1"></i>issuebadge.com
                        </a>
                    </li>
                    <li class="mb-3">
                        <strong>' . get_string('step2_createbadge', 'local_issuebadge') . '</strong><br>
                        <span class="text-muted">' . get_string('step2_createbadge_desc', 'local_issuebadge') . '</span>
                    </li>
                    <li class="mb-3">
                        <strong>' . get_string('step3_getapikey', 'local_issuebadge') . '</strong><br>
                        <a href="https://app.issuebadge.com/developer/index" target="_blank"
                           class="btn btn-sm btn-outline-primary mt-1">
                            <i class="fa fa-key mr-1"></i>' . get_string('getapikey', 'local_issuebadge') . '
                        </a>
                    </li>
                </ol>
            </div>
            <div class="col-md-6">
                <h6 class="mb-3"><i class="fa fa-youtube-play text-danger mr-2"></i>' .
                    get_string('videotutorials', 'local_issuebadge') . '</h6>
                <p class="text-muted mb-3">' . get_string('videotutorials_desc', 'local_issuebadge') . '</p>
                <a href="https://www.youtube.com/@ISSUEBADGE" target="_blank" class="btn btn-danger btn-lg">
                    <i class="fa fa-youtube-play mr-2"></i>' . get_string('watchtutorials', 'local_issuebadge') . '
                </a>
                <hr class="my-4">
                <h6 class="mb-3"><i class="fa fa-life-ring mr-2"></i>' . get_string('needhelp', 'local_issuebadge') . '</h6>
                <a href="' . $guideurl . '" target="_blank" class="btn btn-sm btn-outline-secondary mr-2 mb-2">
                    <i class="fa fa-book mr-1"></i>' . get_string('moodleguide', 'local_issuebadge') . '
                </a>
                <a href="https://issuebadge.com/h/contact" target="_blank" class="btn btn-sm btn-outline-secondary mb-2">
                    <i class="fa fa-envelope mr-1"></i>' . get_string('contactsupport', 'local_issuebadge') . '
                </a>
            </div>
        </div>
    </div>
</div>';

    $settings->add(new admin_setting_heading(
        'local_issuebadge/gettingstartedheading',
        '',
        $gettingstarted
    ));

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
