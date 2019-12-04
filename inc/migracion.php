<?php
  include "conexionL.php";
  include "conexionMySQL.php";

  class migracion {
    public function SQLServer() {
      $class  = new conexion();
      $class->conectar();
      $result = "";
//      $sql    = "SELECT COD_FUNC, USUARIO, USUARIO AS PASSWORD, NOMBRE_Y_APELLIDO, NRO_CEDULA, FEC_NACIMIENTO, EDAD, SEXO, COD_ESTADO_CIVIL, ESTADO_CIVIL, COD_NACIONALIDAD, NACIONALIDAD, CORREO_ELECTRONICO, FECHA_INGRESO, ESTADO, MARCA_ENTRADA_SALIDA, COD_MARCACION_BIOMETRICA, TIPO_MARCACION, NIVEL_ORGANIGRAMA, SUBNIVEL, POSICION_ORGANIGRAMA, COD_UNIDAD, UNIDAD, COD_SUBDIVISION, SUB_DIVISION, COD_SUPERVISION, SUPERVISION, COD_DEPARTAMENTO_AREA, DEPARTAMENTO, COD_GERENCIA, GERENCIA, ORDEN_GERENCIA, COD_CANAL, CANAL, COD_SUPERIOR_INMEDIATO, SUPERIOR_INMEDIATO, NRO_CEDULA_SUP, COD_CARGO, CARGO, COD_JERARQUIA, JERARQUIA, NIVEL_JERARQUIA, COD_CATEGORIA_SV, CATEGORIA_SV, COD_SIGNORITY, SIGNORITY, COD_VENDEDOR, VENDEDOR, REGIMEN_LABORAL, FORMA_DE_PAGO, FOTO_TARGET, TRANSVERSAL, GRADO_ACADEMICO, UNIVERSIDAD, MASTERADO, DIPLOMADO, ANTIGUEDAD, PRIMER_NOMBRE, PRIMER_APELLIDO FROM dbo.COLABORADOR_BASICOS";
      $sql    = "SELECT COD_FUNC, USUARIO, USUARIO AS PASSWORD, NOMBRE_Y_APELLIDO, NRO_CEDULA, FEC_NACIMIENTO, EDAD, SEXO, COD_ESTADO_CIVIL, ESTADO_CIVIL, COD_NACIONALIDAD, NACIONALIDAD, CORREO_ELECTRONICO, FECHA_INGRESO, MARCA_ENTRADA_SALIDA, COD_MARCACION_BIOMETRICA, TIPO_MARCACION, NIVEL_ORGANIGRAMA, SUBNIVEL, POSICION_ORGANIGRAMA, COD_UNIDAD, UNIDAD, COD_SUBDIVISION, SUB_DIVISION, COD_SUPERVISION, SUPERVISION, COD_DEPARTAMENTO_AREA, DEPARTAMENTO, COD_GERENCIA, GERENCIA, ORDEN_GERENCIA, COD_CANAL, CANAL, COD_SUPERIOR_INMEDIATO, SUPERIOR_INMEDIATO, NRO_CEDULA_SUP, COD_CARGO, CARGO, COD_JERARQUIA, JERARQUIA, NIVEL_JERARQUIA, COD_CATEGORIA_SV, CATEGORIA_SV, COD_SIGNORITY, SIGNORITY, COD_VENDEDOR, VENDEDOR, REGIMEN_LABORAL, FORMA_DE_PAGO, FOTO_TARGET, TRANSVERSAL, GRADO_ACADEMICO, UNIVERSIDAD, MASTERADO, DIPLOMADO, ANTIGUEDAD, PRIMER_NOMBRE, PRIMER_APELLIDO FROM dbo.COLABORADOR_BASICOS";
      $query  = mssql_query($sql);

      if (!mssql_num_rows($query)) {
        error_log( 'No records found');
      } else {
        while ($row = mssql_fetch_assoc($query)) {
	        $row['PASSWORD'] = str_replace(" ","",$row['PASSWORD']);
	        $row['PASSWORD'] = md5($row['PASSWORD']);
          $result[]        = $row;
        }
      }

      mssql_free_result($query);

      return $result;
    }

    public function MySQL($array){
      $conexion = new conexionMySQL();
      $conn     = $conexion->conectar();
      $result   = $conn->query("SELECT COD_FUNC FROM colaboradores");
      $rows     = array();

      $conn->query("UPDATE colaboradores SET ESTADO = 'INACTIVO'");

      while($row = $result->fetch_row()) {
        $rows[] = $row[0];
      }

      $contInser  = 0;
      $contUpdate = 0;
      $counDelete = 0;
    
      for ($i=0; $i < count($array) ; $i++) {  
        if (in_array($array[$i]['COD_FUNC'], $rows)) {
          unset($array[$i]['PASSWORD']);
          $valueSets = array();
      
          foreach($array[$i] as $key => $value) {
            $valueSets[] = $key . " = '" . $value . "'";
          }

          $conditionSets = array();
      
          foreach($array[$i] as $key => $value) {
            $conditionSets[] = $key . " = '" . $value . "'";
          }

          $sql = "UPDATE colaboradores SET ESTADO = 'ACTIVO', ". join(",", $valueSets) . " WHERE " .  $conditionSets[0];

          $conn->query($sql);

          $contUpdate = $contUpdate+1;
        } else {
          $fields_arr = array();
          
          foreach ($array[$i] as $key => $val) {
            array_push ($fields_arr, "`" . $key . "`");
          }

          $namedPlaceholders = array();

          foreach ($array[$i]  as &$value) {
            array_push ($namedPlaceholders, ":" . $value);
          }

          $sql = sprintf('INSERT INTO colaboradores (%s) VALUES ("%s")', implode(',',array_keys($array[$i])), implode('","',array_values($array[$i])));
          $conn->query($sql);
          
          $contInser = $contInser + 1;
        }
      }

      error_log("Resumen \n Actulaizaciones:".$contUpdate.", Nuevos:".$contInser);
      
      $result->close();
      $conn->close();
    }
  }

  $class = new migracion();
  $class->MySQL($class->SQLServer());
?>