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
 * External function to issue a badge
 *
 * @package    local_issuebadge
 * @copyright  2025 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_issuebadge\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;

/**
 * Issue badge external function
 */
class issue_badge extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'userid' => new external_value(PARAM_INT, 'User ID'),
            'badgeid' => new external_value(PARAM_ALPHANUMEXT, 'Badge ID'),
            'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * Issue a badge to a user
     *
     * @param int $userid
     * @param string $badgeid
     * @param int $courseid
     * @return array
     * @throws \moodle_exception
     */
    public static function execute($userid, $badgeid, $courseid = 0) {
        global $DB, $USER;

        // Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), [
            'userid' => $userid,
            'badgeid' => $badgeid,
            'courseid' => $courseid,
        ]);

        // Validate context and capability.
        if ($params['courseid'] > 0) {
            $context = \context_course::instance($params['courseid']);
        } else {
            $context = \context_system::instance();
        }
        self::validate_context($context);
        require_capability('local/issuebadge:issue', $context);

        try {
            // Get user details.
            $user = $DB->get_record('user', ['id' => $params['userid']], 'id, firstname, lastname, email', MUST_EXIST);

            // Issue the badge.
            $api = new \local_issuebadge\api\issuebadge_api();
            $result = $api->issue_badge([
                'name' => fullname($user),
                'email' => $user->email,
                'badge_id' => $params['badgeid'],
            ]);

            // Store the issue record.
            $record = new \stdClass();
            $record->userid = $params['userid'];
            $record->courseid = $params['courseid'] > 0 ? $params['courseid'] : null;
            $record->badge_id = $params['badgeid'];
            $record->issue_id = $result['IssueId'];
            $record->public_url = $result['publicUrl'];
            $record->issuerby = $USER->id;
            $record->timecreated = time();
            $record->timemodified = time();

            $DB->insert_record('local_issuebadge_issues', $record);

            // Trigger custom event.
            $event = \local_issuebadge\event\badge_issued::create([
                'context' => $context,
                'relateduserid' => $params['userid'],
                'other' => [
                    'issueid' => $result['IssueId'],
                    'badgeid' => $params['badgeid'],
                ],
            ]);
            $event->trigger();

            return [
                'success' => true,
                'issueid' => $result['IssueId'],
                'publicurl' => $result['publicUrl'],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'issueid' => '',
                'publicurl' => '',
            ];
        }
    }

    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Success status'),
            'issueid' => new external_value(PARAM_TEXT, 'Issue ID'),
            'publicurl' => new external_value(PARAM_URL, 'Public URL'),
            'error' => new external_value(PARAM_TEXT, 'Error message', VALUE_OPTIONAL),
        ]);
    }
}
