<?php
/**
 *@author Ing. Claudio Guzmán Herrera
 */
class MensajeRendicion
{
  private $responsable ;
  private $email ;
  private $fecha ;
  private $codigo ;
  private $total_a_rendir;
  private $monto_asignado;
  private $saldo_favor_empresa ;
  private $saldo_favor_funcionario ;
  private $ruta;

  /**
   * Método Constructor
   */
  function __construct(  $responsable             = null,
                         $email                   = null,
                         $fecha                   = null,
                         $codigo                  = null,
                         $total_a_rendir          = null,
                         $monto_asignado          = null  )
  {
    $this->responsable             = $responsable;
    $this->email                   = $email;
    $this->fecha                   = $fecha;
    $this->codigo                  = $codigo;
    $this->total_a_rendir          = $total_a_rendir;
    $this->monto_asignado          = $monto_asignado;
    $this->saldo_favor_empresa     = $monto_asignado-$total_a_rendir;
    $this->saldo_favor_funcionario = $total_a_rendir-$monto_asignado;
    $oConf                         = new config();
    $cfg                           = $oConf->getConfig();
    $this->ruta      					     = $cfg['base']['template'];
    $this->template  					     = new template();
  }

  /**
   * muestraDetalle(): despligue del detalle del mensaje
   * @return string
   */
  public function muestraDetalle()
  {
    $data = ['###responsable###'              => $this->responsable,
             '###email###'                    => $this->email,
             '###fecha###'                    => $this->fecha,
             '###codigo###'                   => $this->codigo,
             '###fecha###'                    => $this->fecha,
             '###total-a-rendir###'           => $this::separa_miles( $this->total_a_rendir ),
             '###monto-asignado###'           => $this::separa_miles( $this->monto_asignado ),
             '###saldo-favor-empresa###'      => $this::separa_miles( $this->saldo_favor_empresa ),
             '###saldo-favor-funcionario###'  => $this::separa_miles( $this->saldo_favor_funcionario) ];

    return $this::despliegueTemplate( $data, 'msg-mail-rendicion.html' );
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
}
?>
