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

// Render management dashboard using template.
$dashboard = new \local_issuebadge\output\management_dashboard();
$renderer = $PAGE->get_renderer('local_issuebadge');
echo $renderer->render($dashboard);

echo $OUTPUT->footer();
