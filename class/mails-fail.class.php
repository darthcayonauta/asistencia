<?php

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

//use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;


class mails
{

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	/**
	 * @var String id_tipo_accion
	 * @var String mail
	 */
	private $id_tipo_accion;
	private $mail;	
	private $mail_port;
	private $mail_user;
	private $mail_pass;
	private $mail_from;
	private $mail_from_name;
	private $mail_host;

	private $asunto;
	private $mensajeBody;
	private $nombre;
	private $email;
	private $rut;

	private $consultas;

	private $template;
	private $ruta;

	function __construct( 	$id_tipo_accion	=null,
							$asunto			=null,
							$mensajeBody	=null,
							$nombre			=null, 
							$email			=null,
							$rut			=null )
	{
		$oConf    = new config();
		$cfg      = $oConf->getConfig();

		$db       = new mysqldb( $cfg['base']['dbhost'], $cfg['base']['dbuser'], $cfg['base']['dbpass'], $cfg['base']['dbdata'] );
	    
	    $this->consultas = new querys( $db );

		$this->mail_port 	  = $cfg['base']['mail_port'];
		$this->mail_user 	  = $cfg['base']['mail_user'];
		$this->mail_pass 	  = $cfg['base']['mail_pass'];
		$this->mail_from 	  = $cfg['base']['mail_from'];
		$this->mail_from_name = $cfg['base']['mail_from_name'];
		$this->mail_host 	  = $cfg['base']['mail_host'];
			
		$this->id_tipo_accion = $id_tipo_accion;

		//$this->mail 		  = new PHPMailer();

		$this->asunto = $asunto;
		$this->mensajeBody = $mensajeBody;
		
		
		$this->nombre = $nombre;
		$this->email  = $email;
		$this->rut    = $rut;

		
	    $this->template  = new template();
   	    $this->ruta      = $cfg['base']['template'];

   	    
	}

	/**
	 * cuerpoMensaje(): 
	 */
	private function cuerpoFormularioContacto(){

		$data = array('@@@ASUNTO' 		  => "Mensaje de ".$this->nombre." ( ".$this->email." ): ".$this->asunto,
					  '@@@CUERPO-MENSAJE' => $this->mensajeBody );

		return $this->despliegueTemplate( $data, "cuerpoMail.html");
	}

	/**
	 * generacion de boleta
	 */
	private function generaBoleta()
	{
		
		$arr = $this->consultas->dataTimonel( $this->rut );

		$id_timonel = '';

		foreach ($arr['process'] as $key => $value) {
			$id_timonel.= $value['id_timonel'];
		}


		$DATA = array('@@@PARTICIPANTE'      => $this->nombre,
					  '@@@EMAIL-PARTICIPANTE'=> $this->email,
					  '@@@RUT-PARTICIPANTE'  => $this->rut,
					  '@@@ID-PARTICIPANTE'   => $id_timonel,
					  '@@@NUM-VELA'          => $this->mensajeBody );

		return $this->despliegueTemplate($DATA,'notificacionBoleta.html');
	}



	private function cuerpoMensaje(){

		switch ( $this->id_tipo_accion ) {
			case 'normal':

				$msg['body'] = "EJEMPLO DE ENVIO";
				$msg['subject'] = "Mensaje de Pruebas";
				break;
			
			case 'generaBoleta':
				
				$msg['body'] = $this->generaBoleta();
				$msg['subject'] = "Generacion de Boleta para ".$this->nombre;
				break;	

			default:

				$msg['body'] = "no asigno nada";
				$msg['subject'] ="no asigno nada"; 
				break;
		}

		return $msg;
	}



	/**
	 * @return Array()
	 */
	private function cuerpoMensajeoLDER(){

		switch ( $this->id_tipo_accion ) {
			case 'normal':

				$msg['body'] = $this::cuerpoFormularioContacto();
				$msg['subject'] = "Mensaje de ".$this->nombre." ( ".$this->email." ): ".$this->asunto;
				break;
			
			case 'generaBoleta':
				
				$msg['body'] = $this->generaBoleta();
				$msg['subject'] = "Generacion de Boleta para ".$this->nombre;
				break;	

			default:

				$msg['body'] = "no asigno nada";
				$msg['subject'] ="no asigno nada"; 
				break;
		}

		return $msg;
	}

	/**
	 * @return boolean
	 */
	private function enviar(){

		/*
		$msg = $this->cuerpoMensaje();
		$this->mail->SMTPOptions = array(
									'ssl' => array(
									'verify_peer' => false,
									'verify_peer_name' => false,
									'allow_self_signed' => true
								  )
								);

		$this->mail->isSMTP();                                      // Set mailer to use SMTP
		$this->mail->Host = $this->mail_host;  // Specify main and backup SMTP servers
		//$this->mail->SMTPAuth = true;                               // Enable SMTP authentication
		$this->mail->SMTPAuth = true;                               // Enable SMTP authentication
		$this->mail->Username = $this->mail_user;                 // SMTP username
		$this->mail->Password = $this->mail_pass;                           // SMTP password
		//$this->mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		
		//$this->mail->SMTPSecure = 'tls';
		$this->mail->Port = $this->mail_port;                                    // TCP port to connect to

		$this->mail->From = $this->mail_from;
		$this->mail->FromName = $this->mail_from_name;
		//$this->mail->addAddress('jreyes@cpv.cl', 'Jose Reyes');     // Add a recipient. Es quien recibe
		
		if($this->id_tipo_accion == 'normal')
			$this->mail->addAddress('cguzmanherr@gmail.com', 'Claudio Andres Guzman Herrera'); 
		else
			$this->mail->addAddress( $this->email , $this->nombre ); 

		$this->mail->isHTML(true);                                  // Set email format to HTML

		$this->mail->Subject = $msg['subject'];
		$this->mail->Body    = $msg['body'];
		$this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		if(!$this->mail->send() ) 
 			return false;
		 else 
			return true; */
		
			
			//$this->mail_port 	  = $cfg['base']['mail_port'];
			//$this->mail_user 	  = $cfg['base']['mail_user'];
			//$this->mail_pass 	  = $cfg['base']['mail_pass'];
			//$this->mail_from 	  = $cfg['base']['mail_from'];
			//$this->mail_from_name = $cfg['base']['mail_from_name'];
			//$this->mail_host 	  = $cfg['base']['mail_host'];
										

			require '../vendor/autoload.php';

//Create a new PHPMailer instance
$mail = new PHPMailer;

//Tell PHPMailer to use SMTP
$mail->isSMTP();

//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 2;

//Set the hostname of the mail server
$mail->Host = 'smtp.gmail.com';
// use
// $mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6

//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 587;

//Set the encryption system to use - ssl (deprecated) or tls
$mail->SMTPSecure = 'tls';

//Whether to use SMTP authentication
$mail->SMTPAuth = true;

//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = "username@gmail.com";

//Password to use for SMTP authentication
$mail->Password = "yourpassword";

//Set who the message is to be sent from
$mail->setFrom('from@example.com', 'First Last');

//Set an alternative reply-to address
$mail->addReplyTo('replyto@example.com', 'First Last');

//Set who the message is to be sent to
$mail->addAddress('whoto@example.com', 'John Doe');

//Set the subject line
$mail->Subject = 'PHPMailer GMail SMTP test';

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML(file_get_contents('contents.html'), __DIR__);

//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';

//Attach an image file
$mail->addAttachment('images/phpmailer_mini.png');

//send the message, check for errors
if (!$mail->send()) {
    return true;
} else {
    return false;
    //Section 2: IMAP
    //Uncomment these to save your message in the 'Sent Mail' folder.
    #if (save_mail($mail)) {
    #    echo "Message saved!";
    #}
	}



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
	 * getCode()
	 * 
	 * @return boolean
	 */
	public function getCode(){

		return $this->enviar();
	}
}	
?>