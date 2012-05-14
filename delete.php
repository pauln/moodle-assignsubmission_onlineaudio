<?php

require_once('../../../../config.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');
require_once('./locallib.php');

$id = required_param('id', PARAM_INT);  // Course module ID
$sid = required_param('sid', PARAM_INT);  // Submission ID
$delfile = required_param('file', PARAM_FILE);
$delpath = required_param('path', PARAM_PATH);
$confirm = optional_param('confirm', 0, PARAM_INT);

$url = new moodle_url('/mod/assign/submission/onlineaudio/delete.php');
$viewurl = new moodle_url('/mod/assign/view.php');

$cm = get_coursemodule_from_id('assign', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

// Auth
require_login($course, true, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/assign:view', $context);
   
$assignment = new assign($context,$cm,$course);

$url->param('id', $id);
$viewurl->param('id', $id);
$viewurl->param('action', 'editsubmission');

$PAGE->set_url($url);
$PAGE->set_heading($assignment->get_course_module()->name);

$submission_plugin = $assignment->get_submission_plugin_by_type('onlineaudio');
$submission = $DB->get_record('assign_submission', array('assignment'=>$assignment->get_instance()->id, 'id'=>$sid), '*', MUST_EXIST);

if ($submission->userid != $USER->id) {
    require_capability('mod/assign:grade', $assignment->get_context());
}

$files = $submission_plugin->get_files($submission);

$found = false;
foreach($files as $filename => $file) {
    if($filename==$delfile && $file->get_filepath()==$delpath) {
        $found = true;
        break;
    }
}

if(!$found) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('nosuchfile', 'assignsubmission_onlineaudio'));
    echo $OUTPUT->continue_button($viewurl);
} else {
    if($confirm) {
        $files[$delfile]->delete();
        redirect($viewurl);
    } else {
        $confirmurl = new moodle_url('/mod/assign/submission/onlineaudio/delete.php');
        $confirmurl->param('id', $id);
        $confirmurl->param('sid', $sid);
        $confirmurl->param('file', $delfile);
        $confirmurl->param('path', $delpath);
        $confirmurl->param('confirm', 1);

        echo $OUTPUT->header();
        echo $OUTPUT->confirm(get_string('confirmdeletefile', 'assignsubmission_onlineaudio', $delpath.$delfile), $confirmurl, $viewurl);
    }
}