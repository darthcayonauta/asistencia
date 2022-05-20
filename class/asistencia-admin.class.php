<?php
/**
 * @author ing Claudio Guzmán Herrera
 * @version 1.0
 */
class AsistenciaAdmin
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


        if( !isset($_POST['reg_fecha'])) 
                $this->fecha_hoy 		=  date("Y-m-d");
        else    $this->fecha_hoy        = $_POST['reg_fecha'];
        
        
        $this->fecha_hora_hoy	=  date("Y-m-d H:i:s");
        
        $this->mes              =  date("m");
        $this->dia              =  date("d");
        $this->year             =  date("Y");

        $this->tipo_user 		=  $_SESSION['tipo_user'];
        $this->apaterno 		=  utf8_decode(  $_SESSION['apaterno'] );
        $this->amaterno 		=  $_SESSION['amaterno'];
        $this->nombres 			=  $_SESSION['nombres'];	
        $this->error            = "MODULO NO DEFINIDO PARA ID '<strong>{$this->id}</strong>'";    
        

    }

    private function control()
    {
        switch ($this->id) {
            case 'inicio':
            case 'inicioAdmin':
                return $this::inicioAdmin();
                break;
            
            case 'consultarPorFecha':
                //$this->fecha_hoy = $_POST['reg_fecha'];
                return $this::consultarPorFecha();            
                break;

            case 'findDataFuncionario':
                return $this::findDataFuncionario();
                break;    

            default:
                echo $this->error;
                break;
        }
    }

    private function findDataFuncionario()
    {
        $arr         = $this->consultas->usersListado( $_POST['id_user'] );    
        $funcionario = "";

        foreach ($arr['process'] as $key => $value) {
            # code...
            $funcionario .= "{$value['nombres']} {$value['apaterno']} {$value['amaterno']}";
        }


        $arrMes     = $this->consultas->meses( $_POST['id_meses'] );
        $mes        = "";
        $mes_cod    = "";

        foreach ($arrMes['process'] as $key => $value) {
            # code...
            $mes        .= $value['nombre'];
            $mes_cod    .= $value['textual'];

        }

        $data = [ '###tabla-dias###'  => $this::tablaDias() , 
                  '###funcionario###' => strtoupper($funcionario),
                  '###mes###'         => strtoupper($mes)  ,
                  '###mes_cod###'     => $mes_cod,  
                  '###year###'        => $this->year ,
                  '###id_user###'     => $_POST['id_user']         ];


        echo $this::despliegueTemplate( $data, 'asistencia-admin/resultado-buscar.html' );

    }


    private function tablaDias()
    {
        $data = ['###tr###' => $this::trDias() ];
        return $this::despliegueTemplate( $data,'asistencia-admin/tabla-dias.html' );
    }


    private function trDias()
    {
        if(!isset( $_POST['id_meses'] ))
                $mes = $this->mes;
        else    $mes = $_POST['id_meses']; 

              /**
         * consultamos y procesamos el mes que corresponda
         */
        $arr = $this->consultas->meses( $mes );
        $dias = "";
        $code ="";

        foreach ($arr['process'] as $key => $value) {
            $dias .= $value[ 'dias' ];
        }

        for ($i=1; $i <= $dias; $i++) { 
            
            $dia = ( $i < 10 ) ? "0{$i}" : $i;
            $hoy = "{$this->year}-{$mes}-{$dia}"; 
            $id  = null;   

            $consult = $this->consultas->asistencias( $_POST['id_user'], $hoy, $mes,$this->year );           

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

            $data = [   '###dia###'           => $dia , 
                        '###hora-inicio###'   => "{$hora_inicio}",
                        '###hora-salida###'   => $hora_salida,  
                        '###button###'        => null,
                        '###id###'            => $id , 
                        '###identificador###' => $i,
                        '###fecha###'         => $this->fecha_hoy ,
                        '###id_user###'       => $_POST['id_user'],
                        '###mes###'           => $mes   ]; 

            $code .= $this::despliegueTemplate( $data, 'asistencia-admin/tr-dias.html' );
        }

        return $code;

    }

    private function consultarPorFecha()
    {
        //lo que no se debe hacer
        $header= '<h2><i class="fas fa-arrow-alt-circle-right"></i> Fecha _ 
                            <strong>
                                '.$this::arreglaFechas( $_POST['reg_fecha'] ).'
                            </strong>                                                                                 
                        </h2>                                                           
                        <div class="raya-blanca"></div>
                        <br>';

        echo $header.$this::tablaFuncionarios();
    }




    private function inicioAdmin()
    {      
        
        
        $data = [   '###listado###'      => $this::tablaFuncionarios() , 
                    '###fecha-actual###' => $this::arreglaFechas( $this->fecha_hoy),
                    '###fecha-hoy###'    => '[ <strong>HOY</strong> ]',
                    '###buscar###'       => $this::buscar()   
                ];
        return $this::despliegueTemplate( $data , "asistencia-admin/asistencia-admin.html" );
       
    }

    private function buscar(){

        $arr             = $this->consultas->usersListado(null,null,1);
        $user_struct[0]  = 'nombres';
        $user_struct[1]  = 'apaterno';
        $user_struct[2]  = 'amaterno';

        $select          = new Select( $arr['process'], 'id',$user_struct,'id_user','Usuarios',null,1 );

        $arr_meses       = $this->consultas->meses();
        $select_meses    = new Select( $arr_meses['process'], 'textual','nombre','id_meses','Seleccione Mes',null,1 );


        $data = ['###select-usuarios###'    => $select->getCode(), 
                 '###select-mes###'         => $select_meses->getCode()  ];

        return $this::despliegueTemplate( $data, 'asistencia-admin/modulo-buscar.html' ); 

    }


    private function tablaFuncionarios()
    {
        $arr = $this::trFuncionarios();

        $data = ['###tr###' => $arr['code']  ];
        return $this::despliegueTemplate($data, "asistencia-admin/tabla-users.html");
    }
//usersListado

    private function trFuncionarios()
    {
        $arr = $this->consultas->usersListado(null,null,1);
        $code = "";
        $i = 0;

        foreach ($arr['process'] as $key => $value) {
        
            $funcionario = "{$value['nombres']} {$value['apaterno']} {$value['amaterno']} ";

            if( !isset( $_POST[ 'reg_fecha'] ) )
                    $fecha_hoy = $this->fecha_hoy;
            else    $fecha_hoy = $_POST['reg_fecha'];  


            $horas = $this::displayHorasFunc( $value['id'], $fecha_hoy );
            
            $no_entrada ="<div class='my-alert'><i class='fas fa-exclamation-triangle'></i> SIN REGISTRO DE <strong>ENTRADA_</strong></div>";
            $no_salida  ="<div class='my-alert'><i class='fas fa-exclamation-triangle'></i> SIN REGISTRO DE <strong>SALIDA_</strong></div>";

            $hora_inicio = ($horas['hora-inicio'] == '' )? $no_entrada : "<div  class='my-alert-salida'><strong>".$horas['hora-inicio'] ."</strong> hrs_</div>";
            $hora_fin    = ($horas['hora-fin'] == '' )? $no_salida : "<div  class='my-alert-salida'><strong>".$horas['hora-fin'] ."</strong> hrs_</div>";

            $data = ['###funcionario###'    => strtoupper( $funcionario ) , 
                     '###num###'            => $i+1 , 
                     '###hora-entrada###'   => $hora_inicio,
                     '###hora-salida###'    => $hora_fin ];

            $code .= $this::despliegueTemplate( $data, 'asistencia-admin/tr-users.html' );

            $i++;
        }

        $out['code'] = $code;
        $out['total-recs'] = $arr['total-recs'];

        return $out;

    }

    private function displayHorasFunc( $id_user = null, $fecha = null  )
    {
        $arr            = $this->consultas->asistencias( $id_user,$fecha );
        $hora_inicio    = "";
        $hora_fin       = "";
        
        foreach ($arr['process'] as $key => $value) {
            # code...
            $hora_inicio    .= $value['hora_inicio'];
            $hora_fin       .= $value['hora_fin'];
        }

        $out['hora-inicio'] = $hora_inicio;
        $out['hora-fin']    = $hora_fin;

        return $out;
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