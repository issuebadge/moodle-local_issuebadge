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
 * View issued badges
 *
 * @package    local_issuebadge
 * @copyright  2025 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/tablelib.php');

$userid = optional_param('userid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 25, PARAM_INT);

require_login();

if ($courseid > 0) {
    $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    $context = context_course::instance($courseid);
} else {
    $context = context_system::instance();
}

$PAGE->set_url(new moodle_url('/local/issuebadge/view.php', ['userid' => $userid, 'courseid' => $courseid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('viewissued', 'local_issuebadge'));
$PAGE->set_heading(get_string('viewissued', 'local_issuebadge'));

// Check permissions.
if ($userid > 0 && $userid != $USER->id) {
    require_capability('local/issuebadge:manage', $context);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('viewissued', 'local_issuebadge'));

// Build query.
$sql = "SELECT i.*, u.firstname, u.lastname, u.email, c.fullname as coursename
        FROM {local_issuebadge_issues} i
        JOIN {user} u ON u.id = i.userid
        LEFT JOIN {course} c ON c.id = i.courseid
        WHERE 1=1";

$params = [];

if ($userid > 0) {
    $sql .= " AND i.userid = :userid";
    $params['userid'] = $userid;
}

if ($courseid > 0) {
    $sql .= " AND i.courseid = :courseid";
    $params['courseid'] = $courseid;
}

$sql .= " ORDER BY i.timecreated DESC";

// Get total count for pagination.
$countsql = "SELECT COUNT(i.id)
             FROM {local_issuebadge_issues} i
             WHERE 1=1";

if ($userid > 0) {
    $countsql .= " AND i.userid = :userid";
}

if ($courseid > 0) {
    $countsql .= " AND i.courseid = :courseid";
}

$totalcount = $DB->count_records_sql($countsql, $params);

// Get records for current page.
$issues = [];
$paginationhtml = '';

if ($totalcount > 0) {
    $issues = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

    // Generate pagination HTML.
    $baseurl = new moodle_url('/local/issuebadge/view.php', [
        'userid' => $userid,
        'courseid' => $courseid,
        'perpage' => $perpage,
    ]);

    $paginationhtml = $OUTPUT->paging_bar($totalcount, $page, $perpage, $baseurl);
}

// Render table using template.
$badgestable = new \local_issuebadge\output\issued_badges_table($issues, $paginationhtml);
$renderer = $PAGE->get_renderer('local_issuebadge');
echo $renderer->render($badgestable);

echo $OUTPUT->footer();
