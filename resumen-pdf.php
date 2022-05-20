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

require_once( "class/asistencia.class.php" );
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
      
      private $imagen;
      private $firma;
      private $consultas;

      public function Header()
      {
        $this->fecha_hoy 		    =  date("Y-m-d");
        $this->fecha_hora_hoy	  =  date("Y-m-d H:i:s");        
        $this->mes              =  date("m");
        $this->dia              =  date("d");
        $this->year             =  date("Y");

        $oConf    = new config();
    	  $cfg      = $oConf->getConfig();


        $db       = new mysqldb( 	$cfg['base']['dbhost'],
    															$cfg['base']['dbuser'],
    															$cfg['base']['dbpass'],
    															$cfg['base']['dbdata'] );

        $this->consultas 					= new querys( $db );


        $this->mes_cod = $_GET['mes_cod'];
        $this->imagen  = "gfx/logo-socma.png";

        $arr = $this->consultas->meses( $_GET['mes_cod'] );
        $MES_NAME = "";
        $DIAS = "";

        foreach ($arr['process'] as $key => $value) {
          $MES_NAME .= $value['nombre'];
          $DIAS .= $value['dias'];
        }

        $this->mes  = $MES_NAME;
        $this->dias = $DIAS;

        if( !isset($_GET['id_user']) )
              $this->yo   = $_SESSION['yo'];
        else  $this->yo   = $_GET['id_user']; 

      }

      public function cuerpo()
      {

        if( !isset($_GET['id_user']) )                                
            {
              
              $user = "{$_SESSION['nombres']} {$_SESSION['apaterno']} {$_SESSION['amaterno']}";
              $rut  = $this::separa( $_SESSION['rut'],'-' );

            }
        else{
          $arr         = $this->consultas->usersListado( $_GET['id_user'] );    
          $user = "";
          $rut  = "";


          foreach ($arr['process'] as $key => $value) {
              # code...
              $user .= "{$value['nombres']} {$value['apaterno']} {$value['amaterno']}";
              //$rut_aux = $value['rut']; 
              $rut  =  $this::separa( $value['rut'],'-' ) ;
          }
        }

        
        $rut_total = $this::separa_miles( $rut[0] )."-".$rut[1];



        $this->Image( $this->imagen, 75,10, 45  );
        $this->Ln(6);
        $this->setFont('Arial','B',10);
        $this->Cell(120);
        $this->Cell(20,7, null,0,0);
        $this->setFont('Arial','',10);
        $this->Cell(30,7,null,0,0);

        $this->Ln();  
        $this->Ln();
        $this->setFont('Arial','B',10);
        $this->Cell(35);
        $this->Cell(30,7, utf8_decode('Resumen de Asistencia Desarrollo Y Tecnología SOCMA Ltda.'),0,0);
        $this->Ln();
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
        $this->Ln();
        $this->Cell(5);
        $this->setFont('Arial','B',10);
        $this->Cell(30,7, utf8_decode('2. ASISTENCIAS MES DE '. strtoupper($this->mes)),0,0);
        $this->Ln();
        $this->setFont('Arial','B',8);
        $this->Cell(10);
        $this->Cell(10,6, utf8_decode('DIA'),1,0);
        $this->Cell(55,6, utf8_decode('Hora de Entrada '),1,0);
        $this->Cell(55,6, utf8_decode('Hora de Salida '),1,0);
        $this->setFont('Arial','',8);

        for ($i=1; $i <=$this->dias ; $i++) { 
        
          $this->Ln();  
          $this->Cell(10);
          $this->Cell(10,6, $i ,1,0);
          
          $dia          = ( $i < 10 ) ? "0{$i}" : $i;
          $hoy          = "{$this->year}-{$this->mes_cod}-{$dia}"; 
          
          $consult      = $this->consultas->asistencias( $this->yo, $hoy, $this->mes_cod,$this->year );           
          
          $hora_inicio  = "NO HAY REGISTROS DE ENTRADA";
          $hora_fin     = "NO HAY REGISTROS DE SALIDA";
                    
          foreach ($consult['process'] as $key => $value) {
            # code...
                                 
            $hora_inicio  = ( is_null( $value['hora_inicio'] ) || $value['hora_inicio']=='' ) ? "NO HAY REGISTROS DE ENTRADA": $value['hora_inicio']." hrs.";
            $hora_fin     = ( is_null( $value['hora_fin'] ) || $value['hora_fin']=='' ) ? "NO HAY REGISTROS DE SALIDA": $value['hora_fin']." hrs.";
                                                
          }

          $this->Cell(55,6, "{$hora_inicio}" ,1,0);
          $this->Cell(55,6,"{$hora_fin}" ,1,0);
          
        }        
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
