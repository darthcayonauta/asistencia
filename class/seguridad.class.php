<?php	
	
	/**
	* @author Claudio Guzman Herrera
	* @version 1.0
	* @package dissa
	* 
	* Evita SQL injection
	*/
	class seguridad 
	{
		/**
		 * @var String chain (cualquier value de un objeto de formulatio)
		 */
		private $chain;

		function __construct($chain=null)
		{
			
			$this->chain = htmlentities(addslashes(strtoupper(trim($chain))));

		}
		/**
		 * getCode(), corresponde a la salida del atributo
		 */
		public function getCode(){

			return $this->chain;
		}
	}

?>