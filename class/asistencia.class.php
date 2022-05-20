<?php
/**
 * @author ing Claudio Guzmán Herrera
 * @version 1.0
 */
class Asistencia 
{
    private $consultas;
	private $template;
	private $ruta;
	private $id;
	private $menu;
	private $fecha_hoy;
	private $yo;
	private $tipo_user;
    
    function __construct( $id = null )
    {
        $this->id               = $id;
        $this->yo               = $_SESSION['yo'];
        $oConf                  = new config();
        $cfg                    = $oConf->getConfig();
        $db                     = new mysqldb( $cfg['base']['dbhost'],
                                             $cfg['base']['dbuser'],
                                             $cfg['base']['dbpass'],
                                             $cfg['base']['dbdata'] );

        $this->consultas 		= 	new querys( $db );
        $this->template  		= 	new template();
        $this->ruta      		= 	$cfg['base']['template'];
        $this->id 				= 	$id;
        $this->error 			= 	"No existe el modulo para id {$this->id}";
        $this->fecha_hoy 		=  date("Y-m-d");
        $this->fecha_hora_hoy	=  date("Y-m-d H:i:s");
        
        $this->mes              =  date("m");
        $this->dia              =  date("d");
        $this->year             =  date("Y");

        $this->tipo_user 		=  $_SESSION['tipo_user'];
        $this->apaterno 		=  utf8_decode(  $_SESSION['apaterno'] );
        $this->amaterno 		=  $_SESSION['amaterno'];
        $this->nombres 			=  $_SESSION['nombres'];		

    }

    private function control()
    {
        switch ($this->id) {
            //case 'consultarMes':
            case 'consultarMes':
                # code...
                return $this::consultarMes();
                break;    


            case 'inicio':
                return $this::formAsistencia();
                break;
            
             case 'ingresaDataIngreso':
                 return $this::ingresaDataIngreso();                   
                 break;   

             case 'updateDataIngreso':

                return $this::updateDataIngreso();    
                break;

            default:
                return $this->error;
                break;
        }
    }

    private function consultarMes()
    {
        echo $this::tablaDias();
    }



    private function updateDataIngreso()
    {
        //print_r($_POST);
        $hora_fin = $this::horaMinutosOk( $_POST['hora_salida']-1 ).":".$this::horaMinutosOk( $_POST['minutos_salida']-1 );
        if( $this->consultas->updateAsistencia( $this->yo, $this->fecha_hoy, $hora_fin ) )
        {
            echo $this::tablaDias();
        }else{
            echo "Error de update";
        }        
    }

    private function ingresaDataIngreso()
    {      
        $hora_inicio = $this::horaMinutosOk( $_POST['hora_entrada']-1 ).":".$this::horaMinutosOk( $_POST['minutos_entrada']-1 );

        if( $this->consultas->procesaAsistencia( $this->yo,
                                                 $this->fecha_hoy, 
                                                 $hora_inicio, 
                                                 null,
                                                 $_POST['mes'], 
                                                 $_POST['year'] ) )
        {
            echo $this::tablaDias();
        }else{
            return "Existe Error de ingreso de datos";

        }
    }

    private function horaMinutosOk( $data = null )
    {
        $dataOk = ( $data < 10 )? "0{$data}":$data;
        return $dataOk;
    }


    /**
     * botonera() : Despliegue de los botones de los meses
     * @return string
     * 
     */
    private function botonera()
    {
        $arr = $this->consultas->meses();
        $ob_sel = new Select( $arr['process'], 'textual','nombre','id_meses','Seleccione Mes',null,1 );


        $data = ['###select-meses###' => $ob_sel->getCode() ];
        return $this::despliegueTemplate( $data, 'formAsistencia/botonera-meses.html' );
    } 


    /**
     * formAsistencia()::: generacion de formulario de asistencia
     * @return string
     */
    private function formAsistencia()
    {
        return $this::tablaDias();
    }

    /**
     * tablaDias(): Despliegue del listado de días en el template
     * @return string
     */
    public function tablaDias()
    {
 /**
          * $_POST['mes'] , es lo que capturo desde el combobox cuando elijo un mes, en la pantalla principal
          * si no existe este POST, utilizo el valor del atributo '$this->mes' por defecto
         */
        if(!isset( $_POST['mes'] ))
                $mes = $this->mes;
        else    $mes = $_POST['mes']; 


        $arr        = $this->consultas->meses( $mes );
        $mes        = "";
        $mes_cod    = "";

        foreach ($arr['process'] as $key => $value) {
            # code...
            $mes        .= $value['nombre'];
            $mes_cod    .= $value['textual'];

        }

        $data = ['###tr###'         => $this::trDias(),
                 '###year###'       => $this->year  , 
                 '###mes###'        => $mes  , 
                 '###mes_cod###'    => $mes_cod  ,                  
                 '###botonera###'   => $this::botonera()

    ];
        return $this::despliegueTemplate( $data, 'formAsistencia/tabla-dias.html');
    }

    /**
     * trDias(): Despliegue de los días propiamente tal
     * @return string
     */
    private function trDias()
    {
        /**
          * $_POST['mes'] , es lo que capturo desde el combobox cuando elijo un mes, en la pantalla principal
          * si no existe este POST, utilizo el valor del atributo '$this->mes' por defecto
         */
        if(!isset( $_POST['mes'] ))
                $mes = $this->mes;
        else    $mes = $_POST['mes']; 

        /**
         * consultamos y procesamos el mes que corresponda
         */
        $arr = $this->consultas->meses( $mes );
        $dias = "";
        $code ="";

        foreach ($arr['process'] as $key => $value) {
            $dias .= $value[ 'dias' ];
        }

       //el despliegue de las filas depende del numero de días del mes 
       for ($i=1; $i <= $dias; $i++) 
       {                  
           $dia = ( $i < 10 ) ? "0{$i}" : $i;
           $hoy = "{$this->year}-{$mes}-{$dia}"; 
           $id  = null;   

           if( $this->fecha_hoy ==  $hoy  ) 
            {
                $consult = $this->consultas->asistencias( $this->yo, $this->fecha_hoy );       
                #si no hay registros en la db, que muestre formulario
               
                if ( $consult['total-recs'] == 0 )
                {
                    $hora_inicio = $this::horasMinutos('entrada');
                    $hora_salida = $this::horasMinutosDisabled('salida');
    
                    $btn = "<button class='btn btn-block btn-info' 
                                    id = 'send-data-{$i}'>
                                    <i class='fas fa-angle-double-right'></i> INGRESO
                            </button>";
                }else{

                    $hora_inicio = ""; 
                    $hora_salida = "";
                    $salida      = null;

                    foreach ($consult['process'] as $key => $value) 
                    {
                        $hora_inicio .= "<div class='my-alert-salida'><strong>{$value['hora_inicio']}</strong> hrs _</div> ";
                        $salida      =  $value['hora_fin'];
                        $id          =  $value['id'];  
                    }

                    if( is_null($salida) || $salida == '' )
                    {
                        $hora_salida = $this::horasMinutos('salida');

                        $btn = "<button class='btn btn-block btn-secondary' 
                                        id = 'update-data-{$i}'>
                                        <i class='fas fa-angle-double-right'></i> SALIDA
                                </button>";
                    }else {
                        $btn = null;
                        $hora_inicio = "<div class='my-alert-salida'><strong>{$value['hora_inicio']}</strong> hrs _</div> ";
                        $hora_salida .= "<div class='my-alert-salida'><strong>{$salida}</strong> hrs _</div> ";

                    }
                }

            }else {

                if(!isset( $_POST['mes'] ))
                        $mes = $this->mes;
                else    $mes = $_POST['mes']; 


                $consult = $this->consultas->asistencias( $this->yo, $hoy, $mes,$this->year );           
                
                $msg_inicio = "<div class='my-alert'><i class='fas fa-exclamation-triangle'></i> NO HAY REGISTROS DE <strong>ENTRADA _</strong> </div >"; 
                $msg_salida = "<div class='my-alert'><i class='fas fa-exclamation-triangle'></i> NO HAY REGISTROS DE <strong>SALIDA _</strong></div >";

                if( $consult['total-recs'] == 0 )
                {
                    $hora_inicio = $msg_inicio;                    
                    $hora_salida = $msg_salida;
                }else{

                    foreach ($consult['process'] as $key => $value) {

                        $id          = $value['id'];   
                        $hora_inicio = ( is_null( $value['hora_inicio'] ) || $value['hora_inicio'] ==''  ) ? $msg_inicio : "<div class='my-alert-salida'><strong>{$value['hora_inicio']}</strong> hrs _</div>";
                        $hora_salida = ( is_null( $value['hora_fin'] ) || $value['hora_fin'] ==''  ) ? $msg_salida : "<div class='my-alert-salida'><strong>{$value['hora_fin']}</strong> hrs _</div>";                                        
                    }
                }

                $btn = null;                      
            }
            
           $data = [  '###dia###'           => $dia , 
                      '###hora-inicio###'   => "{$hora_inicio}",
                      '###hora-salida###'   => $hora_salida,  
                      '###button###'        => $btn,
                      '###id###'            => $id , 
                      '###identificador###' => $i,
                      '###fecha###'         => $this->fecha_hoy ,
                      '###id_user###'       => $this->yo,
                      '###mes###'           => $mes   ]; 

           $code .= $this::despliegueTemplate( $data, 'formAsistencia/tr-dias.html' );
       }     

       return $code;
    }

   /**
    * selectDisabled(): select disabled
    * @param string nombre
    * @param string identificador
    * 
    *
    */ 
   private function selectDisabled( $nombre = null, $identificador = null, $valor = null )
   {
       $data = ['###nombre###'          => $nombre, 
                '###identificador###'   => $identificador,
                '###valor###'           => $valor           ];

        return $this::despliegueTemplate( $data, 'select-disabled.html' );
   } 

   /**
    * horasMinutosDisabled() : despliego las horas y los minutos pero no puedo ejecutar ni desplegar acciones
    * @param    string identificador
    * @return   string
    */
   private function horasMinutosDisabled( $identificador = null )
   {
       $sel_hora    = $this::selectDisabled("Hora {$identificador}","id_hora_{$identificador}",0);
       $sel_minutos = $this::selectDisabled("Minutos {$identificador}","id_minutos_{$identificador}",0);


       $data = [ '###select-hora###' => $sel_hora,'###select-minutos###' => $sel_minutos,  ];
       return $this::despliegueTemplate( $data, 'formAsistencia/horas-minutos-form.html' );
   }


    private function horasMinutos( $identificador = null )
    {
        $sel_hora    = $this::horas('id_hora_'.$identificador."-{$this->fecha_hoy}", $identificador );
        $sel_minutos = $this::minutos('id_minutos_'.$identificador."-{$this->fecha_hoy}",$identificador);


        $data = [ '###select-hora###' => $sel_hora,'###select-minutos###' => $sel_minutos,  ];
        return $this::despliegueTemplate( $data, 'formAsistencia/horas-minutos-form.html' );
    }


    private function horas( $id,$complemento = null )
    {
        $arr = $this->consultas->horas();

        $select = new Select( $arr['process'], 'id','descripcion', $id ,"Hora {$complemento}" );
        return $select->getCode();
    }

    private function minutos( $id,$complemento = null )
    {
        $arr = $this->consultas->minutos();

        $select = new Select( $arr['process'], 'id','descripcion', $id ,"Minutos {$complemento}" );
        return $select->getCode();
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