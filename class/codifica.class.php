<?php
/**
 *
 */
class Codifica
{

  private $cadena;
  private $accion;

  function __construct($cadena = null, $accion = null)
  {
    $this->cadena = $cadena;
    $this->accion = $accion;
  }

  public function resuelve()
  {
    if( $this->accion == 1  )
          return utf8_encode( $this->cadena );
   else   return $this->cadena;

  }
}
?>
