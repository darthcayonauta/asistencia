<?php
/**
 * @author Ing. Claudio Guzman Herrera
 * @date   07-04-2019
 */
class Usuarios
{

  private $id;
  private $template;
  private $consultas;
  private $ruta;
  private $error;
  private $ftpUser;
  private $ftpPass;
  private $ftpTarget;

  function __construct( $id = NULL )
  {
    $this->id = $id;
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
    $this->yo                 = $_SESSION['yo'];

  }

/*
soniapon
Sonia2019*-.
*/

  private function control()
  {
      switch ( $this->id )
      {

        case 'registro-clave-nueva':

          return $this::registroClaveNueva();
          break;

        case 'cambia-clave':

          return $this::cambiaLaClave();
          break;

        case 'edita-el-usuario':

          return $this::editaElUsuario();
          break;

        case 'edita-user':

          return $this::editaUser();
          break;

        case 'resultado-busqueda':
        case 'lista_usuarios':

          return $this::lista_usuarios();
          break;

        case 'creaTodasLasCarpetas':

          return $this::creaTodasLasCarpetas();
          break;

        case 'ingresa-usuario':

          return $this::ingresaUsuario();
          break;

        case 'generaUsers':

          return $this::generaUsers();
          break;

          case 'cambiaClaveData':
            return $this::cambiaClaveData();
            break;

        default:

          return $this->error;
          break;
      }
  }


 private function cambiaClaveData()
 {
   //print_r( $_POST );
   //return $this->yo;

   $this->btn ='
   <a href="content-page.php?id=aW5pY2lv"
     class="btn btn-sm btn-secondary"
     name="a"
     id="listar-rendiciones">
           <i class="far fa-check-circle"></i> Listar Rendiciones
   </a>
   ';

   $this->msg = $this::notificaciones('success',
                                      '<i class="far fa-thumbs-up"></i>',
                                      "Clave Cambiada Exitosamente! {$this->btn}");


   if( $this->consultas->cambiaClaveData( $this->yo, $_POST['clave'] ) )
          return $this->msg;
    else  return "<strong>Error al cambiar la clave {$_POST['clave']}</strong>";

 }




  private function registroClaveNueva()
  {

    print_r( $_POST );

/*
( $this->consultas->cambiaClave($_POST['yo'],
                                    $_POST['clave']
                                  ))
  {

      $msg = '<div class="alert alert-success" role="alert">
                <h4>Actualizacion de Clave Exitosa</h4>
              </div>';

  }else {

      $msg = '<div class="alert alert-warning" role="alert">
                <h4>Ha ocurrido un error</h4>
              </div>';

          }

    return $msg.$this::cambiaLaClave();
*/
  }

  private function cambiaLaClave()
  {

    $data = array('@@@yo' => $this->yo);
    return $this::despliegueTemplate( $data,'form-usuario.html' );

  //  return $this->yo;

  }

  private function editaElUsuario()
  {

    $code = '';

    if( $this->consultas->editaUser($_POST['apaterno_razonsocial'],
                                    $_POST['rut'],
                                    $_POST['clave'],
                                    $_POST['id_user']))
    {

        $msg = '<div class="alert alert-success" role="alert">
                  <h4>Actulizacion Exitosa</h4>
                </div>';

    }else {
      $msg = '<div class="alert alert-warning" role="alert">
                <h4>Ha ocurrido un error</h4>
              </div>';

            }
    return $msg.$this::editaUser();
  }

  private function editaUser()
  {

      $code = '';

      $arr = $this->consultas->listaUsers( $_POST['id_user'] );

      foreach ($arr['process'] as $key => $value) {

        $data = array('@@@title'                =>  'Edición de Usuarios' ,
                      '@@@apaterno_razonsocial' =>  $value['apaterno_razonsocial'],
                      '@@@rut'                  => $value['rut'],
                      '@@@id_user'              => $_POST['id_user'],
                      '@@@button-name'          => 'edita-usuario',
                      '@@@button-desc'          => 'Editar Usuario');

        $code .= $this::despliegueTemplate( $data, "form-usuario.html" );

      }

      return $code;
  }

  private function lista_usuarios()
  {

    switch ($this->id )
    {
      case 'resultado-busqueda':

        $rut = $_POST['buscar'];
        break;

        case 'lista_usuarios':

        $rut = null;
        break;

      default:

        $rut = $this->error;
        break;
    }

    $arr = $this::tr_lista_usuarios( $rut );

    $data = array('@@@title' => 'Lista de Usuarios',
                  '@@@tr'    => $arr['code'],
                  '@@@num'   => $arr['total']);

    return $this::despliegueTemplate( $data, "tabla-usuarios.html" );
  }

  private function tr_lista_usuarios( $rut = null  )
  {
    $arr = $this->consultas->listaUsers(null,null, $rut );

    $code = "";

    $i =0;
    foreach ($arr['process'] as $key => $value) {

      $rut = $this::separa($value['rut'],'-');

      $data = array('@@@usuario_razonsocial' => $value['apaterno_razonsocial'],
                    '@@@rut'                 => $this::separa_miles( $rut[0] ).'-'.$rut[1],
                    '@@@documentos'          => $this::docs( $value['id_user'] ),
                    '@@@id_user'             => $value['id_user'],
                    '@@@num'                 => $i+1);

      $code .= $this::despliegueTemplate( $data,"tr-tabla-usuarios.html" );

      $i++;
    }

    if( $i > 0 )
    {
      $out['code']  = $code;
      $out['total'] = $i;

    }else{

      $out['code']  = "<h3>No Hay Resultados!</h3>";
      $out['total'] = $i;

    }

    return $out;

  }

  private function docs( $id_user = null  )
  {

      require_once("documentos.class.php");

      $ob_docs = new Documentos('datosArchivos',$id_user );
      $arr     = $ob_docs->getCode();
      $i       = 0;
      $code    = "";

      foreach ($arr['info'] as $key => $value)
      {
        if( $i < count( $arr['info'] )-1 )
              $code   .= '<a href="dox/'.$value['descripcion'].'" class="btn btn-light btn-sm enlace-small" target="_blank">'.$value['descripcion'].'</a>  ';
        else  $code   .= '<a href="dox/'.$value['descripcion'].'" class="btn btn-light btn-sm enlace-small" target="_blank">'.$value['descripcion'].'</a>';

        $i++;
      }

      if( $i > 0 )
            return $code ;
      else  return "No hay archivos";


    //return "Ha ocurrido una anomalia!";

  }

  private function creaTodasLasCarpetas()
  {

    $arr = $this->consultas->listaUsers();


    foreach ($arr['process'] as $key => $value) {

      $this::crearFolders( $value['rut']  );

    }

    return "proceso de creacion de carpetas terminado";

  }

  private function ingresaUsuario()
  {
      if ($this->consultas->creaUser( $_POST['apaterno_razonsocial'],
                                      $_POST['rut'],
                                      $_POST['clave']))
      {

          $msg = '<div class="alert alert-success" role="alert">
                    <h4>Registro Exitoso, Agrega mas usuarios!</h4>
                  </div>';

        //  $this::crearFolders( $_POST['rut'] );

      }else {
        $msg = '<div class="alert alert-warning" role="alert">
                  <h4>Ha ocurrido un error, al parecer el rut ya existe, intenta nuevamente</h4>
                </div>';

              }

      return $msg.$this::generaUsers();

  }

  private function crearFolders( $folderName = null  )
  {

    mkdir("dox/".$folderName , 0777,true);
    chown("dox/".$folderName ,$this->ftpUser );

  }





  private function generaUsers()
  {

      $data = array('@@@title'                => 'Creación de Usuarios' ,
                    '@@@apaterno_razonsocial' => '',
                    '@@@rut'                  => '',
                    '@@@id_user'              => 0,
                    '@@@button-name'          => 'ingresa-usuario',
                    '@@@button-desc'          => 'Ingresar Usuario');

      return $this::despliegueTemplate( $data, "form-usuario.html" );

  }

  /**
   * notificaciones()
   * @param string tipo_alerta
   * @param string icon
   * @param string glosa
   * @return string
    */
   private function notificaciones( $tipo_alerta = null, $icon= null, $glosa = null )
   {
       return $this::despliegueTemplate( array( '@@@tipo-alert@@@' => $tipo_alerta,
                                                '@@@icon@@@'       => $icon,
                                                '@@@glosa'         => $glosa) , 'notificaciones.html' );
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
