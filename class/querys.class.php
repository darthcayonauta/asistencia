<?php
/**
* @author  Claudio Guzman Herrera
* @version 1.0
*/
class querys
{
	private $fecha_hoy;
	private $fecha_hora_hoy;
	private $error;

	function __construct($sql=null)
	{
		# code...
		if ( !is_null( $sql ) ){
			$this->sql   = $sql;
			$this->error = "Modulo no definido";
		}

		else{

			$oConf     = new config();
		  $cfg       = $oConf->getConfig();
		  $this->sql = new mysqldb( $cfg['base']['dbhost'],
				 					$cfg['base']['dbuser'],
									$cfg['base']['dbpass'],
									$cfg['base']['dbdata'] );

		$this->error = $cfg['base']['error'];
		}

		$this->fecha_hoy 		=  date("Y-m-d");
		$this->fecha_hora_hoy 	=  date("Y-m-d H:i:s");
	}

	public function actualizaUsuario( 	$nombres = null,
										$apaterno = null,
										$amaterno = null,
										$id_user  = null   )
	{
		$update = "UPDATE user SET nombres = '{$nombres}', apaterno='{$apaterno}',amaterno='{$amaterno}'
		     	   WHERE id={$id_user}";

		if( $this->sql->update( $update ) )
				return true;
		else 	return false;
	}


	public function eliminaUsuario( $id_user = null )
	{
		$update = "UPDATE user SET id_estado=2 WHERE id={$id_user}";

		if( $this->sql->update( $update ) )
				return true;
		else 	return false;
	}



	public function ingresaNewUsuario( $nombres  = null, 
									   $apaterno = null,
									   $amaterno = null,
									   $rut 	 = null,
									   $clave    = null
	
	)
	{
		$arr = $this::usersListado( null, $rut );	
		
		$insert = "INSERT INTO user(nombres,apaterno,amaterno,rut,email,clave,tipo_user,id_estado) VALUES 
				  ('{$nombres}','{$apaterno}','{$amaterno}','{$rut}','{$rut}',PASSWORD('{$clave}'),2,1)	
		";

		if( $arr['total-recs'] > 0 )
					return false;
		else{
			if( $this->sql->insert( $insert ) )
					return true;
			else 	return false;
		}
	}

	public function cambiaPassword( $clave = null, $id_user = null  )
	{
		$update = "UPDATE user SET clave = PASSWORD('{$clave}') WHERE id={$id_user}";

		if( $this->sql->update( $update ) )
				return true;
		else 	return false;

	}	


	public function usersListado( $id  = null , $rut = null, $no_todos = null )
	{
		$resto = null;

		if( $id )
			$resto = " AND id = {$id}";

		if( $rut )
			$resto = " AND rut = '{$rut}'";	

		if( $no_todos )
			$resto = " AND id_estado = 1";	

		$ssql = "SELECT * FROM user WHERE tipo_user = 2 {$resto} ORDER BY apaterno";
	
		$arr['sql']= $ssql;
		$arr['process']= $this->sql->select( $ssql );
		$arr['total-recs']= count( $arr['process'] );
	
		return $arr;

	}	




	public function updateAsistencia( $id_user     = 	null, 
									  $fecha       = 	null,									  
									  $hora_fin    =	null )
	{

		$update = "UPDATE asistencias SET hora_fin='{$hora_fin}' WHERE id_user={$id_user} AND fecha='{$fecha}'";

		if( $this->sql->update($update) )
				return true;
		else 	return false;
	}


	public function procesaAsistencia( 	$id_user     = null,										
										$fecha       = null,
										$hora_inicio = null,
										$hora_fin    = null,										
										$mes         = null,
										$year        = null,
										$id     	 = null)
	{
		if(!$id)
		{
			$insert = "INSERT INTO asistencias(id_user,id_estado,fecha,hora_inicio,fecha_mov,mes,year) VALUES 
					  ( {$id_user},1,'{$fecha}','{$hora_inicio}','{$this->fecha_hora_hoy}','{$mes}','{$year}'  )";

			if( $this->sql->insert($insert) )
					return true;
			else 	return false;
		}else{
			//return false
		}
	}	




	public function asistencias( $id_user = null, $fecha=null , $mes = null ,$year = null  )
	{
		$resto = "";
		if(  $id_user )
			$resto .= " WHERE id_user = '{$id_user}'";

		if( $fecha )
			$resto .= " AND fecha = '{$fecha}'";
			
		if( $mes )
			$resto .= " AND mes = {$mes}";

		if( $year )
			$resto .= " AND year = {$year}";
	
			$ssql = "SELECT * FROM asistencias {$resto}";
	
			$arr['sql']= $ssql;
			$arr['process']= $this->sql->select( $ssql );
			$arr['total-recs']= count( $arr['process'] );
		
			return $arr;
	
	}



public function meses( $textual = null )
{
	$resto = "";
	if(  $textual )
		$resto .= " WHERE textual = '{$textual}'";

		$ssql = "SELECT * FROM mes {$resto}";

		$arr['sql']= $ssql;
		$arr['process']= $this->sql->select( $ssql );
		$arr['total-recs']= count( $arr['process'] );
	
		return $arr;

}

public function horas()
{
		$ssql = "SELECT * FROM horas ";

		$arr['sql']= $ssql;
		$arr['process']= $this->sql->select( $ssql );
		$arr['total-recs']= count( $arr['process'] );
	
		return $arr;
}

public function minutos()
{
		$ssql = "SELECT * FROM minutos ";

		$arr['sql']= $ssql;
		$arr['process']= $this->sql->select( $ssql );
		$arr['total-recs']= count( $arr['process'] );
	
		return $arr;
}



public function eliminarDetallerendicion( $id = null )
{
	$delete = "DELETE FROM detalle_rendicion WHERE id={$id}";

	if( $this->sql->delete( $delete ) )
				return true;
	else	return false;

}

public function cambiaClaveData( $id_usuario = null, $clave = null )
{
	$update = "UPDATE usuario SET clave = PASSWORD('{$clave}') WHERE id={$id_usuario}";

	if( $this->sql->update( $update ) )
				return true;
	else 	return false;
}


public function ingresaAlerta( $id_usuario = null )
{
	$arr = $this::listaAlerta( $id_usuario );

	if( $arr['total-recs'] > 0  )
		return false;
	else
			{
				$insert="INSERT INTO alerta( id_usuario ) VALUES ('{$id_usuario}')";

				if( $this->sql->insert( $insert ) )
							return true;
				else 	return false;

			}
}

public function listaAlerta( $id_usuario = null )
{
	$ssql = "SELECT * FROM alerta WHERE id_usuario ={$id_usuario}";

	$arr['sql']= $ssql;
	$arr['process']= $this->sql->select( $ssql );
	$arr['total-recs']= count( $arr['process'] );

	return $arr;
}


public function ingresaObservacion( $codigo_rendicion = null,
																		$id_estado        = null,
																		$observacion      = null,
																		$archivo					= null			 )
{
  $UPDATE = "UPDATE rendicion SET  id_estado={$id_estado}
						 WHERE codigo='{$codigo_rendicion}'";


	$insert ="INSERT INTO observaciones(  codigo_rendicion ,
	 																			id_estado        ,
	 																			observacion      ,
	 																			fecha, archivo ) VALUES (  '{$codigo_rendicion}'	,
																																	 '{$id_estado}'	,
																																	 '{$observacion}'	,
																																	 '{$this->fecha_hora_hoy}', '{$archivo}' )";

	if( $this->sql->update( $UPDATE ) )
				$ok = true;
	else	$ok = false;

  if( $this->sql->insert( $insert ) )
				return true;
	else	return false;
}

public function listaObservaciones( $codigo = null )
{

$ssql = "SELECT
  					observaciones.id               ,
  					observaciones.codigo_rendicion ,
  					observaciones.id_estado        ,
  					observaciones.observacion      ,
  					observaciones.fecha            ,
						observaciones.archivo            ,
						estado_rendicion.descripcion AS estado
				 FROM observaciones
				 INNER JOIN estado_rendicion ON ( estado_rendicion.id = observaciones.id_estado )
				 WHERE observaciones.codigo_rendicion = '{$codigo}'
				 ORDER BY observaciones.fecha DESC
";
	//$ssql = "SELECT * FROM observaciones WHERE codigo_rendicion='{$codigo}'";

	$arr['sql']= $ssql;
	$arr['process']= $this->sql->select( $ssql );
	$arr['total-recs']= count( $arr['process'] );

	return $arr;
}


public function listaRendiciones( $tipo_usuario = null, $id_usuario = null, $codigo = null )
{
		$resto = "";

		if($tipo_usuario != 1)
		if( $id_usuario  )
		$resto = " WHERE rendicion.id_usuario={$id_usuario}";

		if( $codigo )
		$resto = " WHERE rendicion.codigo = '{$codigo}'";

		$ssql = "SELECT
      				rendicion.id             ,
      				rendicion.codigo         ,
      				rendicion.fecha_ingreso  ,
      				rendicion.monto_asignado ,
      				rendicion.id_usuario     ,
      				rendicion.id_motivo      ,
      				rendicion.id_medio_pago  ,
      				rendicion.archivo        ,
      				rendicion.lugar          ,
      				rendicion.acompanante    ,
      				rendicion.fecha_viaje    ,
	  					rendicion.id_estado      ,
      				usuario.nombres,
      				usuario.apellido_paterno,
      				usuario.apellido_materno ,
							usuario.login ,
							usuario.rut ,
							motivo.descripcion,
							estado_rendicion.descripcion As descEstado
						FROM rendicion
						INNER JOIN usuario ON ( usuario.id = rendicion.id_usuario )
						INNER JOIN motivo ON ( motivo.id  = rendicion.id_motivo )
						INNER JOIN estado_rendicion ON ( estado_rendicion.id  = rendicion.id_estado )
						{$resto}
						ORDER BY rendicion.fecha_ingreso DESC , rendicion.codigo  DESC
						";

			$arr['sql']= $ssql;
			$arr['process']= $this->sql->select( $ssql );
			$arr['total-recs']= count( $arr['process'] );

			return $arr;
}

public function listaRendicionesBuscar( $codigo = null,$id_usuario=null,$id_estado=null,$fecha_inicial=null,$fecha_final=null )
{
		$resto = null;

		//if( is_null( $fecha_inicial ) &&  is_null( $fecha_final ))
		//	$resto = null;
		//else

		if( $fecha_inicial !='' && $fecha_final !='' )
			$resto = "AND rendicion.fecha_ingreso BETWEEN '{$fecha_inicial}' AND '{$fecha_final}' ";

		$ssql = "SELECT
      				rendicion.id             ,
      				rendicion.codigo         ,
      				rendicion.fecha_ingreso  ,
      				rendicion.monto_asignado ,
      				rendicion.id_usuario     ,
      				rendicion.id_motivo      ,
      				rendicion.id_medio_pago  ,
      				rendicion.archivo        ,
      				rendicion.lugar          ,
      				rendicion.acompanante    ,
      				rendicion.fecha_viaje    ,
	  					rendicion.id_estado      ,
      				usuario.nombres,
      				usuario.apellido_paterno,
      				usuario.apellido_materno ,
							usuario.login ,
							usuario.rut ,
							motivo.descripcion,
							estado_rendicion.descripcion As descEstado
						FROM rendicion
						INNER JOIN usuario ON ( usuario.id = rendicion.id_usuario )
						INNER JOIN motivo ON ( motivo.id  = rendicion.id_motivo )
						INNER JOIN estado_rendicion ON ( estado_rendicion.id  = rendicion.id_estado )

						WHERE
						  rendicion.codigo LIKE '%{$codigo}%' AND
							rendicion.id_usuario = '{$id_usuario}' AND
						  rendicion.id_estado LIKE '%{$id_estado}%'
							{$resto}

						ORDER BY rendicion.fecha_ingreso DESC , rendicion.codigo  DESC
						";

			$arr['sql']= $ssql;
			$arr['process']= $this->sql->select( $ssql );
			$arr['total-recs']= count( $arr['process'] );

			return $arr;
}


public function detalle_rendicion( $codigo_rendicion = null, $id = null )
{
	$resto = "";

  if( $id )
    $resto = " WHERE detalle_rendicion.id ={$id}";

	if( $codigo_rendicion )
		$resto = " WHERE detalle_rendicion.codigo_rendicion ='{$codigo_rendicion}'";

	$ssql = "SELECT
							 detalle_rendicion.id               ,
							 detalle_rendicion.id_item          ,
							 detalle_rendicion.detalle          ,
							 detalle_rendicion.tipo_documento   ,
							 detalle_rendicion.num_documento   ,
							 detalle_rendicion.fecha            ,
							 detalle_rendicion.monto            ,
							 detalle_rendicion.codigo_rendicion ,
							 detalle_rendicion.id_usuario       ,
							 tipo_documento.descripcion AS descTipoDocumento,
							 item.descripcion As descItem
					FROM detalle_rendicion
					INNER JOIN tipo_documento ON ( tipo_documento.id = detalle_rendicion.tipo_documento )
					INNER JOIN item ON ( item.id = detalle_rendicion.id_item )
					{$resto}
					";

		$arr['sql']= $ssql;
		$arr['process']= $this->sql->select( $ssql );
		$arr['total-recs']= count( $arr['process'] );

		return $arr;
}


public function rendiciones( $id = null, $codigo = null )
{
	$resto = "";

	if( $id )
		$resto .= "WHERE rendicion.id = {$id}";

	if( $codigo )
		$resto .= "WHERE rendicion.codigo = '{$codigo}'";

	$ssql = "SELECT
									 rendicion.id             ,
									 rendicion.codigo         ,
									 rendicion.fecha_ingreso  ,
									 rendicion.monto_asignado ,
									 rendicion.id_usuario     ,
									 rendicion.id_motivo      ,
									 rendicion.id_medio_pago  ,
									 rendicion.archivo        ,
									 rendicion.lugar          ,
									 rendicion.acompanante    ,
									 rendicion.fecha_viaje    ,
									 rendicion.id_estado      ,
									 usuario.nombres		  ,
									 usuario.apellido_paterno		  ,
									 usuario.apellido_materno ,
									 usuario.rut,
									 usuario.firma,
									 motivo.descripcion AS descMotivo,
									 estado_rendicion.descripcion As descEstado
						FROM rendicion
						INNER JOIN usuario ON (usuario.id = rendicion.id_usuario)
						INNER JOIN motivo ON ( motivo.id = rendicion.id_motivo )
						INNER JOIN estado_rendicion ON ( estado_rendicion.id = rendicion.id_estado )
						{$resto}
						";

		$arr['sql']= $ssql;
		$arr['process']= $this->sql->select( $ssql );
		$arr['total-recs']= count( $arr['process'] );

		return $arr;

}

public function eliminaArchivoRendicion( $id = null )
{
		$delete = "DELETE FROM archivo_rendicion WHERE id={$id}";

		if( $this->sql->delete( $delete ) )
					return true;
		else 	return false;
}

public function archivo_rendicion( $codigo_rendicion = null )
{
	$ssql = "SELECT * FROM archivo_rendicion
					 WHERE codigo_rendicion='{$codigo_rendicion}'";

	$arr['sql']= $ssql;
	$arr['process']= $this->sql->select( $ssql );
	$arr['total-recs']= count( $arr['process'] );

	return $arr;
}

public function procesaArchivoRendicion(  	$nombre_archivo   = null,
 											$codigo_rendicion = null,
 											$id               = null )
{
	if( $id )
	{
		return false;
	}else{

		$insert= "INSERT INTO archivo_rendicion(nombre_archivo,
		                              			codigo_rendicion,
		                              			fecha)
		VALUES ( '{$nombre_archivo}',
		         '{$codigo_rendicion}',
		         '{$this->fecha_hora_hoy}' )";

		if( $this->sql->insert( $insert ) )
					return true;
		else 	return false;

	}
}

 public function procesaDetalleRendicion( $id_item          = null,
 										  $detalle          = null,
 										  $tipo_documento   = null,
										  $num_documento    = null,
 										  $fecha            = null,
 										  $monto            = null,
 										  $codigo_rendicion = null,
 										  $id_usuario       = null )
 {

	 $insert = "INSERT INTO detalle_rendicion( id_item ,
											detalle          ,
											tipo_documento   ,
											num_documento   ,
											fecha            ,
											monto            ,
											codigo_rendicion ,
											id_usuario       )
							 VALUES( '{$id_item}',
							         '{$detalle}',
							         '{$tipo_documento}',
									 '{$num_documento}',
							         '{$fecha}',
							         '{$monto}',
							         '{$codigo_rendicion}',
							         '{$id_usuario}')";

	 if( $this->sql->insert( $insert ) )
	 			return true;
	 else return false;

	 //return $insert;

 }

public function actualizaRendicion( $codigo         = null,
 									$monto_asignado = null,
 									$id_motivo      = null,
 									$lugar          = null,
 									$acompanante    = null,
 									$fecha_viaje    = null )
{

		switch ($_POST['my_state']) {
			case 6:
				# code...
				$id_estado = 1;
				break;
			
			default:
				# code...
				$id_estado = $_POST['my_state'];
				break;
		}


		$update = "UPDATE rendicion SET monto_asignado='{$monto_asignado}',
						  id_motivo = '{$id_motivo}', 
						  lugar='{$lugar}', 
						  acompanante='{$acompanante}',
						  id_estado = {$id_estado},
						  fecha_viaje = '{$fecha_viaje}' WHERE codigo='{$codigo}'";

		if( $this->sql->update( $update ) )
					return true;
		else 	return false;
}


 public function procesamientoDelDetalleRendicion( 	$id_item  		  = null,
 	 												$detalle          = null,
 	 												$tipo_documento   = null,
 	 												$fecha            = null,
 	 												$monto            = null,
 	 												$codigo_rendicion = null,
 	 												$id_usuario       = null,
 	 												$num_documento    = null,
 	 												$id   			  = null)
 {

	if( $id != 0 )
	{
		//actualiza
		$update = "UPDATE detalle_rendicion SET id_item = '{$id_item}', detalle='{$detalle}',
							tipo_documento = '{$tipo_documento}', fecha='{$fecha}',monto='{$monto}',
							num_documento ='{$num_documento}' WHERE id={$id}";

		if( $this->sql->update( $update ) )
					return true;
		else 	return false;

	}else{

		if( $this::procesaDetalleRendicion( $id_item,
	 										$detalle         ,
	 										$tipo_documento  ,
	 										$num_documento   ,
	 										$fecha           ,
	 										$monto           ,
	 										$codigo_rendicion,
	 										$id_usuario       ) )
				  return true;
		else 	return false;

	}	//hasta aca

 }

  public function procesaRendicion( $codigo         = null,
									$monto_asignado = null,
									$id_usuario     = null,
									$id_motivo      = null,
									$id_medio_pago  = null,
									$lugar          = null,
									$acompanante    = null,
									$fecha_viaje    = null,
									$id             = null)
	{
		if( $id )
		{ return false; }
		else{

			//print_r( $_POST );

			if( !isset( $_POST['my-estado'] ) )
					$estado = 1;
			else 	$estado = $_POST['my-estado'];

			$insert = "INSERT INTO rendicion(codigo,
											 fecha_ingreso  ,
											 monto_asignado ,
											 id_usuario     ,
											 id_motivo      ,
											 id_medio_pago  ,
											 archivo        ,
											 lugar          ,
											 acompanante    ,
											 fecha_viaje    ,
											 id_estado      )
						VALUES(
											'{$codigo}',
											'{$this->fecha_hoy}',
											'{$monto_asignado}',
											'{$id_usuario}',
											'{$id_motivo}',
											'{$id_medio_pago }',
											'no-aplica',
											'{$lugar}',
											'{$acompanante}',
											'{$fecha_viaje}',
											$estado) ";

			if( $this->sql->insert( $insert ) )
						return true;
			else 	return false;

		}
	}

	public function medio_pago()
	{
			$ssql = "SELECT * FROM medio_pago";
			return $this->sql->select( $ssql );
	}

	public function tipo_documento()
	{
			$ssql = "SELECT * FROM tipo_documento";
			return $this->sql->select( $ssql );
	}

	public function items()
	{
			$ssql = "SELECT * FROM item";
			return $this->sql->select( $ssql );
	}

	public function estado_rendicion()
	{
			$ssql = "SELECT * FROM estado_rendicion";
			return $this->sql->select( $ssql );
	}


	public function motivos()
	{
			$ssql = "SELECT * FROM motivo";
			return $this->sql->select( $ssql );
	}

	public function ingresaAccesos( $id_usuario   = null,
									$sesion       = null,
									$ip           = null )
	{

	$INSERT = "INSERT INTO accesos( id_usuario,fecha, sesion ,ip ) VALUES
				('{$id_usuario}' ,'$this->fecha_hora_hoy' ,'{$sesion}' ,'{$ip}' )";

		if( $this->sql->insert( $INSERT ) )
						return true;
		 		else 	return false;
	}

	public function listaAccesos()
	{
		$ssql = "SELECT 
					accesos.id,
					accesos.fecha,
					accesos.ip,
					accesos.sesion,
					accesos.id_usuario,
					user.apaterno,
					user.amaterno,
					user.nombres
				FROM 
				accesos
				INNER JOIN user ON (accesos.id_usuario = user.id)
				ORDER BY accesos.fecha DESC
				";

		$arr['sql'] 	= $ssql;
		$arr['process'] = $this->sql->select( $ssql );
		$arr['total-recs'] = count( $arr['process'] );

		return $arr;
	}




public function listaUsuarios( $email = null , 
							  $clave=null, 
							  $is_admin = null, 
							  $id_usuario = null, 
							  $a_paterno= null)
{

	$resto = "";
	if( $email  ) $resto  .= " AND user.email = '{$email}' ";
	if( $clave )  $resto  .= " AND user.clave = password('{$clave}')   ";


	if( !$is_admin )
	{
		$ssql = "SELECT
						user.id           ,
						user.nombres      ,
						user.apaterno     ,
						user.amaterno     ,
						user.email        ,
						user.rut,
						user.clave        ,
						user.tipo_user,
						user.id_estado
				FROM user
				WHERE user.id_estado = 1	{$resto}
				";
	}else{

		$resto = "";
		if( $id_usuario ) $resto .= " AND user.id = {$id_usuario}";
		if( $a_paterno )   $resto .= " AND user.apaterno  LIKE '%{$a_paterno}%'  ";
		if( $email  ) $resto  .= " AND user.login = '{$email}' ";

		$ssql = "SELECT
						user.id           ,
						user.nombres      ,
						user.apaterno     ,
						user.amaterno     ,
						user.rut,
						user.email        ,
						user.clave        ,
						user.tipo_user,
						user.id_estado

				FROM user
				WHERE user.tipo_user != 1
						AND user.id_estado = 1
				 {$resto}
				ORDER BY user.a_paterno, user.a_materno, user.nombres
				";
	}

	$arr['sql'] 	= $ssql;
	$arr['process'] = $this->sql->select( $ssql );
	$arr['total-recs'] = count( $arr['process'] );

	return $arr;

	}

	public function menu( $tipo_usuario = null )
	{
		$ssql = "SELECT * FROM menu WHERE tipo_user = {$tipo_usuario} ORDER BY orden";

		$arr['sql'] 	= $ssql;
		$arr['process'] = $this->sql->select( $ssql );
		$arr['total-recs'] = count( $arr['process'] );

		return $arr;
	}

	public function sub_menu( $id_menu = null )
	{
		$ssql = "SELECT * FROM sub_menu WHERE id_menu = {$id_menu}";

		$arr['sql'] 	= $ssql;
		$arr['process'] = $this->sql->select( $ssql );
		$arr['total-recs'] = count( $arr['process'] );

		return $arr;
	}
}
?>
