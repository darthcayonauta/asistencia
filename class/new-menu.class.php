<?php

/**
 * 
 */
class NewMenu
{
	
	private $id;
	private $template;
	private $consultas;
	private $ruta;
	private $fecha_hoy;
	//private $yo;
	//private $id_mi_ciudad;


	function __construct($id = null)
	{
		
				# invocar archivo de configuracion

		$oConf    = new config();
	    $cfg      = $oConf->getConfig();
	    $db       = new mysqldb( $cfg['base']['dbhost'], $cfg['base']['dbuser'], $cfg['base']['dbpass'], $cfg['base']['dbdata'] );
	    
	    $this->consultas = new querys( $db );
	    $this->template  = new template();
	    $this->ruta      = $cfg['base']['template'];
	    $this->id        = $id;
	    $this->fecha_hoy = date("Y-m-d"); 
	   // $this->yo 		 = $yo;
	   // $this->id_mi_ciudad = $this::determina_id_mi_ciudad();

	}

	private function control()
	{
		return $this::displayMenu();
	}

	private function displayMenu()
	{
		$data = array('@@@title' =>'');

		return $this::despliegueTemplate($data,'new-menu.html');
	}

	private function myAsistencia($id_usuario = null, $id_evento = null)
	{

		$NUM = count( $this->consultas->miAsistencia($id_usuario,$id_evento) );

		if( $NUM > 0 )
			return true;
		else 
			return false;
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

	/**
	 * arregla_fechas()
	 *
	 * @param  String FECHA
	 * @return String
	 */
	 private function arregla_fechas( $FECHA=null ){

	 	if(!is_null( $FECHA )){
	 		$div = explode("-", $FECHA);

	 		return $div[2]."-".$div[1]."-".$div[0];
	 	}else
	 	    return null;
	 }

	public function getCode(){

		return $this::control();
	}


}

?>