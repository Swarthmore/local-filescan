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
 * Download all instructor files from a given course.
 *
 * @package   local_instructor_files
 * @copyright 2017 Lafayette College ITS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');

$id     = required_param('id', PARAM_INT);
$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

// Force login.
require_login($course);

// Check permissions.
$coursecontext = context_course::instance($course->id);
require_capability('local/filescan:scan', $coursecontext);

// Get files.
$results = local_filescan_helper::get_files($course->id, $coursecontext->id);
//if ($zipfile !== false) {
//    $filename = clean_filename($course->shortname .'.zip');
//    send_temp_file($zipfile, $filename);
//}
print($results);
$returnurl = new moodle_url('/course/view.php', array('id' => $course->id));
print_error('nofiles', 'local_filescan', $returnurl);


function has_config() {return true;}