<?php
  include "conexionL.php";
  include "conexionMySQL.php";

  class contador {
    public function contar($value='') {
      $class  = new conexion ();
      $conn   = $class->conectar();
      $conta  = 0;
      $sql5   = "SELECT CB1.COD_FUNC, CB1.COD_SUPERIOR_INMEDIATO, (SELECT COUNT(CB2.COD_FUNC) FROM COLABORADOR_BASICOS CB2 WHERE CB2.COD_SUPERIOR_INMEDIATO = CB1.COD_FUNC) AS HIJOS FROM COLABORADOR_BASICOS CB1 ORDER BY CB1.COD_FUNC";
      $query5 = mssql_query($sql5);

      while($row5 = mssql_fetch_assoc($query5)) {
        $conta  = $conta + 1;
        $sql    = "SELECT CB1.COD_FUNC, CB1.COD_SUPERIOR_INMEDIATO, (SELECT COUNT(CB2.COD_FUNC) FROM COLABORADOR_BASICOS CB2 WHERE CB2.COD_SUPERIOR_INMEDIATO = CB1.COD_FUNC) AS HIJOS FROM COLABORADOR_BASICOS CB1 WHERE CB1.COD_FUNC = ".$row5['COD_FUNC'];
        $query  = mssql_query($sql);

        if($query === false) {
          echo mssql_get_last_message();
        } else {
          while($row = mssql_fetch_assoc($query)) {
            $result[] = $row;
          }

          $hijos= null;
          $a    = "null";

          while ($a == "null") {
            $aux = array();
            $cont= 0;
            $resPaso2 = 0;
            for ($i = 0; $i < COUNT($result); $i++) {
              $sql2  = "SELECT CB1.COD_FUNC, CB1.COD_SUPERIOR_INMEDIATO, (SELECT COUNT(CB2.COD_FUNC) FROM COLABORADOR_BASICOS CB2 WHERE CB2.COD_SUPERIOR_INMEDIATO = CB1.COD_FUNC) AS HIJOS FROM COLABORADOR_BASICOS CB1 WHERE CB1.COD_SUPERIOR_INMEDIATO = ".$result[$i]['COD_FUNC'];
              $query2= mssql_query($sql2);

              if($query2 === false) {
                echo mssql_get_last_message();
              } else {
                while($row = mssql_fetch_assoc($query2)) {
                  $hijos[]                = $row['COD_FUNC'];
                  $aux[$cont]['COD_FUNC'] = $row['COD_FUNC'];
                  $cont                   = $cont+1;
                }
              }

              mssql_free_result($query2);
            }
            
            if(empty($aux)) {
              $a = null;
            } else {
              $result = array();
              $result = $aux;
            }
          }
        }

        mssql_free_result($query);

        $row5['totalhijos'] = COUNT($hijos);
        $final[]            = $row5;

        if($row5['COD_SUPERIOR_INMEDIATO'] == NULL){
          $row5['COD_SUPERIOR_INMEDIATO'] = "NULL";
        }

        $sqlMy    = "SELECT id FROM org_hijos WHERE COD_FUNC =".$row5['COD_FUNC'];
        $conexion = new conexionMySQL();
        $connMy   = $conexion->conectar();
        $resultMY = $connMy->query($sqlMy);
        $resultMY = $resultMY->fetch_array(MYSQLI_NUM);

        if($resultMY[0] != null) {
          $sqlMy2 = "UPDATE org_hijos SET COD_SUPERIOR_INMEDIATO = ".$row5['COD_SUPERIOR_INMEDIATO'].", HIJOS = ".$row5['totalhijos']." WHERE COD_FUNC=".$row5['COD_FUNC'];
        } else {
          $sqlMy2 = "INSERT INTO org_hijos (COD_FUNC,COD_SUPERIOR_INMEDIATO,HIJOS)VALUES(".$row5['COD_FUNC'].",".$row5['COD_SUPERIOR_INMEDIATO'].",".$row5['totalhijos'].")";
        }

        $connMy->query($sqlMy2);
      }

      mssql_free_result($query5);
      echo "actualizacion de subordinados:success";
    }
  }

  $class = new contador();
  $class->contar("");
?>