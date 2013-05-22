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
 * Strings for component 'assignsubmission_onlineaudio', language 'en'
 *
 * @package assignsubmission_onlineaudio
 * @copyright 2012 Paul Nicholls
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$string['pluginname'] = 'Online audio recording';
$string['recording'] = 'Online audio recording';

$string['enabled'] = 'Online audio recording';
$string['enabled_help'] = 'If enabled, students are able to make audio recordings as their submission.';

$string['configmaxbytes'] = 'Maximum file size';
$string['maxbytes'] = 'Maximum file size';

$string['maxfilessubmission'] = 'Maximum number of recordings';
$string['maxfilessubmission_help'] = 'If online audio recordings are enabled, each student will be able to submit up to this number of recordings.';

$string['defaultname'] = 'Default filename pattern';
$string['defaultname_help'] = 'This option can be used to pre-fill the filename based on a pattern.  The pre-filled filename can be enforced by setting "Allow students to change filename" to "No".';
$string['nodefaultname'] = 'None (blank)';
$string['defaultname1'] = 'username_assignment_course_date';
$string['defaultname2'] = 'fullname_assignment_course_date';

$string['allownameoverride'] = 'Allow students to change filename';
$string['allownameoverride_help'] = 'If enabled, students can override the default file name with one of their own choosing.  This option has no effect if the "Default file name pattern" is set to "None (blank)" as a name must be specified.';

$string['countfiles'] = '{$a} files';
$string['nosuchfile'] = 'No such file was found.';
$string['confirmdeletefile'] = 'Are you sure you want to delete the file <strong>{$a}</strong>?';
$string['upload'] = 'Upload';
$string['uploaderror'] = 'Error uploading recording.';
$string['maxfilesreached'] = 'You already have the maximum number of recordings allowed for this assignment.';