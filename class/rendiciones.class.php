<?php
/**
 *@author Ing. Claudio Guzmán Herrera
 *@version 1.0
 *@copyright SOCMA Ingenieria Limitada
 */
class Rendiciones
{
  private $id;
  private $yo;
  private $tipo_usuario;
  private $id_user;
  private $ruta;
  private $consultas;
  private $template;
  private $error;
  private $ftpUser;
  private $ftpPass;
  private $ftpTarget;
  private $fecha_hora_hoy;
  private $token;
  private $email_admin;

  /**
   * metodo constructor
   */
  function __construct( $id = null )
  {
    $oConf    = new config();
	  $cfg      = $oConf->getConfig();
	  $db       = new mysqldb( 	$cfg['base']['dbhost'],
															$cfg['base']['dbuser'],
															$cfg['base']['dbpass'],
															$cfg['base']['dbdata'] );
    $this->id                 = $id;
    $this->consultas 					= new querys( $db );
    $this->template  					= new template();
    $this->ruta      					= $cfg['base']['template'];
    $this->error              = "Modulo no definido o no Desarrollado para <strong>{$this->id}</strong>";
    $this->yo                 = $_SESSION['yo'];
    $this->rut                = $_SESSION['rut'];
    $this->tipo_usuario       = $_SESSION['tipo_usuario'];
    $this->email_admin        = "claudio.guzman@socma.cl";
    $this->fecha_hora_hoy     =  date("Y-m-d H:i:s");
    $this->fecha_hoy          =  date("Y-m-d");
    $this->token              =  date("YmdHis");

    $this->btn_ver            = '<a href="content-page.php?id=aW5pY2lv"
                                    class="btn btn-sm btn-secondary"
                                    name="a"
                                    id="listar-rendiciones">
                                          <i class="far fa-check-circle"></i> Ver Rendiciones
                                </a>';
  }

  /**
   * control(): metodo principal de entrada, entrega la salida del resto de los métodos definidos
   *
   * @return string
   */
  private function control()
  {
    switch ($this->id) {

      case 'buscarRendiciones':
      case 'listarRendiciones':
      case 'inicio':
      case 'mis-rendiciones':
        return $this::misRendiciones();
        break;

      case 'form-viaje':
        return $this::formViaje();
        break;

      case 'crearRendiciones':
        return $this::crearRendiciones();
        break;

      case 'ingresaDataInicialRencion':
        return $this::ingresaDataInicialRencion();
        break;

      case 'ingresa-files-rendicion':
        return $this::ingresaFilesRendicion();
        break;

      case 'eliminaArchivoRendicion':
        return $this::eliminaArchivoRendicion();
        break;

     case 'verDetalleRendicion':
       return $this::verDetalleRendicion();
       break;

      case 'revisarRendicion':
        return $this::revisarRendicion();
        break;

      case 'ingresa-observacion':
        return $this::ingresaObservacion();
        break;

      case 'editarRendicion':
        return $this::editarRendicion();
        break;

      case 'acepto-terminos':
        return $this::aceptoTerminos();
        break;

      case 'editaDataInicialRendicion':
        return $this::editaDataInicialRendicion();
        break;

      case 'eliminarDetallerendicion':
        return $this::eliminarDetallerendicion();
        break;

      default:
          return $this->error;
        break;
    }
  }

 private function eliminarDetallerendicion()
 {

   if( $this->consultas->eliminarDetallerendicion(  $_POST['id_detalle'] ) )
          $msg = "<strong>REGISTRO ELIMINADO</strong>";
   else   $msg="<strong>ERROR AL ELIMINAR</strong>";


   $arr = $this::espacioFilasLlenas( $_POST['codigo_rendicion'] );

   $tabla = '<table class="table table-dark table-responsive table-hover table-striped">
             <thead>
               <tr>
                 <th>Item</th>
                 <th>Detalle</th>
                 <th>Tipo Documento</th>
                 <th>Número Documento</th>
                 <th>Fecha</th>
                 <th>Monto($)</th>
                 <th></th>
               </tr>
             </thead>
             <tbody class="espacio-filas">
                 '.$arr['code'].'
             </tbody>
           </table>';

   return "{$msg} {$tabla}";
 }

  public function dataRendicion( $codigo_rendicion = null  )
  {
    $arr = $this->consultas->rendiciones( null, $codigo_rendicion );
    $out = array();

    foreach ($arr['process'] as $key => $value) {
      $out['apellido_paterno'] = $value['apellido_paterno'];
      $out['apellido_materno'] = $value['apellido_materno'];
      $out['nombres']          = $value['nombres'];
      $out['motivo']           = $value['descMotivo'];
      $out['fecha_ingreso']    = $this::arreglaFechas( $value['fecha_ingreso'] );
      $out['acompanante']      = $value['acompanante'];
      $out['lugar']            = $value['lugar'];
      $out['firma']            = $value['firma'];
      $out['rut']              = $value['rut'];
      $out['fecha_viaje']      = $this::arreglaFechas( $value['fecha_viaje'] );
      $out['monto_asignado']   = $value['monto_asignado'];
    }
    return $out;
  }

 public function dataDetalleRendicion( $codigo_rendicion = null )
 {
   $out = array();
   $i   = 0;
   $arr = $this->consultas->detalle_rendicion( $codigo_rendicion );

   foreach ($arr['process'] as $key => $value) {

     $out[$i]['descItem']           = $value['descItem'];
     $out[$i]['detalle']            = $value['detalle'];
     $out[$i]['descTipoDocumento']  = $value['descTipoDocumento'];
     $out[$i]['num_documento']      = $value['num_documento'];
     $out[$i]['fecha']              = $value['fecha'];
     $out[$i]['monto']              = $value['monto'];

     $i++;
   }

  // $out['total-recs'] = $arr['total-recs'];

   return $out;
 }

  /**
   * editaDataInicialRendicion(): edicion de rendicion
   *
   * @return string
   */
  private function editaDataInicialRendicion()
  {
    if( $this->consultas->actualizaRendicion( $_POST['codigo'] ,
     																		      $_POST['monto_asignado'] ,
     																		      $_POST['id_motivo'] ,
     																		      $_POST['lugar'] ,
     																		      $_POST['acompanante'] ,
     																		      $_POST['fecha_viaje']  ) )
    {
      if($this::procesamientoDelDetalleRendicion( $_POST['codigo'] ) )
            $ok = true;
      else  $ok = false;

      $ob_mails = new mails( 'msgUpdate',
                             $this->email_admin,
                             "Rendición <strong>{$_POST['codigo']} ACTUALIZADA</strong>, favor Revisar<br>".$this::msgRendicion( $_POST['codigo'] )  );

      if( $ob_mails->getCode() )
            $ok = true;
      else  $ok = false;


      return $this::notificaciones( 'success',
                                    '<i class="far fa-thumbs-up"></i>',
                                    "<strong>REGISTRO ACTUALIZADO</strong> {$this->btn_ver}" );  }
    else{

      return "<strong>ERROR AL ACTUALIZAR</strong>";
    }
  }

  /**
   * procesamientoDelDetalleRendicion(): actualiza o ingresa detalles nuevos
   * @return boolean
   */
  private function procesamientoDelDetalleRendicion( $codigo_rendicion = null )
  {
    $code = "";
    $j = 0;

    $arr_monto_detalle        = $this::separa( $_POST['monto_detalle'],'&' );
    $arr_detalle              = $this::separa( $_POST['detalle'],'&' );
    $arr_fecha_detalle        = $this::separa( $_POST['fecha_detalle'],'&' );
    $arr_tipo_documento       = $this::separa( $_POST['tipo_documento'],'&' );
    $arr_num_documento        = $this::separa( $_POST['num_documento'],'&' );
    $arr_id_detalle_rendicion = $this::separa( $_POST['id_detalle_rendicion'],'&' );
    $arr_id_item              = $this::separa( $_POST['id_item'],'&' );

    for ($i=0; $i < count( $arr_monto_detalle ) ; $i++) {
      // code...
      $aux_monto_detalle        = $this::separa( $arr_monto_detalle       [$i] , '=' );
      $aux_detalle              = $this::separa( $arr_detalle             [$i] , '=' );
      $aux_fecha_detalle        = $this::separa( $arr_fecha_detalle       [$i] , '=' );
      $aux_tipo_documento       = $this::separa( $arr_tipo_documento      [$i] , '=' );
      $aux_num_documento        = $this::separa( $arr_num_documento       [$i] , '=' );
      $aux_id_detalle_rendicion = $this::separa( $arr_id_detalle_rendicion[$i] , '=' );
      $aux_id_item              = $this::separa( $arr_id_item             [$i] , '=' );

      $arr = $this->consultas->detalle_rendicion( null, $aux_id_detalle_rendicion[1]  );

      if( $this->consultas->procesamientoDelDetalleRendicion( $aux_id_item[1],$aux_detalle[1],
                                                              $aux_tipo_documento[1],$aux_fecha_detalle[1],
                                                              $aux_monto_detalle[1],
                                                              $codigo_rendicion,$this->yo,
                                                              $aux_num_documento[1],
                                                              $aux_id_detalle_rendicion[1]
                                                              ))
      { $j++; }
    }

    if( $j > 0 )
          return true;
    else  return false;

  }

  /**
   * editarRendicion(): edicion de rendicion, formuario de despliegueTemplate
   * @return string
   */
  private function editarRendicion()
  {
  //  print_r( $_POST );

    $arr  = $this->consultas->listaRendiciones( null,null, $_POST['codigo'] );
    $code = "";

    //Array ( [id] => editarRendicion [codigo] => 20201127153722 )
    foreach ($arr['process'] as $key => $value) {

      $sel_motivo = new Select( $this->consultas->motivos(),
                                'id',
                                'descripcion',
                                'id_motivo',
                                'Motivo', $value['id_motivo']);

      $rut = $this::separa( $value['rut'],'-' );

      $detalles = $this::espacioFilasLlenas( $value['codigo'] );

      $SALDO_EMPRESA                        = $value['monto_asignado'] - $detalles['suma'];
      $SALDO_FUNCIONARIO                    = $detalles['suma'] - $value['monto_asignado'] ;

      $data = ['###accion###'               => 'Edición',
               '###codigo###'               => $value['codigo'] ,
               '###token###'                => $this->token ,
               '###estado###'               => $value['descEstado'],
               '###rut###'                  => $this::separa_miles( $rut[0] )."-".$rut[1],
               '###lugar###'                => $value['lugar'] ,
               '###acompanante###'          => $value['acompanante'] ,
               '###fecha_viaje###'          => $value['fecha_viaje'] ,
               '###fecha###'                => $this::arreglaFechas( $value['fecha_ingreso'] ),
               '###monto_asignado###'       => $value['monto_asignado'] ,
               '###select-motivo###'        => $sel_motivo->getCode(),
               '###espacio-filas###'        => $detalles['code'],
               '###total_a_rendir###'       => $detalles['suma'],
               '###SALDO_EMPRESA###'        => $SALDO_EMPRESA,
               '###SALDO_FUNCIONARIO###'    => $SALDO_FUNCIONARIO,
               '###yo###'                   => $this->yo,
               '###my-state###'             => $value['id_estado'],
               '###lista-files###'          => $this::tabla_archivos( $value['codigo'] ),
               '###modal###'                => $this::modal('terminos',
                                                            '<i class="fas fa-stream"></i>',
                                                            'Términos y Condiciones',
                                                            $this::terminos() ) ];

      $code .= $this::despliegueTemplate( $data, 'formulario-rendicion-modificacion.html' );
    }

    return $code;
  }

  /**
   * espacioFilasLlenas(): filas que contienen la data del detalle de rendicion
   * @param string codigo
   * @return string
   */
  private function espacioFilasLlenas( $codigo = null )
  {

    $code = "";
    $arr = $this->consultas->detalle_rendicion( $codigo );

    $suma = 0;
    foreach ($arr['process'] as $key => $value) {

      $sel_item = new Select( $this->consultas->items(),
                                'id',
                                'descripcion',
                                'id_item',
                                'Items', $value['id_item']);

      $sel_tipo_documento = new Select( $this->consultas->tipo_documento(),
                                'id',
                                'descripcion',
                                'tipo_documento',
                                'Tipo.Doc.', $value['tipo_documento']);


      $hidden ="<input type='hidden' class='id_detalle_rendicion'
                       name='id_detalle_rendicion'
                       id='id_detalle_rendicion'
                       value='".$value['id']."'>";

      $boton_eliminar = '<button class="btn btn-sm btn-outline-danger"
                          data-toggle="tooltip"
                          data-placement="top"
                          title ="Eliminar Detalle"
                          id = "eliminar-detalle-rendicion-'.$value['id'].'" >
                            <i class="far fa-trash-alt"></i>
                        </button>';

      $data = ['###select-item###'        => $sel_item->getCode(),
               '###select-documento###'   => $sel_tipo_documento->getCode(),
               '###detalle###'            => $value['detalle'],
               '###fecha###'              => $value['fecha'],
               '###monto###'              => $value['monto'],
               '###num_documento###'      => $value['num_documento'],
               '###button-eliminar###'    => $boton_eliminar,
               '###id-detalle###'         => $value['id'],
               '###hidden###'             => $hidden,
               '###codigo_rendicion###'   => $codigo

             ];

      $code.= $this::despliegueTemplate( $data, 'fila-detalle-rendicion.html' );
      $suma = $suma + $value['monto'];

    }

  $out['code']  = $code;
  $out['suma']  = $suma;

  return $out;

  }

  /**
   * ingresaObservacion(): ingreso de la data de observacion a la db y envio de mails
   * @return string
   */
  private function ingresaObservacion()
  {
    require_once("ftp.class.php");
    $ob_ftp = new FTP("archivo",$this->token);

    if( $_FILES['archivo']['size'] == 0 )
      if( $this->consultas->ingresaObservacion( $_POST['codigo'],
                                                $_POST['estado_rendicion'],
                                                $_POST['observacion'],
                                                'no-aplica' ))
      {
        $sube = true; $this->error_log = null;

      }else{ $sube = false; $this->error_log = "Error en base de datos";  }
      else{
        if( $ob_ftp->validaFile( $_FILES['archivo']['name'] ) )
          if( $ob_ftp->procesaFTP() )
            if( $this->consultas->ingresaObservacion( $_POST['codigo'],
                                                      $_POST['estado_rendicion'],
                                                      $_POST['observacion'],
                                                      $ob_ftp->changeNameFile( $_FILES['archivo']['name'] )  ) ){
                      $sube = true;
                      $this->error_log = null;
            }
            else {    $sube = false; $this->error_log = "Error en base de datos";     }
          else{       $sube = false; $this->error_log = "Error en procesamiento FTP"; }
        else{         $sube = false; $this->error_log = "Error en Tipo de Archivo"; }
      }

      if( $sube )
      {
              $ob_mails = new mails('mensajeObservaciones',
                                    $_POST['email'],
                                    $this::listillaObservaciones( $_POST['codigo'] ));

              if( $ob_mails->getCode() )
                     $ok = true;
              else   $ok = false;

         $this->msg = "<strong>REGISTRO INGRESADO</strong> <br><br>".$this::listillaObservaciones( $_POST['codigo'] );
      }else{

        $this->msg = "<strong>". strtoupper( $this->error_log) ."</strong><br><br>".$this::listillaObservaciones( $_POST['codigo'] );
      }

      return $this->msg;
  }

  /**
   * revisarRendicion(): despliegue de interfaz de chequeo o validacion de rendicion por parte del administrador
   * @return string
   */
  private function revisarRendicion()
  {
    //print_r( $_POST );

    return $this::notificacionRendicion( $_POST['codigo'] ). $this::visorAdministador( $_POST['codigo'] ) ;
  }

 /**
  * visorAdministador(): formulario de chequeo de rendicion
  * @param string codigo
  * @return string
  */
 private function visorAdministador( $codigo = null )
 {
   $code = "";
   $arr  = $this->consultas->listaRendiciones( null, null, $codigo );

   foreach ($arr['process'] as $key => $value) {

     if( $value['id_estado'] == 1  )
          $id_estado = 100;
    else  $id_estado = $value['id_estado'];

    //$id_estado = $value['id_estado'];

     $sel_estado = new Select( $this->consultas->estado_rendicion(),
                               'id',
                               'descripcion',
                               'estado_rendicion',
                               'Estado',
                               $id_estado ,'x'   );

     $responsable = "{$value['nombres']} {$value['apellido_paterno']} {$value['apellido_materno']}";

      $form = $this::listillaObservaciones( $codigo );

      switch ($value['id_estado']) {
        case 4:
        case 5:

          $formObs = null;
          break;

        default:
          $formObs = $this::formularioObs( $responsable,$codigo, $sel_estado->getCode(), $value['login'] );
          break;
      }

     $data  = ['###codigo###'               => $codigo,
               '###responsable###'          => $responsable,
               '###select-estado###'        => $sel_estado->getCode(),
               '###email###'                => $value['login'],
               '###lista-observaciones###'  => $form,
               '###formulario-obs###'       => $formObs

               ];
     $code .= $this::despliegueTemplate( $data, 'visor-administrador.html' );
   }

   return $code;

 }

 /**
  * formularioObs: despliegue de formulario de observaciones
  * @param string responsable
  * @param string codigo
  * @param string selectEstado
  * @return string
  */
 private function formularioObs( $responsable   = null,
                                 $codigo        = null,
                                 $selectEstado  = null,
                                 $email         = null     )
 {
   $data = ['###responsable###'   => $responsable,
            '###select-estado###' => $selectEstado,
            '###codigo###'        => $codigo,
            '###email###'         => $email
           ];

  return $this::despliegueTemplate( $data, 'form-obs.html' );

 }

/**
 * listillaObservaciones( $codigo = null ): tabla de historial de observaciones de rendicion
 * @param string codigo
 * @return string
 */
 private function listillaObservaciones( $codigo = null )
 {
   $data = ['###tabla###' => $this::tablaObservaciones( $codigo ) ];
   return $this::despliegueTemplate( $data, 'lista-observaciones.html' );
 }

/**
 * tablaObservaciones( $codigo = null ): tabla de observaciones
 * @param string codigo
 * @return string
 */
private function tablaObservaciones( $codigo = null )
{
  $arr = $this::trTablaObservaciones( $codigo );

  if( $arr['total-recs'] > 0  )
  {
    $data = ['###tr###' => $arr['code'] ];
    return $this::despliegueTemplate( $data, 'tabla-observaciones.html' );

  }else{

    return $this::notificaciones('danger',null,'No Existen Observaciones ingresadas' );
  }
}

/**
 * trTablaObservaciones( $codigo = null ): tabla de observaciones ( detalle  )
 * @param string codigo
 * @return string
 */
private function trTablaObservaciones( $codigo = null )
{

  $arr  = $this->consultas->listaObservaciones( $codigo  );
  $code = "";
  $i = 0;

  foreach ($arr['process'] as $key => $value) {

    if( $value['archivo'] == 'no-aplica' )
          $LINK = "Sin Archivo";
    else  $LINK = "<a href='http://rendiciones-socma.ddns.net/dox/{$value['archivo']}'
                      download='{$value['archivo']}'
                      class='btn btn-sm btn-outline-light'
                      >{$value['archivo']}</a>";

    $data = ['###num###'          => $i+1,
             '###codigo###'       => $codigo,
             '###observacion###'  => $value['observacion'],
             '###fecha###'        => $value['fecha']   ,
             '###estado###'       => $value['estado'],
             '###archivo###'      => $LINK

            ];

    $code .= $this::despliegueTemplate( $data, 'tr-observaciones.html' );

    $i++;
  }

  $out['code']        = $code;
  $out['total-recs']  = $arr['total-recs'];
  $out['sql']         = $arr['sql'];

  return $out;

}
 /**
  * verDetalleRendicion(): visualizacion de detalle de rendicion
  * @return string
  */
  private function verDetalleRendicion()
  {
      return $this::notificacionRendicion( $_POST['codigo'] )."<br><div align='left'>".$this::listillaObservaciones( $_POST['codigo'] )."</div>";
  }

  /**
   * eliminaArchivoRendicion()
   * @return string
   */
  private function eliminaArchivoRendicion()
  {
    if( $this->consultas->eliminaArchivoRendicion( $_POST['id_archivo'] ) )
         {$this->msg = "<strong>Archivo Eliminado</strong>"; $this->color="success"; $this->icon ='<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>';          }
    else {$this->msg = "Error al Eliminar Archivo";$this->color="danger"; $this->icon ='<i class="fa fa-thumbs-o-down" aria-hidden="true"></i>';    }

    $notificacion = $this::notificaciones( $this->color,$this->icon,$this->msg );

    return $notificacion.$this::tabla_archivos( $_POST['codigo_rendicion'] );
  }

  /**
   * ingresaFilesRendicion(): ingreso de archivos
   *
   * @return string
   */
  private function ingresaFilesRendicion()
  {
    //print_r( $_POST );
    require_once("ftp.class.php");
    $ob_ftp = new FTP("archivo",$_POST['codigo_rendicion']);

    if( $ob_ftp->validaFile( $_FILES['archivo']['name'] ) )
      if( $ob_ftp->procesaFTP() )
        if( $this->consultas->procesaArchivoRendicion( $ob_ftp->changeNameFile( $_FILES['archivo']['name'] ),
                                                       $_POST['codigo_rendicion'] ) )
        {
          $sube = true;
          $this->msg = "Archivo Subido Correctamente".$this::tabla_archivos( $_POST['codigo_rendicion'] );

        }else{

          $sube = false;
          $this->msg = "error en Consulta";

        }
     else{

          $sube = false;
          $this->msg = "error en FTP";
     }
     else{

          $sube = false;
          $this->msg = "error en Tipo de Archivo";
     }
     return $this->msg;
  }

  /**
   * tabla_archivos(): listado de archivos que serán asociados a la rendicion
   *
   * @param string codigo_rendicion
   * @return string
   */
  private function tabla_archivos( $codigo_rendicion = null )
  {

    $arr = $this::tr_tabla_archivos( $codigo_rendicion );

    if( $arr['total-recs'] > 0 )
    {
      $data = ['###tr###' => $arr['code'] ];
      return $this::despliegueTemplate( $data, 'tabla-archivos-rendicion.html' );
    }else{

      return $this::notificaciones('danger','<i class="fas fa-radiation"></i>','No hay Archivos');
    }
  }

  /**
   * tr_tabla_archivos(): listado de archivos que serán asociados a la rendicion
   *
   * @param string codigo_rendicion
   * @return string
   */
  private function tr_tabla_archivos( $codigo_rendicion = null )
  {
    $arr  = $this->consultas->archivo_rendicion( $codigo_rendicion );
    $code = "";
    $i    = 0;
    foreach ($arr['process'] as $key => $value) {

      //boton-elimina
      $btn = null;
      if( !$_POST['muestra_elimina_file'] )
        $btn = '<button name="quita-archivo-'.$value['id'].'"
                      id="quita-archivo-'.$value['id'].'"
                      class="btn btn-sm btn-danger">
                      <i class="fas fa-trash"></i> Eliminar Archivo
                </button>';

      $data = ['###num###'              => $i+1 ,
               '###archivo###'          => $value['nombre_archivo'],
               '###fecha_subida###'     => $value['fecha'],
               '###id###'               => $value['id'],
               '###codigo_rendicion###' => $value['codigo_rendicion'],
               '###btn###'              => $btn

              ];

      $code .= $this::despliegueTemplate( $data, 'tr-archivos-rendicion.html' );

      $i++;
    }

    $out['total-recs'] = $i;
    $out['code'] = $code;
    return $out;
  }

  private function ingresaDataInicialRencion()
  {

    if( $this->consultas->procesaRendicion( $_POST['token'],
                                            $_POST['monto_asignado'],
                                            $this->yo,
                                            $_POST['id_motivo'],
                                            1,
                                            $_POST['lugar'],
                                            $_POST['acompanante'],
                                            $_POST['fecha_viaje']
     ) )
    {
      if( $this::procesaDetalleRendicion( $_POST['token']  ) )
           $ok = true;
      else $ok = false;



      $ob_mails = new mails( 'msgIngreso',
                             $this->email_admin,
                             $this::msgRendicion( $_POST['token'] )    );

      if( $ob_mails->getCode() )
           $ok = true;
      else $ok = false;


      $msg = $this::notificacionRendicion( $_POST['token'] );

    }else{
      $msg = "ERROR AL INGRESAR";
    }

    return $msg;
  }

  /**
   * msgRendicion():$codigo = null, generacion de mensaje de la rendicion
   * @param string codigo_rendicion
   * @return string
   */
  private function msgRendicion( $codigo = null )
  {
    require_once("mensajeRendicion.class.php");

    $responsable      = "";
    $email            = "";
    $monto_asignado   = "";
    $fecha            = "";
    $total_a_rendir   = 0;

    $arr_rendicion    = $this->consultas->listaRendiciones( null,null,$codigo );

    foreach ($arr_rendicion['process'] as $key => $value) {

      $responsable    .= "{$value['nombres']} {$value['apellido_paterno']} {$value['apellido_materno']}";
      $email          .= $value['login'];
      $monto_asignado .= $value['monto_asignado'];
      $fecha          .= $this::arreglaFechas(  $value['fecha_ingreso'] );
    }

    $arr_detalle      = $this->consultas->detalle_rendicion( $codigo );

    foreach ($arr_detalle['process'] as $key => $v) {
      $total_a_rendir = $total_a_rendir + $v['monto'];
    }

    $ob_msg  = new MensajeRendicion( $responsable ,
                                     $email,
                                     $fecha,
                                     $codigo,
                                     $total_a_rendir,
                                     $monto_asignado );

    return $ob_msg->muestraDetalle();
  }

  /**
   * notificacionRendicion(): notificacion  estado de rendicion
   */
  private function notificacionRendicion( $token = null )
  {
    //1. data de la rendicion propiamente tal

    $code = "";
    $arr = $this->consultas->rendiciones( null, $token );
    $i = 0;

    foreach ($arr['process'] as $key => $value) {

      $a = $this::detallesRendicion( $token );

      $saldo_favor_empresa      = $value['monto_asignado']-$a['suma_montos'];
      $saldo_favor_funcionario =  $a['suma_montos'] - $value['monto_asignado'];

      $rut = $this::separa( $value['rut'],'-' );


      switch ($value['id_estado']) {
        case 4:
        $exportarPDF = '<a href="resumen-pdf.php?codigo_rendicion='.$token.'"
                          target="_blank"
                          class="btn btn-sm btn-outline-danger"
                          name="b"
                          id="exportar-pdf">
                                <i class="far fa-file-pdf"></i> Exportar a PDF
                       </a>';
          break;

        default:
          $exportarPDF = null;
          break;
      }

      $data = ['###EXPORTAR###'                 => $exportarPDF,
               '###rut###'                      => $value['rut'] ,
               '###motivo###'                   => $value['descMotivo'],
               '###rut###'                      => $this::separa_miles( $rut[0] )."-".$rut[1],
               '###fecha###'                    => $this::arreglaFechas( $value['fecha_ingreso'] ),
               '###monto-asignado###'           => $this::separa_miles( $value['monto_asignado'] ),
               '###lugar###'                    => $value['lugar'],
               '###acompanante###'              => $value['acompanante'],
               '###fecha-viaje###'              => $value['fecha_viaje'],
               '###tabla-detalles###'           => $a['code'],
               '###total-a-rendir###'           => $this::separa_miles( $a['suma_montos'] ),
               '###saldo-favor-empresa###'      => $this::separa_miles( $saldo_favor_empresa ),
               '###saldo-favor-funcionario###'  => $this::separa_miles( $saldo_favor_funcionario),
               '###files###'                    => $this::tabla_archivos( $token ),
               '###token###'                    => $token
             ];
      $code .= $this::despliegueTemplate( $data, 'notificacion-rendicion.html' );
      //$code .= $a['sql'];

      $i++;
    }

    return $code;
  }

  private function detallesRendicion( $token = null )
  {

    $code = "";
    $arr  = $this::trDetallesRendicion( $token );

    $data = ['###total-recs###' => $arr['total-recs'], '###tr###' => $arr['code'] ];

    $code = $this::despliegueTemplate( $data, 'tabla-detalle-rendicion.html' );

    $out['code']        = $code;
    $out['suma_montos'] = $arr['suma_montos'];
    $out['sql']         = $arr['sql'];

    return $out;
  }

  private function trDetallesRendicion( $token = null )
  {
    $arr  = $this->consultas->detalle_rendicion( $token );
    $code = "";
    $i    = 0;

    $suma_montos = 0;
    foreach ($arr['process'] as $key => $value) {

      $monto_miles = $this::separa_miles( $value['monto'] );

      $data = ['###num###'            => $i+1,
               '###item###'           => $value['descItem'],
               '###tipo_documento###' => $value['descTipoDocumento'],
               '###num_documento###'  => $value['num_documento'],
               '###detalle###'        => $value['detalle'],
               '###monto###'          => $monto_miles,
               '###fecha###'          => $this::arreglaFechas(  $value['fecha'] )            ];

      $code .= $this::despliegueTemplate( $data , "tr-detalle-rendicion.html" );

      $suma_montos = $suma_montos + $value['monto'];

      $i++;
    }

    $out['suma_montos'] = $suma_montos;
    $out['code']        = $code;
    $out['total-recs']  = $arr['total-recs'];
    $out['sql']         = $arr['sql'];

    return $out;
  }

  /**
   * procesaDetalleRendicion(): ingresa data de detalle de rendicion
   *
   * @param string token
   * @return boolean
   */
  private function procesaDetalleRendicion( $token = null )
  {
        $arr_item           = $this::separa( $_POST['id_item'],'&' );
        $arr_detalle        = $this::separa( $_POST['detalle'],'&' );
        $arr_fecha_detalle  = $this::separa( $_POST['fecha_detalle'],'&' );
        $arr_tipo_documento = $this::separa( $_POST['tipo_documento'],'&' );
        $arr_num_documento  = $this::separa( $_POST['num_documento'],'&' );
        $arr_monto_detalle  = $this::separa( $_POST['monto_detalle'],'&' );

        $j = 0;
        for ($i=0; $i < sizeof( $arr_item ) ; $i++) {

          $aux_item           = $this::separa( $arr_item[ $i ], '=' );
          $aux_detalle        = $this::separa( $arr_detalle[ $i ], '=' );
          $aux_fecha_detalle  = $this::separa( $arr_fecha_detalle[ $i ], '=' );
          $aux_tipo_documento = $this::separa( $arr_tipo_documento[ $i ], '=' );
          $aux_num_documento  = $this::separa( $arr_num_documento[ $i ], '=' );
          $aux_monto_detalle  = $this::separa( $arr_monto_detalle[ $i ], '=' );

          if( $this->consultas->procesaDetalleRendicion(  $aux_item[ 1 ],
                                                          $aux_detalle[ 1 ],
                                                          $aux_tipo_documento[ 1 ],
                                                          $aux_num_documento[ 1 ],
                                                          $aux_fecha_detalle[ 1 ],
                                                          $aux_monto_detalle[ 1 ],
                                                          $token,
                                                          $this->yo ))
          {  $j++;    }

         }

         if( $j > 0 )
              return true;
        else  return false;
  }

  private function formViaje()
  {
    if( $_POST['id_motivo'] == 1  )
    {
      return $this::despliegueTemplate([],'form-viaje.html');
    }
    else{
       return $this::hiddens();
     }
  }

  /**
   * crearRendiciones(): Creacion de Rendiciones
   * @return string
   */
  private function crearRendiciones()
  {
    $sel_motivo = new Select( $this->consultas->motivos(),
                              'id',
                              'descripcion',
                              'id_motivo',
                              'Motivo');

    $sel_medio_pago = new Select( $this->consultas->medio_pago(),
                              'id',
                              'descripcion',
                              'id_medio_pago',
                              'Medio de Pago');

    $data = ['###accion###'           => 'Ingreso',
             '###yo###'               => $this->yo,
             '###rut###'              => $this->rut,
             '###token###'            => $this->token,
             '###hiddens###'          => $this::hiddens(),
             '###select-motivo###'    => $sel_motivo->getCode(),
             '###espacio-filas###'    => $this::espacioFilas(),
            '###select-medio-pago###' => $sel_medio_pago->getCode(),
             '###fecha###'            => $this::arreglaFechas( $this->fecha_hoy ),
             '###modal###'            => $this::modal('terminos',
                                                      '<i class="fas fa-stream"></i>',
                                                      'Términos y Condiciones',
                                                      $this::terminos())
   ];
    return $this::despliegueTemplate( $data, 'formulario-rendiciones.html' );
  }

/**
 * terminos(): llamada al html que contiene los términos y condiciones
 * @return true;
 */
 private function terminos()
 {
   $data =[];
   return $this::despliegueTemplate( $data, 'terminos.html' );
 }

  /**
   * espacioFilas(): espacio de las filas de la rendicion!!!
   */
  private function espacioFilas()
  {
    $code = "";

    $sel_item = new Select( $this->consultas->items(),
                              'id',
                              'descripcion',
                              'id_item',
                              'Items');

    $sel_tipo_documento = new Select( $this->consultas->tipo_documento(),
                              'id',
                              'descripcion',
                              'tipo_documento',
                              'Tipo.Doc.');

    $data = ['###select-item###'        => $sel_item->getCode(),
             '###select-documento###'   => $sel_tipo_documento->getCode(),
             '###detalle###'            => null,
             '###fecha###'              => null,
             '###monto###'              => null,
             '###hidden###'             => null,
             '###num_documento###'      => null,
             '###button-eliminar###'    => null
           ];

    $code.= $this::despliegueTemplate( $data, 'fila-detalle-rendicion.html' );

  return $code;

  }

  /**
   * hiddens(): muestra hiddens iniciales
   *
   * @return string
   */
  private function hiddens()
  {
      return $this::despliegueTemplate([],'hidden-iniciales.html');
  }

  /**
   * misRendiciones(): listado de rendiciones
   * @return string
   */
  private function misRendiciones()
  {
    //return "Espacio de listado de rendiciones para usuario de tipo {$this->tipo_usuario}";

    $arr = $this::trMisRendiciones();


    if( $arr['total-recs'] > 0 )
    {
      if( $this->tipo_usuario == 1 )
              $tpl = "tabla-rendiciones-admin.html";
      else    $tpl = "tabla-rendiciones-normal.html";

      $data = ['###tr###'         => $arr['code'] ,
               '###total-recs###' => $arr['total-recs'],
               '###nav-links###'  => $arr['nav-links'],
               '###buscar###'     => $this::buscar()
             ];

      return $this::despliegueTemplate( $data, $tpl );
    }else{


      $data = ['###msg###'          => $this::cardMsg(),
              '###notificacion###'  => null];
      return $this::despliegueTemplate( $data, 'pantalla-vacia.html' );
    }
  }

  private function buscar()
  {
      $arr = $this->consultas->listaUsuarios();

      $usuario[0] = "nombres";
      $usuario[1] = "apellido_paterno";
      $usuario[2] = "apellido_materno";

      $sel1 = new Select( $arr['process'], 'id', $usuario, 'id_usuario', 'Usuario' );

      $sel2 = new Select( $this->consultas->estado_rendicion(), 'id','descripcion','id_estado','Estado' );

      $data = [ '###select-usuario###' =>$sel1->getCode(),
                '###select-estado###'  =>$sel2->getCode()  ];

      return $this::despliegueTemplate( $data , 'buscar.html' );
  }

  /**
   * cardMsg(): muestra mensaje al inicio
   */
  private function cardMsg()
  {
    $this->btn = '<a href="content-page.php?id=Y3JlYXJSZW5kaWNpb25lcw=="
                    class="btn btn-sm btn-secondary">
                  <i class="fas fa-angle-double-right"></i> Crear Rendiciones
                  </a>';

    $arr = $this->consultas->listaAlerta( $this->yo );

    if( $arr['total-recs'] > 0 )
      return $this::notificaciones( 'danger', '<i class="fas fa-stream"></i>', "
      <strong>
        Alerta :
      </strong>
       Aún no ha ingresado rendiciones / Sin Resultados {$this->btn}"  );
    else{

      $data = [];
      return $this::despliegueTemplate( $data, 'card-msg.html' );

    }
  }

  /**
   * aceptoTerminos(): aceptas los terminos y condiciones
   */
  private function aceptoTerminos()
  {
    $this->btn = '<a href="content-page.php?id=Y3JlYXJSZW5kaWNpb25lcw=="
                    class="btn btn-sm btn-secondary">
                  <i class="fas fa-angle-double-right"></i> Crear Rendiciones
                  </a>';

    //print_r( $_POST );
    if( $this->consultas->ingresaAlerta( $this->yo ) )
          return $this::notificaciones( 'danger', '<i class="fas fa-stream"></i>', "
          <strong>
            Alerta
          </strong>
           Aún no ha ingresado rendiciones {$this->btn}"  );
    else  return null;
  }

  /**
   * trMisRendiciones(): lista de rendiciones
   *
   * @return string
   */
  private function trMisRendiciones()
  {

    $code = "";
    $i    = 0;

    if( !isset( $_POST['modoBuscar'] ) )
    {
      $arr = $this->consultas->listaRendiciones( $this->tipo_usuario, $this->yo );
      $utils      = new utiles($arr['sql']);
      $rs_dd      = $utils->show();
      $nav_links  = $rs_dd['nav_links'];
      $param      = $rs_dd['result'] ;
    }else{

      //print_r( $_POST );
      $arr = $this->consultas->listaRendicionesBuscar( $_POST['codigo'],
                                                       $_POST['id_usuario'],
                                                       $_POST['id_estado'],
                                                       $_POST['fecha_inicial'],
                                                       $_POST['fecha_final']);
      $nav_links  = $null;
      $param      = $arr['process'] ;

    }

    if( $this->tipo_usuario == 1 )
            $tpl = "tr-tabla-rendiciones-admin.html";
    else    $tpl = "tr-tabla-rendiciones-normal.html";

    foreach ($param as $key => $value) {

      $responsable      = "{$value['nombres']} {$value['apellido_paterno']} {$value['apellido_materno']}";
      //$this->btn_editar = null;

      if( $value['id_estado'] != 6  )
      {
        $this->btn_evalua ="<button class='btn btn-sm btn-outline-info'
                                    id='revisar-{$value['id']}'
                                    name='button'
                                    data-toggle='tooltip'
                                    data-placement='top'
                                    title='Validar rendición'>
                                <i class='far fa-check-circle'></i>
                            </button>";
      }else $this->btn_evalua = null;


      switch ($value['id_estado']) {

        case 2:
        case 3:
        case 6:
        if( $this->yo == $value['id_usuario']  )
              $this->btn_editar ="<button class='btn btn-info btn-sm'
                                          id='editar-{$value['id']}'
                                          data-toggle = 'tooltip'
                                          data-placement='top'
                                          title = 'Editar'>
                                          <i class='far fa-edit'></i>
                                    </button>";
          else $this->btn_editar = null;
          break;

        default:
               $this->btn_editar = null;
          break;
      }

      $data   = ['###codigo###'         => $value['codigo'],
                 '###fecha###'          => $this::arreglaFechas( $value['fecha_ingreso'] ),
                 '###responsable###'    => $responsable,
                 '###btn_editar###'     => $this->btn_editar,
                 '###viaje###'          => $value['descripcion'],
                 '###monto-asignado###' => $this::separa_miles(  $value['monto_asignado'] ),
                 '###estado###'         => $value['descEstado'],
                 '###id###'             => $value['id'],
                 '###btn-evalua###'     => $this->btn_evalua,
                 '###modal###'          => $this::modal("archivos-{$value['id']}",
                                                        '<i class="fas fa-stream"></i>',
                                                        "<strong>Archivos /</strong> {$value['codigo']}",
                                                        $this::listaArchivos( $value['codigo'] ) )
     ];
      $code  .= $this::despliegueTemplate( $data, $tpl );
    }

    $out['code']        = $code;
    $out['total-recs']  = $arr['total-recs'];
    $out['nav-links']   = $nav_links;
    $out['sql']         = $arr['sql'];

    return $out;
  }

  /**
   * listaArchivos(): mostrar archivos desde el listado principal
   *
   * @param string codigo_rendicion
   * @return string
   */
  private function listaArchivos( $codigo_rendicion = null )
  {
      $arr = $this::trListaArchivos( $codigo_rendicion  );

      if( $arr['total-recs'] > 0 )
      {
        return $arr['code'];

      }else{

        return $this::notificaciones('danger','<i class="fas fa-radiation"></i>','No hay Archivos');
      }
  }

  /**
   * listaArchivos(): mostrar archivos desde el listado principal ( continuacion )
   *
   * @param string codigo_rendicion
   * @return string
   */
  private function trListaArchivos( $codigo_rendicion = null )
  {
      $arr  = $this->consultas->archivo_rendicion( $codigo_rendicion );
      $code = "";
      $i    = 0;

      foreach ($arr['process'] as $key => $value) {

        $data = [ '###num###'              => $i+1 ,
                  '###archivo###'          => $value['nombre_archivo'],
                  '###fecha_subida###'     => $value['fecha'],
                  '###id###'               => $value['id'],
                  '###codigo_rendicion###' => $value['codigo_rendicion'],
        ];

        $code .= $this::despliegueTemplate( $data, 'estructura-archivos.html' );

        $i++;

      }

      $out['total-recs'] = $i;
      $out['code'] = $code;
      return $out;
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
    * separa_miles(), coloca separador de miles en una cadena de caracteres
    *
    * @param  String num
    * @return String
    */
   private function separa_miles($num=null){

    return @number_format($num, 0, '', '.');
   }

   /**
    * despliegueTemplate(): fusion de data con templates
    *
    * @param array $arrayData
    * @param string tpl
    */
   private function despliegueTemplate($arrayData,$tpl){

       $tpl = $this->ruta.$tpl;

       $this->template->setTemplate($tpl);
       $this->template->llena($arrayData);

       return $this->template->getCode();
   }

   /**
    * getCode(): salida
    * @return string
    */
   public function getCode()
   {
     return $this::control();
   }
}
?>
