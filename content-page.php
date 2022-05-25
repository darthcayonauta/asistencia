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
    include("class/menu.class.php");
    include("class/codifica.class.php");
    include("class/content-page.class.php");
    include("class/select.class.php");
    include("class/utilesmodulo.class.php");
    include("config.php");

    if( $_SESSION['autenticado'] == 1 )
    {
        $ob = new ContentPage( base64_decode(  $_GET['id']  ) );
        echo $ob->getCode();
    }else{

        header('location:index.php?ix=error_auth');
        exit();
    }
?>
