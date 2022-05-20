<?php

/**
* archivo de pruebas de conexion a base de datos
*/
class Index
{
	private $id;
	private $consultas;
	private $template;
	private $ruta;
	private $yo;

	function __construct( $id = null, $yo = null  )
	{
		# invocar archivo de configuracion

		$oConf    = new config();
	    $cfg      = $oConf->getConfig();
	    $db       = new mysqldb( 	$cfg['base']['dbhost'],
																$cfg['base']['dbuser'],
																$cfg['base']['dbpass'],
																$cfg['base']['dbdata'] );

	    $this->consultas = new querys( $db );
	    $this->template  = new template();
	    $this->ruta      = $cfg['base']['template'];
	    $this->id        = $id;
	    $this->yo 		 = $yo;
	}

		private function control()
		{
			$notificacion = null;

			if( $_GET['ix']  ) $notificacion = "<p class='h4'><i class='fas fa-exclamation-triangle'></i> Error :
																	 								<strong>Credenciales Incorrectas</strong>
															 						</p>" ;

			$data = ['@@@TITLE' 		  => "Sistema de Asistencia" ,
					 '###notificacion###' => $notificacion];
			return $this::despliegueTemplate( $data , 'index.html' );
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

	public function getCode(){

		return $this::control();
	}

}

?>
