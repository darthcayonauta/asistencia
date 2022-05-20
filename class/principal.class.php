<?php

class Principal
{
	private $consultas;
	private $template;
	private $ruta;
	private $sesion;
	private $id;
	private $menu;
	private $contenido_sesion;
	private $usuario;
	private $clave;
	private $id_user;
	private $id_tipo_usuario;
	private $fecha_hoy;
	private $yo;
	private $apateno;
	private $mpateno;
	private $nombres;
	private $tipo_usuario;


	function __construct( $id = null,$yo = null )
	{

		if(is_null($yo))
			$this->yo = $_SESSION['yo'];
		else {
			$this->yo = $yo;
		}

		$oConf    = new config();
	  	$cfg      = $oConf->getConfig();
	  	$db       = new mysqldb( $cfg['base']['dbhost'],
								 $cfg['base']['dbuser'],
								 $cfg['base']['dbpass'],
								 $cfg['base']['dbdata'] );

   		$this->consultas 		= 	new querys( $db );
   		$this->template  		= 	new template();
   		$this->ruta      		= 	$cfg['base']['template'];
		$this->id 				= 	$id;
		$this->error 			= 	$cfg['base']['error'];
		$this->fecha_hoy 		=  date("Y-m-d");
		$this->fecha_hora_hoy	=  date("Y-m-d H:i:s");
		$this->tipo_user 		=  $_SESSION['tipo_user'];
		$this->apaterno 		=  utf8_decode(  $_SESSION['apaterno'] );
		$this->amaterno 		=  $_SESSION['amaterno'];
		$this->nombres 			=  $_SESSION['nombres'];		
		$this->menu  			=  new Menu( $this->yo , $this->tipo_user  );
	}

	private function control()
	{

		switch ($this->id)
		{
			case 'logged':

				return $this::logged();
				break;

			default:
					return $this->error;
			break;
		}
	}

	private function logged()
	{
			$data = array(  '@@@TITLE'  	=> 'Sistema de Asistencia',
							'@@@USER' 		=> utf8_encode( "{$this->nombres} {$this->apaterno} {$this->amaterno}"),
							'@@@FECHA' 		=> $this::arreglaFechas(  $this->fecha_hoy ),
							'@@@CONTENT'	=> $this::content(),
							//'@@@MENU'		=> null );
							'@@@MENU'		=> $this->menu->getCode() );

			return $this::despliegueTemplate($data,'inicio-principal.html');
	}

	private function content()
	{

	  if( $this->tipo_user == 2 )
	  {	
			try {
					require_once( 'asistencia.class.php' );
					$ob = new Asistencia( 'inicio' ); return $ob->getCode();
				} 	catch (\Throwable $th) {
					return "Error de clase {$th}";
				}		
		}else{
			try {
				require_once( 'asistencia-admin.class.php' );
				$ob = new AsistenciaAdmin( 'inicio' ); return $ob->getCode();
			} 	catch (\Throwable $th) {
				return "Error de clase {$th}";
			}

		}
	}


	/**
	 * arreglaFechas()
	 * @param string fecha
	 * @return string
	 */
	private function arreglaFechas( $fecha = null )
	{
			$div = $this::separa( $fecha , '-'  );

			if( count( $div ) > 0 )
					return "{$div[2]}-{$div[1]}-{$div[0]}";
			else return "Error de Formato";
	}


	private function separa($cadena=null,$simbolo=null)
	{
		if( is_null($cadena) )
			return "";
		else
			return explode($simbolo,$cadena);
	}

	 /**
	  * despliegueTemplate(), metodo que sirve para procesar los templates
	  *
	  * @param  array   arrayData (array de datos)
	  * @param  array   tpl ( template )
	  * @return String
	  */
    private function despliegueTemplate($arrayData,$tpl){

     	  $tpl = $this->ruta.$tpl;

	      $this->template->setTemplate($tpl);
	      $this->template->llena($arrayData);

	      return $this->template->getCode();
	  }

	public function getCode()
	{

		return $this::control();
	}
}
?>
