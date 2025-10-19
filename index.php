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
 * IssueBadge management page
 *
 * @package    local_issuebadge
 * @copyright  2025 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('local_issuebadge_manage');

$context = context_system::instance();
require_capability('local/issuebadge:manage', $context);

$PAGE->set_url(new moodle_url('/local/issuebadge/index.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('issuebadgemanagement', 'local_issuebadge'));
$PAGE->set_heading(get_string('issuebadgemanagement', 'local_issuebadge'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('issuebadgemanagement', 'local_issuebadge'));

// Display links to different management pages.
echo html_writer::start_div('local_issuebadge_dashboard');

echo html_writer::tag('h3', get_string('managebadges', 'local_issuebadge'));
echo html_writer::tag('p', get_string('managebadges', 'local_issuebadge'));

$links = [
    new moodle_url('/local/issuebadge/issue.php') => get_string('issuemanual', 'local_issuebadge'),
    new moodle_url('/local/issuebadge/view.php') => get_string('viewissued', 'local_issuebadge'),
    new moodle_url('/admin/settings.php', ['section' => 'local_issuebadge']) => get_string('settings', 'local_issuebadge'),
];

echo html_writer::start_tag('ul');
foreach ($links as $url => $text) {
    echo html_writer::tag('li', html_writer::link($url, $text));
}
echo html_writer::end_tag('ul');

echo html_writer::end_div();

echo $OUTPUT->footer();
