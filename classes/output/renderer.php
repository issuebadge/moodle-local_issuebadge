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
 * Renderer for IssueBadge plugin
 *
 * @package    local_issuebadge
 * @copyright  2025 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_issuebadge\output;
defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;

/**
 * Renderer class for IssueBadge plugin
 */
class renderer extends plugin_renderer_base {
    /**
     * Render the management dashboard
     *
     * @param management_dashboard $page
     * @return string HTML output
     */
    public function render_management_dashboard(management_dashboard $page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_issuebadge/management_dashboard', $data);
    }

    /**
     * Render the badge issuance form
     *
     * @param issue_form $page
     * @return string HTML output
     */
    public function render_issue_form(issue_form $page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_issuebadge/issue_form', $data);
    }

    /**
     * Render the issued badges table
     *
     * @param issued_badges_table $page
     * @return string HTML output
     */
    public function render_issued_badges_table(issued_badges_table $page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_issuebadge/issued_badges_table', $data);
    }
}
