<?php
// Modified by Barbara Vergara www.fecyl.com
// Written at Louisiana State University

require_once($CFG->dirroot . '/blocks/quickmail/lib.php');

class block_quickmail extends block_list {
    function init() {
     //   $this->title = quickmail::_s('pluginname');
  	 $this->title = 'Quick-Argo';
    }

    function applicable_formats() {
        return array('site' => false, 'my' => false, 'course-view' => true);
    }

    function get_content() {
        global $CFG, $COURSE, $OUTPUT,$USER, $DB;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);

        $config = quickmail::load_config($COURSE->id);
        $permission = has_capability('block/quickmail:cansend', $context);

        $can_send = ($permission or !empty($config['allowstudents']));

        $icon_class = array('class' => 'icon');

        if ($can_send) {
            $cparam = array('courseid' => $COURSE->id);

            $send_email_str = quickmail::_s('composenew');
            $send_email = html_writer::link(
                new moodle_url('/blocks/quickmail/email.php', $cparam),
                $send_email_str
            );
            $this->content->items[] = $send_email;
            $this->content->icons[] = $OUTPUT->pix_icon('i/email', $send_email_str, 'moodle', $icon_class);

            $signature_str = quickmail::_s('signature');
            $signature = html_writer::link(
                new moodle_url('/blocks/quickmail/signature.php', $cparam),
                $signature_str
            );
            $this->content->items[] = $signature;
            $this->content->icons[] = $OUTPUT->pix_icon('i/edit', $signature_str, 'moodle', $icon_class);

		 
            $recibidos_params = $cparam + array('action'=>'received','typeid'=>$USER->id,'type' =>'received');
            $recibidos_email_str = quickmail::_s('received');
            $recibidos = html_writer::link(
                new moodle_url('/blocks/quickmail/emaillog.php', $recibidos_params),
                $recibidos_email_str
            );
            $this->content->items[] = $recibidos;
            $this->content->icons[] = $OUTPUT->pix_icon('i/settings', $recibidos_email_str, 'moodle', $icon_class);
			
		    $otras= $DB->get_records('block_quickmail_folders', array ('id_curso'=>$COURSE->id, 'id_alumno'=>$USER->id), 'nombre ASC', 'nombre,id');
			 foreach ($otras as $carpeta) 
			  {
			   $ruta_otras_capertas=new moodle_url('/blocks/quickmail/emaillog.php', array('courseid' => $COURSE->id, 'action'=>'otrasCapetas','typeid'=>$USER->id,'id_folder' =>$carpeta->id, 'type'=>'folders_msg'));
		 
			     $this->content->items[] = html_writer::link($ruta_otras_capertas, $carpeta->nombre);
				$this->content->icons[] = $OUTPUT->pix_icon('i/settings', $carpeta->nombre, 'moodle', $icon_class);

			   }
		    $draft_params = $cparam + array('type' => 'drafts');
            $drafts_email_str = quickmail::_s('drafts');
            $drafts = html_writer::link(
                new moodle_url('/blocks/quickmail/emaillog.php', $draft_params),
                $drafts_email_str
            );
            $this->content->items[] = $drafts;
            $this->content->icons[] = $OUTPUT->pix_icon('i/settings', $drafts_email_str, 'moodle', $icon_class);

            $history_str = quickmail::_s('history');
            $history = html_writer::link(
                new moodle_url('/blocks/quickmail/emaillog.php', $cparam),
                $history_str
            );
            $this->content->items[] = $history;
            $this->content->icons[] = $OUTPUT->pix_icon('i/settings', $history_str, 'moodle', $icon_class);
        }

        if (has_capability('block/quickmail:allowalternate', $context)) {
            $alt_str = quickmail::_s('alternate');
            $alt = html_writer::link(
                new moodle_url('/blocks/quickmail/alternate.php', $cparam),
                $alt_str
            );

            $this->content->items[] = $alt;
            $this->content->icons[] = $OUTPUT->pix_icon('i/edit', $alt_str, 'moodle', $icon_class);
        }

        if (has_capability('block/quickmail:canconfig', $context)) {
            $config_str = quickmail::_s('config');
            $config = html_writer::link(
                new moodle_url('/blocks/quickmail/config.php', $cparam),
                $config_str
            );
            $this->content->items[] = $config;
            $this->content->icons[] = $OUTPUT->pix_icon('i/settings', $config_str, 'moodle', $icon_class);
        }

        return $this->content;
    }
}