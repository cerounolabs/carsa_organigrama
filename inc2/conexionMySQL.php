<?php
  class conexionMySQL {
    public function conectar() {
      $conn = new mysqli("192.168.16.17", "root", "prabhupada1A+", "intranetcarsa");
  
      if ($conn->connect_error) {
        die("ERROR: Unable to connect: " . $conn->connect_error);
        error_log( "MySql error");
      } else {
        return $conn;
      }
    }
  }

  $class = new conexionMySQL();
  $class->conectar();
?>
