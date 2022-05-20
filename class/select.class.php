<?php
/**
* @author Ing. Claudio Guzman Herrera
* @version 1.0
* @package salas
*/
class select
{

	private $template;
	private $arrayData;
	private $idMainField;
	private $descriptionField;
	private $selectId;
	private $selectDescription;
	private $ruta;
	private $id_select;
	private $muestra_num;

	function __construct( $arrayData,
						  $idMainField 		 = null,
						  $descriptionField,
						  $selectId			 = null,
						  $selectDescription = null,
						  $id_select 		 = null,
						  $muestra_num 		 = null )
	{
		$oConf    = new config();
		$cfg      = $oConf->getConfig();

		$this->arrayData 			= $arrayData;
		$this->idMainField 			= $idMainField;
		$this->descriptionField 	= $descriptionField;
		$this->selectId 			= $selectId;
		$this->selectDescription 	= $selectDescription;
		$this->id_select 			= $id_select;
		$this->muestra_num 			= $muestra_num;

		$this->ruta = $cfg['base']['template'];
		$this->template = new template();
	}

	/**
	 * select(), CUERPO DEL SELECT
	 *
	 * @return String
	 */
	private function select(){

		$data = array('###NAME_ID###' => $this->selectId ,
					  '###ITEM###'    => $this->selectDescription,
					  '###OPTION###'  =>  $this::option() );

		return  $this->despliegueTemplate($data,$this->ruta."select.html");

	}

	/**
	 * option(), LISTADO DEL SELECT
	 *
	 * @return String
	 */
	private function option()
	{
		$code="";

		$i=1;

		foreach ( $this->arrayData as $key => $value) {

			if($this->id_select != $value[ $this->idMainField ] )
				$selected = "";
			else
				$selected = "selected";

			if(!is_array( $this->descriptionField ))
				$description = $value[ $this->descriptionField ];
			else{

				$codigo = "";
				foreach ($this->descriptionField as $key => $valor) {

					$codigo.=$value[ $valor ]." ";
				}

				$description = $codigo;
			}

			if( is_null( $this->muestra_num) )
				$num = "";
			else
				$num = $i.") ";

			$data= array('###NUMBER###'							=>	$num,
									 '###SELECTED###'						=>	$selected,
									 '###OPTION_VALUE###'				=>	$this::codifica( $value[ $this->idMainField ] ,2 ),
									 '###OPTION_DESCRIPTION###'	=>	$description  );

			$code.= $this->despliegueTemplate($data,$this->ruta."option.html");

		$i++;
		}

	return $code;
	}

	/**
   * codifica(): ressuelve codificar en uft8 o no dependiendo del server
   */
   private function codifica( $cadena = null, $accion = null  )
   {
     $ob_codifica = new Codifica( $cadena , $accion  );
     return $ob_codifica->resuelve();
   }



	/**
	 * despliegueTemplate(), metodo que sirve para procesar los templates
	 *
	 * @param  array   arrayData (array de datos)
	 * @param  array   tpl ( template )
	 * @return String
	 */
	 private function despliegueTemplate($arrayData,$tpl){

	      $this->template->setTemplate($tpl);
	      $this->template->llena($arrayData);

	      return $this->template->getCode();
	}

	 /**
	  * getCode(), imprime el resultado final
	  */
	 public function getCode(){

	 	return $this->select();
	 }
}
?>
