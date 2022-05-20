<?php
	
	/**
	* @author  Claudio Guzman Herrera
	* @package celulares
	* @version 1.0
	*/
	class sesion
	{
		
		/**
		 * @var Boolean argumento
		 */
		private $argumento;
		private $id_user;
		private $sesionVars;
		private $fecha_hoy;
		private $consultas;
		
		function __construct($argumento=null,$id_user=null)
		{

		$this->fecha_hoy = date("Y-m-d H:i:s");		
		$oConf    = new config();
	    $cfg      = $oConf->getConfig();
	    $db       = new mysqldb( $cfg['base']['dbhost'], $cfg['base']['dbuser'], $cfg['base']['dbpass'], $cfg['base']['dbdata'] );

		$this->consultas = new querys( $db );

			# code... necesito determinar el tipo de usuario

		if(!is_null($argumento))		
			$this->argumento = $argumento;
        else
        	$this->argumento ="";

			$this->id_user = $id_user;

		}

		/**
		 * capturaIP(): captura de ip remota
		 * @return String
		 */
		private function capturaIP()
		{

			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			    
			    $ip = $_SERVER['HTTP_CLIENT_IP'];

			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

			    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

			} else {

			    $ip = $_SERVER['REMOTE_ADDR'];
			}

			return $ip;
		}

		/**
		 * generaSesion(), generacion de sesiones en php  
		 * @return void
		 */
		public function generaSesion()
		{
			
			if( $this->argumento )
			{
				
				session_start();

				$_SESSION['usuario'] 		 	 = $_POST['strEmail'];
				$_SESSION['clave'] 			 	 = $_POST['strPassword'];
				$_SESSION['id_user'] 	     	 = $this->id_user;
				$_SESSION['MM_UsuarioRedSocial'] = $this->id_user;
				$_SESSION['autenticado'] 	 	 = 1;
				$ip 							 = $this->capturaIP();
				$id_sesion						 = session_id();
				
				//if( $this->consultas->ingresaAcceso($_SESSION['id_user'],$id_sesion,$this->fecha_hoy,$ip) )
			//		$ac = true;
			//	else
			//		$ac = false;

			    header("location:inicio.php");

				//print_r($_SESSION);

			}else{
				header("location:index.php?id=error");
		
				print_r($_SESSION);
			}
		

		}

		/**
		 * seguridad(), funcion que verifica acceso a recursos dentro de la sesion		
		 * @return void
		 */
		public function seguridad()
		{

			session_start();
			print_r($_SESSION);


			/*
			session_start();
	
			if( $_SESSION['autenticado'] != 1 )
			{

			header("location:index.php?id=no_auth");
			exit();
			}
			*/

			
	
			/*
			if( !isset($_SESSION['MM_UsuarioRedSocial']) )
			{

			header("location:index.php?id=no_auth");
			exit();
			}
			*/

			//header("location:inicio.php");

		}

		/**
		 * @return void
		 */
		public function sesionDestroy()
		{

			session_start();
			session_destroy();

			header("location:index.php");	
		}
	}
?>