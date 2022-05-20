<?php
class mails
{
	/**
	 * @var String id_tipo_accion
	 * @var String mail
	 */

	private $template;
	private $ruta;
	private $id;
	private $contenido;
	private $mail_target;
	private $ruta_abs;

	function __construct( $id 			= null,
						  $mail_target	= null,
						  $contenido 	= null)
	{
		$oConf    				= new config();
		$cfg      				= $oConf->getConfig();
	  	$this->template 		= new template();
   		$this->ruta     		= $cfg['base']['template'];
		$this->id  				= $id;
		$this->contenido  		= $contenido;
		$this->mail_target  	= $mail_target;
		$this->fecha_hora_hoy 	=  date("Y-m-d H:i:s");

		if( is_null( $ruta_abs ) )
				$this->$ruta_abs = null;
		else 	$this->$ruta_abs = $ruta_abs;

	}

	private function control()
	{
		switch ($this->id)
		{
			case 'asignacion':
			case 'msgUpdate':
			case 'msgIngreso':
			case 'mensajeObservaciones':
				return $this::mensajeObservaciones();
				break;

			default:
				return false;
				break;
		}
	}

	private function mensajeObservaciones()
	{
			return $this->contenido;
	}

	/**
	 * enviar(): envio de la data por mail
	 * @return boolean
	 */
	private function enviar()
	{

		$GUSER = 'claudio.guzman@socma.cl';
		$GPWD  = 'Guzman2020';

		global $error;
		$mail = new PHPMailer();  // create a new object
		$mail->IsSMTP(); // enable SMTP
		//$mail->SMTPDebug = 2;  // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true;  // authentication enabled
		$mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for GMail
		$mail->SMTPAutoTLS = false;
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 587;

		$mail->Username = $GUSER;
		$mail->Password = $GPWD;
		$mail->SetFrom($GUSER, 'Administrador de Sistemas');
		$mail->Subject = "Mensaje de Rendiciones ";
		$mail->isHTML(true);

		$mail->Body = $this::control();

		//a quien diriges
		$mail->AddAddress($this->mail_target);
		$mail->AddAddress("claudio.guzman@socma.cl");

		if($mail->Send()) {

			return true;

		} else {

			return false;
		}
	}

	 /**
	  * despliegueTemplate(), metodo que sirve para procesar los templates
	  *
	  * @param  array   arrayData (array de datos)
	  * @param  array   tpl ( template )
	  * @return String
	  */
    private function despliegueTemplate($arrayData,$tpl,$ruta_abs=null ){

			//if( is_null( $ruta_abs )  )
			//	$tpl = $this->ruta.$tpl;
			//else 	$tpl = "/home/inventario/public_html/Templates/{$tpl}";

		  $tpl = $this->ruta.$tpl;
	      $this->template->setTemplate($tpl);
	      $this->template->llena($arrayData);

	      return $this->template->getCode();
	  }

	/**
	 * getCode()
	 *
	 * @return boolean
	 */
	public function getCode(){

		return $this::enviar();
	}
}
?>
