<?php

defined('MOODLE_INTERNAL') || die();

function xmldb_local_filescan_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2017062109) {

        // Define field id to be added to local_filescan_files.
        $table = new xmldb_table('local_filescan_files');
        $table->add_field('checked', XMLDB_TYPE_BINARY, null, XMLDB_NOTNULL, null, null);



        // Filescan savepoint reached.
        upgrade_plugin_savepoint(true, 2017062109, 'local', 'filescan');
    }


    return true;
}