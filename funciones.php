<?php

// Written at Louisiana State University

require_once '../../config.php';
require_once 'lib.php';

require_login();

echo $OUTPUT->notification(get_string('changessaved'), 'notifysuccess');

$rop=$_POST['rop']; if ($rop=='')$rop=$_GET['rop'];
 switch($rop) {  
	 case "guardar_carpeta":
		 quickmail::guardar_nombre_carpeta(utf8_decode($_GET['nombre_carpeta']),$_GET['id_carpeta']);
	 break;

	 case "borrar_carpeta":
		 quickmail::borrar_carpeta($_GET['id_carpeta'],$_GET['courseid']);
	 break;
	 
	 case "mover_mensajes":
 		quickmail::mover_mensajes($_GET['usuario'],$_GET['curso'],$_GET['carpeta_destino'],$_GET['carpeta_origen'],$_GET['mensajes'] );
	 break;
 
 }
