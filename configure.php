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
 * Course badge configuration page - set up auto-issue triggers
 *
 * @package    local_issuebadge
 * @copyright  2026 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);

require_login();

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_course::instance($courseid);
require_capability('local/issuebadge:manage', $context);

$PAGE->set_course($course);
$PAGE->set_url(new moodle_url('/local/issuebadge/configure.php', ['courseid' => $courseid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('courseconfiguration', 'local_issuebadge'));
$PAGE->set_heading(get_string('courseconfiguration', 'local_issuebadge'));

// Get existing configuration.
$config = $DB->get_record('local_issuebadge_course', ['courseid' => $courseid]);

// Handle form submission.
$message = '';
$messagetype = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && confirm_sesskey()) {
    $badgeid = required_param('badge_id', PARAM_ALPHANUMEXT);
    $triggertype = required_param('trigger_type', PARAM_ALPHA);
    $quizid = optional_param('quiz_id', 0, PARAM_INT);
    $passinggrade = optional_param('passing_grade', 70, PARAM_INT);
    $enabled = optional_param('enabled', 0, PARAM_INT);

    // Validate passing grade.
    if ($passinggrade < 0) {
        $passinggrade = 0;
    }
    if ($passinggrade > 100) {
        $passinggrade = 100;
    }

    $record = new stdClass();
    $record->courseid = $courseid;
    $record->badge_id = $badgeid;
    $record->trigger_type = $triggertype;
    $record->quiz_id = ($triggertype === 'quiz') ? $quizid : 0;
    $record->passing_grade = $passinggrade;
    $record->enabled = $enabled ? 1 : 0;
    $record->timemodified = time();

    if ($config) {
        $record->id = $config->id;
        $DB->update_record('local_issuebadge_course', $record);
    } else {
        $record->timecreated = time();
        $DB->insert_record('local_issuebadge_course', $record);
    }

    $message = get_string('configsaved', 'local_issuebadge');
    $messagetype = 'success';

    // Refresh config.
    $config = $DB->get_record('local_issuebadge_course', ['courseid' => $courseid]);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('courseconfiguration', 'local_issuebadge') . ': ' . format_string($course->fullname));

// Show message if any.
if (!empty($message)) {
    echo $OUTPUT->notification($message, $messagetype);
}

// Get cached badges.
$badges = $DB->get_records('local_issuebadge_badges', null, 'name ASC');

if (empty($badges)) {
    echo $OUTPUT->notification(get_string('nobadgescached', 'local_issuebadge'), 'warning');
    echo html_writer::link(
        new moodle_url('/local/issuebadge/badges.php'),
        get_string('managebadges', 'local_issuebadge'),
        ['class' => 'btn btn-primary']
    );
    echo $OUTPUT->footer();
    exit;
}

// Get quizzes in this course.
$quizzes = $DB->get_records('quiz', ['course' => $courseid], 'name ASC', 'id, name');

// Configuration form.
echo html_writer::start_tag('form', [
    'method' => 'post',
    'action' => $PAGE->url,
    'class' => 'mform',
]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);

// Enable toggle.
echo html_writer::start_div('form-group row mb-3');
echo html_writer::tag('label', get_string('enableautoissue', 'local_issuebadge'), [
    'for' => 'enabled',
    'class' => 'col-sm-3 col-form-label',
]);
echo html_writer::start_div('col-sm-9');
echo html_writer::start_div('form-check');
echo html_writer::empty_tag('input', [
    'type' => 'checkbox',
    'name' => 'enabled',
    'id' => 'enabled',
    'value' => '1',
    'class' => 'form-check-input',
    'checked' => ($config && $config->enabled) ? 'checked' : null,
]);
echo html_writer::tag('label', get_string('enableautoissue_desc', 'local_issuebadge'), [
    'for' => 'enabled',
    'class' => 'form-check-label text-muted',
]);
echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div();

// Badge selection.
echo html_writer::start_div('form-group row mb-3');
echo html_writer::tag('label', get_string('selectbadge', 'local_issuebadge'), [
    'for' => 'badge_id',
    'class' => 'col-sm-3 col-form-label',
]);
echo html_writer::start_div('col-sm-9');
$badgeoptions = ['' => get_string('selectbadge', 'local_issuebadge')];
foreach ($badges as $badge) {
    $badgeoptions[$badge->badge_id] = $badge->name;
}
$selectedbadge = $config ? $config->badge_id : '';
echo html_writer::select($badgeoptions, 'badge_id', $selectedbadge, false, [
    'id' => 'badge_id',
    'class' => 'form-control',
    'required' => 'required',
]);
echo html_writer::end_div();
echo html_writer::end_div();

// Trigger type selection.
echo html_writer::start_div('form-group row mb-3');
echo html_writer::tag('label', get_string('triggertype', 'local_issuebadge'), [
    'class' => 'col-sm-3 col-form-label',
]);
echo html_writer::start_div('col-sm-9');

$currenttrigger = $config ? $config->trigger_type : 'manual';

echo html_writer::start_div('form-check');
echo html_writer::empty_tag('input', [
    'type' => 'radio',
    'name' => 'trigger_type',
    'id' => 'trigger_manual',
    'value' => 'manual',
    'class' => 'form-check-input',
    'checked' => ($currenttrigger === 'manual') ? 'checked' : null,
]);
echo html_writer::tag('label', get_string('trigger_manual', 'local_issuebadge'), [
    'for' => 'trigger_manual',
    'class' => 'form-check-label',
]);
echo html_writer::end_div();

echo html_writer::start_div('form-check');
echo html_writer::empty_tag('input', [
    'type' => 'radio',
    'name' => 'trigger_type',
    'id' => 'trigger_completion',
    'value' => 'completion',
    'class' => 'form-check-input',
    'checked' => ($currenttrigger === 'completion') ? 'checked' : null,
]);
echo html_writer::tag('label', get_string('trigger_completion', 'local_issuebadge'), [
    'for' => 'trigger_completion',
    'class' => 'form-check-label',
]);
echo html_writer::end_div();

echo html_writer::start_div('form-check');
echo html_writer::empty_tag('input', [
    'type' => 'radio',
    'name' => 'trigger_type',
    'id' => 'trigger_quiz',
    'value' => 'quiz',
    'class' => 'form-check-input',
    'checked' => ($currenttrigger === 'quiz') ? 'checked' : null,
]);
echo html_writer::tag('label', get_string('trigger_quiz', 'local_issuebadge'), [
    'for' => 'trigger_quiz',
    'class' => 'form-check-label',
]);
echo html_writer::end_div();

echo html_writer::end_div();
echo html_writer::end_div();

// Passing grade (shown for both completion and quiz triggers).
$gradedivstyle = ($currenttrigger === 'completion' || $currenttrigger === 'quiz') ? '' : 'display:none;';
echo html_writer::start_div('form-group row mb-3', ['id' => 'passing_grade_div', 'style' => $gradedivstyle]);
echo html_writer::tag('label', get_string('passinggrade', 'local_issuebadge'), [
    'for' => 'passing_grade',
    'class' => 'col-sm-3 col-form-label',
]);
echo html_writer::start_div('col-sm-9');
$currentgrade = $config ? $config->passing_grade : 0;
echo html_writer::start_div('input-group', ['style' => 'max-width: 150px;']);
echo html_writer::empty_tag('input', [
    'type' => 'number',
    'name' => 'passing_grade',
    'id' => 'passing_grade',
    'value' => $currentgrade,
    'min' => '0',
    'max' => '100',
    'class' => 'form-control',
]);
echo html_writer::tag('span', '%', ['class' => 'input-group-text']);
echo html_writer::end_div();
echo html_writer::tag('small', get_string('passinggrade_desc_all', 'local_issuebadge'), [
    'class' => 'form-text text-muted',
]);
echo html_writer::end_div();
echo html_writer::end_div();

// Quiz selection (only shown when quiz trigger is selected).
$quizdivstyle = ($currenttrigger === 'quiz') ? '' : 'display:none;';
echo html_writer::start_div('form-group row mb-3', ['id' => 'quiz_selection_div', 'style' => $quizdivstyle]);
echo html_writer::tag('label', get_string('selectquiz', 'local_issuebadge'), [
    'for' => 'quiz_id',
    'class' => 'col-sm-3 col-form-label',
]);
echo html_writer::start_div('col-sm-9');

if (!empty($quizzes)) {
    $quizoptions = ['' => get_string('selectquiz', 'local_issuebadge')];
    foreach ($quizzes as $quiz) {
        $quizoptions[$quiz->id] = $quiz->name;
    }
    $selectedquiz = $config ? $config->quiz_id : '';
    echo html_writer::select($quizoptions, 'quiz_id', $selectedquiz, false, [
        'id' => 'quiz_id',
        'class' => 'form-control',
    ]);
} else {
    echo html_writer::tag('p', get_string('noquizzes', 'local_issuebadge'), ['class' => 'text-muted']);
}
echo html_writer::end_div();
echo html_writer::end_div();

// Submit button.
echo html_writer::start_div('form-group row mb-3');
echo html_writer::start_div('col-sm-9 offset-sm-3');
echo html_writer::empty_tag('input', [
    'type' => 'submit',
    'value' => get_string('save', 'local_issuebadge'),
    'class' => 'btn btn-primary',
]);
echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_tag('form');

// JavaScript to toggle fields based on trigger type.
$js = <<<JS
document.addEventListener('DOMContentLoaded', function() {
    var triggerManual = document.getElementById('trigger_manual');
    var triggerCompletion = document.getElementById('trigger_completion');
    var triggerQuiz = document.getElementById('trigger_quiz');
    var quizDiv = document.getElementById('quiz_selection_div');
    var gradeDiv = document.getElementById('passing_grade_div');

    function toggleFields() {
        // Show passing grade for both completion and quiz
        if (triggerCompletion.checked || triggerQuiz.checked) {
            gradeDiv.style.display = '';
        } else {
            gradeDiv.style.display = 'none';
        }

        // Show quiz selection only for quiz trigger
        if (triggerQuiz.checked) {
            quizDiv.style.display = '';
        } else {
            quizDiv.style.display = 'none';
        }
    }

    triggerManual.addEventListener('change', toggleFields);
    triggerCompletion.addEventListener('change', toggleFields);
    triggerQuiz.addEventListener('change', toggleFields);
    toggleFields();
});
JS;

echo html_writer::script($js);

// Navigation links.
echo html_writer::start_div('mt-4');
echo html_writer::link(
    new moodle_url('/course/view.php', ['id' => $courseid]),
    get_string('back', 'local_issuebadge') . ' ' . get_string('course'),
    ['class' => 'btn btn-secondary mr-2']
);
echo html_writer::link(
    new moodle_url('/local/issuebadge/issue.php', ['courseid' => $courseid]),
    get_string('issuebadge', 'local_issuebadge'),
    ['class' => 'btn btn-primary']
);
echo html_writer::end_div();

echo $OUTPUT->footer();
