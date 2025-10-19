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

$issues = $DB->get_records_sql($sql, $params);

if (empty($issues)) {
    echo html_writer::tag('p', get_string('nobadges', 'local_issuebadge'));
} else {
    // Create table.
    $table = new html_table();
    $table->head = [
        get_string('recipientname', 'local_issuebadge'),
        get_string('recipientemail', 'local_issuebadge'),
        get_string('course'),
        get_string('badge', 'local_issuebadge'),
        get_string('issuedate', 'local_issuebadge'),
        get_string('publicurl', 'local_issuebadge'),
    ];

    foreach ($issues as $issue) {
        $row = [];
        $row[] = fullname($issue);
        $row[] = $issue->email;
        $row[] = $issue->coursename ? $issue->coursename : '-';
        $row[] = $issue->badge_id;
        $row[] = userdate($issue->timecreated);
        $row[] = html_writer::link($issue->public_url, get_string('view'), ['target' => '_blank']);

        $table->data[] = $row;
    }

    echo html_writer::table($table);
}

echo $OUTPUT->footer();
