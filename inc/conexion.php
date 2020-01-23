<?phP
  class conexion {
    public function conectar($base = "") {
      $serverName     = "190.128.229.38\MSSQLSERVER, 9090";
      $connectionInfo = array("Database"=>"SISTEMAA", "UID"=>"sumar", "PWD"=>"carsa_2018", "CharacterSet"=>"UTF-8");
      $conn           = sqlsrv_connect($serverName, $connectionInfo);

      
      if($conn) {
        return $conn;
      } else {
        echo "Conexión no se pudo establecer.<br />";
        die( print_r( sqlsrv_errors(), true));
      }
    }
  }
?>