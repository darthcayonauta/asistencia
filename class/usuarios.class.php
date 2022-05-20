<?php
/**
 * @author Ing. Claudio Guzman Herrera
 * @package users
 * @date   07-04-2022
 */
class Usuarios
{

  private $id;
  private $template;
  private $consultas;
  private $ruta;
  private $error;

  /**
   * __construct(): Método constructor
   * @param int id
   */
  function __construct( $id = null )
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
    $this->error              = "Modulo no definido o no Desarrollado para {$this->id}";
    $this->yo                 = $_SESSION['yo'];

  }

  /**
   * control(): conjunto de llamadas a funciones a desplegar, dependiendon del 'id' recibido en el constructor
   * @return string
   */
  private function control()
  {
      switch ( $this->id )
      {
        case 'crear-usuario':
          return $this::crearUsuario();
        break;  

        case 'cambia-password':       
          return $this::cambiaPassword();
          break;

        case 'cambiaClaveData':          
          return $this::cambiaClaveData();
          break;

        case 'lista-usuarios':          
          return $this::listaUsuarios();
          break;  

        case 'ingresaNewUsuario':
          # code...
          return $this::ingresaNewUsuario();
          break;  

        case 'eliminaUsuario':
          # code...
          return $this::eliminaUsuario();
          break;  

        case 'editarUsuario':
          # code...
          return $this::editarUsuario();
          break;  

        case 'actualizaUsuario':
          # code...
          return $this::actualizaUsuario();
          break;  

        default:

          return $this->error;
          break;
      }
  }

 /**
  * actualizaUsuario(): accion de actualizar usuarios
  * @return string 
  *
  */ 
 private function actualizaUsuario()
 {
  
  if( $this->consultas->actualizaUsuario( $_POST['nombres'], 
                                          $_POST['apaterno'],
                                          $_POST['amaterno'],
                                          $_POST['id_user']  ) )
  {
    $not = $this::notificaciones( 'success' , '<i class="far fa-thumbs-up"></i>', 'USUARIO ACTUALIZADO' );
  }else{
    $not = $this::notificaciones( 'danger' , '<i class="far fa-thumbs-down"></i>', 'ERROR!!, USUARIO NO ACTUALIZADO' );
  }

  return $not;
 }

  /**
   * editarUsuario(): muestra formulario de edicion de usuarios
   * 
   * @return string
   */
  private function editarUsuario()
  {

    $code = "";
    $arr = $this->consultas->usersListado( $_POST['id_user'] );

    foreach ($arr['process'] as $key => $value) {
      # code...

      $hidden = "<input type='hidden' name='id_user' id='id_user' value='{$value['id']}'>";

      $data = ['###tipo###'       => 'Edición',
              '###nombres###'    => $value['nombres'],
              '###apaterno###'   => $value['apaterno'],  
              '###amaterno###'   => $value['amaterno'],  
              '###rut###'        => $value['rut'],
              '###disabled###'   => 'disabled',                
              '###hidden###'     => $hidden,
              '###button-id###'  => 'update' ];

      $code .= $this::despliegueTemplate( $data, 'users/form-usuario.html' );
    }

    return $code;  
  } 

  /**
   * eliminaUsuario(): Accion de eliminar usuario desde la base de datos
   * 
   * @return string
   */
  private function eliminaUsuario()
  {
    if( $this->consultas->eliminaUsuario( $_POST['id_user'] ) )
    {
      $not = $this::notificaciones( 'warning' , '<i class="far fa-thumbs-up"></i>', 'USUARIO ELIMINADO' );
    }
    else{
      $not = $this::notificaciones( 'danger' , '<i class="far fa-thumbs-down"></i>', 'ERROR!!, USUARIO NO ELIMINADO' );
    }
    return $not . $this::listaUsuarios();
  }

  /**
   * ingresaNewUsuario(): ingresar un nuevo usuario en la base de datos
   * @return string
   */
  private function ingresaNewUsuario()
  {
    
    if( $this->consultas->ingresaNewUsuario( strtoupper( $_POST['nombres'] ), 
                                             strtoupper( $_POST['apaterno']),
                                             strtoupper( $_POST['amaterno']),
                                             $_POST['rut'],
                                             $_POST['clave'] )  )
    {
      $not = $this::notificaciones( 'success' , '<i class="far fa-thumbs-up"></i>', 'USUARIO CREADO EXITOSAMENTE' );
    }else{
      $not = $this::notificaciones( 'danger' , '<i class="far fa-thumbs-down"></i>', 'ERROR!!, USUARIO NO CREADO / USUARIO REPETIDO' );
    }

    return $not . $this::listaUsuarios();
    
  }

  /**
   * listaUsuarios(): pantalla principal o interfaz que despliega lista de usuarios
   * @return string
   */
  private function listaUsuarios()
  {
    $data = ['###listado###' => $this::tabla() ];
    return $this::despliegueTemplate( $data, 'users/lista-usuarios.html' );
  }

  /**
   * tabla(): tabla que contiene al grupo de usuarios 
   * @return string
   */
  private function tabla()
  {
    $data = ['###tr###' => $this::tr() ];
    return $this::despliegueTemplate( $data, 'users/tabla-usuarios.html' );
  }

  /**
   * tr(): contenido de la funcion tabla
   * @return string
   */
  private function tr()
  {
    $arr  = $this->consultas->usersListado();
    $code = "";
    $i    = 0;

    foreach ($arr['process'] as $key => $value) {

      $usuario = "{$value['nombres']} {$value['apaterno']} {$value['amaterno']}";

      $rut = $this::separa( $value['rut'],'-' );

      if( $value['id_estado'] == 1  )
      {
        $btn_edit = "<button class='btn btn-sm btn-secondary' id='edit-{$value['id']}'>
                            <i class='fas fa-edit'></i> Edit
                      </button>";

                      $disabled = null;               
      }else{

        $btn_edit = "<button class='btn btn-sm btn-secondary' id='edit-{$value['id']}' disabled>
                        <i class='fas fa-edit'></i> Edit
                      </button>";

        $disabled = "disabled";

      }

      $data = ['###id###'       => $value['id'], 
               '###num###'      => $i+1,
               '###btn-edit###' => $btn_edit,
               '###usuario###'  => strtoupper($usuario),
               '###rut###'      => $this::separa_miles( $rut[0] )."-".$rut[1] ,
               '###estado###'   => $this::determinaEstado( $value['id_estado'] ) , 
               '###disabled###' => $disabled  ];


      $code .= $this::despliegueTemplate( $data , 'users/tr-usuarios.html' );
      $i++;
    }

    return $code;
  }

  /**
   * determinaEstado(): determina el estado 
   * @param int estado_num
   * @return string
   */
  private function determinaEstado( $estado_num = null )
  {
    $estado = ( $estado_num == 1 ) ? "ACTIVO/VIGENTE" : "<strong><i class='fas fa-exclamation-triangle'></i> INACTIVO/ELIMINADO</strong>";
    return $estado;
  }

  /**
   * crearUsuario(): despliegue de formulario de creacion vacío de usuarios
   * @return string
   */
  private function crearUsuario()
  {
    $data = ['###tipo###'       => 'Creación',
             '###nombres###'    => null,
             '###apaterno###'   => null,  
             '###amaterno###'   => null,  
             '###rut###'        => null,
             '###disabled###'   => null,  
             '###hidden###'     => null,
             '###button-id###'  => 'send' ];

    return $this::despliegueTemplate( $data, 'users/form-usuario.html' );
  }

  /**
   * cambiaClaveData(): recepcion de parametros de cambio de clave y display final
   * @return string
   */
  private function cambiaClaveData()
  {
    
    if( $this->consultas->cambiaPassword( $_POST[ 'clave'], $this->yo ) )
    {
        return $this::notificaciones( 'success' , '<i class="far fa-thumbs-up"></i>', 'CLAVE CAMBIADA EXITOSAMENTE' );
    }else{
        return $this::notificaciones( 'danger' , '<i class="far fa-thumbs-down"></i>', 'ERROR, LA CLAVE NO HA PODIDO SER CAMBIADA' );
    }
  }

  /**
   * cambiaPassword(): despliegue de formulario de cambio de password
   * @return string
   */
  private function cambiaPassword()
  {
    $data = ['###id_user###' => $this->yo ];
    return $this::despliegueTemplate( $data, 'users/cambia-password.html' );
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

  /**
   * separa(): método que que divide cadenas de acuerdo a un determinado simbolo, ej '.','-',' '
   * @param string cadena
   * @param string simbolo
   * @return array() of string
   * 
   *  */ 
  private function separa($cadena=null,$simbolo=null)
	{
		if( is_null($cadena) )
			return null;
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

  /**
   * getCode(): llamada de método control y despliegue de los elementos de éste
   * @return string
   *  */   
	public function getCode()
	{
		return $this::control();
	}
}
 ?>
