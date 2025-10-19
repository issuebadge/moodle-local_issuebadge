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
 * Event observers for IssueBadge plugin
 *
 * @package    local_issuebadge
 * @copyright  2025 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_issuebadge;

defined('MOODLE_INTERNAL') || die();

/**
 * Event observer class
 */
class observer {

    /**
     * Triggered when a user completes a course
     *
     * @param \core\event\course_completed $event
     */
    public static function course_completed(\core\event\course_completed $event) {
        global $DB;

        // Check if automatic badge issuance is enabled.
        if (!local_issuebadge_auto_issue_enabled()) {
            return;
        }

        $userid = $event->relateduserid;
        $courseid = $event->courseid;

        // Get badge configuration for this course.
        $config = $DB->get_record('local_issuebadge_course', [
            'courseid' => $courseid,
            'enabled' => 1,
        ]);

        if (!$config || empty($config->badge_id)) {
            // No badge configured for this course.
            return;
        }

        // Check if badge was already issued to this user for this course.
        $exists = $DB->record_exists('local_issuebadge_issues', [
            'userid' => $userid,
            'courseid' => $courseid,
            'badge_id' => $config->badge_id,
        ]);

        if ($exists) {
            // Badge already issued, don't issue again.
            return;
        }

        try {
            // Get user details.
            $user = $DB->get_record('user', ['id' => $userid], 'id, firstname, lastname, email', MUST_EXIST);

            // Issue the badge.
            $api = new \local_issuebadge\api\issuebadge_api();
            $result = $api->issue_badge([
                'name' => fullname($user),
                'email' => $user->email,
                'badge_id' => $config->badge_id,
            ]);

            // Store the issue record.
            $record = new \stdClass();
            $record->userid = $userid;
            $record->courseid = $courseid;
            $record->badge_id = $config->badge_id;
            $record->issue_id = $result['IssueId'];
            $record->public_url = $result['publicUrl'];
            $record->issuerby = 0; // System issued.
            $record->timecreated = time();
            $record->timemodified = time();

            $DB->insert_record('local_issuebadge_issues', $record);

            // Trigger custom event.
            $event = \local_issuebadge\event\badge_issued::create([
                'context' => \context_course::instance($courseid),
                'relateduserid' => $userid,
                'other' => [
                    'issueid' => $result['IssueId'],
                    'badgeid' => $config->badge_id,
                ],
            ]);
            $event->trigger();

            // Log success.
            debugging('Badge issued automatically for user ' . $userid . ' in course ' . $courseid, DEBUG_DEVELOPER);

        } catch (\Exception $e) {
            // Log error but don't throw - we don't want to break the course completion process.
            debugging('Failed to automatically issue badge: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }
}
