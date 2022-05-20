<?php
/**
 * @author  Ing. Claudio Guzmán Herrera
 * @version 1.0
 * @package class
 */
class Archivos
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
    private $btn_listar;

    private $mail_port ;
    private $mail_host ;
    private $mail_user ;
    private $mail_pass ;
    private $mail_from ;
    private $mail_from_name ;


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

        $this->ftpUser                  = 'system';
        $this->ftpPass                  = 'x';
        $this->ftpFolder                =  'public_html/dox';
        $this->ftpHost                  = 'localhost';
        $this->ftpPort                  = '21';

        $this->token 		            =  date("YmdHis");
        $this->btn_listar               = "content-page.php?id=bGlzdGFyLWFyY2hpdm9z";

        //MAILS
        $this->mail_port                = 25;
        $this->mail_host                = $cfg['base']['mail_host'];
        $this->mail_user                = $cfg['base']['mail_user'];
        $this->mail_pass                = $cfg['base']['mail_pass'];
        $this->mail_from                = $cfg['base']['mail_from'];
        $this->mail_from_name           = $cfg['base']['mail_from_name'];


	}


    private function control()
    {
        switch ($this->id) {

            case 'ingresar-archivos':
                return $this::ingresaArchivos();
                break;

            case 'categorias':
                return $this::categorias();
                break;

            case 'ingreseCategoria':
                return $this::ingreseCategoria();
                break;

            case 'eliminaCategoria':
                return $this::eliminaCategoria();
                break;

            case 'updateCategoria':
                return $this::updateCategoria();
                break;

             case 'updateCategoriaData':
                 return $this::updateCategoriaData();
                 break;

            case 'ingresaArchivo':
                return $this::ingresaArchivo();
                break;

            case 'buscarXArchivoName':
            case 'findXcarpeta':
            case 'buscaArchivo':
            case 'listar-archivos':
                return $this::listarArchivos();
                break;

            case 'eliminaArchivo':
                return $this::eliminaArchivo();
                break;

            case 'invocaSubfolder':
                return $this::invocaSubfolder();
                break;

            case 'invocaSubSubFolder':
                return $this::invocaSubSubFolder();
                break;

            case 'all-files':
                return $this::allFiles();
                break;

            case 'muestraSubFolders':
                return $this::muestraSubFolders();
                break;

            case 'innFiles':
                # code...
                return $this::innFiles();
                break;


            default:
                return $this->error.'::::'.$this->id;
                break;
        }
    }

    private function innFiles()
    {

        $btn = "<a href='content-page.php?id=bGlzdGFyLWFyY2hpdm9z' class='btn btn-sm btn-success'>
                    Listar Archivos/Documentos
                </a>";

        if( $this::validaFile( $_FILES['archivo']['name'] ) )
            if( $this::procesaFTP( $this::path( $_POST['id_folder'], $_POST['id_sub_folder'] )  ) )
                if( $this->consultas->procesaArchivos( $this::changeNameFile( $_FILES['archivo']['name'] ),
                                                       addslashes( $_POST['descripcion']),
                                                       $this->fecha_hoy,
                                                       $this->yo,
                                                       1,
                                                       $this::path( $_POST['id_folder'], $_POST['id_sub_folder']),
                                                       $_POST['id_folder'],
                                                       $_POST['id_sub_folder']

                 ) )
                        { $sube = true;   }
                else    { $sube = false; $this->error = "Error en la DB"; }
            else        { $sube = false; $this->error = "Error FTP"; }
        else            { $sube = false; $this->error = "Error en Tipo de Archivo"; }


        if( $sube  )
        {

            $this->color = "success";
            $this->icon  = '<i class="far fa-thumbs-up"></i>';

            $ruta = $this::path( $_POST['id_folder'] , $_POST['id_sub_folder'] );

            $this::send( $this::changeNameFile( $_FILES['archivo']['name'] ), $ruta, $this::extraeUser( $this->yo )  );

            return $this::notificaciones( $this->color, $this->icon,'Subida Exitosa '.$btn."<br>" );

        }else{
            $this->color = "danger";
            $this->icon  = '<i class="far fa-thumbs-down"></i>';
            return $this::notificaciones( $this->color,$this->icon , $this->error.$btn  );
        }
    }


    private function send( $archivo = null , $ruta= null , $autor = null  )
    {
        $obj_mail = new mails( $archivo, $ruta, $autor );
        $obj_mail->getCode();

    }

    private function  extraeUser( $id_usuario = null )
    {
        if( require_once( 'users.class.php' ) )
            { $ob = new Users(); return $ob->extraeUser( $id_usuario );  }
        else return " Clase no encontrada";
    }



    private function muestraSubFolders()
    {

        $arr = $this->consultas->listaSubFolder( $this->yo, $_POST['id_folder'] );

        $sel = new Select( $arr['process'] , 'id_sub_folder','descripcion','id_sub_folder','Subcarpeta'  );

        return $sel->getCode();
    //    print_r( $_POST );


    }

    private function allFiles()
    {
        $data = array( '###archivos-ser###'    => null,
                       '###archivos-crecer###' => null,
                       '###folder-ser###'      => $this::foldersArchivos( 'ser' ),
                       '###folder-crecer###'   => $this::foldersArchivos( 'crecer' ),
                       '###archivos-estar###'   => $this::foldersArchivos( 'estar' )
                       ,

                    );

        return $this::despliegueTemplate( $data, 'all-files.html' );

    }

    private function foldersArchivos( $ruta = null )
    {
        $code = "";
        $arr = $this->consultas->listaArchivos( null, null, $ruta );
        $i = 0;
        foreach ($arr['process'] as $key => $value) {

             $data = array( '###ruta###'    =>  $value['ruta'] ,
                            '###archivo###' =>  $value['nombre_archivo'] ,
                            '###num###'     => $i +1
            );



            $code .= $this::despliegueTemplate( $data , 'folders-archivos.html' );

           //$code .= "{$value['ruta']}/{$value['nombre_archivo']}  <br>";
           $i ++;
        }

        return $code;
    }





    private function invocaSubSubFolder()
    {
        $arr = $this->consultas->subSubFolder();
        $sel = new Select( $arr['process'],
                               'id',
                               'descripcion',
                               'sub_sub_folder',
                               'Sub - Sub Carpeta ( o puede dejarla vacía )', null, 'x' );

        return $sel->getCode();
    }

    private function invocaSubfolder()
    {
        return $this::objeto();
    }

    private function objeto()
    {
      switch ($_POST['id_folder']) {
          case 1:
            $objeto = "<input type='hidden'
                              name = 'sub_folder'
                              id   = 'sub_folder'
                              value = ''>";
            break;

        case 2:
        case 3:

             $arr = $this->consultas->listaSubFolder( $_POST['id_folder'] );
             $sel = new Select( $arr['process'],
                               'id',
                               'descripcion',
                               'sub_folder',
                               'Sub Carpeta ( o puede dejarla vacía )', null, 'x' );

             $objeto = $sel->getCode();

            break;

          default:
              $objeto = null;
              break;
      }

        $data = array('###objeto###' => $objeto );
        return $this::despliegueTemplate( $data, 'objeto.html' );
    }

    private function eliminaArchivo()
    {

        //Array ( [id_archivo] => 3 [id] => eliminaArchivo )


        if( $this->consultas->cambiaEstado( 'archivo','estado',2, $_POST['id_archivo']) )
        {
            return $this::notificaciones(   'success',
                                            '<i class="far fa-thumbs-up"></i>',
                                            'Registro Eliminado').$this::listarArchivos() ;
        }else{
            return $this::notificaciones(   'danger',
                                            '<i class="far fa-thumbs-down"></i>',
                                            'Error al eliminar').$this::listarArchivos() ;
        }

        //print_r( $_POST );


    }


    private function buscarXArchivoName()
    {

        //Array ( [id] => buscarXArchivoName [archivo_name] => xzxz )

        print_r( $_POST );
    }

    /**
     * listarArchivos()
     * @param
     * @return string
     */
    private function listarArchivos()
    {

        $q = $this->consultas->listaCategorias();
        $arr = $this::trListaArchivos();

        if( $this->tipo_usuario != 1 )
                $btn = null;
        else    $btn = '<a class="btn btn-sm btn-light" href="content-page.php?id=aW5ncmVzYXItYXJjaGl2b3M=">
                            [ Ingresar Archivo]
                        </a>';


        if( $arr['total-recs']  > 0 )
        {

            $arrF =  $this->consultas->listaFolder();
            $selF = new Select( $arrF['process'], 'id', 'descripcion', 'id_folder', 'Carpeta', null, 'x' );

            $data = array(  '###tr###'              => $arr['code'] ,
                            '###total-recs###'      => $arr['total-recs'],
                            '@@@NAV-LINKS'          => $arr['nav-links'],
                            '###button-ingresar###' => $btn ,
                            '###buscar###'          => $this::buscar( "archivo_name" , "buscar por nombre de archivo" ),
                            '###select-folder####'  => $selF->getCode()
                        );

            return $this::despliegueTemplate( $data, 'tabla-archivos.html' );

        }else{

            $btn ="<a href='{$this->btn_listar}' class='btn btn-sm btn-success'>
                    Volver a Listar
                  </a>
            ";

            return $this::notificaciones('danger', '<i class="far fa-thumbs-down"></i>',"No hay Registros {$btn}");
        }
    }

    /**
    * listarArchivos()
    * @param
    * @return string
    */
    private function trListaArchivos()
    {
        $code ="";

        if( !$_POST['archivo_name'] )
        {
            if( !$_POST['id_folder']  )
            {

                if( $this->tipo_usuario == 1 )
                        $arr        = $this->consultas->listaArchivos();
                else    $arr        = $this->consultas->listaArchivosUsuarios( $this->yo );


                $utils      = new utiles($arr['sql']);
                $rs_dd      = $utils->show();
                $nav_links  = $rs_dd['nav_links'];
                $param      = $rs_dd['result'] ;


            }else{

                $destino = $this::sacaRuta(  $_POST['id_folder'] ,
                                             $_POST['sub_folder'],
                                             $_POST['sub_sub_folder'] );

                if( $this->tipo_usuario == 1 )
                     $arr        = $this->consultas->listaArchivos( null, null, $destino );
                else $arr        = $this->consultas->listaArchivosUsuarios( $this->yo );

                $nav_links  = "";
                $param      = $arr['process'] ;
            }

        }else{

                if( $this->tipo_usuario == 1 )
                    $arr        = $this->consultas->listaArchivos( null, $_POST['archivo_name'] );
                else
                    $arr        = $this->consultas->listaArchivosUsuarios( $this->yo, $_POST['archivo_name'] );


                $nav_links  = "";
                $param      = $arr['process'] ;
        }

            $i          = 0;

        foreach ($param as $key => $value) {

            if( $this->tipo_usuario !=  1 )
            {
              if( $this->yo != $value['subido_por'] )
                $actions = $i+1;
              else {
                $actions = '<button class="btn btn-sm btn-danger" id="elimina-archivo-'.$value['id'].'">
                                <i class="far fa-trash-alt"></i>
                            </button>';
              }

            }else{
                $actions = '<button class="btn btn-sm btn-danger" id="elimina-archivo-'.$value['id'].'">
                                <i class="far fa-trash-alt"></i>
                            </button>';
            }

            $subido_por = "{$value['nombres']} {$value['apaterno']}";

            $data = array(  '###descripcion###'  => $value['descripcion'],
                            '###archivo###'      => $value['nombre_archivo'],
                            '###fecha_subida###' => $this::arreglaFechas(  $value['fecha_subida']  ),
                            '###categoria###'    => $subido_por,
                            '###ruta###'         => $value['ruta'],
                            '###actions###'      => $actions,
                            '###id_archivo###'   => $value['id']    ,
                                                        );

            $code .= $this::despliegueTemplate( $data, 'tr-archivos.html' );

            $i++;
        }

        $out['code']        = $code;
        $out['total-recs']  = $arr['total-recs'];
        $out['nav-links']   = $nav_links;

        return $out;
    }

   /**
    *ingresaArchivo()
    * @param
    * @return string
     */
    private function ingresaArchivo()
    {
        $destino = $this::sacaRuta(  $_POST['id_folder'] ,
                                     $_POST['sub_folder'],
                                     $_POST['sub_sub_folder'] );

        if( $this::validaFile( $_FILES['archivo']['name'] ) )
            if( $this::procesaFTP( $destino ) )
                if( $this->consultas->procesaArchivos(  addslashes( $this::changeNameFile( $_FILES['archivo']['name'] ) ),
                                                        addslashes( $_POST['descripcion'] ),
                                                        $this->fecha_hoy,
                                                        $this->yo,
                                                        $_POST['id_categoria'] ,
                                                        $destino  ))
                {
                            $sube = true;
                }else{      $sube = false; $this->error = "Error en DB";   }
            else     {      $sube = false; $this->error = "Error en FTP";  }
        else  {             $sube = false; $this->error = "Error en Tipo de Archivo";}

        if( $sube )
        {
                 return $this::notificaciones(   'success',
                                                 '<i class="far fa-thumbs-up"></i>',
                                                 'Registro ingresado').$this::listarArchivos() ;
         }
        else {
                return $this::notificaciones(   'danger',
                                                '<i class="far fa-thumbs-down"></i>',
                                                 $this->error).$this::listarArchivos() ;
         }
    }


    private function sacaRuta(  $id_folder      = null,
                                $sub_folder     = null,
                                $sub_sub_folder = null )
    {
            $folder1 = "";
            $folder2 = "";
            $folder3 = "";

            $arr1 = $this->consultas->listaFolder( $id_folder );

            foreach ($arr1['process'] as $key => $v1) {
                $folder1 .= $v1['descripcion'];
            }

            if( $sub_folder == '' )
                    { $folder2 .= ""; }
            else    {
                $arr2 = $this->consultas->listaSubFolder( null,  $sub_folder );

                foreach ($arr2['process'] as $key => $v2) {
                    $folder2 .= $v2['descripcion'];
                }
            }

            if( $sub_sub_folder == ''  )
                    { $folder3 .= ""; }
            else    {
                $arr3 = $this->consultas->subSubFolder( $sub_sub_folder);

                foreach ($arr3['process'] as $key => $v3) {
                    $folder3 .= $v3['descripcion'];
                }
            }

            if( $folder2 == '' )
                    $f2 = "";
             else   $f2 = "/{$folder2}";

             if( $folder3 == '' )
                    $f3 = "";
            else    $f3 = "/{$folder3}";

            return "{$folder1}{$f2}{$f3}";
    }

    private function updateCategoriaData()
    {

        if( $this->consultas->procesaCategorias( addslashes( $_POST['name_categoria'] ) ,$_POST['id_categoria'] ) )
        {
            return $this::notificaciones(   'success',
                                            '<i class="far fa-thumbs-up"></i>',
                                            'Registro Actualizado').$this::tablaCategorias() ;
        }
        else
        {
            return $this::notificaciones(   'danger',
                                            '<i class="far fa-thumbs-down"></i>',
                                            'Error al Actualizar').$this::tablaCategorias() ;
        }
    }

    private function updateCategoria()
    {
        $code = "";
        $arr = $this->consultas->listaCategorias( $_POST['id_categoria'] );

        foreach ($arr['process'] as $key => $value)
        {
            $hidden = "<input type='hidden'
                               name = 'id_categoria'
                               id   = 'id_categoria'
                               value = '{$_POST['id_categoria']}'>";

            $data = array(  '###title###'           => 'Formulario de Categorias ( Edición ) ',
                            '###name_categoria###'  => $value['descripcion'],
                            '###button-id###'       => 'update',
                            '###hidden###'          => $hidden
        );

            $code .= $this::despliegueTemplate( $data, 'form-categoria.html' );
        }

        return $code;
    }

    private function eliminaCategoria()
    {
        //Array ( [id_categoria] => 1 [id] => eliminaCategoria )
        if( $this->consultas->cambiaEstado( 'categoria', 'estado', 0 , $_POST['id_categoria']) )
        {
            return $this::notificaciones(   'success',
                                            '<i class="far fa-thumbs-up"></i>',
                                            'Registro Eliminado').$this::tablaCategorias() ;
        }else{
            return $this::notificaciones(   'danger',
                                            '<i class="far fa-thumbs-down"></i>',
                                            'Error al eliminar').$this::tablaCategorias() ;
        }
    }


    private function ingreseCategoria()
    {

        if( $this->consultas->procesaCategorias( $_POST['name_categoria'] ) )
        {
            return $this::notificaciones(   'success',
                                            '<i class="far fa-thumbs-up"></i>',
                                            'Registro Ingresado').$this::tablaCategorias() ;
        }else{
            return $this::notificaciones(   'danger',
                                            '<i class="far fa-thumbs-up"></i>',
                                            'Error al ingresar / Registro repetido').$this::tablaCategorias() ;
        }
    }

    private function categorias(  )
    {
        $data = array( '###lista-categorias###'  => $this::tablaCategorias(),
                        '###form-categorias###'  => $this::formCategoria(   'Formulario de Categorias ( Ingreso )' ,
                                                                            null,
                                                                            null,
                                                                           'send'));

       return $this::despliegueTemplate( $data, 'categorias.html' );
    }


    private function tablaCategorias()
    {
        $arr = $this::trTablaCategorias();

        if( $arr[ 'total-recs' ]  > 0 )
        {
            $data = array( '###tr###'  => $arr['code'] );

            return $this::despliegueTemplate( $data, 'tabla-categorias.html' );
        }
        else{

            return $this::notificaciones(   'danger',
                                            '<i class="far fa-thumbs-down"></i>',
                                            'No hay registros de categorias');
        }
    }

    private function trTablaCategorias()
    {
        $arr = $this->consultas->listaCategorias();

        $code = "";

        foreach ($arr['process'] as $key => $value) {

            $data = array(  '###id_categoria###'  => $value['id'],
                            '###descripcion###'   => $value['descripcion']      );

            $code .= $this::despliegueTemplate( $data, 'tr-categorias.html' );
        }

        $out['code'] = $code;
        $out['total-recs'] = $arr['total-recs'];

    return $out;
    }


    /**
     *
     */
    private function formCategoria( $title = null, $name_categoria= null , $hidden = null ,  $button_id = null)
    {
        $data = array(  '###title###'           => $title,
                        '###name_categoria###'  => $name_categoria,
                        '###button-id###'       => $button_id,
                        '###hidden###'          => $hidden
        );

        return $this::despliegueTemplate( $data, 'form-categoria.html' );
    }

    /**
     * ingresaArchivos()
     *
     * @param
     * @return string
     */
    private function ingresaArchivos()
    {


     $arrF = $this->consultas->listaFolder( $this->yo );

     $sel1 = new Select( $arrF['process'] , 'id_folder','nameFolder', 'id_folder','Carpeta'  );

     $data = ['###select-folder####' => $sel1->getCode() ,
              '###descripcion###'    => null          ,
              '###hidden###'         => null ,
              '###title###'          => 'Ingreso de Archivos',
              '###target###'         => 'innFiles'   ,
              '###button-id###'      => 'send',
              '###id_usuario###'     => $this->yo           ];

     return $this::despliegueTemplate( $data , 'form-files.html' );
   }


    /**
     * PATH(): DETERMINA EL FOLDER  O PATH DEL ARCHIVO
     */
    private function path( $id_folder = null , $id_subfolder = null )
    {
        $arr    = $this->consultas->listaFolder( $this->yo, $id_folder );

        $folder = "";
        $sub_folder = "";

        foreach ($arr['process'] as $key => $value) {
            $folder .= $value['nameFolder'];
        }

        foreach ($this->consultas->subFolderSimple( $id_subfolder ) as $key => $v) {
            # code...
            $sub_folder .= $v['descripcion'];
        }

        return "/{$folder}/{$sub_folder}";
    }

    /**
     * procesaFTP() :  proceso de subida de archivo mediante FTP
     * @param
     * @return boolean
     */
    private function procesaFTP( $destino = null )
    {
      $conn = ftp_connect($this->ftpHost,$this->ftpPort);

      if ( $conn )
        if( ftp_login( $conn , $this->ftpUser, $this->ftpPass ) )
          if( ftp_chdir($conn, $this->ftpFolder.$destino ))
              if(ftp_put($conn , $this::changeNameFile(  $_FILES["archivo"]["name"] ),
                                                         $_FILES["archivo"]["tmp_name"],
                                                         FTP_BINARY))
                    return  true;
              else 	return false;
              else 	return false;
              else 	return false;
              else 	return false;
    }

  /**
     * validaFile(): validacion del tipo de archivo que es ingresado al sistema. sólo acepta los desplegados en el switch
     * @param string fileName
     * @return boolean
     */
	private  function validaFile( $fileName=null )
  	{
        $arr = $this::separa( $fileName,".");

	    if(  count( $arr ) > 1 )
	    {
	      switch ( $arr[1] ) {
	              //case 'jpg':
	              //case 'png':
	              //case 'JPG':
                  //case 'jpeg':
                  //case 'doc':
                  //case 'docx':
                  case 'pdf':
                  //case 'xlsx':
                  //case 'ppt':
                  //case 'pptx':
                 // case 'mp3':
                 // case 'mp4':

	                return true;
	                break;

	              default:
	                return false;
	                break;
	            }
	    }else
	        return false;
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
