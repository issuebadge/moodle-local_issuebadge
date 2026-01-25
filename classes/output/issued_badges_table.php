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
 * Issued badges table renderable
 *
 * @package    local_issuebadge
 * @copyright  2026 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_issuebadge\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Issued badges table renderable class
 */
class issued_badges_table implements renderable, templatable {
    /** @var array Badge issues */
    protected $issues;

    /** @var string Pagination HTML */
    protected $pagination;

    /**
     * Constructor.
     *
     * @param array $issues Array of badge issues.
     * @param string $pagination Pagination HTML.
     */
    public function __construct($issues, $pagination = '') {
        $this->issues = $issues;
        $this->pagination = $pagination;
    }

    /**
     * Export data for template.
     *
     * @param renderer_base $output The renderer base.
     * @return stdClass The data for the template.
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();

        $data->hasissues = !empty($this->issues);

        if ($data->hasissues) {
            $issuesdata = [];
            foreach ($this->issues as $issue) {
                // Determine recipient name - use Moodle user fullname or external recipient_name.
                if (!empty($issue->firstname) || !empty($issue->lastname)) {
                    $recipientname = trim($issue->firstname . ' ' . $issue->lastname);
                } else if (!empty($issue->recipient_name)) {
                    $recipientname = $issue->recipient_name;
                } else {
                    $recipientname = get_string('unknown', 'moodle');
                }

                // Determine email.
                $recipientemail = !empty($issue->email) ? $issue->email : $issue->recipient_email;

                // Determine badge name.
                $badgename = !empty($issue->badgename) ? $issue->badgename : $issue->badge_id;

                $issuesdata[] = [
                    'recipientname' => $recipientname,
                    'recipientemail' => $recipientemail ?: '-',
                    'coursename' => !empty($issue->coursename) ? $issue->coursename : '-',
                    'badgeid' => $issue->badge_id,
                    'badgename' => $badgename,
                    'issuedate' => userdate($issue->timecreated),
                    'publicurl' => $issue->public_url,
                    'haspublicurl' => !empty($issue->public_url),
                    'isexternal' => empty($issue->userid) || $issue->userid == 0,
                ];
            }
            $data->issues = $issuesdata;
            $data->pagination = $this->pagination;
        }

        return $data;
    }
}
