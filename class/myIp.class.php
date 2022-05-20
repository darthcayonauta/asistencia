<?php

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


	public function getCode()
	{
		return $this->ip;
	}

}
?>