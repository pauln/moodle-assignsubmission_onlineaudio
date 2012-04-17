<?php  // $Id: upload.php,v 1.26 2006/08/08 22:09:56 skodak Exp $

require_once("../../../../config.php");
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once('./locallib.php');
require_once('./simpleupload_form.php');

$id = required_param('id', PARAM_INT);  // Course module ID
$sid = required_param('sid', PARAM_INT);  // Submission ID


$url = new moodle_url('/mod/assign/submission/onlineaudio/delete.php');
$viewurl = new moodle_url('/mod/assign/view.php');

$cm = get_coursemodule_from_id('assign', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

// Auth
require_login($course, true, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/assign:view', $context);
   
$assignment = new assignment($context,$cm,$course);

$url->param('id', $id);
$viewurl->param('id', $id);
$viewurl->param('action', 'editsubmission');

$PAGE->set_url($url);

$submission_plugin = $assignment->get_submission_plugin_by_type('onlineaudio');
$submission = $DB->get_record('assign_submission', array('assignment'=>$assignment->get_instance()->id, 'id'=>$sid), '*', MUST_EXIST);


$mform = new mod_assign_submission_onlineaudioupload_form();
if (!$mform->is_cancelled() && $formdata = $mform->get_data()) {
    if($submission_plugin->add_recording($submission, $formdata)) {
        redirect($viewurl);
    } else {
        redirect($viewurl, get_string('uploaderror', 'assignsubmission_onlineaudio'), 10);
    }
} else {
    redirect($viewurl);
}