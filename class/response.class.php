<?php
/**
* @author Claudio, cguzmanherr@gmail.com
*/
class response
{
	private $id;
	private $id_user;

	function __construct($id=null,$id_user = null )
	{
		$this->id = $id;
		$this->id_user = $id_user;
	}

	private function cargaModulos(){

		switch ($this->id)
		{			
			case 'consultarPorFecha':
			case 'findDataFuncionario':
				# code...
				$this::obtenerContenidoClaseOption('asistencia-admin.class.php',
												   'AsistenciaAdmin');
				break;	

			case 'consultarMes':
			case 'updateDataIngreso':
			case 'ingresaDataIngreso':
				$this::obtenerContenidoClaseOption('asistencia.class.php',
												   'Asistencia');
				break;

			case 'actualizaUsuario':	
			case 'editarUsuario':
			case 'eliminaUsuario':	
			case 'ingresaNewUsuario':		
			case 'cambiaClaveData':
				return $this::obtenerContenidoClaseOption('usuarios.class.php','Usuarios');
				break;

			default:
				# code...
				return "<div class='principal'>MODULO NO DEFINIDO / TIMEOUT DE CARGA</div>";
				break;
		}
	}

/**
 * obtenerContenidoClaseOption(), obtiene un despliegue de resultados de una clase cualquiera para el metodo anterior, Alex aprende a programar
 *
 * @param  String file_class
 * @param  String class
 * @return String
 */
	private function obtenerContenidoClaseOption($file_class=null,$class=null){

	   include($file_class);

	   $obj_class  = new $class( $this->id, $this->id_user);
	   return $obj_class->getCode();

	}

	public function getCode(){

		return $this->cargaModulos();
	}
}
?>
