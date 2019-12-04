<?php
// Conectar a MSSQL
$link = mssql_connect("192.168.16.10","sumar","carsa_2018");
if (!$link || !mssql_select_db("bdproduc", $link)) {
    die('No se puede conectar o seleccionar una base de datos!');
}


?>