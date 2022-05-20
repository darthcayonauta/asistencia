<?php
/**
 *
 */
class Menu
{

  private $id_tipo_user;
  private $template;
  private $ruta;
  private $consultas;
  private $yo;

  function __construct(  $yo           = null,
                         $id_tipo_user = null )
  {

      $this->id_tipo_user       = $id_tipo_user;
      $oConf                    = new config();
  	  $cfg                      = $oConf->getConfig();
  	  $db                       = new mysqldb( 	 $cfg['base']['dbhost'],
  															                 $cfg['base']['dbuser'],
  															                 $cfg['base']['dbpass'],
  															                 $cfg['base']['dbdata'] );

      $this->consultas 					= new querys( $db );
      $this->template  					= new template();
      $this->ruta      					= $cfg['base']['template'];
  		$this->error 							= $cfg['base']['error'];
  		$this->fecha_hoy 					= date("Y-m-d");
  		$this->fecha_hora_hoy 		= date("Y-m-d H:i:s");
      $this->yo                 = $yo;
  }

  private function control()
  {
      $data = array('@@@LI-CONJUNTO' => $this::enlaces(),
                    '@@@yo'          => $this->yo);

      return $this::despliegueTemplate( $data,"navbar.html" );
  }

  private function enlaces()
  {
      $code = "";
      $arr  = $this->consultas->menu( $this->id_tipo_user );

      $i = 0;

      foreach ($arr['process'] as $key => $value)
      {
        $class ="";
        if( $i < ( $arr['total-recs'] -1)  )
        $class = "";

          if( $value['dropdown']  == 0 )
          {
            $data = array('@@@DROPDOWN'     =>'',
                          '@@@TOGGLE'       =>'',
                          '@@@ID-LINK'      => $value['id_link'],
                          '@@@LINK'         => $value['link'].'?id='.base64_encode( $value['id_link'] ),
                          '@@@RESTO'        => '',
                          '@@@DESCRIPCION'  => $value['descripcion'],
                          '@@@SUBMENU'      => '',
                          '@@@class'        => $class);

            $code.= $this::despliegueTemplate( $data, "navbar-li.html" );

          }else {

            $data = array('@@@DROPDOWN'     =>'dropdown',
                          '@@@TOGGLE'       =>'dropdown-toggle',
                          '@@@ID-LINK'      => $value['id_link'],
                          '@@@LINK'         => $value['link'],
                          '@@@RESTO'        => 'id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"',
                          '@@@DESCRIPCION'  => $value['descripcion'],
                          '@@@SUBMENU'      => $this::submenu( $value['id'] ),
                          '@@@class'        => $class
                          );

            $code.= $this::despliegueTemplate( $data, "navbar-li.html" );
          }

         $i++;
      }

      return $code;
  }

  private function submenu( $id_menu = null  )
  {

    $data = array( '@@@enlaces' => $this::enlacesSubmenu( $id_menu ) );
    return $this::despliegueTemplate( $data, "navbar-submenu-principal.html" );

  }

  private function enlacesSubmenu( $id_menu = null )
  {
    $code = "";

    $arr = $this->consultas->sub_menu( $id_menu );

    foreach ($arr['process'] as $key => $value) {

      $data = array('@@@ID-LINK'      => base64_encode( $value['id_link'] ),
                    '@@@LINK'         =>   $value['link'] ,
                    '@@@DESCRIPCION'  => $value['descripcion']
                  );

      $code.= $this::despliegueTemplate( $data, "navbar-final.html" );
    }

    return $code;
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

	public function getCode()
	{

		return $this::control();
	}
}
?>
