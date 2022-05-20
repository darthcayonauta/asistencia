<?php
/**
 * @author  Ing. Claudio Guzmán Herrera
 * @version 1.0
 * @package class
 */
class Users
{
	private $consultas;
	private $template;
	private $ruta;
	private $id;
	private $fecha_hoy;
	private $id_tipo_user;
  private $ftpUser;
  private $ftpPass;
  private $ftpHost;
  private $ftpFolder;
  private $token;
	private $msg;

  function __construct( $id = null )
	{
		$oConf    = new config();
	  	$cfg      = $oConf->getConfig();
	  	$db       = new mysqldb( 	    $cfg['base']['dbhost'],
									    $cfg['base']['dbuser'],
									    $cfg['base']['dbpass'],
									    $cfg['base']['dbdata'] );

        $this->yo                       = $_SESSION['yo'];
        $this->tipo_usuario             = $_SESSION['tipo_usuario'];

        $this->consultas 				= new querys( $db );
        $this->template  				= new template();
        $this->ruta      				= $cfg['base']['template'];
        $this->id 						= $id;
        $this->error 					= "MODULO NO DESARROLLADO O INEXISTENTE";
        $this->fecha_hoy 				= date("Y-m-d");
        $this->fecha_hora_hoy 			= date("Y-m-d H:i:s");
        $this->id_tipo_user 			= $_SESSION['tipo_usuario'];

        $this->ftpUser                  = $cfg['base']['ftpUser'];
        $this->ftpPass                  = $cfg['base']['ftpPass'];
        $this->ftpFolder                = $cfg['base']['ftpFolder'];
        $this->ftpHost                  = $cfg['base']['ftpHost'];
        $this->ftpPort                  = $cfg['base']['ftpPort'];
        $this->token 		            =  date("YmdHis");

				$this->btn_listar 		= "<a href='content-page.php?id=bGlzdGFyLXVzdWFyaW9z' class='btn btn-sm btn-secondary'>
																			Listar Usuarios
																</a>";

				$this->btn_crear 		= "<a href='content-page.php?id=Y3JlYXItdXN1YXJpb3M=' class='btn btn-sm btn-secondary'>
																			Crear Usuarios
																</a>";

	}

    private function control()
    {
        switch ($this->id) {

						case 'editaUsuario':
							return $this::editaUsuario();
							break;

						case 'crear-usuarios':
							return $this::crearUsuarios();
						break;

            case 'crea_users':
            case 'registroUsuario':
                return $this::registroUsuario();
                break;

            case 'ingresaUsuario':
                return $this::ingresaUsuario();
                break;

            case 'cambio-clave':
                return $this::cambioClave();
                break;

            case 'cambiaClave':
                return $this::cambiaClave();
                break;

            case 'lista-accesos':
                return $this::listaAccesos();
                break;

            case 'buscarUser':
            case 'listar-usuarios':
                return $this::listarUsuarios();
                break;

            case 'cambiaEstadoUsuario':
                return $this::cambiaEstadoUsuario();
                break;

            case 'listaSubFolders':
                return $this::listaSubFolders();
                break;

            case 'addPermiso':
                return $this::addPermiso();
                break;

            case 'quitaPermiso':
                return $this::quitaPermiso();
                break;

						case 'creaUserData':
							return $this::creaUserData();
							break;

						case 'actualizaUserData':
							return $this::actualizaUserData();
							break;


            default:
                return $this->error;
                break;
        }
    }

private function actualizaUserData()
{
	/*
	Array ( [nombres] => user1
	[apaterno] => socma [amaterno] => .cl
	[login] => user1@socma.cl
	[clave] => x [clave2] => x
	[id_usuario] => 6 [id] => actualizaUserData )


	*/

	if( $this->consultas->procesaUser( $_POST['nombres'],
 																				$_POST['apaterno'],
																				$_POST['amaterno'],
																				$_POST['login'],
																				$_POST['clave'],
																				$_POST['id_usuario']))
  {
		$this->msg = $this::notificaciones('success',null,'Registro Actualizado');

	}else{

		$this->msg = $this::notificaciones('danger',null,'Error, no se ha actualizado el registro');
	}

	return "{$this->msg} {$this->btn_crear} {$this->btn_listar}";
}


private function editaUsuario()
{
	$code = "";
	$arr = $this->consultas->listaUsuarios( null,null,1,$_POST['id_usuario'] );

	foreach ($arr['process'] as $key => $value) {

		$hidden = "<input type='hidden'
											id='id_usuario'
											name='id_usuario'
											value ='{$_POST['id_usuario']}'  >";

		$data = ['###title###' 			=> 'Edición',
						 '###nombres###'		=> $value['nombres'],
						 '###apaterno###'		=> $value['apaterno'],
						 '###amaterno###'		=> $value['amaterno'],
						 '###login###'			=> $value['login'],
						 '###hidden###'   	=> $hidden,
						 '###button_id###' 	=> 'update'];

		$code .= $this::despliegueTemplate( $data, 'formulario-usuario.html' );
	}

	return $code;

}

public function creaUserData()
{
/*
Array ( [nombres_] => CLAUDIO ANDRÉS
 [apaterno] => guzman
 [amaterno] => herrera
 [login] => claudio.guzman@socma.cl
 [clave] => cayofilo [clave2] => cayofilo [id] => creaUserData )
*/

	if( $this->consultas->procesaUser( addslashes( $_POST['nombres'] ),
																		 addslashes( $_POST['apaterno'] ),
																		 addslashes( $_POST['amaterno'] ),
																		 addslashes( $_POST['login'] ),
																		 addslashes( $_POST['clave'] ) ))
	{
		$this->msg = $this::notificaciones('success',null,'Usuario Ingresado');
	}else{

		$this->msg = $this::notificaciones('danger',null,'Error, no se ha podido ingresar usuario / Usuario Repetido');

	}

	return "{$this->msg} {$this->btn_crear} {$this->btn_listar}";
}

public function crearUsuarios()
{
	$data = ['###nombres###'  	=> null,
					 '###apaterno###' 	=> null,
					 '###amaterno###' 	=> null,
					 '###login###' 			=> null,
					 '###hidden###' 		=> null,
					 '###title###' 			=> 'Ingreso',
					 '###button_id###'  => 'send' 		];

	return $this::despliegueTemplate( $data, 'formulario-usuario.html' );
}

public function extraeUser( $id_usuario = null )
{

    $code = "";
    $arr = $this->consultas->listaUsuariosSimple( $id_usuario  );

    foreach ($arr as $key => $value) {

        $code .= "{$value['nombres']} {$value['apaterno']} {$value['amaterno']}";
    }

    return $code;
}


private function addPermiso(){

    if( $this::procesaPeticion( $_POST['id_usuario'] , $_POST['id_folder']) )
         {$this->msg =  "Registro(s) ingresado(s)"; $this->color= "success"; $this->icon ='<i class="far fa-thumbs-up"></i>';

        //procesar folders
        if( $this->consultas->procesaFolder( $_POST['id_usuario'] , $_POST['id_folder']  ) )
        {
            $ok = true;
        }else{
            $ok = false;
        }

        }
    else {$this->msg =  "Error, revisar DB";    $this->color= "danger";  $this->icon ='<i class="far fa-thumbs-down"></i>';   }

   return $this::notificaciones( $this->color, $this->icon, $this->msg ). $this::listaSubFolders() ;

}

private function quitaPermiso(){

    if( $this::procesaPeticion( $_POST['id_usuario'] , $_POST['id_folder']) )
         {$this->msg =  "Registro(s) Eliminado(s)"; $this->color= "success"; $this->icon ='<i class="far fa-thumbs-up"></i>';   }
    else {$this->msg =  "Error, revisar DB";    $this->color= "danger";  $this->icon ='<i class="far fa-thumbs-down"></i>';   }

    return $this::notificaciones( $this->color, $this->icon, $this->msg ). $this::listaSubFolders() ;

}

private function procesaPeticion( $id_usuario = null , $id_folder = null)
{
    $arr = $this::separa( $_POST['id_sub_folder'] , "&" );
    $j = 0;

    for ($i=0; $i < count( $arr ); $i++) {

        $aux = $this::separa( $arr[ $i ] , "=" );

        switch ($this->id) {
            case 'addPermiso':

                if( $this->consultas->procesaSubFolder( $id_usuario , $id_folder , $aux[ 1 ] ) )
                    $j++;

                break;

            case 'quitaPermiso':

                if( $this->consultas->eliminaSubFolder( $id_usuario , $id_folder , $aux[ 1 ] ) )
                    $j++;

                break;

            default:
                $j = 0;
                break;
        }
    }

    if( $j > 0 )
         return true;
    else return false;

}

private function listaSubFolders()
{
    $data =[ '###tr###'             => $this::trListaSubFolders( $_POST['id_folder'], $_POST['id_usuario'] ),
             '###id_usuario###'     => $_POST['id_usuario']   ,
             '###id_folder###'      => $_POST['id_folder']
            ];

    return $this::despliegueTemplate( $data , 'tabla-sub-folders.html' );
}

private function trListaSubFolders( $id_folder = null , $id_usuario = null )
{
    $code = "";

    foreach ($this->consultas->subFolderSimple( null, $id_folder  ) as $key => $value) {

        if( $this::checked( $value['id'],$id_folder,$id_usuario ) )
                $checked = "checked";
        else    $checked = "";


        $data = ['###id_subfolder###'   => $value['id'],
                 '###subFolder###'      => $value['descripcion'],
                 '@@@CHECKED'           => $checked                 ];

        $code .= $this::despliegueTemplate( $data , 'tr-sub-folders.html' );

    }

    return $code;
}

/**
 * checked():comprueba si ya usuario tiene permisos sobre carpetas
 *
 *  */

private function checked( $id_sub_folder = null, $id_folder = null, $id_usuario = null )
{

    $arr = $this->consultas->listaSubFolder( $id_usuario,$id_folder,$id_sub_folder );

    if( $arr['total-recs'] > 0 )
            return true;
    else    return false;
}



private function cambiaEstadoUsuario()
{
    $arr = $this->consultas->listaUsuarios( null, null, true, $_POST['id_usuario'] );

    $estado = 1;

    foreach ($arr['process'] as $key => $value) {
        $estado = $value['id_estado'];
    }

    if( $estado == 1 )
    {
        if( $this->consultas->cambiaEstado( 'usuario', 'id_estado', 2, $_POST['id_usuario']  ) )
             {  $estado = 2;  $cambia = true;}
        else {  $estado = 1;  $cambia = false;}
    }else{
        if( $this->consultas->cambiaEstado( 'usuario', 'id_estado', 1, $_POST['id_usuario']  ) )
             {  $estado = 1; $cambia = true;}
        else {  $estado = 2; $cambia = false;}
    }

        if( $cambia )
            return $this::estados( $estado );
        else return "Error de ejecución!";
}

private function listarUsuarios()
{
    $arr = $this::trListarUsuarios();

    if( $arr['total-recs'] > 0  )
    {
        $data = array(  '###total-recs###'  => $arr['total-recs'],
                        '###tr###'          => $arr['code'],
                        '@@@NAV-LINKS'      => $arr['nav-links'],
												'###menu_admin###'  => $this::menu_admin(),
                        '###buscar###'      => $this::buscar('apaterno' , 'Buscar por Apellido Paterno')

        );
        return $this::despliegueTemplate( $data , 'tabla-users.html' );
    }else{

        return $this::notificaciones(   'danger',
                                        '<i class="far fa-thumbs-down"></i>',
                                        'No hay registros') ;
    }
}


private function trListarUsuarios()
{
    $code       = "";
    $i          = 0;

    if( !$_POST['apaterno'] )
    {
        $arr        = $this->consultas->listaUsuarios( null, null, true );
        $utils      = new utiles($arr['sql']);
        $rs_dd      = $utils->show();
        $nav_links  = $rs_dd['nav_links'];
        $param      = $rs_dd['result'] ;

    }else{

        $arr        = $this->consultas->listaUsuarios( null, null, true, null, $_POST['apaterno'] );
        $nav_links  = "";
        $param      = $arr['process'] ;
    }

//modal( $target = null,$img = null, $title = null, $content = null)

    foreach ($param as $key => $value) {

        if( $i%2 == 0 )
                $color = "#FFFFFF";
        else    $color = "#EFEFEF";

        $usuario = " {$value['nombres']} {$value['apaterno']} {$value['amaterno']}";

        $data = array(  '###num###'          => $i+1,
                        '###usuario###'      => utf8_encode( $usuario ),
                        '###email###'        => $value['login'],
                        '###profesion###'    => $value['rut'],
                        '###institucion###'  => $value['institucion'],
                        '###estado####'      => $this::estados( $value['id_estado'] ),
                        '###id_usuario###'  => $value['id'],
                        '###color###'        => $color   ,
                        '###modal###'        => $this::modal("permisos-{$value['id']}",
                                                '',
                                                "Permisos para <strong>{$usuario}</strong>",
                                                $this::moduloPermisos( $value['id'] )  )
                    );

        if( $this->yo != $value['id'] )
        {
            $code .= $this::despliegueTemplate( $data , 'tr-users.html' );
            $i++;

        }
    }

    $out['code']        = $code;
    $out['total-recs']  = $arr['total-recs'];
    $out['nav-links']   = $nav_links;
    return $out;

}

private function estados( $id_estado = null )
{
    if( $id_estado == 1 )
            return "ACTIVO";
    else    return "INACTIVO / BLOQUEADO";
}


 private function moduloPermisos( $id_usuario = null  )
 {

  //  $sel = new Select( $this->consultas->folderSimple(),'id','descripcion', "id_folder_{$id_usuario}", "Carpeta" );
  //  $data = ['###select-folder###' => $sel->getCode(), '###id_usuario###' => $id_usuario  ];

  //  return $this::despliegueTemplate( $data , 'modulo-permisos.html' );

	return null;
 }


  /**
     * buscar()
     * @param string id_text
     * @param string placeholder
     */
    private function buscar( $id_text = null, $placeholder = null )
    {
        return $this::despliegueTemplate( array( '@@@id_text'       => $id_text,
                                                 '@@@placeholder'   => $placeholder ), 'buscar.html' );
    }

private function listaAccesos()
{
    $arr = $this::trAccesos();

    $data = array(  '###tr###'          => $arr['code'],
                    '###total-recs###'  => $arr['total-recs'],
                    '@@@NAV-LINKS'      => $arr['nav-links'],

);
    return $this::despliegueTemplate( $data , 'tabla-accesos.html' );

}

private function trAccesos()
{
    $code       = "";
    $i          = 0;

    $arr        = $this->consultas->listaAccesos();
    $utils      = new utiles($arr['sql']);
    $rs_dd      = $utils->show();
    $nav_links  = $rs_dd['nav_links'];
    $param      = $rs_dd['result'] ;

    foreach ($param as $key => $value) {

        if( $i%2 == 0 )
             $color = "#FFFFFF";
        else $color = "#EFEFEF";

        $usuario = "{$value['nombres']} {$value['apaterno']} {$value['amaterno']}";

        $data = array( '###num###'          => $i +1,
                       '###usuario###'      => utf8_encode( $usuario ),
                       '###ip###'           => $value['ip'],
                       '###color###'        => $color,
                       '###fecha_acceso###' => $value['fecha_acceso']);

        $code .= $this::despliegueTemplate( $data , 'tr-accesos.html' );

        $i ++;
    }


    $out['code']        = $code;
    $out['total-recs']  = $arr['total-recs'];
    $out['nav-links']   = $nav_links;

    return $out;
}


private function cambiaClave()
{
    //Array ( [id] => cambiaClave [clave] => 222222222 )

    if( $this->consultas->cambiaClave( $this->yo, $_POST['clave'] ) )
    {
        return $this::notificaciones(   'success',
                                        '<i class="far fa-thumbs-up"></i>',
                                        'Registro Actualizado') ;
    }
    else{
        return $this::notificaciones(   'danger',
                                        '<i class="far fa-thumbs-down"></i>',
                                        'Error al Actualizar Clave') ;
    }
}

//cambio-clave
    private function cambioClave()
    {
        $data = array();
        return $this::despliegueTemplate( $data, 'form-cambio-clave.html' );
    }

    /**
     * ingresaUsuario()
     * @param
     * @return string
     */
    private function ingresaUsuario()
    {

        $btn="<a href='content-page.php?id=bGlzdGFyLXVzdWFyaW9z' class='btn btn-sm btn-success'>
                  Listar usuarios
              </a>";


        if(  $this->consultas->procesaUsuario(  addslashes( $_POST['nombres'] ) ,
                                                addslashes( $_POST['apaterno'] ) ,
                                                addslashes( $_POST['amaterno'] ) ,
                                                addslashes( $_POST['rut'] ) ,
                                                addslashes( $_POST['email'] ) ,
                                                addslashes( $_POST['clave1'] ),
                                                addslashes( $_POST['institucion'] )))
        {

           // $comentario = "Su cuenta ha sido creada con éxito.
           // Su usuario es '{$_POST['email']}' y su clave es : '{$_POST['clave1']}'  ";
//
           // $para      =  $_POST['email'] ;
           // $titulo    = 'Cuenta creada para aprenderacrecer.cl/file-web';
           // $mensaje   = $comentario;
           // $cabeceras = 'From: no-reply@aprenderacrecer.cl' . "\r\n" .
           //              'Reply-To: no-reply@aprenderacrecer.cl' . "\r\n" ;
//
           // if( mail($para, $titulo, $mensaje, $cabeceras) )
           // {  $envia = "Mensaje enviado";  }
           // else{  $envia = "Error al enviar"; }

            return "<div class='row'>
                    <div class='col-sm-1'></div>
                    <div class='col-sm-11'>".$this::notificaciones(   'success',
                                            '<i class="far fa-thumbs-up"></i>',
                                            "Usuario Creado exitosamente {$btn}")." </div></div>" ;
        }else{
            return "<div class='row'>
                                    <div class='col-sm-1'></div>
                                    <div class='col-sm-11'>".$this::notificaciones(   'danger',
                                            '<i class="far fa-thumbs-up"></i>',
                                            "Error , Email repetido {$btn}")."</div></div>" ;
        }
    }

    private function login()
    {
        $data = array();

        return $this::despliegueTemplate( $data , 'login.html' );
    }


/**
   * modal()
   * @param string target
   * @param string img
   * @param string title
   * @param string content
   * @return string
   */
  private function modal( $target = null,$img = null, $title = null, $content = null)
  {
      $data = array('@@@TARGET'     => $target,
                    '@@@IMG-TITLE'  => $img,
                    '@@@TITLE'      => $title,
                    '@@@CONTENT'    => $content
      );

      return $this::despliegueTemplate( $data,'modal.html');

  }

	private function menu_admin()
  {
      $data = [];
      return $this::despliegueTemplate( $data, 'menu-admin.html' );
  }


    /**
     * registroUsuario()
     *  @param
     *  @return string
     */
    private function registroUsuario()
    {
        $data = array();
        return $this::despliegueTemplate( $data , 'form-registro.html' );
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
       * changeNameFile(): cambia el nombre de una cadena añadiendo el token definido en el constructor
       * @param string fileName
       * @return string
       *  */
      private function changeNameFile( $fileName = null )
      {
          $div = $this::separa( $fileName,'.' );
          return $div[0].'-'.$this->token.'.'.$div[1];
      }

	/**
	 * arregla_fechas()
	 *
	 * @param  string FECHA
	 * @return string
	 */
    private function arreglaFechas( $FECHA=null ){

        if(!is_null( $FECHA )){
            $div = explode("-", $FECHA);

            return $div[2]."-".$div[1]."-".$div[0];
        }else
            return null;
    }

    /**
     * separaMiles(): separa con un punto los numeros mayores que 1000
     * @param integer number
     * @return string
     */
    private function separaMiles( $number = null )
    {
      return @number_format( $number,0, ",","." );
    }

    /**
     * separa()
     * @param string cadena
     * @param string simbolo
     * @return string
     */
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
