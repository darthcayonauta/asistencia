<?php
/**
* 
*/
class contacto 
{
	
	private $id;	
	private $consultas;
	private $template;
	private $ruta;

	function __construct( $id = null )
	{

		$this->id = $id;		
		$oConf    = new config();
	    $cfg      = $oConf->getConfig();
	    $db       = new mysqldb( $cfg['base']['dbhost'], $cfg['base']['dbuser'], $cfg['base']['dbpass'], $cfg['base']['dbdata'] );
	    
	    $this->consultas = new querys( $db );
	    $this->template  = new template();
	    $this->ruta      = $cfg['base']['template'];
	}

	private function process(){

		switch ($this->id) {
				case 'form_contacto':
					# code...
					return  $this->form_contacto();
					break;

				case 'enviaMensajeMail':
					# code...

					return $this->enviaMensajeMail();

					break;

					default:
					# code...
					break;
			}	

	}
	/**
	 * enviaMensajeMail(): envia los mensajes del formulario de contacto a casillas de correo
	 *
	 * @return String
	 */
	public function enviaMensajeMail(){

		$obj_mail = new mails('normal',$_POST['asunto'],$_POST['comentario'],$_POST['nombre'],$_POST['email']);

		if( $obj_mail->getCode()  )
			return "<h2>Su mensaje ha sido exitosamente enviado</h2>";
		else
			return "<h2>Error: no se ha podido enviar el mensaje</h2>";
	}

	/**
	 * form_contacto(): despliegue de formulario de contacto
	 * 
	 * @return String
	 */
	private function form_contacto(){

		$data = array('@@@TITLE' => 'Formulario de Contacto');

		return $this->despliegueTemplate($data,"contacto.html");
	}

	/**
	 * separa()
	 *
	 * @param String cadena
	 * @param String simbolo
	 *
	 * @return array()
	 */
	private function separa($cadena=null,$simbolo=null)
	{
		if( is_null($cadena) )
			return "";
		else
			return explode($simbolo,$cadena);
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

		return $this->process();
	}
}

?>