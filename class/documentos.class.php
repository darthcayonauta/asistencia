<?php

/**
 * @author Ing. Claudio Guzman Herrera
 * @version 1.0
 * @package dox
 */
class Documentos
{

  private $id;
  private $id_user;
  private $ruta;
  private $consultas;
  private $template;
  private $error;
  private $ftpUser;
  private $ftpPass;
  private $ftpTarget;
  private $fecha_hora_hoy;

  function __construct(       $id = null,$id_user = null )
  {

    $this->id                 = $id;

    if( is_null( $id_user  ))
         $this->id_user       = $_POST['id_user'];
    else $this->id_user       = $id_user;

    $oConf    = new config();
	  $cfg      = $oConf->getConfig();
	  $db       = new mysqldb( 	$cfg['base']['dbhost'],
															$cfg['base']['dbuser'],
															$cfg['base']['dbpass'],
															$cfg['base']['dbdata'] );

    $this->consultas 					= new querys( $db );
    $this->template  					= new template();
    $this->ruta      					= $cfg['base']['template'];
    $this->error              = "<h3>Modulo no definido o no Desarrollado</h3>";
    $this->ftpUser            = "soniapon";
    $this->ftpPass            = "Sonia2019*-.";
    $this->ftpFolder  	      = "public_html/dox/dox";
    $this->fecha_hora_hoy     =  date("Y-m-d H:i:s");

  }

  private function control()
  {

    switch ($this->id )
    {

      case 'elimina-varios-files':

        return $this::eliminaVariosFiles();
        break;

      case 'ver-docs':
        return $this::archivos();
      break;

      case 'del-file':

        return $this::delFile();
        break;

      case 'ingresa-file':

        return $this::ingresaFile();
        break;

      case 'datosArchivos':

        return $this::datosArchivos();
        break;

      case 'asigna-docs':

        return $this::asignaDocs();
        break;

      default:

        return $this->error;
        break;
    }
  }

  private function eliminaVariosFiles()
  {
    //print_r($_POST);
  
    $arr = $this::separa( $_POST['id_documento'],'&' );

    $j = 0;
    for ($i=0; $i < count( $arr ) ; $i++) { 

      $aux = $this::separa( $arr[$i],'=' );

      if( $this->consultas->deleteDocs( $aux[1] ) )
          $j++;
    }
  
    if( $j > 0 )
          { $del = true;  $error = " No hay Errores"; }
    else  { $del = false; $error = " Error, no se ha eliminado el reg. revisar DB"; }

    if ( $del  )
          return $this::tablaFiles();
    else  return $error;

  }


  private function delFile()
  {

    if( $this->consultas->deleteDocs( $_POST['id_documento'] ) )
          { $del = true;  $error = " No hay Errores"; }
    else  { $del = false; $error = " Error, no se ha eliminado el reg. revisar DB"; }

    if ( $del  )
          return $this::tablaFiles();
    else  return $error;
  }

  private function ingresaFile()
  {
    $div_file = $this::separa( $_FILES['archivo']['name'],'.' );
    $new_file = $div_file[0].'-'.$this->fecha_hora_hoy.'.'.$div_file[1];


    if( $this::validaFile( $_FILES['archivo']['name'] )  )
      if( $this::procesaFTP() )
          if( $this->consultas->ingresaDocs( $new_file, $this->id_user )  )
                  $sube = true;
          else {  $sube = false; $error = "Error DB"; }
      else {      $sube = false; $error = "Error FTP"; }
    else {        $sube = false; $error = "Error Tipo File"; }

    if( $sube )
          return $this::tablaFiles();
    else  return $error;
  }

  private function tablaFiles()
  {

    $arr = $this::trAsignaDocs();

    $data = array('@@@tr' => $arr['code']);
    return $this::despliegueTemplate( $data, 'tabla-files.html' );

  }

  private function datosArchivos()
  {

    $info = array();

    $arr = $this->consultas->listaDocs(null,$this->id_user);

    $i = 0;

    foreach ($arr['process'] as $key => $value) {

      $info[$i]['descripcion']  = $value['descripcion'];
      $info[$i]['id_documento'] = $value['id_documento'];

      $i++;
    }

    $salida['info'] = $info;

    return $salida;

  }

  private function asignaDocs()
  {
    //data-user
      $apaterno_razonsocial = '';
      $rut                  = '';

      $arr      = $this->consultas->listaUsers( $_POST['id_user']);
      $arr_docs = $this::trAsignaDocs();

      foreach ( $arr['process']  as $key => $value)
      {
        $apaterno_razonsocial  .= $value['apaterno_razonsocial'];
        $rut                   .= $value['rut'];

      }

      $div_rut = $this::separa( $rut , '-' );

      $data = array('@@@apaterno_razonsocial' => $apaterno_razonsocial,
                    '@@@rut'                  => $this::separa_miles($div_rut[0]).'-'.$div_rut[1],
                    '@@@tr'                   => $arr_docs['code'],
                    '@@@num'                  => $arr_docs['total-recs'],
                    '@@@id_user'              => $this->id_user);

      return $this::despliegueTemplate( $data, 'modulo-docs.html' );

  }

  private function trAsignaDocs()
  {

    $arr  = $this::datosArchivos( $this->id_user );
    $i    = 0;
    $code = '';

    foreach ($arr['info'] as $key => $value) {

      $data = array('@@@num' => $i+1,
                    '@@@nombre-archivo' => $value['descripcion'],
                    '@@@nombre-archivo' => $value['descripcion'],
                    '@@@id_documento'   => $value['id_documento'],
                    '@@@id_user'        => $this->id_user )  ;

      $code .= $this::despliegueTemplate( $data,'tr-modulo-docs.html' );

      $i++;
    }

    $salida['code'] = $code;
    $salida['total-recs'] = $i;

    return $salida;

  }

  private  function validaFile( $fileName=null )
  	{

    $arr = $this::separa( $fileName,".");

	    if(  count( $arr ) > 1 )
	    {
	      switch ( $arr[1] )
        {
	              case 'pdf':

	                return true;
	                break;

	              default:
	                return false;
	                break;
	            }
	    }else
	        return false;
  }


   private function procesaFTP()
   {
	    $conn = ftp_connect("localhost",21);

      //CAMBIAR NOMBRE-ARCHIVO

      $div_file = $this::separa( $_FILES['archivo']['name'],'.' );
      $new_file = $div_file[0].'-'.$this->fecha_hora_hoy.'.'.$div_file[1];

      //$_FILES["archivo"]["name"]

	    if ( $conn )
	      if( ftp_login( $conn , $this->ftpUser, $this->ftpPass ) )
	        if( ftp_chdir($conn, $this->ftpFolder ))
	            if(ftp_put($conn ,$new_file,$_FILES["archivo"]["tmp_name"],FTP_BINARY))
	              return true;
	            else
	              return false;
	        else
	          return false;
	      else
	        return false;
	    else
	      return false;
	  }

    private function archivos()
  	{
  		require_once('documentos.class.php');

  		$ob_docs = New Documentos( 'datosArchivos',$this->id_user );

  		$arr = $ob_docs->getCode();

  		$code = '<h3>Archivos Asignados</h3><hr>
  							<div class="row">' ;
  		$i = 0;
  		foreach ($arr['info'] as $key => $value)
  		{
  			$data  = array('@@@archivo-name' =>  $value['descripcion']);
  			$code .= $this::despliegueTemplate( $data , 'vista-files.html' );

  			$i++;

  		}

  		$code.= '</div>';
  		if( $i > 0 )
  					return $code;
  		else 	return "<h3>No hay documentos asignados</h3>";
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
  	  * separa_miles(), coloca separador de miles en una cadena de caracteres
  	  *
  	  * @param  String num
  	  * @return String
  	  */
  	 private function separa_miles($num=null){

   		return @number_format($num, 0, '', '.');
  	 }

	public function getCode()
	{
		return $this::control();
	}
}
?>
