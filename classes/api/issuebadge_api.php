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
 * IssueBadge API client
 *
 * @package    local_issuebadge
 * @copyright  2025 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_issuebadge\api;

/**
 * Class issuebadge_api
 *
 * Handles communication with the IssueBadge API
 */
class issuebadge_api {
    /** @var string API base URL */
    private $apiurl;

    /** @var string API bearer token */
    private $apikey;

    /**
     * Constructor
     *
     * @throws \moodle_exception
     */
    public function __construct() {
        $this->apiurl = local_issuebadge_get_api_url();
        $this->apikey = local_issuebadge_get_api_key();

        if (empty($this->apikey)) {
            throw new \moodle_exception('error_noapi', 'local_issuebadge');
        }
    }

    /**
     * Make an API request
     *
     * @param string $endpoint API endpoint (e.g., 'badge/getall')
     * @param string $method HTTP method (GET or POST)
     * @param array $data Request data for POST requests
     * @return array Response data
     * @throws \moodle_exception
     */
    private function request($endpoint, $method = 'GET', $data = []) {
        $url = rtrim($this->apiurl, '/') . '/' . ltrim($endpoint, '/');

        $options = [
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_HTTPHEADER' => [
                'Authorization: Bearer ' . $this->apikey,
                'Content-Type: application/json',
            ],
        ];

        if ($method === 'POST') {
            $options['CURLOPT_POST'] = true;
            $options['CURLOPT_POSTFIELDS'] = json_encode($data);
        }

        $curl = new \curl();
        $response = $curl->post($url, json_encode($data), $options);

        // Check for cURL errors.
        if ($curl->get_errno()) {
            throw new \moodle_exception('error_api', 'local_issuebadge', '', $curl->error);
        }

        // Decode response.
        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \moodle_exception('error_invalidresponse', 'local_issuebadge');
        }

        // Check for API errors.
        if (!isset($result['success']) || !$result['success']) {
            $error = isset($result['message']) ? $result['message'] : get_string('error_api', 'local_issuebadge');
            throw new \moodle_exception('error_api', 'local_issuebadge', '', $error);
        }

        return $result;
    }

    /**
     * Get all available badges
     *
     * @return array List of badges
     * @throws \moodle_exception
     */
    public function get_badges() {
        $response = $this->request('badge/getall', 'GET');

        if (!isset($response['data']) || !is_array($response['data'])) {
            throw new \moodle_exception('error_invalidresponse', 'local_issuebadge');
        }

        return $response['data'];
    }

    /**
     * Issue a badge to a recipient
     *
     * @param array $params Issue parameters (name, email, badge_id)
     * @return array Issue result with IssueId and publicUrl
     * @throws \moodle_exception
     */
    public function issue_badge($params) {
        // Validate required parameters.
        if (empty($params['name']) || empty($params['badge_id'])) {
            throw new \moodle_exception('error_missingdata', 'local_issuebadge');
        }

        // Prepare request data.
        $data = [
            'name' => $params['name'],
            'badge_id' => $params['badge_id'],
            'idempotency_key' => $this->generate_uuid(),
        ];

        // Add optional email.
        if (!empty($params['email'])) {
            $data['email'] = $params['email'];
        }

        $response = $this->request('issue/create', 'POST', $data);

        // Validate response structure.
        if (!isset($response['IssueId']) || !isset($response['publicUrl'])) {
            throw new \moodle_exception('error_invalidresponse', 'local_issuebadge');
        }

        return [
            'IssueId' => $response['IssueId'],
            'publicUrl' => $response['publicUrl'],
        ];
    }

    /**
     * Generate a UUID for idempotency key
     *
     * @return string UUID v4
     */
    private function generate_uuid() {
        // Use Moodle's built-in UUID generator if available (Moodle 3.9+).
        if (function_exists('wp_generate_uuid4')) {
            return wp_generate_uuid4();
        }

        // Fallback UUID v4 generation.
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
