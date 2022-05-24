<?php
/**
 * 
 * @author Ing. Claudio Guzman Herrera
 * @version 1.0
 * @package asistencia/class
 * @copyright DYT SOCMA Ltda.
 * 
 * Uso: $ob_myIp= MyIp();
 * echo $ob_myIp->getCode()
 */
class MyIp
{	
	private $ip;

	function __construct()
	{	
		$this->ip = $this::capturaIP();
	}
	
	/**
	 * capturaIP(): captura de ip remota
	 * @return String
	 */
	private function capturaIP()
	{

		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		    
		    $ip = $_SERVER['HTTP_CLIENT_IP'];

		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

		} else {

		    $ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	/**
	 * getCode()
	 * @return string
	 */
	public function getCode()
	{
		return $this->ip;
	}
}
?>