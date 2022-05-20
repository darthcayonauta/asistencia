<?php
/**
 * Manejo de template por marca
 *
 * Ejemplo
 * <code>
 * $data = array('###title###' => 'mi website', '###content###' => 'Under Construction');
 * $obtpl = new template();
 * $obtpl->setTemplate('/miproyecto/template/index.tpl');
 * $obtpl->llena($data);
 * $obtpl->limpia();
 * echo $obtpl->getCode();
 * </code>
 */

/**
 * @package tmonitor
 * @author Roy Alvear <racl@gulix.cl>
 * @license GPL 2
 * @version 1.2.0
 */
class template
{
    /**
     * @var string 
     */
    private $template;

    /**
     * constructor de la clase
     *
     * @param string $archi Archivo con su path
     */
    public function __construct( $archi=null )
    {
        $this->template = "";
        if($archi)  
            $this->setTemplate($archi);
    }

    /**
     * llenar un template con el arreglo
     *
     * @param array $arreglo
     * @return void
     */
    public function llena($arreglo)
    {
        if(count($arreglo))
            foreach($arreglo as $var => $val)
                $this->template=str_replace($var,$val,$this->template);
    }

    /**
     * elimina etiquetas no llenadas
     *
     * @param void
     * @return void
     */
    public function limpia()
    {
        $this->template = preg_replace("/###(\w+)(\d*)###/i","",$this->template);
    }

    /**
     * lee un archivo de template y lo carga
     *
     * @param string $archi Archivo con su path
     * @return void
     */
    public function setTemplate($archi)
    {
        if(file_exists($archi)){
            $fil = fopen($archi,"r");
            $cont="";
            while(!feof($fil))
                $cont .= fgets($fil);
            fclose($fil);
            $this->template=$cont;
        } else
            die("No existe ".$archi." inconsistencia!!! ...por favor, avise de este error al webmaster");
    }

    /**
     * regresa el codigo
     *
     * @param void
     * @return string
     */
    public function getCode()
    {
        return $this->template;
    }

}

?>
