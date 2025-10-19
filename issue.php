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

// Display form.
echo html_writer::start_div('local_issuebadge_issue_form');

echo html_writer::start_tag('form', ['id' => 'issuebadge_form', 'method' => 'post']);
echo html_writer::input_hidden_params(new moodle_url('/local/issuebadge/issue.php'));

// Badge selection.
echo html_writer::start_div('form-group');
echo html_writer::tag('label', get_string('badge', 'local_issuebadge'), ['for' => 'badge_id']);
echo html_writer::tag('select', '', [
    'id' => 'badge_id',
    'name' => 'badge_id',
    'class' => 'form-control',
    'required' => 'required',
]);
echo html_writer::tag('small', get_string('loadingbadges', 'local_issuebadge'), [
    'id' => 'badge_loading',
    'class' => 'form-text text-muted',
]);
echo html_writer::end_div();

// User selection.
echo html_writer::start_div('form-group');
echo html_writer::tag('label', get_string('selectuser', 'local_issuebadge'), ['for' => 'user_id']);

// Get enrolled users if in course context.
if ($courseid > 0) {
    $enrolledusers = get_enrolled_users($context, '', 0, 'u.id, u.firstname, u.lastname, u.email');
    $useroptions = [];
    foreach ($enrolledusers as $user) {
        $useroptions[$user->id] = fullname($user) . ' (' . $user->email . ')';
    }
    echo html_writer::select($useroptions, 'user_id', '', ['' => get_string('selectuser', 'local_issuebadge')], [
        'id' => 'user_id',
        'class' => 'form-control',
        'required' => 'required',
    ]);
} else {
    // For system-wide, use autocomplete (simplified version).
    echo html_writer::tag('input', '', [
        'type' => 'number',
        'id' => 'user_id',
        'name' => 'user_id',
        'class' => 'form-control',
        'placeholder' => 'User ID',
        'required' => 'required',
    ]);
}
echo html_writer::end_div();

// Submit button.
echo html_writer::start_div('form-group');
echo html_writer::tag('button', get_string('issuebadge', 'local_issuebadge'), [
    'type' => 'button',
    'id' => 'issue_button',
    'class' => 'btn btn-primary',
]);
echo html_writer::tag('span', '', ['id' => 'issue_spinner', 'class' => 'spinner-border spinner-border-sm ml-2 d-none']);
echo html_writer::end_div();

echo html_writer::end_tag('form');

// Result display area.
echo html_writer::div('', 'alert d-none', ['id' => 'issue_result']);

echo html_writer::end_div();

echo $OUTPUT->footer();
