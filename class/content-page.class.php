<?php

class ContentPage
{
	private $consultas;
	private $template;
	private $ruta;
	private $id;
	private $menu;
	private $fecha_hoy;
	private $yo;
	private $id_tipo_user;

	function __construct( $id = null,$yo = null )
	{
	if( !$yo )
		$this->yo = $_SESSION['yo'];
	else {
		$this->yo = $yo;
	}

	$oConf    				= new config();
	$cfg      				= $oConf->getConfig();
	$db       				= new mysqldb(  $cfg['base']['dbhost'],
											$cfg['base']['dbuser'],
											$cfg['base']['dbpass'],
											$cfg['base']['dbdata'] );

    $this->consultas 		= new querys( $db );
    $this->template  		= new template();
    $this->ruta      		= $cfg['base']['template'];
    $this->id 				= $id;
    $this->error 			= $cfg['base']['error'];
    $this->fecha_hoy 		= date("Y-m-d");
    $this->fecha_hora_hoy 	= date("Y-m-d H:i:s");
    $this->nombres          = utf8_decode($_SESSION['nombres']);
    $this->apaterno         = utf8_decode($_SESSION['apaterno']);
    $this->amaterno         = utf8_decode($_SESSION['amaterno']);
    $this->id_tipo_user 	= $_SESSION['tipo_user'];
    $this->menu 			= new Menu( $this->yo, $this->id_tipo_user );

	}

	/**
	 * control(): algoritmo de deciciones
	 * @return string
	 */
	private function control()
	{
		switch ($this->id)
		{
			case 'accesos':
			case 'lista-usuarios':
			case 'crear-usuario':
			case 'cambia-password':			
			case 'inicio':

			return $this::baseHtml();
			break;

			default:
					return $this::baseHtmlError();
			break;
		}
	}

	/**
	 * baseHtml(): despliegue del contenido de un enlace desde menu o link
	 * @return string
	 */
	private function baseHtml()
	{
        if (!isset( $_GET['id']  ))
        {       $content = "CONTENIDO INICIAL DE LA PAGINA  <br>" .$this->id ;
        }else{  $content = $this::importaModulos(); }

			$data = array('@@@TITLE'  	=> 'Sistema de Asistencia',
                      	  '@@@USER' 	=> utf8_encode( "{$this->nombres} {$this->apaterno} {$this->amaterno}" ),
						  '@@@FECHA'  	=> $this::arreglaFechas(  $this->fecha_hoy ),
						  '@@@CONTENT' 	=> $content,
						  '###tags###'  => null,
						  '@@@MENU'		=> $this->menu->getCode() );

			return $this::despliegueTemplate($data,'inicio-principal.html');
	}

	/**
	 * baseError(): despliegue del contenido de un enlace desde menu o link, cuando es error
	 * @return string
	 */
	private function baseHtmlError()
	{

			$data = array(	'@@@TITLE'  	=> 'Sistema de Asistencia',
                      		'@@@USER' 		=> utf8_encode(  "{$this->nombres} {$this->apaterno} {$this->amaterno}"),
							'@@@FECHA'  	=> $this::arreglaFechas( $this->fecha_hoy ),
							'@@@CONTENT' 	=> "{$this->error} ::: {$this->id}",
							'@@@MENU'		=> $this->menu->getCode() );

			return $this::despliegueTemplate($data,'inicio-principal.html');
	}

    /**
     * importaModulos()
     * @param
     * @return string
     */
    private function importaModulos()
    {
        switch ($this->id) {

			case 'accesos':
				# code...
				return $this::generalCall( 'accesos.class.php', 'Accesos', $this->id );
				break;

			case 'lista-usuarios':
			case 'crear-usuario':
			case 'cambia-password':
				return $this::generalCall( 'usuarios.class.php', 'Usuarios', $this->id );
			break;

			//el inicio varía respecto al tipo de usuario. El 2 indica Usuario Normal, de lo contrario es Admin 
			case 'inicio':
				if($this->id_tipo_user == 2)
				{
					return $this::generalCall( 'asistencia.class.php', 'Asistencia', $this->id );
				}else{
					return $this::generalCall( 'asistencia-admin.class.php', 'AsistenciaAdmin', $this->id );
				}

			break;

            default:
                # code...
				return $this->error;
                break;
        }
    }

    /**
     * generalCall()
     * @param string file
     * @param string className
     * @param string idItem
     * @return string
     */
    private function generalCall( $file        = null,
                                  $className   = null,
                                  $idItem      = null   )
    {
        if( require_once( $file ) ) { $ob = new $className($idItem); return $ob->getCode();   }
        else return "error al cargar clase";
    }

    /**
     * arreglaFechas()
     * @param string fecha
     * @return string
     */
    private function arreglaFechas( $fecha = null )
    {
        $div = $this::separa( $fecha , '-'  );

        if( count( $div ) > 0 )
            return "{$div[2]}-{$div[1]}-{$div[0]}";
        else return "Error de Formato";
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
