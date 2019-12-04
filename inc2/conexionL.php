<?php
	ini_set('mssql.charset', 'UTF-8');
	 
	class conexion {
		function conectar() {
    		$msconnect = mssql_connect("192.168.16.10","sumar","carsa_2018");  
//			$msconnect = mssql_connect("192.168.16.9","czelaya","carsa_2019");  	 

			if(!$msconnect || !mssql_select_db("BDPRODUC", $msconnect)) {
//			if(!$msconnect || !mssql_select_db("PRODUCCION_AYER", $msconnect)) {
				echo 'MSSQL error: '.mssql_get_last_message();
			}
  		}
	}
?>