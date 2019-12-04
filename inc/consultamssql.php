<?php
include "conexion.php";
$class = new conexion ();
$conn=$class->conectar();
error_log(">>>>>>>>>>>>>>>>>>>>".$_GET['sql']);
$query = sqlsrv_query( $conn, $_GET['sql']);

if( $query === false)
{
    die( print_r( sqlsrv_errors(), true) );
}
else
{
$result="";
$row = sqlsrv_fetch_array( $query,  SQLSRV_FETCH_ASSOC) ;
print_r($row);
}
	?>