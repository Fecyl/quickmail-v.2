<?php
// Modified by Barbara Vergara www.fecyl.com
// Written at Louisiana State University

require_once '../../config.php';
require_once 'lib.php';
require_once 'config_form.php';


require_login();

$courseid = required_param('courseid', PARAM_INT);
$reset = optional_param('reset', 0, PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('no_course', 'block_quickmail', '', $courseid);
}

$context= get_context_instance(CONTEXT_COURSE, $courseid);

require_capability('block/quickmail:canconfig', $context);

//$blockname = quickmail::_s('pluginname');
$blockname = 'Quick-Argo';
$header = quickmail::_s('config');

$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_url('/blocks/quickmail/config.php', array('courseid' => $courseid));
$PAGE->set_title($blockname . ': '. $header);
$PAGE->set_heading($blockname. ': '. $header);
$PAGE->navbar->add($header);
$PAGE->set_pagetype($blockname);
$PAGE->requires->js('/blocks/quickmail/js/aad.js');

$changed = false;

if ($reset) {
    $changed = true;
    quickmail::default_config($courseid);
}

$roles = $DB->get_records_menu('role', null, 'sortorder ASC', 'id, name');
$form = new config_form(null, array(
    'courseid' => $courseid,
    'roles' => $roles
));

if ($data = $form->get_data()) {
    $config = get_object_vars($data);
   global $USER;

    unset($config['save'], $config['courseid']);

    $config['roleselection'] = implode(',', $config['roleselection']);

    quickmail::save_config($courseid, $config);

	$folder=array('nombre'=>$config['carpeta'],'id_curso'=>$courseid,'id_alumno'=>$USER->id,'creado'=>date('Y-m-d'));
	if (!quickmail::existe_carpeta($folder['id_curso'], $folder['id_alumno'], $folder['nombre'])){
		$uno=$DB->insert_record_raw('block_quickmail_folders',$folder);
	 }
			
    $changed = true;
}

$config = quickmail::load_config($courseid);
$config['roleselection'] = explode(',', $config['roleselection']);

$form->set_data($config);

echo $OUTPUT->header();
//echo $OUTPUT->heading($header);
echo $OUTPUT->heading('Quick-Argo');
echo $OUTPUT->box_start();

if ($changed) {
    echo $OUTPUT->notification(get_string('changessaved'), 'notifysuccess');
}

$form->display();
echo $OUTPUT->box_end();

echo $OUTPUT->footer();
