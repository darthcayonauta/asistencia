<?php
	header('Cache-Control: no cache');
	//session_cache_limiter('public'); // works too session_start();
	session_cache_limiter('private, must-revalidate');
	session_cache_expire(60);
	define('DURACION_SESION','7200'); //2 horas
	ini_set("session.cookie_lifetime",DURACION_SESION);
	ini_set("session.gc_maxlifetime",DURACION_SESION);
	ini_set("session.save_path","/tmp");
	session_cache_expire(DURACION_SESION);
	session_start();
	session_regenerate_id(true);

	include("class/mysqldb.class.php");
	include("class/querys.class.php");
	include("class/template.class.php");
	include("class/codifica.class.php");
	include ("class/utilesmodulo.class.php");
	//include ("class/link_modulo.class.php");
	include ("class/select.class.php");

	include("class/response.class.php");
	include("config.php");

	if( isset( $_POST['id_user'] ) )
				$id_user = $_POST['id_user'];
	else
				$id_user = null;

	$ob_response = new response($_POST['id'],$id_user);
	echo $ob_response->getCode();

?>
