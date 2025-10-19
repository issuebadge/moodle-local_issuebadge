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
 * Badge issued event
 *
 * @package    local_issuebadge
 * @copyright  2025 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_issuebadge\event;

/**
 * Badge issued event class
 */
class badge_issued extends \core\event\base {
    /**
     * Initialize event
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Return localized event name
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventbadgeissued', 'local_issuebadge');
    }

    /**
     * Return event description
     *
     * @return string
     */
    public function get_description() {
        return "A badge was issued to user {$this->relateduserid} with issue ID {$this->other['issueid']}.";
    }

    /**
     * Get URL related to the event
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/local/issuebadge/view.php', ['userid' => $this->relateduserid]);
    }
}
