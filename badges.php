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
 * Badge management page - import, refresh, and manage cached badges
 *
 * @package    local_issuebadge
 * @copyright  2026 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();

$context = context_system::instance();
require_capability('local/issuebadge:manage', $context);

$action = optional_param('action', '', PARAM_ALPHA);
$badgeid = optional_param('badgeid', '', PARAM_ALPHANUMEXT);
$confirm = optional_param('confirm', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/local/issuebadge/badges.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('managebadges', 'local_issuebadge'));
$PAGE->set_heading(get_string('managebadges', 'local_issuebadge'));
$PAGE->set_pagelayout('admin');

// Handle actions.
if ($action === 'refresh' && confirm_sesskey()) {
    // Refresh badges from API.
    try {
        $api = new \local_issuebadge\api\issuebadge_api();
        $badges = $api->get_badges();

        $count = 0;
        $now = time();

        foreach ($badges as $badge) {
            $existing = $DB->get_record('local_issuebadge_badges', ['badge_id' => $badge['id']]);

            $record = new stdClass();
            $record->badge_id = $badge['id'];
            $record->name = $badge['name'];
            $record->description = isset($badge['description']) ? $badge['description'] : '';
            $record->image_url = isset($badge['image_url']) ? $badge['image_url'] : '';
            $record->timemodified = $now;

            if ($existing) {
                $record->id = $existing->id;
                $DB->update_record('local_issuebadge_badges', $record);
            } else {
                $record->timecreated = $now;
                $DB->insert_record('local_issuebadge_badges', $record);
                $count++;
            }
        }

        redirect(
            new moodle_url('/local/issuebadge/badges.php'),
            get_string('refreshsuccess', 'local_issuebadge', count($badges)),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } catch (Exception $e) {
        redirect(
            new moodle_url('/local/issuebadge/badges.php'),
            get_string('refresherror', 'local_issuebadge', $e->getMessage()),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }
}

if ($action === 'delete' && $badgeid && confirm_sesskey()) {
    if ($confirm) {
        $DB->delete_records('local_issuebadge_badges', ['badge_id' => $badgeid]);
        redirect(
            new moodle_url('/local/issuebadge/badges.php'),
            get_string('badgedeleted', 'local_issuebadge'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
}

// Handle add badge form submission.
if ($action === 'add' && confirm_sesskey()) {
    $newbadgeid = required_param('new_badge_id', PARAM_ALPHANUMEXT);
    $newbadgename = required_param('new_badge_name', PARAM_TEXT);
    $newbadgedesc = optional_param('new_badge_description', '', PARAM_TEXT);

    if (!empty($newbadgeid) && !empty($newbadgename)) {
        $existing = $DB->get_record('local_issuebadge_badges', ['badge_id' => $newbadgeid]);

        $record = new stdClass();
        $record->badge_id = $newbadgeid;
        $record->name = $newbadgename;
        $record->description = $newbadgedesc;
        $record->timemodified = time();

        if ($existing) {
            $record->id = $existing->id;
            $DB->update_record('local_issuebadge_badges', $record);
            $message = get_string('badgeupdated', 'local_issuebadge');
        } else {
            $record->timecreated = time();
            $DB->insert_record('local_issuebadge_badges', $record);
            $message = get_string('badgeadded', 'local_issuebadge');
        }

        redirect(
            new moodle_url('/local/issuebadge/badges.php'),
            $message,
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managebadges', 'local_issuebadge'));

// Show delete confirmation if needed.
if ($action === 'delete' && $badgeid && !$confirm) {
    $badge = $DB->get_record('local_issuebadge_badges', ['badge_id' => $badgeid]);
    if ($badge) {
        $confirmurl = new moodle_url('/local/issuebadge/badges.php', [
            'action' => 'delete',
            'badgeid' => $badgeid,
            'confirm' => 1,
            'sesskey' => sesskey(),
        ]);
        $cancelurl = new moodle_url('/local/issuebadge/badges.php');
        echo $OUTPUT->confirm(
            get_string('confirmdelete', 'local_issuebadge') . '<br><strong>' . s($badge->name) . '</strong>',
            $confirmurl,
            $cancelurl
        );
        echo $OUTPUT->footer();
        exit;
    }
}

// Refresh button.
$refreshurl = new moodle_url('/local/issuebadge/badges.php', ['action' => 'refresh', 'sesskey' => sesskey()]);
echo html_writer::start_div('mb-4');
echo html_writer::link($refreshurl, get_string('refreshbadges', 'local_issuebadge'), [
    'class' => 'btn btn-primary',
]);
echo html_writer::end_div();

// Get cached badges.
$badges = $DB->get_records('local_issuebadge_badges', null, 'name ASC');

if ($badges) {
    // Display badges table.
    $table = new html_table();
    $table->head = [
        get_string('badgeid', 'local_issuebadge'),
        get_string('badgename', 'local_issuebadge'),
        get_string('lastrefreshed', 'local_issuebadge'),
        get_string('actions', 'local_issuebadge'),
    ];
    $table->attributes['class'] = 'table table-striped';

    foreach ($badges as $badge) {
        $deleteurl = new moodle_url('/local/issuebadge/badges.php', [
            'action' => 'delete',
            'badgeid' => $badge->badge_id,
            'sesskey' => sesskey(),
        ]);

        $actions = html_writer::link($deleteurl, get_string('delete'), [
            'class' => 'btn btn-sm btn-danger',
        ]);

        $table->data[] = [
            html_writer::tag('code', s($badge->badge_id)),
            s($badge->name),
            userdate($badge->timemodified),
            $actions,
        ];
    }

    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('nobadges', 'local_issuebadge'), 'info');
}

// Add badge manually form.
echo html_writer::start_tag('div', ['class' => 'card mt-4']);
echo html_writer::start_tag('div', ['class' => 'card-header']);
$helpicon = $OUTPUT->help_icon('addbadgemanually', 'local_issuebadge');
echo html_writer::tag('h4', get_string('addbadgemanually', 'local_issuebadge') . ' ' . $helpicon, ['class' => 'mb-0']);
echo html_writer::end_tag('div');
echo html_writer::start_tag('div', ['class' => 'card-body']);

echo html_writer::start_tag('form', [
    'method' => 'post',
    'action' => new moodle_url('/local/issuebadge/badges.php'),
    'class' => 'form-inline',
]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => 'add']);

echo html_writer::start_div('form-group mr-3 mb-2');
echo html_writer::tag('label', get_string('badgeid', 'local_issuebadge') . ': ', ['for' => 'new_badge_id', 'class' => 'mr-2']);
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'id' => 'new_badge_id',
    'name' => 'new_badge_id',
    'class' => 'form-control',
    'required' => 'required',
    'placeholder' => 'e.g., abc123',
]);
echo html_writer::end_div();

echo html_writer::start_div('form-group mr-3 mb-2');
echo html_writer::tag('label', get_string('badgename', 'local_issuebadge') . ': ', ['for' => 'new_badge_name', 'class' => 'mr-2']);
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'id' => 'new_badge_name',
    'name' => 'new_badge_name',
    'class' => 'form-control',
    'required' => 'required',
    'placeholder' => 'Badge Name',
]);
echo html_writer::end_div();


echo html_writer::start_div('form-group mb-2');
echo html_writer::empty_tag('input', [
    'type' => 'submit',
    'value' => get_string('addbadge', 'local_issuebadge'),
    'class' => 'btn btn-success',
]);
echo html_writer::end_div();

echo html_writer::end_tag('form');
echo html_writer::end_tag('div');
echo html_writer::end_tag('div');

// Navigation links.
echo html_writer::start_div('mt-4');
echo html_writer::link(
    new moodle_url('/local/issuebadge/index.php'),
    get_string('back', 'local_issuebadge') . ' ' . get_string('dashboard', 'local_issuebadge'),
    ['class' => 'btn btn-secondary']
);
echo html_writer::end_div();

echo $OUTPUT->footer();
