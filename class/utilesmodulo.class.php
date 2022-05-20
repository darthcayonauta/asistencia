<?php

include("paginas.class.php");

class utiles{

	private $sql;
	private $getCode;
	private $page;	
	private $cfgSpecial;
	private $dbName;

	function __construct($sql=null,$page=null, $dbName = null){

		
		$this->dbName 		= $dbName;

		$this->sql = $sql;
		$this->getCode = $this->paginacion();

		if(is_null( $page ))
			$this->page = 0;
		else
			$this->page = $page;

	}

   /**
	* paginacion(), metodo que despliega la paginacion.
	*
	* @return array()
    */
   private function paginacion(){
		$Objpaginas    = new paginas( $this->sql, $this->dbName );

	  //  $Objpaginas->sql=$this->sql;
		$Objpaginas->varNavegation	= " ";

		if(isset($page)) $numRegxpag = ($page * 4) + 1;

		$result	            = $Objpaginas->get_page_result();
		$num_rows           = $Objpaginas->get_page_num_rows();
		$nav_links 			= $Objpaginas->navigation("&nbsp;", "btn btn-sm btn-warning");
		$nav_info 			= $Objpaginas->page_info("al");
		$simple_nav_links 	= $Objpaginas->back_forward_link();
		$total_recs 		= $Objpaginas->get_total_rows();

		$numero_afectados   = $total_recs;

		$pag['nav_links']   = $nav_links;
		$pag['result']      = $result;
		$pag['total_recs']  = $total_recs;
		$pag['dbName'] 		= $this->dbName;
		

	return $pag;

	}

	public function show(){
		return $this->getCode;
	}
}

?>