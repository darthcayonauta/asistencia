<?php

/**
 * adaptacion de nav_links a php7
 */
class paginas
{

	private $db;
	private $user;
	private $pass;
	private $host;
	private $con;
	public  $sql;
	public $get_var = "page";
	public $rows_on_page = "15";
	public $str_forward = "SIGUIENTE";
	public $str_backward = "ANTERIOR";
	public $all_rows;
	public $num_rows;
	public $page;
	public $number_pages;
	public $varNavegation;
	private $cfgSpecial;
	private $dbName;

	function __construct($sql = null,  $dbName = null)
	{

		if( is_null( $dbName ) )
		{
			$oConf    = new config();
			$cfg      = $oConf->getConfig();

			$this->host = $cfg['base']['dbhost'];
			$this->user = $cfg['base']['dbuser'];
			$this->pass = $cfg['base']['dbpass'];
			$this->db   = $cfg['base']['dbdata'];


		}else{

			$oConf    = new config( $dbName );
			$cfg      = $oConf->getConfig();

			$this->host = $cfg['base']['dbhost'];
			$this->user = $cfg['base']['dbuser'];
			$this->pass = $cfg['base']['dbpass'];
			$this->db   = $dbName;

		}

		$this->sql       = $sql;
	}

    private function connect()
    {

		$this->con  = mysqli_connect( $this->host,$this->user,$this->pass,$this->db );

        if ($this->con->connect_errno)
            die("error: DB die, no connect");
    }

    private function close()
    {
        $this->con->close();
    }

	private	function set_page()
	{
		$this->page = (isset($_REQUEST[$this->get_var]) && $_REQUEST[$this->get_var] != "") ? $_REQUEST[$this->get_var] : 0;
		return $this->page;
	}

	public	function get_total_rows()
	{

		$this->connect();
		$aux 			=  $this->con->query ($this->sql ) ;
		$this->all_rows = $aux->num_rows;


		return $this->all_rows;
	}

	private	function get_num_pages()
	{
		$this->number_pages = ceil($this::get_total_rows() / $this->rows_on_page);
		return $this->number_pages;
	}

	public function get_page_result()
	{
		$this->connect();
		$start = $this::set_page() * $this->rows_on_page;
		$page_sql = sprintf("%s LIMIT %s, %s", $this->sql, $start, $this->rows_on_page);
		$this->result = $this->con->query($page_sql);
		return $this->result;
	}

	public	function get_page_num_rows()
	{
		$this->num_rows = count( $this->result );
		return $this->num_rows;
	}

	private	function free_page_result()
	{
		$this::close();
	}

	private	function rebuild_qs($curr_var)
	{
		if (!empty($_SERVER['QUERY_STRING'])) {
			$parts = explode("&", $_SERVER['QUERY_STRING']);
			$newParts = array();
			foreach ($parts as $val) {
				if (stristr($val, $curr_var) == false)  {
					array_push($newParts, $val);
				}
			}
			if ( count($newParts) != 0) {
				$qs = "&".implode("&", $newParts);
			} else {
				return false;
			}
			return $qs;
		} else {
			return false;
		}
	}

	public	function navigation($separator = "  ", $css_current = "", $back_forward = false)
	{

		$max_links  	= "25";
		$curr_pages 	= $this::set_page();
		$all_pages  	= $this::get_num_pages() - 1;
		$var 			= $this->get_var;
		$navi_string 	= "";

		if (!$back_forward) {
			$max_links = ($max_links < 2) ? 2 : $max_links;
		}
		if ($curr_pages <= $all_pages && $curr_pages >= 0) {

			if ($curr_pages > ceil($max_links/2))
			{
				$start = ($curr_pages - ceil($max_links/2) > 0) ? $curr_pages - ceil($max_links/2) : 1;
				$end   = $curr_pages + ceil($max_links/2);
				if ($end >= $all_pages) {
					$end = $all_pages + 1;
					$start = ($all_pages - ($max_links - 1) > 0) ? $all_pages  - ($max_links - 1) : 1;
				}
			} else {
				$start = 0;
				$end   = ($all_pages >= $max_links) ? $max_links : $all_pages + 1;
			}
			if($all_pages >= 1) {
				$forward     = $curr_pages + 1;
				$backward    = $curr_pages - 1;

				$navi_string = ($curr_pages > 0) ? " <li class='pages'><a href='#' class='pagination btn btn-sm btn-dark' data=\"".$var."=".$backward.$this::rebuild_qs($var)."\">".$this->str_backward."</a> " : $this->str_backward." ";


				if (!$back_forward) {
					for($a = $start + 1; $a <= $end; $a++){
						$theNext = $a - 1; // because a array start with 0
						if ($theNext != $curr_pages) {

							$navi_string .= " <li class='pages'><a href='#' class='pagination btn btn-sm btn-dark' data=\"".$var."=".$theNext.$this::rebuild_qs($var)."\"> ";

							$navi_string .= $a."</a></li> ";
							$navi_string .= ($theNext < ($end - 1)) ? $separator : "";
						} else {
							$navi_string .= ($css_current != "") ? "".$a."" : $a;
							$navi_string .= ($theNext < ($end - 1)) ? $separator : "";
						}
					}
				}

			$navi_string .= ($curr_pages < $all_pages) ? " <li class='pages'><a href='#' class='pagination btn btn-sm btn-dark' data=\"".$var."=".$forward.$this::rebuild_qs($var)."\">".$this->str_forward."</a></li>" : " <li class='pages'>".$this->str_forward."</li>";

			}
		}
		return $navi_string;
	}

	public function page_info($to = "-")
	{
		$first_rec_no = ($this::set_page() * $this->rows_on_page) + 1;
		$last_rec_no = $first_rec_no + $this->rows_on_page - 1;
		$last_rec_no = ($last_rec_no > $this::get_total_rows()) ? $this::get_total_rows() : $last_rec_no;
		$to = trim($to);
		$info = $first_rec_no." ".$to." ".$last_rec_no;
		return $info;
	}


	public function back_forward_link()
	{
		$simple_links = $this::navigation(" ", "", true);
		return $simple_links;
	}
}
?>
