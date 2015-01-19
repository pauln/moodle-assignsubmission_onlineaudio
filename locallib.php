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
 * This file contains the definition for the library class for file submission plugin
 *
 * This class provides all the functionality for the new assign module.
 *
 * @package assignsubmission_onlineaudio
 * @copyright 2012 Paul Nicholls
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** Include eventslib.php */
require_once($CFG->libdir.'/eventslib.php');

defined('MOODLE_INTERNAL') || die();
/**
 * File areas for file submission assignment
 */
define('ASSIGN_MAX_SUBMISSION_ONLINERECORDINGS', 20);
define('ASSIGN_SUBMISSION_ONLINEAUDIO_MAX_SUMMARY_FILES', 5);
define('ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO', 'submission_onlineaudio');

/*
 * library class for online audio recording submission plugin extending submission plugin base class
 *
 * @package   assignsubmission_onlineaudio
 * @copyright 2012 Paul Nicholls
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_onlineaudio extends assign_submission_plugin {

    /**
     * Get the name of the file submission plugin
     * @return string
     */
    public function get_name() {
        return get_string('recording', 'assignsubmission_onlineaudio');
    }

    /**
     * Load the submission object for a particular user, optionally creating it if required
     * I don't want to have to do this, but it's private on the assign() class, so can't be used!
     *
     * @param int $userid The id of the user whose submission we want or 0 in which case USER->id is used
     * @param bool $create optional Defaults to false. If set to true a new submission object will be created in the database
     * @return stdClass The submission
     */
    public function get_user_submission_record($userid, $create) {
        global $DB, $USER;

        if (!$userid) {
            $userid = $USER->id;
        }
        // if the userid is not null then use userid
        $submission = $DB->get_record('assign_submission', array('assignment'=>$this->assignment->get_instance()->id, 'userid'=>$userid));

        if ($submission) {
            return $submission;
        }
        if ($create) {
            $submission = new stdClass();
            $submission->assignment   = $this->assignment->get_instance()->id;
            $submission->userid       = $userid;
            $submission->timecreated = time();
            $submission->timemodified = $submission->timecreated;

            if ($this->assignment->get_instance()->submissiondrafts) {
                $submission->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
            } else {
                $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
            }
            $sid = $DB->insert_record('assign_submission', $submission);
            $submission->id = $sid;
            return $submission;
        }
        return false;
    }

    /**
     * Get file submission information from the database
     *
     * @global moodle_database $DB
     * @param int $submissionid
     * @return mixed
     */
    private function get_file_submission($submissionid) {
        global $DB;
        return $DB->get_record('assignsubmission_onlineaudio', array('submission'=>$submissionid));
    }

    /**
     * Get the default setting for file submission plugin
     *
     * @global stdClass $CFG
     * @global stdClass $COURSE
     * @param MoodleQuickForm $mform The form to add elements to
     * @return void
     */
    public function get_settings(MoodleQuickForm $mform) {
        global $CFG, $COURSE;

        $defaultmaxfilesubmissions = $this->get_config('maxfilesubmissions');
        $defaultmaxsubmissionsizebytes = $this->get_config('maxsubmissionsizebytes');
        $defaultname = $this->get_config('defaultname');
        $defaultnameoverride = $this->get_config('nameoverride');
        if ($defaultnameoverride === false) { // Fallback default for defaultname is 0 anyway, so false should suffice
            $defaultnameoverride = 1;
        }


        $settings = array();
        $options = array();
        for($i = 1; $i <= ASSIGN_MAX_SUBMISSION_ONLINERECORDINGS; $i++) {
            $options[$i] = $i;
        }

        $mform->addElement('select', 'assignsubmission_onlineaudio_maxfiles', get_string('maxfilessubmission', 'assignsubmission_onlineaudio'), $options);
        $mform->addHelpButton('assignsubmission_onlineaudio_maxfiles', 'maxfilessubmission', 'assignsubmission_onlineaudio');
        $mform->setDefault('assignsubmission_onlineaudio_maxfiles', $defaultmaxfilesubmissions);
        $mform->disabledIf('assignsubmission_onlineaudio_maxfiles', 'assignsubmission_onlineaudio_enabled', 'notchecked');

        $filenameoptions = array( 0 => get_string("nodefaultname", "assignsubmission_onlineaudio"), 1 => get_string("defaultname1", "assignsubmission_onlineaudio"), 2 =>get_string("defaultname2", "assignsubmission_onlineaudio"));
        $mform->addElement('select', 'assignsubmission_onlineaudio_defaultname', get_string("defaultname", "assignsubmission_onlineaudio"), $filenameoptions);
        $mform->addHelpButton('assignsubmission_onlineaudio_defaultname', 'defaultname', 'assignsubmission_onlineaudio');
        $mform->setDefault('assignsubmission_onlineaudio_defaultname', $defaultname);
        $mform->disabledIf('assignsubmission_onlineaudio_defaultname', 'assignsubmission_onlineaudio_enabled', 'notchecked');

        $ynoptions = array( 0 => get_string('no'), 1 => get_string('yes'));
        $mform->addElement('select', 'assignsubmission_onlineaudio_nameoverride', get_string("allownameoverride", "assignsubmission_onlineaudio"), $ynoptions);
        $mform->addHelpButton('assignsubmission_onlineaudio_nameoverride', 'allownameoverride', 'assignsubmission_onlineaudio');
        $mform->setDefault('assignsubmission_onlineaudio_nameoverride', $defaultnameoverride);
        $mform->disabledIf('assignsubmission_onlineaudio_nameoverride', 'assignsubmission_onlineaudio_enabled', 'notchecked');
        $mform->disabledIf('assignsubmission_onlineaudio_nameoverride', 'assignsubmission_onlineaudio_defaultname', 'eq', 0);
    }

    /**
     * Save the settings for file submission plugin
     *
     * @param stdClass $data
     * @return bool
     */
    public function save_settings(stdClass $data) {
        $this->set_config('maxfilesubmissions', $data->assignsubmission_onlineaudio_maxfiles);
        $this->set_config('defaultname', $data->assignsubmission_onlineaudio_defaultname);
        $this->set_config('nameoverride', $data->assignsubmission_onlineaudio_nameoverride);
        return true;
    }


    /**
     * Produces a list of links to the files uploaded by a user
     *
     * @param $userid int optional id of the user. If 0 then $USER->id is used.
     * @param $return boolean optional defaults to false. If true the list is returned rather than printed
     * @return string optional
     */
    public function print_user_files($submissionid, $allowdelete=true) {
        global $CFG, $OUTPUT, $DB;

        $strdelete = get_string('delete');
        $output = '';
        $fs = get_file_storage();
        $files = $fs->get_area_files($this->assignment->get_context()->id, 'assignsubmission_onlineaudio', ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO, $submissionid, "id", false);
        if (!empty($files)) {
            require_once($CFG->dirroot . '/mod/assign/locallib.php');
            if ($CFG->enableportfolios) {
                require_once($CFG->libdir.'/portfoliolib.php');
                $button = new portfolio_add_button();
            }
            if (!empty($CFG->enableplagiarism)) {
                require_once($CFG->libdir . '/plagiarismlib.php');
            }
            foreach ($files as $file) {
                $filename = $file->get_filename();
                $filepath = $file->get_filepath();
                $mimetype = $file->get_mimetype();
                $path = file_encode_url($CFG->wwwroot.'/pluginfile.php', '/'.$this->assignment->get_context()->id.'/assignsubmission_onlineaudio/submission_onlineaudio/'.$submissionid.'/'.$filename);
                $output .= '<span style="white-space:nowrap;"><img src="'.$OUTPUT->pix_url(file_mimetype_icon($mimetype)).'" class="icon" alt="'.$mimetype.'" />';
                // Dummy link for media filters
                $options = array(
                            'context'=>$this->assignment->get_context(),
                            'trusted'=>true,
                            'noclean'=>true
                        );
                $filtered = format_text('<a href="'.$path.'" style="display:none;"> </a> ', $format = FORMAT_HTML, $options);
                $filtered = preg_replace('~<a.+?</a>~','',$filtered);
                // Add a real link after the dummy one, so that we get a proper download link no matter what
                $output .= $filtered . '</span><a href="'.$path.'" >'.s($filename).'</a>';
                if($allowdelete) {
                    $delurl  = "$CFG->wwwroot/mod/assign/submission/onlineaudio/delete.php?id={$this->assignment->get_course_module()->id}&amp;sid={$submissionid}&amp;path=$filepath&amp;file=$filename";//&amp;userid={$submission->userid} &amp;mode=$mode&amp;offset=$offset

                    $output .= '<a href="'.$delurl.'">&nbsp;'
                              .'<img title="'.$strdelete.'" src="'.$OUTPUT->pix_url('/t/delete').'" class="iconsmall" alt="" /></a> ';
                }
                if ($CFG->enableportfolios && has_capability('mod/assign:exportownsubmission', $this->assignment->get_context())) {
                    $button->set_callback_options('assign_portfolio_caller', array('cmid' => $this->assignment->get_course_module()->id, 'sid'=>$submissionid, 'area'=>ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO), '/mod/assign/portfolio_callback.php');
                    $button->set_format_by_file($file);
                    $output .= $button->to_html(PORTFOLIO_ADD_ICON_LINK);
                }
                if (!empty($CFG->enableplagiarism)) {
                    // Wouldn't it be nice if the assignment's get_submission method wasn't private?
                    $submission = $DB->get_record('assign_submission', array('assignment'=>$this->assignment->get_instance()->id, 'id'=>$submissionid), '*', MUST_EXIST);
                    $output .= plagiarism_get_links(array('userid'=>$submission->userid, 'file'=>$file, 'cmid'=>$this->assignment->get_course_module()->id, 'course'=>$this->assignment->get_course(), 'assignment'=>$this->assignment));
                }
                $output .= '<br />';
            }
            if ($CFG->enableportfolios && count($files) > 1  && has_capability('mod/assign:exportownsubmission', $this->assignment->get_context())) {
                $button->set_callback_options('assign_portfolio_caller', array('cmid' => $this->assignment->get_course_module()->id, 'sid'=>$submissionid, 'area'=>ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO), '/mod/assign/portfolio_callback.php');
                $output .= '<br />'  . $button->to_html(PORTFOLIO_ADD_TEXT_LINK);
            }
        }

        $output = '<div class="files" style="float:left;margin-left:25px;">'.$output.'</div><br clear="all" />';

        return $output;
    }

    /**
     * Add elements to submission form
     *
     * @param mixed stdClass|null $submission
     * @param MoodleQuickForm $submission
     * @param stdClass $data
     * @return bool
     */
    public function get_form_elements($submission, MoodleQuickForm $mform, stdClass $data) {
        global $CFG, $USER;
        $submissionid = $submission ? $submission->id : 0;
        $maxfiles = $this->get_config('maxfilesubmissions');
        $defaultname = $this->get_config('defaultname');
        $allownameoverride = $this->get_config('nameoverride');
        if ($maxfiles <= 0) {
            return false;
        }
        $count = $this->count_files($submissionid, ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO);
        if($count < $maxfiles) {
            $url='submission/onlineaudio/assets/recorder.swf?gateway='.urlencode($CFG->wwwroot.'/mod/assign/submission/onlineaudio/upload.php');
            $flashvars = '&return='.urlencode($CFG->wwwroot."/mod/assign/view.php?id={$this->assignment->get_course_module()->id}&action=editsubmission");
            $flashvars .= "&filefield=assignment_file&id={$this->assignment->get_course_module()->id}&sid={$submissionid}";

            if($defaultname) {
                $field=($allownameoverride)?'filename':'forcename';
                $filename=($defaultname==2)?fullname($USER):$USER->username;
                $filename=clean_filename($filename);
                $assignname=clean_filename($this->assignment->get_instance()->name);
                $coursename=clean_filename($this->assignment->get_course()->shortname);
                $filename.='_-_'.substr($assignname,0,20).'_-_'.$coursename.'_-_'.date('Y-m-d');
                $filename=str_replace(' ', '_', $filename);
                $flashvars .= "&$field=$filename";
            }

            $html = '<script type="text/javascript" src="submission/onlineaudio/assets/swfobject.js"></script>
                <script type="text/javascript">
                swfobject.registerObject("onlineaudiorecorder", "10.1.0", "submission/onlineaudio/assets/expressInstall.swf");
                </script>';

            $html .= '<div id="onlineaudiorecordersection" style="float:left">
                <object id="onlineaudiorecorder" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="215" height="138">
                        <param name="movie" value="'.$url.$flashvars.'" />
                        <!--[if !IE]>-->
                        <object type="application/x-shockwave-flash" data="'.$url.$flashvars.'" width="215" height="138">
                        <!--<![endif]-->
                        <div>
                                <p><a href="http://www.adobe.com/go/getflashplayer"><img src="//www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
                        </div>
                        <!--[if !IE]>-->
                        </object>
                        <!--<![endif]-->
                </object></div>';
            $mform->addElement('html', $html);
        } else {
            $mform->addElement('html', '<p>'.get_string('maxfilesreached', 'assignsubmission_onlineaudio').'</p>');
        }
        $mform->addElement('html', $this->print_user_files($submissionid));

        return true;
    }

    /**
     * Count the number of files
     *
     * @param int $submissionid
     * @param string $area
     * @return int
     */
    private function count_files($submissionid, $area) {

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->assignment->get_context()->id, 'assignsubmission_onlineaudio', $area, $submissionid, "id", false);

        return count($files);
    }

    /**
     * Save the uploaded recording and trigger plagiarism plugin, if enabled, to scan the uploaded files via events trigger
     *
     * @global stdClass $USER
     * @global moodle_database $DB
     * @param stdClass $submission
     * @param stdClass $file
     * @return bool
     */
    public function add_recording(stdClass $submission) {
        global $USER, $DB;

        $fs = get_file_storage();
        $filesubmission = $this->get_file_submission($submission->id);

        // Process uploaded file
        if (!array_key_exists('assignment_file', $_FILES)) {
            return false;
        }
        $filedetails = $_FILES['assignment_file'];

        $filename = $filedetails['name'];
        $filesrc = $filedetails['tmp_name'];

        if (!is_uploaded_file($filesrc)) {
            return false;
        }

        $ext = substr(strrchr($filename, '.'), 1);
        if (!preg_match('/^(mp3|wav|wma)$/i',$ext)) {
            return false;
        }

        $temp_name=basename($filename,".$ext"); // We want to clean the file's base name only
        // Run param_clean here with PARAM_FILE so that we end up with a name that other parts of Moodle
        // (download script, deletion, etc) will handle properly.  Remove leading/trailing dots too.
        $temp_name=trim(clean_param($temp_name, PARAM_FILE),".");
        $filename=$temp_name.".$ext";
        // check for filename already existing and add suffix #.
        $n=1;
        while($fs->file_exists($this->assignment->get_context()->id, 'assignsubmission_onlineaudio', ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO, $submission->id, '/', $filename)) {
            $filename=$temp_name.'_'.$n++.".$ext";
        }

        // Create file
        $fileinfo = array(
              'contextid' => $this->assignment->get_context()->id,
              'component' => 'assignsubmission_onlineaudio',
              'filearea' => ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO,
              'itemid' => $submission->id,
              'filepath' => '/',
              'filename' => $filename
              );
        if ($newfile = $fs->create_file_from_pathname($fileinfo, $filesrc)) {
            $files = $fs->get_area_files($this->assignment->get_context()->id, 'assignsubmission_onlineaudio', ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO, $submission->id, "id", false);
            $count = $this->count_files($submission->id, ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO);
            // send files to event system
            // Let Moodle know that an assessable file was uploaded (eg for plagiarism detection)
            $eventdata = new stdClass();
            $eventdata->modulename = 'assign';
            $eventdata->cmid = $this->assignment->get_course_module()->id;
            $eventdata->itemid = $submission->id;
            $eventdata->courseid = $this->assignment->get_course()->id;
            $eventdata->userid = $USER->id;
            if ($count > 1) {
                $eventdata->files = $files;
            }
                $eventdata->file = $files;
            events_trigger('assessable_file_uploaded', $eventdata);


            if ($filesubmission) {
                $filesubmission->numfiles = $this->count_files($submission->id, ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO);
                return $DB->update_record('assignsubmission_onlineaudio', $filesubmission);
            } else {
                $filesubmission = new stdClass();
                $filesubmission->numfiles = $this->count_files($submission->id, ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO);
                $filesubmission->submission = $submission->id;
                $filesubmission->assignment = $this->assignment->get_instance()->id;
                return $DB->insert_record('assignsubmission_onlineaudio', $filesubmission) > 0;
            }
        }
    }

    /**
     * Produce a list of files suitable for export that represent this feedback or submission
     *
     * @param stdClass $submission The submission
     * @return array - return an array of files indexed by filename
     */
    public function get_files(stdClass $submission, stdClass $user=null) {
        $result = array();
        $fs = get_file_storage();

        $files = $fs->get_area_files($this->assignment->get_context()->id, 'assignsubmission_onlineaudio', ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO, $submission->id, "timemodified", false);

        foreach ($files as $file) {
            $result[$file->get_filename()] = $file;
        }
        return $result;
    }

    /**
     * Display the list of files  in the submission status table
     *
     * @param stdClass $submission
     * @return string
     */
    public function view_summary(stdClass $submission, & $showviewlink) {
        $count = $this->count_files($submission->id, ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO);

        // show we show a link to view all files for this plugin?
        $showviewlink = $count > ASSIGN_SUBMISSION_ONLINEAUDIO_MAX_SUMMARY_FILES;
        if ($count <= ASSIGN_SUBMISSION_ONLINEAUDIO_MAX_SUMMARY_FILES) {
            return $this->print_user_files($submission->id, false);
        } else {
            return get_string('countfiles', 'assignsubmission_onlineaudio', $count);
        }
    }

    /**
     * No full submission view - the summary contains the list of files and that is the whole submission
     *
     * @param stdClass $submission
     * @return string
     */
    public function view(stdClass $submission) {
        return $this->assignment->render_area_files('assignsubmission_onlineaudio', ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO, $submission->id);
    }



    /**
     * Return true if this plugin can upgrade an old Moodle 2.2 assignment of this type
     * and version.
     *
     * @param string $type
     * @param int $version
     * @return bool True if upgrade is possible
     */
    public function can_upgrade($type, $version) {
        if ($type == 'onlineaudio') {
            return true;
        }
        return false;
    }


    /**
     * Upgrade the settings from the old assignment
     * to the new plugin based one
     *
     * @param context $oldcontext - the old assignment context
     * @param stdClass $oldassignment - the old assignment data record
     * @param string log record log events here
     * @return bool Was it a success? (false will trigger rollback)
     */
    public function upgrade_settings(context $oldcontext,stdClass $oldassignment, & $log) {
        // Old assignment plugin ran out of vars so couldn't do max files, just default to module max
        $this->set_config('maxfilesubmissions', ASSIGN_MAX_SUBMISSION_ONLINERECORDINGS);
        $this->set_config('defaultname', $oldassignment->var2);
        $this->set_config('nameoverride', $oldassignment->var3);
        return true;
    }

    /**
     * Upgrade the submission from the old assignment to the new one
     *
     * @global moodle_database $DB
     * @param context $oldcontext The context of the old assignment
     * @param stdClass $oldassignment The data record for the old oldassignment
     * @param stdClass $oldsubmission The data record for the old submission
     * @param stdClass $submission The data record for the new submission
     * @param string $log Record upgrade messages in the log
     * @return bool true or false - false will trigger a rollback
     */
    public function upgrade(context $oldcontext, stdClass $oldassignment, stdClass $oldsubmission, stdClass $submission, & $log) {
        global $DB;

        $filesubmission = new stdClass();

        $filesubmission->numfiles = $oldsubmission->numfiles;
        $filesubmission->submission = $submission->id;
        $filesubmission->assignment = $this->assignment->get_instance()->id;

        if (!$DB->insert_record('assignsubmission_onlineaudio', $filesubmission) > 0) {
            $log .= get_string('couldnotconvertsubmission', 'mod_assign', $submission->userid);
            return false;
        }

        // now copy the area files
        $this->assignment->copy_area_files_for_upgrade($oldcontext->id,
                                                        'mod_assignment',
                                                        'submission',
                                                        $oldsubmission->id,
                                                        // New file area
                                                        $this->assignment->get_context()->id,
                                                        'assignsubmission_onlineaudio',
                                                        ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO,
                                                        $submission->id);

        return true;
    }

    /**
     * The assignment has been deleted - cleanup
     *
     * @global moodle_database $DB
     * @return bool
     */
    public function delete_instance() {
        global $DB;
        // will throw exception on failure
        $DB->delete_records('assignsubmission_onlineaudio', array('assignment'=>$this->assignment->get_instance()->id));

        return true;
    }

    /**
     * Formatting for log info
     *
     * @param stdClass $submission The submission
     *
     * @return string
     */
    public function format_for_log(stdClass $submission) {
        // format the info for each submission plugin add_to_log
        $filecount = $this->count_files($submission->id, ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO);
        $fileloginfo = '';
        $fileloginfo .= ' the number of file(s) : ' . $filecount . " file(s).<br>";

        return $fileloginfo;
    }

    /**
     * Return true if there are no submission files
     */
    public function is_empty(stdClass $submission) {
        return $this->count_files($submission->id, ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO) == 0;
    }

    /**
     * Get file areas returns a list of areas this plugin stores files
     * @return array - An array of fileareas (keys) and descriptions (values)
     */
    public function get_file_areas() {
        return array(ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO=>$this->get_name());
    }

}
