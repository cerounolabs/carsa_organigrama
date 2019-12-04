<?php
    include "conexionMySQL.php";
    include "org_log.php";

    session_start();
    
    if(isset($_SESSION['admin'])) {
        $fecha01 = $_GET['var01'];
        $fecha02 = $_GET['var02'];

        (new log())->consultar($fecha01, $fecha02);
    }
?>