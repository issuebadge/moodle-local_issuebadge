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
 * Badge issuance page
 *
 * @package    local_issuebadge
 * @copyright  2025 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$courseid = optional_param('courseid', 0, PARAM_INT);

require_login();

if ($courseid > 0) {
    $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    $context = context_course::instance($courseid);
    require_capability('local/issuebadge:issue', $context);
    $PAGE->set_course($course);
} else {
    $context = context_system::instance();
    require_capability('local/issuebadge:issue', $context);
}

$PAGE->set_url(new moodle_url('/local/issuebadge/issue.php', ['courseid' => $courseid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('issuebadge', 'local_issuebadge'));
$PAGE->set_heading(get_string('issuebadge', 'local_issuebadge'));

// Include JavaScript.
$PAGE->requires->js_call_amd('local_issuebadge/issue', 'init', [$courseid]);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('issuebadge', 'local_issuebadge'));

// Render issue form using template.
$issueform = new \local_issuebadge\output\issue_form($courseid, $context);
$renderer = $PAGE->get_renderer('local_issuebadge');
echo $renderer->render($issueform);

echo $OUTPUT->footer();
