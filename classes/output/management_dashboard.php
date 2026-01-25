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
 * Management dashboard renderable
 *
 * @package    local_issuebadge
 * @copyright  2026 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_issuebadge\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Management dashboard renderable class
 */
class management_dashboard implements renderable, templatable {
    /**
     * Export data for template.
     *
     * @param renderer_base $output The renderer base.
     * @return stdClass The data for the template.
     */
    public function export_for_template(renderer_base $output) {
        global $DB;

        $data = new stdClass();

        // Stats.
        $data->badgecount = $DB->count_records('local_issuebadge_badges');
        $data->issuedcount = $DB->count_records('local_issuebadge_issues');
        $data->coursecount = $DB->count_records('local_issuebadge_course', ['enabled' => 1]);

        // Check API configuration.
        $apikey = get_config('local_issuebadge', 'apikey');
        $data->apiconfigured = !empty($apikey);

        // Menu cards.
        $data->menucards = [
            [
                'title' => get_string('managebadges', 'local_issuebadge'),
                'description' => get_string('managebadges_desc', 'local_issuebadge'),
                'url' => (new \moodle_url('/local/issuebadge/badges.php'))->out(false),
                'icon' => 'fa-certificate',
                'btnclass' => 'btn-primary',
            ],
            [
                'title' => get_string('issuebadge', 'local_issuebadge'),
                'description' => get_string('issuemanual_desc', 'local_issuebadge'),
                'url' => (new \moodle_url('/local/issuebadge/issue.php'))->out(false),
                'icon' => 'fa-paper-plane',
                'btnclass' => 'btn-success',
            ],
            [
                'title' => get_string('viewissued', 'local_issuebadge'),
                'description' => get_string('viewissued_desc', 'local_issuebadge'),
                'url' => (new \moodle_url('/local/issuebadge/view.php'))->out(false),
                'icon' => 'fa-list-alt',
                'btnclass' => 'btn-info',
            ],
            [
                'title' => get_string('settings', 'local_issuebadge'),
                'description' => get_string('settings_desc', 'local_issuebadge'),
                'url' => (new \moodle_url('/admin/settings.php', ['section' => 'local_issuebadge']))->out(false),
                'icon' => 'fa-cog',
                'btnclass' => 'btn-secondary',
            ],
        ];

        // How it works steps.
        $data->steps = [
            [
                'number' => '1',
                'title' => get_string('step1_title', 'local_issuebadge'),
                'description' => get_string('step1_desc', 'local_issuebadge'),
            ],
            [
                'number' => '2',
                'title' => get_string('step2_title', 'local_issuebadge'),
                'description' => get_string('step2_desc', 'local_issuebadge'),
            ],
            [
                'number' => '3',
                'title' => get_string('step3_title', 'local_issuebadge'),
                'description' => get_string('step3_desc', 'local_issuebadge'),
            ],
            [
                'number' => '4',
                'title' => get_string('step4_title', 'local_issuebadge'),
                'description' => get_string('step4_desc', 'local_issuebadge'),
            ],
        ];

        // Get courses with badges configured for the course list.
        $courses = $DB->get_records_sql("
            SELECT c.id, c.fullname, lb.badge_id, lb.trigger_type, lb.enabled, b.name as badgename
            FROM {local_issuebadge_course} lb
            JOIN {course} c ON c.id = lb.courseid
            LEFT JOIN {local_issuebadge_badges} b ON b.badge_id = lb.badge_id
            ORDER BY c.fullname ASC
            LIMIT 10
        ");

        $data->configuredcourses = [];
        foreach ($courses as $course) {
            $data->configuredcourses[] = [
                'coursename' => format_string($course->fullname),
                'badgename' => $course->badgename ?: $course->badge_id,
                'triggertype' => get_string('trigger_' . $course->trigger_type, 'local_issuebadge'),
                'enabled' => $course->enabled,
                'configureurl' => (new \moodle_url('/local/issuebadge/configure.php', ['courseid' => $course->id]))->out(false),
            ];
        }
        $data->hasconfiguredcourses = !empty($data->configuredcourses);

        return $data;
    }
}
