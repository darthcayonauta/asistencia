<?php
header('Cache-Control: no cache');
//session_cache_limiter('public'); // works too session_start();
session_cache_limiter('private, must-revalidate');
session_cache_expire(60);
define('DURACION_SESION','7200'); //2 horas
ini_set("session.cookie_lifetime",DURACION_SESION);
ini_set("session.gc_maxlifetime",DURACION_SESION);
ini_set("session.save_path","/tmp");
session_cache_expire(DURACION_SESION);

session_start();
session_regenerate_id(true);

include("class/mysqldb.class.php");
include("class/querys.class.php");
include("class/template.class.php");
include("class/menu.class.php");
include("class/codifica.class.php");

include("class/content-page.class.php");
include("class/select.class.php");
require_once( "class/rendiciones.class.php" );
include("class/utilesmodulo.class.php");
include("config.php");

  if( $_SESSION['autenticado'] == 1   )
  {
    require_once( "fpdf/fpdf.php" );
    /**
     *
     */
    class PDF extends FPDF
    {
      private $codigo_rendicion;
      private $imagen;
      private $firma;
      private $consultas;

      public function Header()
      {
        $oConf    = new config();
    	  $cfg      = $oConf->getConfig();


        $db       = new mysqldb( 	$cfg['base']['dbhost'],
    															$cfg['base']['dbuser'],
    															$cfg['base']['dbpass'],
    															$cfg['base']['dbdata'] );

        $this->consultas 					= new querys( $db );


        $this->codigo_rendicion = $_GET['codigo_rendicion'];
        $this->imagen           = "gfx/logo-socma.png";
      }

      public function cuerpo()
      {

        $ob_rendiciones = new Rendiciones();
        $data_rendiciones = $ob_rendiciones->dataRendicion( $this->codigo_rendicion );

        $user = "{$data_rendiciones['nombres']} {$data_rendiciones['apellido_paterno']} {$data_rendiciones['apellido_materno']}";
        $this->firma = "gfx/{$data_rendiciones['firma']}" ;

        $rut = $this::separa( $data_rendiciones['rut'],'-' );
        $rut_total = $this::separa_miles( $rut[0] )."-".$rut[1];
        $this->Image( $this->imagen, 75,15, 45  );
        $this->Ln(8);
        $this->setFont('Arial','B',10);
        $this->Cell(120);
        $this->Cell(20,7, utf8_decode( 'Código :' ),0,0);
        $this->setFont('Arial','',10);
        $this->Cell(30,7,$this->codigo_rendicion,0,0);
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->setFont('Arial','B',10);
        $this->Cell(35);
        $this->Cell(30,7, utf8_decode('Resumen de Rendición Desarrollo Y Tecnología S.O.C.M.A.'),0,0);
        $this->Ln();$this->Ln();
        $this->setFont('Arial','B',11);
        $this->Cell(5);
        $this->Cell(30,7, utf8_decode('1. INFORMACION PERSONAL'),0,0);
        $this->Ln();
        $this->setFont('Arial','',10);
        $this->Cell(10);
        $this->Cell(15,7, utf8_decode('Nombre : '),0,0);
        $this->Cell(10,7, utf8_decode($user),0,0);
        $this->Ln();
        $this->setFont('Arial','',10);
        $this->Cell(10);
        $this->Cell(15,7, utf8_decode('RUN : '),0,0);
        $this->Cell(10,7, utf8_decode( $rut_total ),0,0);
        $this->Ln();$this->Ln();
        $this->setFont('Arial','B',11);
        $this->Cell(5);
        $this->Cell(30,7, utf8_decode('2. DETALLE RENDICION'),0,0);
        $this->Ln();
        $this->setFont('Arial','B',9);
        $this->Cell(10);
        $this->Cell(20,7, utf8_decode('Item'),1,0);
        $this->Cell(65,7, utf8_decode('Detalle'),1,0);
        $this->Cell(30,7, utf8_decode('Tipo Documento'),1,0);
        $this->Cell(25,7, utf8_decode('N° Documento'),1,0);
        $this->Cell(20,7, utf8_decode('Fecha'),1,0);
        $this->Cell(20,7, utf8_decode('Monto'),1,0);

        $arr = $this->consultas->detalle_rendicion( $this->codigo_rendicion );

        $this->setFont('Arial','',9);
        $suma_montos = 0;
        foreach ($arr['process'] as $key => $value) {

          $this->Ln();
          $this->Cell(10);
          $this->Cell(20,7, utf8_decode($value['descItem']),1,0);
          $this->Cell(65,7, utf8_decode( str_replace( '+',' ', $value['detalle'] )  ),1,0);
          $this->Cell(30,7, utf8_decode($value['descTipoDocumento']),1,0);
          $this->Cell(25,7, utf8_decode( $value['num_documento'] ),1,0);
          $this->Cell(20,7, utf8_decode( $this::arreglaFechas(  $value['fecha'] ) ),1,0);
          $this->Cell(20,7, utf8_decode( "$". $this::separa_miles( $value['monto'] ) ),1,0);
          $suma_montos = $suma_montos + $value['monto'];

        }

        $saldo_favor_empresa = $data_rendiciones['monto_asignado'] - $suma_montos;
        $saldo_favor_funcionario = $suma_montos - $data_rendiciones['monto_asignado'];

        $this->Ln();$this->Ln();
        $this->setFont('Arial','B',11);
        $this->Cell(5);
        $this->Cell(30,7, utf8_decode('3. RESUMEN RENDICION'),0,0);
        $this->setFont('Arial','',9);
        $this->Ln();
        $this->Cell(10);
        $this->Cell(40,7, utf8_decode('Total a Rendir '),1,0);
        $this->Cell(140,7, utf8_decode( "$". $this::separa_miles(  $suma_montos )   ),1,0);
        $this->Ln();
        $this->Cell(10);
        $this->Cell(40,7, utf8_decode('Monto Asignado '),1,0);
        $this->Cell(140,7, utf8_decode( "$". $this::separa_miles(  $data_rendiciones['monto_asignado'] )   ),1,0);
        $this->Ln();
        $this->Cell(10);
        $this->Cell(40,7, utf8_decode('Saldo Favor Empresa '),1,0);
        $this->Cell(140,7, utf8_decode( "$". $this::separa_miles(  $saldo_favor_empresa )   ),1,0);
        $this->Ln();
        $this->Cell(10);
        $this->Cell(40,7, utf8_decode('Saldo Favor Funcionario '),1,0);
        $this->Cell(140,7, utf8_decode( "$". $this::separa_miles(  $saldo_favor_funcionario )   ),1,0);
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->Ln();
        $this->setFont('Arial','B',11);
        $this->Cell(75);
        $this->Cell(50,7, utf8_decode($user),0,0);
        $this->Ln();
        $this->Cell(83);
        $this->Cell(50,7, $rut_total,0,0);
        $this->Ln();
        //$this->Image( $this->firma, 93,$this->GetY(),25  );

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
    }

    //main()
      $pdf = new PDF();
      $pdf->AliasNbPages();
      $pdf->addPage('P','Letter');
      $pdf->cuerpo();
      $pdf->Output();

  }else{

    echo "FUERA DE SESION O NO LOGUEADO";
  }
?>
