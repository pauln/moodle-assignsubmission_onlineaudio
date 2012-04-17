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

require_once($CFG->libdir.'/formslib.php');//putting this is as a safety as i got a class not found error.
MoodleQuickForm::registerElementType('simplefile', dirname(__FILE__).'/simplefile.php', 'MoodleQuickForm_simplefile');
/**
 * @package assignsubmission_onlineaudio
 * @copyright 2012 Paul Nicholls
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_submission_onlineaudioupload_form extends moodleform {
    function simpleupload_get_errors() {
        return $this->_form->_errors;
    }
    function simpleupload_setMaxFileSize($newval) {
        return $this->_form->setMaxFileSize($newval);
    }
    function definition() {
        $mform = $this->_form;
        $instance = $this->_customdata;
        $this->simpleupload_setMaxFileSize($instance['options']['maxbytes']);

        // visible elements
        $mform->addElement('hidden', 'MAX_FILE_SIZE', $instance['options']['maxbytes']);
        $mform->addElement('simplefile', 'assignment_file', $instance['caption'], array('size'=>32));

        // hidden params
        $mform->addElement('hidden', 'sid', $instance['sid']);
        $mform->setType('userid', PARAM_INT);
        $mform->addElement('hidden', 'id', $instance['cmid']);
        $mform->setType('action', PARAM_INT);

        // buttons
        $mform->addElement('submit', 'submitbutton', get_string('upload', 'assignsubmission_onlineaudio'));
    }
}
