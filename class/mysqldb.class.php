<?php
/**
 * Clase para manejar base de datos MySQL
 *
 * Ejemplo de uso:
 * <code>
 * $foo  = new mysqldb("localhost","user","pass","dbname");
 * $id   = $foo->insert("INSERT INTO bar VALUES ('foo','bar')");
 * $data = $foo->select("SELECT count(*) as foobar FROM bar");
 * echo "hay " . $data[0]['foobar'];
 * </code>
 */

/**
 * Clase para manejar base de datos MySQL
 *
 * Clase para manejar base de datos MySQL a travez de una sentencias SQL liberando la coneccion a la base de datos.
 * Ajustada al formato para phpDoc
 *
 * @author Roy Alvear <roy@tchile.com>
 * @license GPL 2
 * Original date 2006-12-18
 * Change date 2013-01-08 mysql to mysqli
 * @version 2.0.0
 * @package tmonitor
 */
class mysqldb
{
    /**
     * @var string hostname
     */
    private $host;

    /**
     * @var string username
     */  
    private $user;

    /**
     * @var string password
     */  
    private $pwd;

    /**
     * @var string database name
     */  
    private $bd;
    
    /**
     * @var bdconection
     */
    private $link;


    /**
     * @param string $host
     * @param string $user
     * @param string $pwd
     * @param string $bd
     */ 
    public function __construct($host=null,$user=null,$pwd=null,$bd=null)
    {
        $this->init();
        if($host)
            $this->host	= $host;
        if($user)
            $this->user	= $user;
        if($pwd)
            $this->pwd	= $pwd;
        if($bd)
            $this->bd	= $bd;
    }

    /**
     * @param void
     * @return void
     */ 
    private function init()
    {
        $this->host = "";
        $this->user = "";
        $this->pwd  = "";
        $this->bd   = "";
    }

    /**
     * @param void
     * @return void
     */ 
    private function connect()
    {
        
        $this->link = new mysqli( $this->host, $this->user, $this->pwd, $this->bd );
        if ($this->link->connect_errno)
            die("error: DB die, no connect");
    }

    /**
     * @param void
     * @return void
     */ 
    private function close()
    {
        $this->link->close();
    }

    /**
     * ingresa usando una sentencia SQL retornando el valor del registro creado
     * @param string $strsql
     * @return int
     */
    public function insert($strsql)
    {
        $this->connect();
        if( ! $this->link->query($strsql) ){
            echo "fallo";
            return null;
        }
        $id = $this->link->insert_id;
        $this->close();
        return $id;
    }

    /**
     * actualiza usando una sentencia SQL retornando el valor verdadero si tiene exito
     * @param string $strsql
     * @return bool
     */
    public function update($strsql)
    {
        $this->connect();
        if(!$this->link->query($strsql))
            return false;
        $ok = true;
        $this->close();
        return $ok;
    }

    /**
     * elimina usando una sentencia SQL retornando el valor verdadero si tiene exito
     * @param string $strsql
     * @return bool
     */
    public function delete($strsql)
    {
        $this->connect();
        if(!$this->link->query($strsql))
            return false;
        $ok = true;
        $this->close();
        return $ok;
    }

    /**
     * retorna un arreglo con el resultado de una sentencia SQL
     * @param string $qry 
     * @return array
     */
    public function select($qry)
    {
        $this->connect();
        $arr = array();
        $selec = $this->link->query($qry);
        if (!$selec)
           return $arr;
        if ($selec->num_rows > 0){
            $x=0;
            while($row = $selec->fetch_row()){
                foreach($row as $i => $value) {
                    $info_campo = $selec->fetch_field_direct($i);
                    $column = $info_campo->name;
                    $data["$column"] = $value;
                    $arr[$x] = $data;
                }
                $x++;
            }
        }
        $selec->free_result();
        $this->close();
        return $arr;
    }

}
?>
