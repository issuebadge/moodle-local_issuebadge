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
 * @copyright  2026 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_issuebadge;

/**
 * Event observer class
 */
class observer {
    /**
     * Triggered when a user completes a course.
     *
     * @param \core\event\course_completed $event
     * @return void
     */
    public static function course_completed(\core\event\course_completed $event) {
        global $DB, $CFG;

        $userid = $event->relateduserid;
        $courseid = $event->courseid;

        // Get badge configuration for this course with completion trigger.
        $config = $DB->get_record('local_issuebadge_course', [
            'courseid' => $courseid,
            'trigger_type' => 'completion',
            'enabled' => 1,
        ]);

        if (!$config || empty($config->badge_id)) {
            return;
        }

        // Check if grade threshold is set.
        $passinggrade = $config->passing_grade ?? 0;
        $grade = null;

        if ($passinggrade > 0) {
            // Get user's course grade.
            require_once($CFG->libdir . '/gradelib.php');

            $gradeitem = \grade_item::fetch([
                'itemtype' => 'course',
                'courseid' => $courseid,
            ]);

            if ($gradeitem) {
                $usergrade = $gradeitem->get_grade($userid, false);
                if ($usergrade && $usergrade->finalgrade !== null) {
                    // Calculate percentage.
                    $grade = ($usergrade->finalgrade / $gradeitem->grademax) * 100;

                    if ($grade < $passinggrade) {
                        debugging("IssueBadge: Course grade $grade% below threshold $passinggrade%", DEBUG_DEVELOPER);
                        return;
                    }
                } else {
                    // No grade yet, skip if threshold is required.
                    debugging("IssueBadge: No course grade found for user $userid, threshold required", DEBUG_DEVELOPER);
                    return;
                }
            }
        }

        self::issue_badge_to_user($userid, $courseid, $config->badge_id, 'completion', $grade);
    }

    /**
     * Triggered when a user submits a quiz attempt.
     *
     * @param \mod_quiz\event\attempt_submitted $event
     * @return void
     */
    public static function quiz_attempt_submitted(\mod_quiz\event\attempt_submitted $event) {
        global $DB;

        $userid = $event->relateduserid;
        $quizid = $event->objectid;
        $attemptid = $event->objectid;

        // Get quiz to find the course.
        $quiz = $DB->get_record('quiz', ['id' => $event->other['quizid'] ?? 0]);
        if (!$quiz) {
            // Try to get from context.
            $cm = get_coursemodule_from_id('quiz', $event->contextinstanceid);
            if ($cm) {
                $quiz = $DB->get_record('quiz', ['id' => $cm->instance]);
            }
        }

        if (!$quiz) {
            debugging('IssueBadge: Could not find quiz for attempt', DEBUG_DEVELOPER);
            return;
        }

        $courseid = $quiz->course;

        // Get badge configuration for this course with quiz trigger.
        $config = $DB->get_record('local_issuebadge_course', [
            'courseid' => $courseid,
            'trigger_type' => 'quiz',
            'quiz_id' => $quiz->id,
            'enabled' => 1,
        ]);

        if (!$config || empty($config->badge_id)) {
            return;
        }

        // Get the grade for this attempt.
        $attempt = $DB->get_record('quiz_attempts', ['id' => $event->objectid]);
        if (!$attempt) {
            return;
        }

        // Calculate percentage.
        $grade = 0;
        if ($quiz->sumgrades > 0 && $attempt->sumgrades !== null) {
            $grade = ($attempt->sumgrades / $quiz->sumgrades) * 100;
        }

        // Check if grade meets the threshold.
        $passinggrade = $config->passing_grade ?? 70;
        if ($grade < $passinggrade) {
            debugging("IssueBadge: Grade $grade% below threshold $passinggrade%", DEBUG_DEVELOPER);
            return;
        }

        self::issue_badge_to_user($userid, $courseid, $config->badge_id, 'quiz', $grade);
    }

    /**
     * Issue a badge to a user.
     *
     * @param int $userid User ID
     * @param int $courseid Course ID
     * @param string $badgeid Badge ID
     * @param string $trigger Trigger type (completion or quiz)
     * @param float|null $grade Grade percentage (for quiz trigger)
     * @return void
     */
    private static function issue_badge_to_user($userid, $courseid, $badgeid, $trigger, $grade = null) {
        global $DB;

        // Check if badge was already issued to this user for this course.
        $exists = $DB->record_exists('local_issuebadge_issues', [
            'userid' => $userid,
            'courseid' => $courseid,
            'badge_id' => $badgeid,
        ]);

        if ($exists) {
            debugging("IssueBadge: Badge already issued to user $userid for course $courseid", DEBUG_DEVELOPER);
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
                'badge_id' => $badgeid,
            ]);

            // Store the issue record.
            $record = new \stdClass();
            $record->userid = $userid;
            $record->recipient_name = fullname($user);
            $record->recipient_email = $user->email;
            $record->courseid = $courseid;
            $record->badge_id = $badgeid;
            $record->issue_id = $result['IssueId'];
            $record->public_url = $result['publicUrl'];
            $record->issuerby = 0; // System issued.
            $record->timecreated = time();
            $record->timemodified = time();

            $DB->insert_record('local_issuebadge_issues', $record);

            // Trigger custom event.
            $badgeevent = \local_issuebadge\event\badge_issued::create([
                'context' => \context_course::instance($courseid),
                'relateduserid' => $userid,
                'other' => [
                    'issueid' => $result['IssueId'],
                    'badgeid' => $badgeid,
                    'trigger' => $trigger,
                    'grade' => $grade,
                ],
            ]);
            $badgeevent->trigger();

            // Log success.
            $gradeinfo = $grade !== null ? " (grade: $grade%)" : "";
            debugging("IssueBadge: Badge issued to user $userid via $trigger trigger$gradeinfo", DEBUG_DEVELOPER);
        } catch (\Exception $e) {
            // Log error but don't throw - we don't want to break the triggering process.
            debugging('IssueBadge: Failed to issue badge: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }
}
