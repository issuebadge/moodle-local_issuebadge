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
 * Library functions for IssueBadge plugin
 *
 * @package    local_issuebadge
 * @copyright  2025 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Extend global navigation
 *
 * @param global_navigation $navigation
 */
function local_issuebadge_extend_navigation(global_navigation $navigation) {
    global $PAGE, $COURSE;

    // Add to site administration for managers.
    if (has_capability('local/issuebadge:manage', context_system::instance())) {
        $node = $navigation->add(
            get_string('pluginname', 'local_issuebadge'),
            new moodle_url('/local/issuebadge/index.php'),
            navigation_node::TYPE_CUSTOM,
            null,
            'issuebadge',
            new pix_icon('icon', '', 'local_issuebadge')
        );
        $node->showinflatnavigation = true;
    }
}

/**
 * Extend settings navigation
 *
 * @param settings_navigation $navigation
 * @param context $context
 */
function local_issuebadge_extend_settings_navigation(settings_navigation $navigation, context $context) {
    global $PAGE;

    // Add to course administration for teachers.
    if ($context->contextlevel == CONTEXT_COURSE && has_capability('local/issuebadge:issue', $context)) {
        if ($settingnode = $navigation->find('courseadmin', navigation_node::TYPE_COURSE)) {
            $url = new moodle_url('/local/issuebadge/issue.php', ['courseid' => $context->instanceid]);
            $node = navigation_node::create(
                get_string('issuebadge', 'local_issuebadge'),
                $url,
                navigation_node::TYPE_SETTING,
                null,
                'issuebadge',
                new pix_icon('icon', '', 'local_issuebadge')
            );
            $settingnode->add_node($node);
        }
    }
}

/**
 * Get configured API key
 *
 * @return string|false
 */
function local_issuebadge_get_api_key() {
    $key = get_config('local_issuebadge', 'apikey');
    return $key ? $key : false;
}

/**
 * Get configured API URL
 *
 * @return string
 */
function local_issuebadge_get_api_url() {
    $url = get_config('local_issuebadge', 'apiurl');
    return !empty($url) ? $url : 'https://app.issuebadge.com/api/v1';
}

/**
 * Check if automatic badge issuance is enabled
 *
 * @return bool
 */
function local_issuebadge_auto_issue_enabled() {
    return (bool) get_config('local_issuebadge', 'enableautoissue');
}
