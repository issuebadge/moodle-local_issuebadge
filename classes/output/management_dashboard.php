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
 * @copyright  2025 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_issuebadge\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Management dashboard renderable class
 */
class management_dashboard implements renderable, templatable {
    /**
     * Export data for template
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();

        $data->heading = get_string('managebadges', 'local_issuebadge');
        $data->description = get_string('managebadges_desc', 'local_issuebadge');

        $data->links = [
            [
                'url' => new \moodle_url('/local/issuebadge/issue.php'),
                'text' => get_string('issuemanual', 'local_issuebadge'),
            ],
            [
                'url' => new \moodle_url('/local/issuebadge/view.php'),
                'text' => get_string('viewissued', 'local_issuebadge'),
            ],
            [
                'url' => new \moodle_url('/admin/settings.php', ['section' => 'local_issuebadge']),
                'text' => get_string('settings', 'local_issuebadge'),
            ],
        ];

        return $data;
    }
}
