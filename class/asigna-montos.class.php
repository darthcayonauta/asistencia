<?php

/**
 * class AsignaMontos
 * @author Ing. Claudio Guzmán Herrera
 * @package rendiciones
 */

class AsignaMontos 
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

    function __construct( $id = null )
    {
      $oConf    = new config();
      $cfg      = $oConf->getConfig();
      $db       = new mysqldb( 	$cfg['base']['dbhost'],
                                $cfg['base']['dbuser'],
                                $cfg['base']['dbpass'],
                                $cfg['base']['dbdata'] );

      $this->id                 = $id;
      $this->consultas 			= new querys( $db );
      $this->template  			= new template();
      $this->ruta      			= $cfg['base']['template'];
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
     * control(): método de control de id's
     * 
     * @return string
     */
    private function control()
    {
        switch ($this->id) {
            case 'asigna-montos':
                # code...
                return $this::asignaMontos();
                break;
            
             case 'asignaMontoColaborador':
                 # code...
                 return $this::asignaMontoColaborador();   
                 break;   

            default:
                return $this->error;
                break;
        }
    }

   /**
    * asignaMontoColaborador():
    *
    * @return string
    */ 
   private function asignaMontoColaborador()
   {
    if( $this->consultas->procesaRendicion( $this->token,
                                            htmlentities( $_POST['monto'] ),
                                            $_POST['id_colaborador'],1,
                                            1,
                                            '',
                                            '',
                                            $this->fecha_hoy ))
    {
        $arr = $this->consultas->listaUsuarios(null,null,1,$_POST['id_colaborador']); 

        $usuario = null; $email = null;

        foreach ($arr['process'] as $key => $value) 
        {
            $usuario = "{$value['nombres']} {$value['apellido_paterno']} {$value['apellido_paterno']}";
            $email   = $value['login'];
        }

        $ob_mails = new mails('asignacion',
                               $email,
                               $this::mensaje($usuario, $_POST['monto']) );

        if( $ob_mails->getCode() )
                $ok = true;
        else    $ok = false;                        

        return $this::notificaciones('success',
                                    '<i class="far fa-thumbs-up"></i>',
                                    "RENDICION ASIGNADA");
    }
    else{
        return "ERROR AL INGRESAR!!!";
    }
   } 

    /**
     * mensaje(): funcion que envia mensajes de usuario
     * 
     * @param string usuario
     * @param string monto
     */
    private function mensaje( $usuario = null, $monto=null )
    {
        return $this::despliegueTemplate( ['###codigo-rendicion###' => $this->token,
                                           '###usuario###'          => $usuario,
                                           '###monto###'            => $this::separa_miles( $monto )],'msg-asignacion.html' );
    }

    /**
     * asignaMontos()
     * @return string
     */
    private function asignaMontos()
    {
        $arr = $this->consultas->listaUsuarios();

        $user[0]="nombres";
        $user[1]="apellido_paterno";
        $user[2]="apellido_materno";

        $select = new select( $arr['process'],
                              'id',
                               $user,
                               'id_colaborador',
                               'Colaborador',
                               null,
                               1                       );

       // return "MODULO EN CONSTRUCCION";
        $data = ['###select###' => $select->getCode() ];
        return $this::despliegueTemplate( $data,'formulario-asignacion.html' );
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