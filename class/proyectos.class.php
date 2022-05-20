<?php
/**
* @author Claudio Guzman Herrera
* @version 1.0
*/
class Proyectos
{
  private $id;
  private $yo;
  private $consultas;
  private $template;
  private $error;
  private $token;
  private $msg;
  private $btn;

  function __construct( $id = null )
  {
    $oConf                = new config();
    $cfg                  = $oConf->getConfig();
    $db                   = new mysqldb( 	$cfg['base']['dbhost'],
                                          $cfg['base']['dbuser'],
                                          $cfg['base']['dbpass'],
                                          $cfg['base']['dbdata'] );

    $this->yo             = $_SESSION['yo'];
    $this->tipo_usuario   = $_SESSION['tipo_usuario'];
    $this->id             = $id;
    $this->error          = "Esto es un Error";
    $this->consultas 			= new querys( $db );
    $this->template  			= new template();
    $this->ruta      			= $cfg['base']['template'];
    $this->token 		      =  date("YmdHis");
    $this->fecha_hoy 		  =  date("Y-m-d");
  }

  private function control()
  {
    switch ($this->id) {

      case 'sacaArchivo':
        return $this::tagArchivo("archivo");
        break;

      case 'ingresa_proyecto':
        return $this::ingresaProyecto();
        break;

      case 'crear-proyecto':

        return $this::crearProyecto();
        break;

      case 'findProyectos':
      case 'lista-proyectos':

        return $this::listaProyectos();
        break;

      case 'editaProyecto':
        // code...
        return $this::editaProyecto();
        break;

      case 'edita_proyecto':
        return $this::edita_proyecto();
        break;

      default:
        return " {$this->error} para id : { $this->id } ";
        break;
    }
  }

  private function edita_proyecto()
  {
    $this->btn = "<a href='content-page.php?id=bGlzdGEtcHJveWVjdG9z' class='btn btn-success btn-sm'>
                    Listar Proyectos </a>";

    if( $_POST['archivo'] )
    {
      if( $this->consultas->procesaProyecto( $_POST['nombre_proyecto'],
                                             $_POST['descripcion'] ,
                                             $this->fecha_hoy,
                                             $this->fecha_hoy,
                                             $_POST['fecha_inicio'],
                                             $_POST['fecha_entrega'],
                                             $_POST['id_etapa'],
                                             $_POST['id_estado_etapa'],
                                             $_POST['archivo'],
                                             $_POST['responsable'],
                                             $this->token,
                                             $_POST['tipo_proyecto'],
                                             $this->yo,
                                             $_POST['id_proyecto'] ))
      {   $sube       = true;
          $this->msg  = "Registro Actualizado";
          $this->icon = '<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>';
      }
      else{ $sube       = false;
            $this->msg  = "Error en Consulta";
            $this->icon ='<i class="fa fa-thumbs-o-down" aria-hidden="true"></i>'; }
    }
    else{

      require_once("ftp.class.php");
      $ob_ftp = new FTP( "archivo", $this->token );

      if( $ob_ftp->validaFile( $_FILES["archivo"]["name"] ) )
        if( $ob_ftp->procesaFTP() )
          if( $this->consultas->procesaProyecto( $_POST['nombre_proyecto'],
                                                 $_POST['descripcion'] ,
                                                 $this->fecha_hoy,
                                                 $this->fecha_hoy,
                                                 $_POST['fecha_inicio'],
                                                 $_POST['fecha_entrega'],
                                                 $_POST['id_etapa'],
                                                 $_POST['id_estado_etapa'],
                                                 $ob_ftp->changeNameFile( $_FILES["archivo"]["name"] ),
                                                 $_POST['responsable'],
                                                 $this->token,
                                                 $_POST['tipo_proyecto'],
                                                 $this->yo,
                                                 $_POST['id_proyecto']                      ))
          {
                 $sube = true;  $this->msg ="Registro Actualizado"; $this->icon ='<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>';
          }else{ $sube = false; $this->msg ="Error en Consulta";            $this->icon ='<i class="fa fa-thumbs-o-down" aria-hidden="true"></i>'; }
        else   { $sube = false; $this->msg ="Error FTP";                    $this->icon ='<i class="fa fa-thumbs-o-down" aria-hidden="true"></i>'; }
        else   { $sube = false; $this->msg ="Error Tipo de Archivo";        $this->icon ='<i class="fa fa-thumbs-o-down" aria-hidden="true"></i>'; }

    }

    if( $sube )
          return $this::notificaciones( "success", $this->icon, "{$this->msg} {$this->btn}" );
    else  return $this::notificaciones( "danger", $this->icon,  "{$this->msg} {$this->btn}" );
  }


/**
 * editaProyecto(): formulario de edicion de proyectos
 * @return string
 */
  private function editaProyecto()
  {
    $code = "";

    $arr = $this->consultas->listaProyectos( $_POST['id_proyecto'] );

    foreach ($arr['process'] as $key => $value) {

      $hidden = '<input type="hidden" name = "id_proyecto" id="id_proyecto" value="'.$value['id'].'" >';

      $a1 = $this->consultas->tipoProyecto();
      $a2 = $this->consultas->etapas();
      $a3 = $this->consultas->estadoEtapas();

      $selTipoProyecto = new Select( $a1['process'],'id','descripcion','tipo_proyecto','Tipo de Proyecto', $value['id_tipo_proyecto'] );
      $selEtapas       = new Select( $a2['process'],'id','descripcion','id_etapa','Etapa', $value['etapa'] );
      $selEstadoEtapas = new Select( $a3['process'],'id','descripcion','id_estado_etapa','Estado de Etapa', $value['estado_etapa'] );

      $DATA = ['###SELECT-TIPO-PROYECTO###' => $selTipoProyecto->getCode()  ,
               '###select-etapa####'        => $selEtapas->getCode(),
               '###select-estado-etapa####' => $selEstadoEtapas->getCode(),
               '###title###'                => 'Edición',
               '###hidden###'               => $hidden,
               '###valor_id###'             => 'edita_proyecto',
               '###button_name###'          => 'update',
               '###nombre_proyecto###'      => $value['titulo'],
               '###responsable###'          => $value['responsable'],
               '###descripcion###'          => $value['descripcion'],
               '###fecha_inicio###'         => $value['fecha_inicio'],
               '###fecha_entrega###'        => $value['fecha_entrega'],
               '###archivo###'              => $this::tagArchivo("archivo",true,$value['archivo']),
               '###advertencia###'          => $this::notificaciones("danger",
                                                                     '<i class="fa fa-exclamation-circle" aria-hidden="true"></i>',
                                                                     "<strong>Nota</strong> <hr> Todos los campos son obligatorios")];

     $code .= $this::despliegueTemplate( $DATA , "form-proyectos.html" );

    }

    return $code;
  }


  /**
  * ingresaProyecto: ingresa data del proyecto a la DB y sube Archivo
  * @return string
  */
  private function ingresaProyecto()
  {
    $this->btn = "<a href='content-page.php?id=bGlzdGEtcHJveWVjdG9z' class='btn btn-success btn-sm'>
                    Listar Proyectos </a>";

    require_once("ftp.class.php");
    $ob_ftp = new FTP( "archivo", $this->token );

    if( $ob_ftp->validaFile( $_FILES["archivo"]["name"] ) )
      if( $ob_ftp->procesaFTP() )
        if( $this->consultas->procesaProyecto( $_POST['nombre_proyecto'],
                                               $_POST['descripcion'] ,
                                               $this->fecha_hoy,
                                               $this->fecha_hoy,
                                               $_POST['fecha_inicio'],
                                               $_POST['fecha_entrega'],
                                               $_POST['id_etapa'],
                                               $_POST['id_estado_etapa'],
                                               $ob_ftp->changeNameFile( $_FILES["archivo"]["name"] ),
                                               $_POST['responsable'],
                                               $this->token,
                                               $_POST['tipo_proyecto'],
                                               $this->yo                      ))
        {
               $sube = true;  $this->msg ="Archivo Subido Correctamente"; $this->icon ='<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>';
        }else{ $sube = false; $this->msg ="Error en Consulta";            $this->icon ='<i class="fa fa-thumbs-o-down" aria-hidden="true"></i>'; }
      else   { $sube = false; $this->msg ="Error FTP";                    $this->icon ='<i class="fa fa-thumbs-o-down" aria-hidden="true"></i>'; }
      else   { $sube = false; $this->msg ="Error Tipo de Archivo";        $this->icon ='<i class="fa fa-thumbs-o-down" aria-hidden="true"></i>'; }

      if( $sube )
            return $this::notificaciones( "success", $this->icon, "{$this->msg} {$this->btn}" );
      else  return $this::notificaciones( "danger",  $this->icon, "{$this->msg} {$this->btn}" );
  }

  /**
   * crearProyecto(): formulario de creacion de proyectos
   * @return string
   */
  private function crearProyecto()
  {
     $a1 = $this->consultas->tipoProyecto();
     $a2 = $this->consultas->etapas();
     $a3 = $this->consultas->estadoEtapas();

     $selTipoProyecto = new Select( $a1['process'],'id','descripcion','tipo_proyecto','Tipo de Proyecto' );
     $selEtapas       = new Select( $a2['process'],'id','descripcion','id_etapa','Etapa' );
     $selEstadoEtapas = new Select( $a3['process'],'id','descripcion','id_estado_etapa','Estado de Etapa' );

     $DATA = ['###SELECT-TIPO-PROYECTO###' => $selTipoProyecto->getCode()  ,
              '###select-etapa####'        => $selEtapas->getCode(),
              '###select-estado-etapa####' => $selEstadoEtapas->getCode(),
              '###title###'                => 'Ingreso',
              '###hidden###'               => null,
              '###valor_id###'             => 'ingresa_proyecto',
              '###button_name###'          => 'send',
              '###nombre_proyecto###'      => null,
              '###responsable###'          => null,
              '###descripcion###'          => null,
              '###fecha_inicio###'         => null,
              '###fecha_entrega###'        => null,
              '###archivo###'              => $this::tagArchivo("archivo"),
              '###advertencia###'          => $this::notificaciones("danger",
                                                                    '<i class="fa fa-exclamation-circle" aria-hidden="true"></i>',
                                                                    "<strong>Nota</strong> <hr> Todos los campos son obligatorios")];

     return $this::despliegueTemplate( $DATA, 'form-proyectos.html' );
  }

 /**
 * tagArchivo()
 */
  private function tagArchivo( $nombre_archivo = null, $existe = null, $fileName = null )
  {
     if( !$existe )
    {  $data = ['###archivo###' => $nombre_archivo ];
       return $this::despliegueTemplate( $data, "tag-archivo.html" );
    }else{

      $data = ['###fileName###' => $fileName ];
      return $this::despliegueTemplate( $data , "archivo-form.html" );
    }
  }

  private function buscar()
  {
    $a3               = $this->consultas->estadoEtapas();
    $selEstadoEtapas  = new Select( $a3['process'],'id','descripcion','id_estado_etapa','Estado de Etapa' );

    $data = ['###select-estado###' => $selEstadoEtapas->getCode() ];
    return $this::despliegueTemplate( $data, "buscar-full.html" );
  }


  private function findProyectos()
  {
    print_r( $_POST );

    /*
    Array ( [id] => findProyectos
            [buscar_action] => 1
            [codigo] => qq
            [titulo] => qq
            [id_estado_etapa] => 1
            [fecha_inicio] => 2020-07-24
            [fecha_entrega] => 2020-07-24 )
    */

  }


  private function listaProyectos()
  {
      //return "LISTA EN CONSTRUCCION";

      $arr = $this::trListaProyectos( );

      if( $arr['total-recs'] > 0  )
      {
        $data = [ '###tr###'          => $arr['code'],
                  '###total-recs###'  => $arr['total-recs'],
                  '###nav-links###'   => $arr['nav-links'],
                  '###buscar###'      => $this::buscar()
                  ];
        return $this::despliegueTemplate( $data , 'lista-proyectos.html' );

      }else{

        return $this::notificaciones('danger','','Elementos no ingresados / No Existentes...<br>');

      }
  }

  /**
   * trListaProyectos(): listado de proyectos desde DB
   * @return array()
   */
  private function trListaProyectos()
  {
    $code       = "";

    if( !$_POST['buscar_action']  )
    {
      $arr        = $this->consultas->listaProyectos();
      $utils      = new utiles($arr['sql']);
      $rs_dd      = $utils->show();
      $nav_links  = $rs_dd['nav_links'];
      $param      = $rs_dd['result'] ;

    }else{

      $arr        = $this->consultas->listaProyectos( null,
                                                      1,
                                                      $_POST['codigo'],
                                                      $_POST['titulo'],
                                                      $_POST['id_estado_etapa'],
                                                      $_POST['fecha_inicio'],
                                                      $_POST['fecha_entrega']  );
      $nav_links  = null;
      $param      = $arr['process'] ;
    }

    $i = 0;

    foreach ($param as $key => $value) {

      $AUTOR = "{$value['nombres']} {$value['apaterno']} {$value['amaterno']}";

      if( $this->yo == $value['id_usuario'] )
            $edit = "<a href='#' class='btn btn-sm btn-secondary' id='edita-proyecto-{$value['id']}' >
                        <i class='far fa-edit'></i> Edit
                        </a>";
      else  $edit = null;

      $data = ['###num###'              => $i+1,
               '###proyecto###'         => $value['titulo'],
               '###f_Inicio###'         => $this::arreglaFechas( $value['fecha_inicio'] ),
               '###f_Entrega###'        => $this::arreglaFechas( $value['fecha_entrega']),
               '###tipo###'             => $value['nameTipoProyecto'],
               '###responsable###'      => $value['responsable'],
               '###codigo###'           => $value['codigo'],
               '###archivo###'          => $value['archivo'],
               '###codigo###'           => $value['codigo'],
               '###id###'               => $value['id'],
               '###estado###'           => $value['nameEstadoEtapa'],
               '###edit###'             => $edit,
               '###modal###'            => $this::modal( "detalle-proyecto-{$value['id']}",
                                                         '<i class="fas fa-arrow-circle-right"></i>',
                                                         "Detalle del Proyecto {$value['titulo']}",
                                                         $this::content(  $value['titulo'] ,
                                                                          $value['fecha_creacion'],
                                                                          $value['fecha_modificacion'],
                                                                          $AUTOR,
                                                                          $value['responsable'],
                                                                          $value['descripcion'],
                                                                          $value['nameEtapa'],
                                                                          $value['fecha_inicio'],
                                                                          $value['fecha_entrega'],
                                                                          $value['nameEstadoEtapa'],
                                                                          $value['archivo'],
                                                                        )),
     ];

     $code .= $this::despliegueTemplate( $data , 'tr-proyectos.html' );
      $i++;
    }

    $out['code']        = $code;
    $out['total-recs']  = $arr['total-recs'];
    $out['nav-links']   = $nav_links;
    $out['sql']         = $arr['sql'];

    return $out;
  }

  /**
   * content(): contennido del modal
   * @return string
   */
   private function content( $titulo              = null,
                             $fecha_creacion      = null ,
                             $fecha_modificacion  = null,
                             $autor               = null,
                             $responsable         = null,
                             $descripcion         = null,
                             $etapa               = null,
                             $fecha_inicio        = null ,
                             $fecha_entrega       = null,
                             $estado_etapa        = null ,
                             $archivo             = null     )
   {
      $DATA = ['###titulo###'             => $titulo ,
               '###fecha_creacion###'     => $this::arreglaFechas( $fecha_creacion ),
               '###fecha_modificacion###' => $this::arreglaFechas( $fecha_modificacion ),
               '###autor###'              => $autor,
               '###responsable###'        => $responsable,
               '###detalles###'           => $descripcion,
               '###etapa###'              => $etapa,
               '###fecha_inicio###'       => $this::arreglaFechas( $fecha_inicio  ) ,
               '###fecha_entrega###'      => $this::arreglaFechas( $fecha_entrega ) ,
               '###estado_etapa###'       => $estado_etapa,
               '###archivo###'            => $archivo,
              ];

      return $this::despliegueTemplate( $DATA , 'detalle-proyecto.html' );
   }

  /**
   * modal(): extrae un modal desde una Clase
   *
   *@param string target
   *@param string img
   *@param string title
   *@param string content
   */
  private function modal( $target = null,$img = null, $title = null, $content = null )
  {
      require_once("modal.class.php");

      $ob_modal = new Modal($target ,$img , $title , $content );
      return $ob_modal->salida();
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
  * separa(): metodo que separa elementos distanciados por simbolos
  * @param string cadena
  * @param string simbolos
  * @return array
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

  /**
  *
  */
  public function getCode()
  {
    return $this::control();
  }
}
 ?>
