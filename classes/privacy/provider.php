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
 * Privacy Subsystem implementation for IssueBadge plugin
 *
 * @package    local_issuebadge
 * @copyright  2025 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_issuebadge\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_privacy\local\request\transform;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider for IssueBadge
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns metadata about this plugin's data storage
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        // Database table: local_issuebadge_issues.
        $collection->add_database_table(
            'local_issuebadge_issues',
            [
                'userid' => 'privacy:metadata:local_issuebadge_issues:userid',
                'badge_id' => 'privacy:metadata:local_issuebadge_issues:badge_id',
                'issue_id' => 'privacy:metadata:local_issuebadge_issues:issue_id',
                'public_url' => 'privacy:metadata:local_issuebadge_issues:public_url',
                'courseid' => 'privacy:metadata:local_issuebadge_issues:courseid',
                'timecreated' => 'privacy:metadata:local_issuebadge_issues:timecreated',
            ],
            'privacy:metadata:local_issuebadge_issues'
        );

        // External service: IssueBadge API.
        $collection->add_external_location_link(
            'issuebadge_api',
            [
                'name' => 'privacy:metadata:issuebadge_api:name',
                'email' => 'privacy:metadata:issuebadge_api:email',
            ],
            'privacy:metadata:issuebadge_api'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user
     *
     * @param int $userid
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        // Get course contexts where user has badges.
        $sql = "SELECT ctx.id
                FROM {context} ctx
                JOIN {local_issuebadge_issues} i ON i.courseid = ctx.instanceid AND ctx.contextlevel = :contextlevel
                WHERE i.userid = :userid";

        $contextlist->add_from_sql($sql, ['userid' => $userid, 'contextlevel' => CONTEXT_COURSE]);

        // Also add system context for badges not associated with courses.
        $sql = "SELECT ctx.id
                FROM {context} ctx
                JOIN {local_issuebadge_issues} i ON ctx.contextlevel = :contextlevel
                WHERE i.userid = :userid AND (i.courseid IS NULL OR i.courseid = 0)";

        $contextlist->add_from_sql($sql, ['userid' => $userid, 'contextlevel' => CONTEXT_SYSTEM]);

        return $contextlist;
    }

    /**
     * Get the list of users within a specific context
     *
     * @param userlist $userlist
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if ($context->contextlevel == CONTEXT_COURSE) {
            $sql = "SELECT userid
                    FROM {local_issuebadge_issues}
                    WHERE courseid = :courseid";

            $userlist->add_from_sql('userid', $sql, ['courseid' => $context->instanceid]);
        } else if ($context->contextlevel == CONTEXT_SYSTEM) {
            $sql = "SELECT userid
                    FROM {local_issuebadge_issues}
                    WHERE courseid IS NULL OR courseid = 0";

            $userlist->add_from_sql('userid', $sql, []);
        }
    }

    /**
     * Export all user data for the specified user, in the specified contexts
     *
     * @param approved_contextlist $contextlist
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        foreach ($contextlist->get_contexts() as $context) {
            $params = ['userid' => $user->id];

            if ($context->contextlevel == CONTEXT_COURSE) {
                $params['courseid'] = $context->instanceid;
                $issues = $DB->get_records('local_issuebadge_issues', $params);
            } else if ($context->contextlevel == CONTEXT_SYSTEM) {
                $issues = $DB->get_records_select(
                    'local_issuebadge_issues',
                    'userid = :userid AND (courseid IS NULL OR courseid = 0)',
                    $params
                );
            } else {
                continue;
            }

            if (empty($issues)) {
                continue;
            }

            $data = [];
            foreach ($issues as $issue) {
                $data[] = (object)[
                    'badge_id' => $issue->badge_id,
                    'issue_id' => $issue->issue_id,
                    'public_url' => $issue->public_url,
                    'timecreated' => transform::datetime($issue->timecreated),
                ];
            }

            writer::with_context($context)->export_data(
                [get_string('pluginname', 'local_issuebadge')],
                (object)['badges' => $data]
            );
        }
    }

    /**
     * Delete all data for all users in the specified context
     *
     * @param \context $context
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel == CONTEXT_COURSE) {
            $DB->delete_records('local_issuebadge_issues', ['courseid' => $context->instanceid]);
        } else if ($context->contextlevel == CONTEXT_SYSTEM) {
            $DB->delete_records_select(
                'local_issuebadge_issues',
                'courseid IS NULL OR courseid = 0'
            );
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts
     *
     * @param approved_contextlist $contextlist
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel == CONTEXT_COURSE) {
                $DB->delete_records('local_issuebadge_issues', [
                    'userid' => $userid,
                    'courseid' => $context->instanceid,
                ]);
            } else if ($context->contextlevel == CONTEXT_SYSTEM) {
                $DB->delete_records_select(
                    'local_issuebadge_issues',
                    'userid = :userid AND (courseid IS NULL OR courseid = 0)',
                    ['userid' => $userid]
                );
            }
        }
    }

    /**
     * Delete multiple users within a single context
     *
     * @param approved_userlist $userlist
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        $userids = $userlist->get_userids();

        if (empty($userids)) {
            return;
        }

        list($insql, $inparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        if ($context->contextlevel == CONTEXT_COURSE) {
            $params = array_merge($inparams, ['courseid' => $context->instanceid]);
            $DB->delete_records_select(
                'local_issuebadge_issues',
                "userid $insql AND courseid = :courseid",
                $params
            );
        } else if ($context->contextlevel == CONTEXT_SYSTEM) {
            $DB->delete_records_select(
                'local_issuebadge_issues',
                "userid $insql AND (courseid IS NULL OR courseid = 0)",
                $inparams
            );
        }
    }
}
