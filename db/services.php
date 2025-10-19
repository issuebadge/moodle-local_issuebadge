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
 * Web service/AJAX function declarations for IssueBadge plugin
 *
 * @package    local_issuebadge
 * @copyright  2025 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_issuebadge_get_badges' => [
        'classname' => 'local_issuebadge\external\get_badges',
        'methodname' => 'execute',
        'description' => 'Fetch available badges from IssueBadge API',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'local/issuebadge:view',
        'loginrequired' => true,
    ],
    'local_issuebadge_issue_badge' => [
        'classname' => 'local_issuebadge\external\issue_badge',
        'methodname' => 'execute',
        'description' => 'Issue a badge to a user',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/issuebadge:issue',
        'loginrequired' => true,
    ],
];

$services = [
    'IssueBadge Service' => [
        'functions' => ['local_issuebadge_get_badges', 'local_issuebadge_issue_badge'],
        'restrictedusers' => 0,
        'enabled' => 1,
    ],
];
