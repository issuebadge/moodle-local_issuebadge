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
 * Issue form renderable
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
use context_course;

/**
 * Issue form renderable class
 */
class issue_form implements renderable, templatable {
    /** @var int Course ID */
    protected $courseid;

    /** @var context Context */
    protected $context;

    /**
     * Constructor
     *
     * @param int $courseid Course ID
     * @param \context $context Context
     */
    public function __construct($courseid, $context) {
        $this->courseid = $courseid;
        $this->context = $context;
    }

    /**
     * Export data for template
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $SESSION;

        $data = new stdClass();
        $data->courseid = $this->courseid;
        $data->sesskey = sesskey();
        $data->loadingtext = get_string('loadingbadges', 'local_issuebadge');
        $data->selectbadgetext = get_string('selectbadge', 'local_issuebadge');
        $data->selectusertext = get_string('selectuser', 'local_issuebadge');
        $data->issuebuttontext = get_string('issuebadge', 'local_issuebadge');

        // If in course context, provide enrolled users.
        if ($this->courseid > 0 && $this->context->contextlevel == CONTEXT_COURSE) {
            $enrolledusers = get_enrolled_users($this->context, '', 0, 'u.id, u.firstname, u.lastname, u.email');
            $users = [];
            foreach ($enrolledusers as $user) {
                $users[] = [
                    'id' => $user->id,
                    'fullname' => fullname($user),
                    'email' => $user->email,
                ];
            }
            $data->users = $users;
        }

        return $data;
    }
}
