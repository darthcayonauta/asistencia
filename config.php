<?php
/**
 * config.php
 *
 * Ejemplo:
 * <code>
 * $oConf  = new config();
 * $cfg    = $oConf->getConfig();
 * $template = $cfg['base']['template'];
 * </code>
 * modificado el dia 050320201
 */

include_once("class/mysqldb.class.php");

/**
 * config
 *
 * @author Claudio Guzman Herrera
 * @version 0.0.1
 * @package dissa
 */
class config
{
    private $data;
    /**
     *
     */
    function __construct()
    {
       // @ session_start();
        $conf = array();
        if ( isset($_SESSION['config']) ){ //si la configuracion esta en memoria
            $conf = $_SESSION['config'];
        } else { // sino leer configuraciones y cargarlas en memoria

          //  $home = "/home/claudio/webs/inventario/";

          $home = null;

            $conf['base']['template']   =  $home.'tpl/';
            $conf['base']['lang']       = 'es';

            $conf['base']['dbdata']     = 'asistencia';
            $conf['base']['dbuser']     = 'root';
            $conf['base']['dbpass']     = '';

            //$conf['base']['dbdata']     = 'socmacl_asistencias';
            //$conf['base']['dbuser']     = 'socmacl_claudio';
            //$conf['base']['dbpass']     = 'cayofilo102';


            $conf['base']['dbhost']     = 'localhost';


            $conf['base']['dbpref']     = '';
            $conf['base']['error']      = 'Error inesperado';

            $conf['base']['modulos']    = 'modulos/';
            $conf['base']['images']     = 'img/';
            $conf['base']['thumbs']     = 'thumbs/';
            $conf['base']['tipo_regata']    = 1;

            $conf['base']['ftpUser']    = 'claudio';
            $conf['base']['ftpPass']    = 'x';
            $conf['base']['ftpFolder']  = '/home/claudio/webs/repositorio/dox/';

            $conf['base']['email_admin'] = "claudio.guzman@socma.cl";

            $conf['base']['ftpHost']    = 'localhost';
            $conf['base']['ftpPort']    = 21;

            if ( isset($_SESSION['base']['template'])) {
                $conf['base']['template'] = $_SESSION['base']['template'];
            } else {
                $_SESSION['base']['template'] = $conf['base']['template'];
            }

            $_SESSION['base']   = $conf['base'];
            $_SESSION['config'] = $conf;
        }
        $this->data = $conf;
    }

    public function getConfig()
    {
        return $this->data;
    }
}
?>
