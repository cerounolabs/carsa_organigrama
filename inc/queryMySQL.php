<?php

include 'conexionMySQL.php';


class consulta
{

public function queryArray($value='')
{
error_log($value);
$conexion = new  conexionMySQL();
$conn = $conexion ->conectar();
$this->$value=$value;
$result = $conn->query($value);
$rows=NULL;
while($row = $result->fetch_array(MYSQLI_NUM))
{
$rows[] = $row;
}

$result->close();
$conn->close();
return $rows;
}

public function query_simple($value='')
{
error_log($value);
$conexion = new  conexionMySQL();
$conn = $conexion ->conectar();
$this->$value=$value;
$conn->query($value);
$conn->close();

}


public function query_assoc($value='')
{
error_log($value);
$conexion = new  conexionMySQL();
$conn = $conexion ->conectar();
$rows = array();
$result = $conn->query($value);
while($row = $result->fetch_assoc())
{
$rows[] = $row;
}
$result->close();
$conn->close();
return $rows;
}

public function query_json($value='')
{
error_log($value);
$conexion = new  conexionMySQL();
$conn = $conexion ->conectar();
$rows = array();
$result = $conn->query($value);
while($row = $result->fetch_assoc())
{
$rows[] = $row;
}
$result->close();
$conn->close();
header("Content-type:application/json");
return json_encode($rows);
}

}

 ?>
