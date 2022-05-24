<?php 
    class Accesos
    {
        private $id;
        private $template;
        private $consultas;
        private $ruta;
        private $error;
        
        function __construct( $id = null )
        {
            $this->id = $id;
            $oConf    = new config();
              $cfg      = $oConf->getConfig();
              $db       = new mysqldb( 	$cfg['base']['dbhost'],
                                        $cfg['base']['dbuser'],
                                        $cfg['base']['dbpass'],
                                        $cfg['base']['dbdata'] );
        
            $this->consultas = new querys( $db );
            $this->template  = new template();
            $this->ruta      = $cfg['base']['template'];
            $this->error     = "Modulo no definido o no Desarrollado para {$this->id}";
            $this->yo        = $_SESSION['yo'];
        }

        private function control()
        {
            switch ($this->id) {
                case 'accesos':
                    # code...
                    return $this::tablaAccesos();
                    break;
                
                default:
                    # code...
                    return $this->error;
                    break;
            }
        }

        private function tablaAccesos()
        {
            $arr = $this::trAccesos();

            $data = ['###tr###' => $arr['code'] , '###total-recs###' => $arr['total-recs'] ];
            return $this::despliegueTemplate( $data, 'accesos/tabla-accesos.html' );
        }

        private function trAccesos()
        {
            #some..
            $arr  = $this->consultas->listaAccesos();
            $code = "";
            $i    = 0;

            foreach ($arr['process'] as $key => $value) {
                # code...

                $user = "{$value['nombres']} {$value['apaterno']} {$value['amaterno']}";

                $data = ['###num###'        => $i+1,
                         '###fecha###'      => $value['fecha'],
                         '###ip###'         => $value['ip'],    
                         '###session###'    => $value['sesion'],    
                         '###usuario###'    => strtoupper( $user) ];
                $code.= $this::despliegueTemplate($data, 'accesos/tr-accesos.html');

                $i++;
            }

            $out['code'] = $code;
            $out['total-recs'] = $arr['total-recs'];

            return $out;
        }

          /**
         * separa(): método que que divide cadenas de acuerdo a un determinado simbolo, ej '.','-',' '
         * @param string cadena
         * @param string simbolo
         * @return array() of string
         * 
         *  */ 
        private function separa($cadena=null,$simbolo=null)
        {
            if( is_null($cadena) )
                return null;
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
         * getCode(): llamada de método control y despliegue de los elementos de éste
         * @return string
         *  */   
        public function getCode()
        {
            return $this::control();
        }
    }
?>