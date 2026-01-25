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
 * Badge issuance page - issue badges to Moodle users or external recipients
 *
 * @package    local_issuebadge
 * @copyright  2026 IssueBadge
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

// Handle form submission.
$message = '';
$messagetype = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && confirm_sesskey()) {
    $badgeid = required_param('badge_id', PARAM_ALPHANUMEXT);
    $issuetype = required_param('issue_type', PARAM_ALPHA);

    $recipientname = '';
    $recipientemail = '';
    $userid = 0;

    if ($issuetype === 'user') {
        // Issue to Moodle user.
        $userid = required_param('user_id', PARAM_INT);
        $user = $DB->get_record('user', ['id' => $userid], 'id, firstname, lastname, email', MUST_EXIST);
        $recipientname = fullname($user);
        $recipientemail = $user->email;
    } else {
        // Issue to external recipient.
        $recipientname = required_param('recipient_name', PARAM_TEXT);
        $recipientemail = required_param('recipient_email', PARAM_EMAIL);

        if (empty($recipientname) || empty($recipientemail)) {
            $message = get_string('error_norecipient', 'local_issuebadge');
            $messagetype = 'error';
        }
    }

    if (empty($message)) {
        try {
            $api = new \local_issuebadge\api\issuebadge_api();
            $result = $api->issue_badge([
                'name' => $recipientname,
                'email' => $recipientemail,
                'badge_id' => $badgeid,
            ]);

            // Store the issue record.
            $record = new stdClass();
            $record->userid = $userid;
            $record->recipient_name = $recipientname;
            $record->recipient_email = $recipientemail;
            $record->courseid = $courseid > 0 ? $courseid : null;
            $record->badge_id = $badgeid;
            $record->issue_id = $result['IssueId'];
            $record->public_url = $result['publicUrl'];
            $record->issuerby = $USER->id;
            $record->timecreated = time();
            $record->timemodified = time();

            $DB->insert_record('local_issuebadge_issues', $record);

            // Trigger custom event.
            $event = \local_issuebadge\event\badge_issued::create([
                'context' => $context,
                'relateduserid' => $userid > 0 ? $userid : null,
                'other' => [
                    'issueid' => $result['IssueId'],
                    'badgeid' => $badgeid,
                ],
            ]);
            $event->trigger();

            $message = get_string('badgeissued', 'local_issuebadge');
            $message .= '<br><strong>' . get_string('recipientname', 'local_issuebadge') . ':</strong> ' . s($recipientname);
            $message .= '<br><strong>' . get_string('issueid', 'local_issuebadge') . ':</strong> ' . s($result['IssueId']);
            if (!empty($result['publicUrl'])) {
                $message .= '<br><strong>' . get_string('publicurl', 'local_issuebadge') . ':</strong> ';
                $message .= '<a href="' . s($result['publicUrl']) . '" target="_blank">' . s($result['publicUrl']) . '</a>';
            }
            $messagetype = 'success';
        } catch (Exception $e) {
            $message = get_string('error_issuefailed', 'local_issuebadge', $e->getMessage());
            $messagetype = 'error';
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('issuebadge', 'local_issuebadge'));

// Show message if any.
if (!empty($message)) {
    if ($messagetype === 'success') {
        echo $OUTPUT->notification($message, 'success');
    } else {
        echo $OUTPUT->notification($message, 'error');
    }
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

// Get enrolled users if in course context.
$enrolledusers = [];
if ($courseid > 0) {
    $enrolledusers = get_enrolled_users($context, '', 0, 'u.id, u.firstname, u.lastname, u.email');
}

// Issue form.
echo html_writer::start_tag('form', [
    'method' => 'post',
    'action' => $PAGE->url,
    'class' => 'mform',
]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);

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
echo html_writer::select($badgeoptions, 'badge_id', '', false, [
    'id' => 'badge_id',
    'class' => 'form-control',
    'required' => 'required',
]);
echo html_writer::end_div();
echo html_writer::end_div();

// Issue type selection.
echo html_writer::start_div('form-group row mb-3');
echo html_writer::tag('label', get_string('issueto', 'local_issuebadge'), [
    'class' => 'col-sm-3 col-form-label',
]);
echo html_writer::start_div('col-sm-9');

echo html_writer::start_div('form-check');
echo html_writer::empty_tag('input', [
    'type' => 'radio',
    'name' => 'issue_type',
    'id' => 'issue_type_user',
    'value' => 'user',
    'class' => 'form-check-input',
    'checked' => 'checked',
]);
echo html_writer::tag('label', get_string('issuetouser', 'local_issuebadge'), [
    'for' => 'issue_type_user',
    'class' => 'form-check-label',
]);
echo html_writer::end_div();

echo html_writer::start_div('form-check');
echo html_writer::empty_tag('input', [
    'type' => 'radio',
    'name' => 'issue_type',
    'id' => 'issue_type_external',
    'value' => 'external',
    'class' => 'form-check-input',
]);
echo html_writer::tag('label', get_string('issuetoexternal', 'local_issuebadge'), [
    'for' => 'issue_type_external',
    'class' => 'form-check-label',
]);
echo html_writer::end_div();

echo html_writer::end_div();
echo html_writer::end_div();

// User selection (for Moodle users).
echo html_writer::start_div('form-group row mb-3', ['id' => 'user_selection_div']);
echo html_writer::tag('label', get_string('selectuser', 'local_issuebadge'), [
    'for' => 'user_id',
    'class' => 'col-sm-3 col-form-label',
]);
echo html_writer::start_div('col-sm-9');

if (!empty($enrolledusers)) {
    $useroptions = ['' => get_string('selectuser', 'local_issuebadge')];
    foreach ($enrolledusers as $user) {
        $useroptions[$user->id] = fullname($user) . ' (' . $user->email . ')';
    }
    echo html_writer::select($useroptions, 'user_id', '', false, [
        'id' => 'user_id',
        'class' => 'form-control',
    ]);
} else {
    // For system context, show a text input for user ID.
    echo html_writer::empty_tag('input', [
        'type' => 'number',
        'name' => 'user_id',
        'id' => 'user_id',
        'class' => 'form-control',
        'placeholder' => 'Enter Moodle User ID',
    ]);
}
echo html_writer::end_div();
echo html_writer::end_div();

// External recipient fields.
echo html_writer::start_div('form-group row mb-3', ['id' => 'external_recipient_div', 'style' => 'display:none;']);
echo html_writer::tag('label', get_string('recipientname', 'local_issuebadge'), [
    'for' => 'recipient_name',
    'class' => 'col-sm-3 col-form-label',
]);
echo html_writer::start_div('col-sm-9');
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'recipient_name',
    'id' => 'recipient_name',
    'class' => 'form-control',
    'placeholder' => get_string('enterrecipientname', 'local_issuebadge'),
]);
echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::start_div('form-group row mb-3', ['id' => 'external_email_div', 'style' => 'display:none;']);
echo html_writer::tag('label', get_string('recipientemail', 'local_issuebadge'), [
    'for' => 'recipient_email',
    'class' => 'col-sm-3 col-form-label',
]);
echo html_writer::start_div('col-sm-9');
echo html_writer::empty_tag('input', [
    'type' => 'email',
    'name' => 'recipient_email',
    'id' => 'recipient_email',
    'class' => 'form-control',
    'placeholder' => get_string('enterrecipientemail', 'local_issuebadge'),
]);
echo html_writer::end_div();
echo html_writer::end_div();

// Submit button.
echo html_writer::start_div('form-group row mb-3');
echo html_writer::start_div('col-sm-9 offset-sm-3');
echo html_writer::empty_tag('input', [
    'type' => 'submit',
    'value' => get_string('issuebadgebtn', 'local_issuebadge'),
    'class' => 'btn btn-primary',
]);
echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_tag('form');

// JavaScript to toggle between user and external fields.
$js = <<<JS
document.addEventListener('DOMContentLoaded', function() {
    var userRadio = document.getElementById('issue_type_user');
    var externalRadio = document.getElementById('issue_type_external');
    var userDiv = document.getElementById('user_selection_div');
    var externalNameDiv = document.getElementById('external_recipient_div');
    var externalEmailDiv = document.getElementById('external_email_div');
    var userIdField = document.getElementById('user_id');
    var recipientNameField = document.getElementById('recipient_name');
    var recipientEmailField = document.getElementById('recipient_email');

    function toggleFields() {
        if (userRadio.checked) {
            userDiv.style.display = '';
            externalNameDiv.style.display = 'none';
            externalEmailDiv.style.display = 'none';
            if (userIdField) userIdField.required = true;
            if (recipientNameField) recipientNameField.required = false;
            if (recipientEmailField) recipientEmailField.required = false;
        } else {
            userDiv.style.display = 'none';
            externalNameDiv.style.display = '';
            externalEmailDiv.style.display = '';
            if (userIdField) userIdField.required = false;
            if (recipientNameField) recipientNameField.required = true;
            if (recipientEmailField) recipientEmailField.required = true;
        }
    }

    userRadio.addEventListener('change', toggleFields);
    externalRadio.addEventListener('change', toggleFields);
    toggleFields();
});
JS;

echo html_writer::script($js);

// Navigation links.
echo html_writer::start_div('mt-4');
echo html_writer::link(
    new moodle_url('/local/issuebadge/index.php'),
    get_string('back', 'local_issuebadge') . ' ' . get_string('dashboard', 'local_issuebadge'),
    ['class' => 'btn btn-secondary mr-2']
);
echo html_writer::link(
    new moodle_url('/local/issuebadge/view.php'),
    get_string('viewissued', 'local_issuebadge'),
    ['class' => 'btn btn-info']
);
echo html_writer::end_div();

echo $OUTPUT->footer();
