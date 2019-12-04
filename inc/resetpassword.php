<?php
include "queryMySQL.php";
$sql="SELECT resetpassword FROM `colaboradores` WHERE id =".$_SESSION['id_usuario'];
$consulta = new consulta();
$result=$consulta->queryArray($sql);

if($result[0][0]==0)
{
	
	
//	echo '<script> alert("SELECT resetpassword FROM `colaboradores` WHERE COD_FUNC = '.$_SESSION['id_usuario'].' ") </script>';
header('Location:nuevacontrasena.php');
echo '<script> alert("Tendra que cambiar su contraseña por seguridad " '.$result[0].') </script>';
	
}





?>