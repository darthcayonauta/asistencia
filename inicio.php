<?php
  //print_r( $_POST );

  	include("class/mysqldb.class.php");
	include("class/querys.class.php");
	include("class/template.class.php");
	include("class/codifica.class.php");
	include("class/myIp.class.php");
	include("class/menu.class.php");
	include("class/principal.class.php");
	include("class/select.class.php");	
	include("class/seguridad.class.php");
	include("class/utilesmodulo.class.php");

	include("config.php");
	#1. invoca seguridad en argumentos del formulario

	function seguridad($arg){

		$seguridad = new seguridad($arg);
		return $seguridad->getCode();
	}

	function validaEmail( $email_a = null )
	{
		if (filter_var($email_a, FILTER_VALIDATE_EMAIL)) {

			return true;

		}else return true;
  }


    # 2. invoca consultas y aplica seguridad
  	$query = new querys();

      $i = 0;

  	$apaterno 	 = "";
  	$amaterno 	 = "";
    $nombres  			 = "";
  	$id  	  			 = "";
  	$rut  	  			 = "";
  	$tipo_user        = "";
    $email               = "";

    if( $_POST['email'] =='' || $_POST['clave'] =='')
    {
      header('location:index.php?ix=no_session');
    exit();

    }elseif ( !validaEmail( $_POST['email'] ) ) {

      header('location:index.php?ix=no_session');
    exit();

    }else{

    $arr = $query->listaUsuarios(  seguridad( $_POST['email'] ) , $_POST['clave'] );

    foreach ($arr['process'] as $key => $value)
    {
      $apaterno 	.= $value['apaterno'];
      $amaterno 	.= $value['amaterno'];
      $nombres  	.= $value['nombres'];
      $id  	  		.= $value['id'];
      $rut  	  	.= $value['rut'];
      $tipo_user    .= $value['tipo_user'];
      $email        .= $value['email'];

      $i++;
    }

	if($i>0) 	$ingresa = true;
	else 		$ingresa = false;

  if( $ingresa)
	{

		header('Cache-Control: no cache');
		//session_cache_limiter('public'); // works too session_start();
		session_cache_limiter('private, must-revalidate');
		session_cache_expire(60);

		define('DURACION_SESION','7200'); //2 horas
		ini_set("session.cookie_lifetime",DURACION_SESION);
		ini_set("session.gc_maxlifetime",DURACION_SESION);
		ini_set("session.save_path","/tmp");
		session_cache_expire(DURACION_SESION);


		@session_start();
		@session_regenerate_id(true);


		$_SESSION['autenticado']	= 1;
		$_SESSION['yo']				= $id;
    	$_SESSION['rut']			= $rut;
		$_SESSION['nombres'] 		= $nombres;
		$_SESSION['apaterno'] 		= $apaterno;
		$_SESSION['amaterno'] 		= $amaterno;
		$_SESSION['tipo_user'] 		= $tipo_user;
    	$_SESSION['email'] 	    	= $email;

		//ingresa acceso

		$ip 		= new MyIp();
		$obQuery 	= new querys();

		if( $obQuery->ingresaAccesos( $_SESSION['yo'], session_id(), $ip->getCode() ) )
		{ $ok = true;  }else{ $ok = true; }

		if( $_SESSION['autenticado'] == 1 )
			{
				$obj_principal = new Principal('logged',$id);
				echo $obj_principal->getCode();

			}else{

				print_r( $_SESSION );
			}

	}else{
       //echo $arr['sql'];
			header('location:index.php?ix=error_auth');
			exit();
			}
    }
?>
