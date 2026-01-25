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
 * Upgrade script for IssueBadge plugin
 *
 * @package    local_issuebadge
 * @copyright  2026 IssueBadge
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute upgrade tasks
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_issuebadge_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025012400) {
        // Define table local_issuebadge_badges to be created.
        $table = new xmldb_table('local_issuebadge_badges');

        // Adding fields.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('badge_id', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('image_url', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes.
        $table->add_index('badge_id', XMLDB_INDEX_UNIQUE, ['badge_id']);

        // Create table if not exists.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Add new fields to local_issuebadge_course.
        $table = new xmldb_table('local_issuebadge_course');

        // Add trigger_type field.
        $field = new xmldb_field('trigger_type', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, 'manual', 'badge_id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add quiz_id field.
        $field = new xmldb_field('quiz_id', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'trigger_type');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add passing_grade field.
        $field = new xmldb_field('passing_grade', XMLDB_TYPE_INTEGER, '3', null, null, null, '70', 'quiz_id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add new fields to local_issuebadge_issues for external recipients.
        $table = new xmldb_table('local_issuebadge_issues');

        // Add recipient_name field.
        $field = new xmldb_field('recipient_name', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'userid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add recipient_email field.
        $field = new xmldb_field('recipient_email', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'recipient_name');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Make userid nullable (allow 0 for external recipients).
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $dbman->change_field_notnull($table, $field);

        upgrade_plugin_savepoint(true, 2025012400, 'local', 'issuebadge');
    }

    return true;
}
