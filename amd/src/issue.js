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
 * JavaScript for badge issuance
 *
 * @module     local_issuebadge/issue
 * @copyright  2025 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {

    return {
        /**
         * Initialize the badge issuance page
         *
         * @param {number} courseid Course ID
         */
        init: function(courseid) {
            // Load badges on page load.
            this.loadBadges();

            // Handle issue button click.
            $('#issue_button').on('click', function() {
                this.issueBadge(courseid);
            }.bind(this));
        },

        /**
         * Load available badges from API
         */
        loadBadges: function() {
            var badgeSelect = $('#badge_id');
            var loadingText = $('#badge_loading');

            badgeSelect.prop('disabled', true);
            loadingText.show();

            var promises = Ajax.call([{
                methodname: 'local_issuebadge_get_badges',
                args: {}
            }]);

            promises[0].done(function(response) {
                badgeSelect.empty();
                badgeSelect.append($('<option>', {
                    value: '',
                    text: M.util.get_string('selectbadge', 'local_issuebadge')
                }));

                if (response.success && response.badges && response.badges.length > 0) {
                    response.badges.forEach(function(badge) {
                        badgeSelect.append($('<option>', {
                            value: badge.id,
                            text: badge.name
                        }));
                    });
                    loadingText.hide();
                } else {
                    loadingText.text(M.util.get_string('nobadges', 'local_issuebadge'))
                        .addClass('text-danger');
                }

                badgeSelect.prop('disabled', false);

            }).fail(function(ex) {
                loadingText.text(M.util.get_string('error_api', 'local_issuebadge'))
                    .addClass('text-danger');
                Notification.exception(ex);
            });
        },

        /**
         * Issue a badge to the selected user
         *
         * @param {number} courseid Course ID
         */
        issueBadge: function(courseid) {
            var badgeid = $('#badge_id').val();
            var userid = $('#user_id').val();
            var button = $('#issue_button');
            var spinner = $('#issue_spinner');
            var result = $('#issue_result');

            // Validate inputs.
            if (!badgeid) {
                result.removeClass('d-none alert-success').addClass('alert-danger')
                    .text(M.util.get_string('error_missingdata', 'local_issuebadge'));
                return;
            }

            if (!userid) {
                result.removeClass('d-none alert-success').addClass('alert-danger')
                    .text(M.util.get_string('error_missingdata', 'local_issuebadge'));
                return;
            }

            // Show loading state.
            button.prop('disabled', true);
            spinner.removeClass('d-none');
            result.addClass('d-none');

            var promises = Ajax.call([{
                methodname: 'local_issuebadge_issue_badge',
                args: {
                    userid: parseInt(userid),
                    badgeid: badgeid,
                    courseid: courseid
                }
            }]);

            promises[0].done(function(response) {
                button.prop('disabled', false);
                spinner.addClass('d-none');

                if (response.success) {
                    result.removeClass('d-none alert-danger').addClass('alert-success')
                        .html(
                            '<strong>' + M.util.get_string('badgeissued', 'local_issuebadge') + '</strong><br>' +
                            'Issue ID: ' + response.issueid + '<br>' +
                            'URL: <a href="' + response.publicurl + '" target="_blank">' + response.publicurl + '</a>'
                        );

                    // Reset form.
                    $('#badge_id').val('');
                    $('#user_id').val('');

                    Notification.addNotification({
                        message: M.util.get_string('badgeissued', 'local_issuebadge'),
                        type: 'success'
                    });
                } else {
                    result.removeClass('d-none alert-success').addClass('alert-danger')
                        .text(M.util.get_string('error_issuefailed', 'local_issuebadge') + ': ' + response.error);
                }

            }).fail(function(ex) {
                button.prop('disabled', false);
                spinner.addClass('d-none');
                result.removeClass('d-none alert-success').addClass('alert-danger')
                    .text(M.util.get_string('error_api', 'local_issuebadge'));
                Notification.exception(ex);
            });
        }
    };
});
