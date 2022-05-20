<?php
/**
 * @author  Claudio Guzman Herrera
 * @version 1.0
 */
class Trabajos
{
  private $id;
  private $yo;
  private $consultas;
  private $template;
  private $error;
  private $token;
  private $msg;
  private $btn;
  private $btn_critico;
  private $email;


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

    $this->btn_listar     = '<a href="content-page.php?id=bGlzdGEtdHJhYmFqb3M="
                                class="btn btn-sm btn-success" >
                                  Listar Trabajos
                             </a>';

    $this->btn_crear      = '<a href="content-page.php?id=Z2VuZXJhci10cmFiYWpvcw=="
                                class="btn btn-sm btn-secondary" >
                                  [ Crear Trabajos ]
                             </a>';

  $this->email = "claudio.guzman@socma.cl";

  }

  private function control()
  {
    switch ($this->id) {

      case 'ingresaTrabajo':
        return $this::ingresaTrabajo();
        break;

      case 'generar-trabajos':
        return $this::crearTrabajos();
        break;

      case 'lista-trabajos':
        return $this::listarTrabajos();
        break;

      case 'eliminaTrabajo':
        return $this::eliminaTrabajo();
        break;

      case 'editaTrabajo':
        return $this::editaTrabajo();
        break;

      case 'editaTrabajoData':
        return $this::editaTrabajoData();
        break;

      default:
        return " {$this->error} para id : { $this->id } ";
        break;
    }
  }

  private function editaTrabajoData()
  {
      if( $this->consultas->procesaTrabajos( addslashes( $_POST['nombre_trabajo'] ),
                                                $_POST['id_trabajo'] ) )
      {
        $this->color  = "success";
        $this->icon   = '<i class="far fa-thumbs-up"></i>';
        $this->glosa  = "Actualizado!!!";

      }else{

        $this->color  = "danger";
        $this->icon   = '<i class="far fa-thumbs-down"></i>';
        $this->glosa  = "error al Actualizar";
      }

      return $this::notificaciones( $this->color,
                                    $this->icon,
                                   "{$this->glosa} {$this->btn_crear} {$this->btn_listar}" );
  }

  private function editaTrabajo()
  {
    $arr  = $this->consultas->listaTrabajos( $_POST['id_trabajo'] );
    $code = "";

    foreach ($arr['process'] as $key => $value) {

      $hidden = "<input type='hidden'
                        id='id_trabajo'
                        name='id_trabajo'
                        value ='{$_POST['id_trabajo']}'  >";

      $data = ['###title###'            => 'Edición',
               '###hidden###'           => $hidden,
               '###nombre_trabajo###'   => $value['descripcion'],
               '###id_button###'        => 'update'       ];
      $code .= $this::despliegueTemplate( $data, 'formulario-trabajos.html' );

    }

    return $code;

  }

  private function eliminaTrabajo()
  {

    if( $this->consultas->cambiaEstado( 'trabajos', 'id_estado' ,2, $_POST['id_trabajo'] ) )
    {
      $this->color  = "success";
      $this->icon   = '<i class="far fa-thumbs-up"></i>';
      $this->glosa  = "Registro Eliminado";

    }else{

      $this->color  = "danger";
      $this->icon   = '<i class="far fa-thumbs-down"></i>';
      $this->glosa  = "error al Eliminar";
    }

    return $this::notificaciones( $this->color,
                                  $this->icon,
                                 "{$this->glosa} {$this->btn_crear} {$this->btn_listar}" ).$this::listarTrabajos();
  }

  private function ingresaTrabajo()
  {

    if( $this->consultas->procesaTrabajos( addslashes( $_POST['nombre_trabajo'] )) )
    {
      $this->color  = "success";
      $this->icon   = '<i class="far fa-thumbs-up"></i>';
      $this->glosa  = "Registro Ingresado";

    }else{
      $this->color  = "danger";
      $this->icon   = '<i class="far fa-thumbs-down"></i>';
      $this->glosa  = "error al ingresar";
    }

    return $this::notificaciones( $this->color,
                                  $this->icon,
                                 "{$this->glosa} {$this->btn_crear} {$this->btn_listar}" );
  }

  private function crearTrabajos()
  {
  //  return "MODULO EN CONSTRUCCION PARA {$this->id}";
    $data = ['###title###'            => 'Creación',
             '###hidden###'           => null,
             '###nombre_trabajo###'   => null,
             '###id_button###'        => 'send'       ];
    return $this::despliegueTemplate( $data, 'formulario-trabajos.html' );

  }

  private function listarTrabajos()
  {
    $arr = $this::trListarTrabajos();

    if( $arr['total-recs'] > 0 )
    {

      if( $this->tipo_usuario == 3 )
            $menu_aux = $this::menu_admin();
      else  $menu_aux = null;

      $data = ['###tr###'         => $arr['code'],
               '###total-recs###' => $arr['total-recs'],
               '###menu_aux###'   => $menu_aux
             ];

      return $this::despliegueTemplate( $data, 'tabla-trabajos.html' );

    }else{

      $this->color  = "danger";
      $this->icon   = '<i class="far fa-thumbs-down"></i>';
      $this->glosa  = "No Hay Registros";

      return $this::notificaciones( $this->color,
                                    $this->icon,
                                   "{$this->glosa} {$this->btn_crear} {$this->btn_listar}" );
    }
  }

  private function trListarTrabajos()
  {
    $arr  = $this->consultas->listaTrabajos();
    $code = "";
    $i    = 0;

    foreach ($arr['process'] as $key => $value) {

      $data = ['###num###'        => $i+1,
               '###trabajo###'    => $value['descripcion'],
               '###id###'         => $value['id']     ];

      $code .= $this::despliegueTemplate( $data, 'tr-trabajos.html' );

      $i++;
    }

    $out['code']       = $code;
    $out['total-recs'] = $arr['total-recs'];

    return $out;
  }

  private function menu_admin()
  {
      $data = [];
      return $this::despliegueTemplate( $data, 'menu-admin.html' );
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
   * codifica(): ressuelve codificar en uft8 o no dependiendo del server
   */
   private function codifica( $cadena = null, $accion = null  )
   {
     $ob_codifica = new Codifica( $cadena , $accion  );
     return $ob_codifica->resuelve();
   }


  /**
   * despliegueTemplate(), metodo que sirve para procesar los templates
   *
   * @param  array   arrayData (array de datos)
   * @param  array   tpl ( template )
   * @return String
   */
   private function despliegueTemplate($arrayData,$tpl, $ruta_abs =null ){

        if( is_null( $ruta_abs ) )
            $tpl = $this->ruta.$tpl;
         else $tpl = "/home/claudio/webs/inventario/Templates/{$tpl}";

       $this->template->setTemplate($tpl);
       $this->template->llena($arrayData);

       return $this->template->getCode();
   }


 /**
 * getCode(): salida general del resultado del método de Control
 * @return string
 */
 public function getCode()
 {
   return $this::control();
 }
}
 ?>
