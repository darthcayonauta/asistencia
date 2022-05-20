<?php
/**
 * @author  Ing. Claudio Guzmán Herrera
 * @version 1.0
 * @package class
 */
class Inicio 
{
	private $consultas;
	private $template;
	private $ruta;
	private $id;
	private $fecha_hoy;
	private $id_usuario;
	private $id_tipo_user;
    private $ftpUser;
    private $ftpPass;
    private $ftpHost;
    private $ftpFolder;
    private $token;

    function __construct( $id = null )
	{
		$oConf    = new config();
	  	$cfg      = $oConf->getConfig();
	  	$db       = new mysqldb( 	    $cfg['base']['dbhost'],
									    $cfg['base']['dbuser'],
									    $cfg['base']['dbpass'],
									    $cfg['base']['dbdata'] );

        $this->id_usuario               = $_SESSION['id_usuario'];                                        
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
	}

    private function control()
    {
        switch ($this->id) {
            case 'inicio':
                return $this::inicio();
                break;
            
            default:
                return $this->error;
                break;
        }
    }

    private function inicio()
    {
        if( require_once( 'archivos.class.php' ) )
        { $ob = new Archivos( 'listar-archivos' ); return $ob->getCode(); }
        else{
            return "problemas con  la clase seleccionada";
        }
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