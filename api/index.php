<?php

require_once("../class/pdo.class.php");
header('Access-Control-Allow-Origin: *');

$ob_pdo = new PdoConnect();
//aun nada de api rest o fetch

$query="SELECT 
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
            ORDER BY accesos.fecha DESC";
$result= $ob_pdo->getMethod( $query );

echo json_encode( $result->fetchAll() );
header("HTTP/1.1 200 OK");
exit();

?>