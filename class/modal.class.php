<?php
/**
 * @author Claudio Guzman Herrera
 */
class Modal
{

  private $target;
  private $img;
  private $title;
  private $content;
  private $template;
  private $ruta;

  function __construct( $target   = null,
                        $img      = null,
                        $title    = null,
                        $content  = null  )
  {
    $oConf              = new config();
    $cfg                = $oConf->getConfig();
    $this->ruta         = $cfg['base']['template'];
    $this->template     = new template();
    $this->title        = $title;
    $this->img          = $img;
    $this->target       = $target;
    $this->content      = $content;
  }

  /**
   * modal()
   * @return string
   */
    public function salida()
    {
        $data = array('@@@TARGET'     => $this->target,
                      '@@@IMG-TITLE'  => $this->img,
                      '@@@TITLE'      => $this->title,
                      '@@@CONTENT'    => $this->content
        );

        return $this::despliegueTemplate( $data,'modal.html');
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

}

 ?>
