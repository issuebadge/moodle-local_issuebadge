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
 * @copyright  2026 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_issuebadge\api;

defined('MOODLE_INTERNAL') || die();

global $CFG;

// Ensure lib.php is loaded for helper functions.
require_once(__DIR__ . '/../../lib.php');
// Ensure Moodle's curl class is available.
require_once($CFG->libdir . '/filelib.php');

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

    /** @var string Last raw response for debugging */
    private $lastresponse;

    /**
     * Constructor
     *
     * @throws \moodle_exception
     */
    public function __construct() {
        $this->apiurl = \local_issuebadge_get_api_url();
        $this->apikey = \local_issuebadge_get_api_key();

        if (empty($this->apikey)) {
            throw new \moodle_exception('error_noapi', 'local_issuebadge');
        }
    }

    /**
     * Get last raw response (for debugging).
     *
     * @return string The last raw API response.
     */
    public function get_last_response() {
        return $this->lastresponse;
    }

    /**
     * Make an API request.
     *
     * @param string $endpoint API endpoint (e.g., 'badge/getall').
     * @param string $method HTTP method (GET or POST).
     * @param array $data Request data for POST requests.
     * @return array Response data.
     * @throws \moodle_exception When API call fails.
     */
    private function request($endpoint, $method = 'GET', $data = []) {
        $url = rtrim($this->apiurl, '/') . '/' . ltrim($endpoint, '/');

        $curl = new \curl();
        $curl->setHeader('Authorization: Bearer ' . $this->apikey);
        $curl->setHeader('Content-Type: application/json');
        $curl->setHeader('Accept: application/json');

        if ($method === 'POST') {
            $response = $curl->post($url, json_encode($data));
        } else {
            $response = $curl->get($url);
        }

        // Store for debugging.
        $this->lastresponse = $response;

        // Check for cURL errors.
        if ($curl->get_errno()) {
            throw new \moodle_exception('error_api', 'local_issuebadge', '', $curl->error);
        }

        // Check HTTP status.
        $info = $curl->get_info();
        if (isset($info['http_code']) && $info['http_code'] >= 400) {
            $errmsg = 'HTTP ' . $info['http_code'] . ': ' . substr($response, 0, 200);
            throw new \moodle_exception('error_api', 'local_issuebadge', '', $errmsg);
        }

        // Decode response.
        $result = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $errmsg = 'JSON decode error: ' . json_last_error_msg() . '. Response: ' . substr($response, 0, 200);
            throw new \moodle_exception('error_invalidresponse', 'local_issuebadge', '', $errmsg);
        }

        return $result;
    }

    /**
     * Get all available badges.
     *
     * @return array List of badges.
     * @throws \moodle_exception When API call fails.
     */
    public function get_badges() {
        $result = $this->request('badge/getall', 'GET');

        // Handle different response formats.
        // Format 1: success with data array.
        if (isset($result['success']) && $result['success'] && isset($result['data'])) {
            return $result['data'];
        }

        // Format 2: data array only.
        if (isset($result['data']) && is_array($result['data'])) {
            return $result['data'];
        }

        // Format 3: badges array.
        if (isset($result['badges']) && is_array($result['badges'])) {
            return $result['badges'];
        }

        // Format 4: direct array of badges.
        if (is_array($result) && !isset($result['success']) && !isset($result['error'])) {
            // Check if it looks like a list of badges.
            if (!empty($result) && isset($result[0]) && isset($result[0]['id'])) {
                return $result;
            }
        }

        // Check for error.
        if (isset($result['success']) && !$result['success']) {
            $error = isset($result['message']) ? $result['message'] : 'Unknown API error';
            throw new \moodle_exception('error_api', 'local_issuebadge', '', $error);
        }

        // Unknown format.
        $errmsg = 'Unexpected response format: ' . json_encode($result);
        throw new \moodle_exception('error_invalidresponse', 'local_issuebadge', '', $errmsg);
    }

    /**
     * Issue a badge to a recipient.
     *
     * @param array $params Issue parameters (name, email, badge_id).
     * @return array Issue result with IssueId and publicUrl.
     * @throws \moodle_exception When API call fails or parameters are missing.
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

        $result = $this->request('issue/create', 'POST', $data);

        // Handle different response formats.
        $issueid = null;
        $publicurl = null;

        // Check various response formats for IssueId.
        if (isset($result['IssueId'])) {
            $issueid = $result['IssueId'];
        } else if (isset($result['issue_id'])) {
            $issueid = $result['issue_id'];
        } else if (isset($result['data']['IssueId'])) {
            $issueid = $result['data']['IssueId'];
        } else if (isset($result['data']['issue_id'])) {
            $issueid = $result['data']['issue_id'];
        } else if (isset($result['id'])) {
            $issueid = $result['id'];
        }

        if (isset($result['publicUrl'])) {
            $publicurl = $result['publicUrl'];
        } else if (isset($result['public_url'])) {
            $publicurl = $result['public_url'];
        } else if (isset($result['url'])) {
            $publicurl = $result['url'];
        } else if (isset($result['data']['publicUrl'])) {
            $publicurl = $result['data']['publicUrl'];
        } else if (isset($result['data']['public_url'])) {
            $publicurl = $result['data']['public_url'];
        }

        // Check for error response.
        if (isset($result['success']) && !$result['success']) {
            $error = isset($result['message']) ? $result['message'] : 'Unknown API error';
            throw new \moodle_exception('error_api', 'local_issuebadge', '', $error);
        }

        if (empty($issueid)) {
            $errmsg = 'Missing IssueId in response: ' . json_encode($result);
            throw new \moodle_exception('error_invalidresponse', 'local_issuebadge', '', $errmsg);
        }

        return [
            'IssueId' => $issueid,
            'publicUrl' => $publicurl ?? '',
        ];
    }

    /**
     * Generate a UUID for idempotency key.
     *
     * @return string UUID v4.
     */
    private function generate_uuid() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
