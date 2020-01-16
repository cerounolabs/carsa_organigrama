<?php
  /*PRODUCCION*/
//  include "../../inc/query.php";

  /*TESTING*/
  include "../../inctesting/query.php";
  
  include "org_log.php";

  session_start();
  
  class funciones {
    function switchFuncion() {
      switch ($_GET["funcion"]){
        case 'SelectChildrens':
          {
            if (isset($_GET["id"])){
              $var = $_GET["id"];
              error_log($_GET["id"]);

              if ($var == "undefined") {
                $query    = new query();
                $result   = $query->queryJson("select COD_FUNC AS id ,PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;'+CONVERT(varchar(10),((NIVEL_JERARQUIA * 50)))+';'+USUARIO+';'+CONVERT(varchar(10),COD_FUNC) +';'+ANTIGUEDAD+';'+NOMBRE_Y_APELLIDO+';'+NRO_CEDULA+';'+GERENCIA+';'+SUPERIOR_INMEDIATO+';'+FECHA_INGRESO AS name ,CARGO AS title, (NIVEL_JERARQUIA * 100) AS nivel  from  COLABORADOR_BASICOS where TRANSVERSAL =0 AND COD_SUPERIOR_INMEDIATO =580"." ORDER BY POSICION_ORGANIGRAMA DESC ","SelectChildrens");
                header('Content-Type: application/json');
                $arrayName= array('children' => $result);
                echo json_encode($arrayName);
              } else {
                $var      = str_replace("'","",$var);
                $var      = str_replace("-","",$var);
                $var      = str_replace("=","",$var);
                $query    = new query();
                $result   = $query->queryJson("select COD_FUNC AS id ,PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;'+CONVERT(varchar(10),((NIVEL_JERARQUIA * 50)))+';'+USUARIO+';'+CONVERT(varchar(10),COD_FUNC)  +';'+ANTIGUEDAD+';'+NOMBRE_Y_APELLIDO+';'+NRO_CEDULA+';'+GERENCIA+';'+SUPERIOR_INMEDIATO+';'+FECHA_INGRESO AS name ,CARGO AS title, (NIVEL_JERARQUIA * 100) AS nivel  from  COLABORADOR_BASICOS where TRANSVERSAL =0 AND COD_SUPERIOR_INMEDIATO =$var"." ORDER BY POSICION_ORGANIGRAMA DESC ","SelectChildrens");
                header('Content-Type: application/json');
                $arrayName= array('children' => $result);
                echo json_encode($arrayName);
              }
            } else {
            }
          }
          break;

        case 'selectTablas':
          { 
            $var = $_GET["id"];
            $var = str_replace("'", "", $var);
            $var = str_replace("-", "", $var);
            $var = str_replace("=", "", $var);
		
            $_SESSION['ultimocid'] = $var;
			
		        (new log())->registrar($_SESSION['nombre'], "Colaborador seleccionado", "SELECT NOMBRE_Y_APELLIDO FROM `colaboradores` WHERE cod_func = $var limit 1", true);
			
            if(isset($_GET['dateIni']) AND isset($_GET['dateEnd']) AND isset($_GET['consulta'])) {
					
			        if(($_GET['dateIni'] == "NaN-NaN-NaN") OR ($_GET['dateEnd'] == "NaN-NaN-NaN")){
				        $_GET['dateIni'] = "1900-01-01";
				        $_GET['dateEnd'] = date("Y-m-d");
			        }
				
              $query        = new query();
              $fechaInicio  = $_GET['dateIni'];
              $fechaFin     = $_GET['dateEnd'];
          
              header('Content-Type: application/json');
            
              switch ($_GET['consulta']) {
                case 'logros':
                  {
                    (new log())->registrar($_SESSION['nombre'], "Filtro aplicado a la tabla de productividad ".$fechaInicio."-".$fechaFin, "SELECT NOMBRE_Y_APELLIDO FROM `colaboradores` WHERE cod_func = $var LIMIT 1", true);
                
                    if (isset($_SESSION['permitidos']) AND in_array($var, $_SESSION['permitidos']) AND in_array("1".$var, $_SESSION['permisos'])) {
                      $logros     = $query->queryJson("SET LANGUAGE Spanish; SELECT MES+'-'+CONVERT(varchar(10),ANO) AS PERIODO, META, CONVERT(varchar(20),LOGRADO) AS LOGRADO, RATIO, COLOR, COLOR_TEXTO, TIPO_DE_PRODUCCION AS TIPO FROM COLABORADOR_LOGROS WHERE CONVERT(date, FECHA, 103) > '$fechaInicio' AND CONVERT(date, FECHA, 103) <= '$fechaFin' AND COD_FUNC = $var ORDER BY CONVERT(DATE, FECHA) DESC","logros");
                      $arrayName  = array('logros' => $logros);
                      echo json_encode($arrayName);
                    } else {
                      $arrayName  = array('logros' => null);
                      echo json_encode($arrayName);
                    }
                  }
                  break;

                case 'salario':
                  {
                    (new log())->registrar($_SESSION['nombre'], "Filtro aplicado a la tabla de salarios ".$fechaInicio."-".$fechaFin, "SELECT NOMBRE_Y_APELLIDO FROM `colaboradores` WHERE cod_func = $var LIMIT 1", true);

                    if (isset($_SESSION['permitidos']) AND in_array($var, $_SESSION['permitidos']) AND in_array("2".$var, $_SESSION['permisos'])) {
                      // $salario    = $query->queryJson("SELECT COD_FUNC AS id, PERIODO AS periodo, SALARIO_BRUTO_FIJO AS fijo, SALARIO_BRUTO_VARIABLE AS variable, (SALARIO_BRUTO_FIJO + SALARIO_BRUTO_VARIABLE) AS total, AGUINALDO AS aguinaldo, APORTE_PATRONAL AS aporte FROM COLABORADOR_SALARIOS WHERE CONVERT(date, PERIODO, 103) > '$fechaInicio' AND CONVERT(date, PERIODO, 103) <= '$fechaFin' AND COD_FUNC = $var ORDER BY CONVERT(DATE, PERIODO) DESC", "salario");
                      $salario    = $query->queryJson("SELECT COD_FUNC AS id, MES+'-'+CONVERT(VARCHAR(10), YEAR(PERIODO)) AS periodo, SALARIO_BRUTO_FIJO AS fijo, SALARIO_BRUTO_VARIABLE AS variable, TOTAL AS total, AGUINALDO AS aguinaldo, APORTE_PATRONAL AS aporte FROM COLABORADOR_SALARIO WHERE CONVERT(date, PERIODO, 103) > '$fechaInicio' AND CONVERT(date, PERIODO, 103) <= '$fechaFin' AND COD_FUNC = $var ORDER BY CONVERT(DATE, PERIODO) DESC", "salario");
                      $arrayName  = array('salario' => $salario);
                      echo json_encode($arrayName);
                    } else {
					            $arrayName  = array('salario' => null);
                      echo json_encode($arrayName);
                    }
                  }
                  break;
              
                case 'eventos':
                  {
                    (new log())->registrar($_SESSION['nombre'], "Filtro aplicado a la tabla de disciplina ".$fechaInicio."-".$fechaFin, "SELECT NOMBRE_Y_APELLIDO FROM `colaboradores` WHERE cod_func = $var LIMIT 1", true);

                    if (isset($_SESSION['permitidos']) AND in_array($var, $_SESSION['permitidos']) AND in_array("3".$var, $_SESSION['permisos'])) {
                      $eventos    = $query->queryJson("SELECT COD_FALTA AS codigo, DESC_FALTA AS falta, MES AS mes, ANHO AS ano, TOTAL_CONCEPTO AS total, COLOR AS color FROM COLABORADOR_FALTAS_EVENTOS WHERE COD_FUNC = $var AND CONVERT(DATE, CONVERT(VARCHAR(20), ANHO) + '-'+CONVERT (VARCHAR (20),MES )+'-'+'01') BETWEEN '$fechaInicio' AND '$fechaFin' ORDER BY COD_FALTA","eventos");
                      $arrayName  = array('eventos' => $eventos);
                      echo json_encode($arrayName);
                    } else {
                      $arrayName  = array('eventos' => null);
                      echo json_encode($arrayName);
                    }
                  }
                  break;
                
                case 'movimientos':
                  {
                    (new log())->registrar($_SESSION['nombre'], "Filtro aplicado a la tabla de carrera ".$fechaInicio."-".$fechaFin, "SELECT NOMBRE_Y_APELLIDO FROM `colaboradores` WHERE cod_func = $var LIMIT 1", true);

                    if (isset($_SESSION['permitidos']) AND in_array($var, $_SESSION['permitidos']) AND in_array("4".$var, $_SESSION['permisos'])) {
                      $movimientos  = $query->queryJson(" SELECT  CONVERT(VARCHAR,ULTIMA_MODIFICACION, 103) as desde ,CARGO as cargo ,DEPARTAMENTO as departamento,UNIDAD as unidad FROM COLABORADOR_DATO_ESTRUCTURA where CONVERT(date,ULTIMA_MODIFICACION,103) BETWEEN '$fechaInicio' AND '$fechaFin'  AND COD_FUNC = $var","movimientos");
                      $arrayName    = array('movimientos' => $movimientos);
                      echo json_encode($arrayName);
                    } else {
                      $arrayName = array('movimientos' => null );
                      echo json_encode($arrayName);
                    }
                  }
                  break;

                case 'documentos':
                  {
                    (new log())->registrar($_SESSION['nombre'], "Filtro aplicado a la tabla de documentos ".$fechaInicio."-".$fechaFin, "SELECT NOMBRE_Y_APELLIDO FROM `colaboradores` WHERE cod_func = $var LIMIT 1", true);

                    if (isset($_SESSION['permitidos']) AND in_array($var, $_SESSION['permitidos']) AND in_array("7".$var, $_SESSION['permisos'])) {
                      $documentos   = $query->queryJson("SELECT FUNC_TIPO, FUNC_DOCUMENTO, FUNC_PATH FROM COLABORADOR_DOCUMENTOS WHERE FUNC_CODIGO = $var", "documentos");
                      $arrayName    = array('documentos' => $documentos);
                      echo json_encode($arrayName);
                    } else {
                      $arrayName = array('documentos' => null );
                      echo json_encode($arrayName);
                    }
                  }
                  break;

                case 'anotaciones':
                  {
                    (new log())->registrar($_SESSION['nombre'], "Filtro aplicado a la tabla de anotaciones ".$fechaInicio."-".$fechaFin, "SELECT NOMBRE_Y_APELLIDO FROM `colaboradores` WHERE cod_func = $var LIMIT 1", true);

                    if (isset($_SESSION['permitidos']) AND in_array($var, $_SESSION['permitidos']) AND in_array("8".$var, $_SESSION['permisos'])) {
                      $anotaciones  = $query->queryJson("SELECT FUNC_NRO_ANOTACION, FUNC_FECHA, FUNC_EVENTO, FUNC_OBSERVACION FROM COLABORADOR_ANOTACIONES WHERE FUNC_CODIGO = $var", "anotaciones");
                      $arrayName    = array('anotaciones' => $anotaciones);
                      echo json_encode($arrayName);
                    } else {
                      $arrayName = array('anotaciones' => null );
                      echo json_encode($arrayName);
                    }
                  }
                  break;

                case 'capacitaciones':
                  {
                    (new log())->registrar($_SESSION['nombre'], "Filtro aplicado a la tabla de capacitaciones ".$fechaInicio."-".$fechaFin, "SELECT NOMBRE_Y_APELLIDO FROM `colaboradores` WHERE cod_func = $var LIMIT 1", true);

                    if (isset($_SESSION['permitidos']) AND in_array($var, $_SESSION['permitidos']) AND in_array("9".$var, $_SESSION['permisos'])) {
                      $capacitaciones = $query->queryJson("SELECT FUNC_NRO_CAPACITACION, FUNC_EMPRESA, FUNC_CURSO, FUNC_ANHO, FUNC_MES, FUNC_CANT_HORA FROM COLABORADOR_CAPACITACIONES WHERE FUNC_CODIGO = $var", "capacitaciones");
                      $arrayName      = array('capacitaciones' => $capacitaciones);
                      echo json_encode($arrayName);
                    } else {
                      $arrayName      = array('capacitaciones' => null );
                      echo json_encode($arrayName);
                    }
                  }
                  break;

                case 'antlaborales':
                  {
                    (new log())->registrar($_SESSION['nombre'], "Filtro aplicado a la tabla de antecedente laborales ".$fechaInicio."-".$fechaFin, "SELECT NOMBRE_Y_APELLIDO FROM `colaboradores` WHERE cod_func = $var LIMIT 1", true);

                    if (isset($_SESSION['permitidos']) AND in_array($var, $_SESSION['permitidos']) AND in_array("10".$var, $_SESSION['permisos'])) {
                      $antlaborales = $query->queryJson("SELECT FUNC_NRO_ANTECEDENTE, FUNC_EMPRESA, FUNC_FECHA_DESDE, FUNC_FECHA_HASTA FROM COLABORADOR_ANTECEDENTE_LABORAL WHERE FUNC_CODIGO = $var", "antlaborales");
                      $arrayName    = array('antlaborales' => $antlaborales);
                      echo json_encode($arrayName);
                    } else {
                      $arrayName      = array('antlaborales' => null );
                      echo json_encode($arrayName);
                    }
                  }
                  break;
                
                default:
                  break;
              }
            } else {
              $query      = new query();	
	            $academico  = $query->queryJson("SELECT LOWER(convert(varchar(100), row_number() OVER (ORDER BY ANTECEDENTE_ACADEMICO))+' ' + ANTECEDENTE_ACADEMICO) AS ANTECEDENTE_ACADEMICO FROM COLABORADOR_ANTECEDENTES WHERE COD_FUNC = $var", "academico");
              $info       = $query->queryJson("SELECT UNIVERSIDAD, MASTERADO, DIPLOMADO, EDAD, CAN_PER_DEP_ECO, TIPO_VIVIENDA, CAN_CONT_GASTOS, MOV_PROPIA, NRO_CEDULA, GERENCIA, SUPERIOR_INMEDIATO, FECHA_INGRESO, COD_GERENCIA FROM COLABORADOR_BASICOS WHERE COD_FUNC = $var", "academico2");
              $backups    = $query->queryJson("SELECT COD_FUNC, TIPO, CODIGO_BACKUP, NOMBRE_BACKUP FROM COLABORADOR_BACKUPS WHERE COD_FUNC = $var","backups");

		          if (isset($_SESSION['permitidos']) AND in_array($var, $_SESSION['permitidos'])) {
                error_log("Permiso otorgado a colaborador $var ...");
                error_log(">>>><<<<".print_r($_SESSION['permisos'], true));

                $acFecha      = date("Y-m-d");
                $auFecha      = date("Y-m-d", strtotime($acFecha."-6 month"));

		            if (in_array("1".$var, $_SESSION['permisos'])) {
                  $logros = $query->queryJson("SET LANGUAGE Spanish; SELECT MES+'-'+CONVERT(varchar(10),ANO) AS PERIODO, META, CONVERT(varchar(20), LOGRADO) AS LOGRADO, RATIO, COLOR, COLOR_TEXTO, TIPO_DE_PRODUCCION AS TIPO FROM COLABORADOR_LOGROS WHERE COD_FUNC = $var AND CONVERT(DATE, FECHA, 23) >= '$auFecha' ORDER BY convert(date,'01-'+MES+'-'+CONVERT(varchar(10),ANO)) DESC", "logros");
                  error_log("E1".$var);
                } else {
                  $logros = null;
                  error_log("N1".$var);
                }
        
                //MODIFICAR OBTENER DATOS SALARIOS EN CASO DE TEST
		            if (in_array("2".$var, $_SESSION['permisos'])) {
				          error_log("E2".$var);
                  // $salario  = $query->queryJson("SELECT TOP 6 COD_FUNC AS id, PERIODO AS periodo, SALARIO_BRUTO_FIJO AS fijo, SALARIO_BRUTO_VARIABLE AS variable, (SALARIO_BRUTO_FIJO + SALARIO_BRUTO_VARIABLE) AS total, AGUINALDO AS aguinaldo, APORTE_PATRONAL AS aporte FROM COLABORADOR_SALARIOS WHERE COD_FUNC = $var ORDER BY CONVERT(DATE, PERIODO) DESC", "salario");
                  $salario  = $query->queryJson("SELECT TOP 6 COD_FUNC AS id, MES+'-'+CONVERT(VARCHAR(10), YEAR(PERIODO)) AS periodo, SALARIO_BRUTO_FIJO AS fijo, SALARIO_BRUTO_VARIABLE AS variable, TOTAL AS total, AGUINALDO AS aguinaldo, APORTE_PATRONAL AS aporte FROM COLABORADOR_SALARIO WHERE COD_FUNC = $var ORDER BY CONVERT(DATE, PERIODO) DESC", "salario");
                } else { 
                  $salario  = null;
                  error_log("N1".$var);
                }
		
		            if (in_array("3".$var, $_SESSION['permisos'])) {
                  error_log("E3".$var);
                  $eventos  = $query->queryJson("SELECT COD_FALTA AS codigo, DESC_FALTA AS falta, MES AS mes, ANHO AS ano, TOTAL_CONCEPTO AS total, COLOR AS color FROM COLABORADOR_FALTAS_EVENTOS WHERE COD_FUNC = $var AND CONVERT(DATE, CONVERT(VARCHAR(20), ANHO) + '-'+CONVERT (VARCHAR (20),MES )+'-'+'01') >= '$auFecha' ORDER BY COD_FALTA", "eventos");
		            } else { 
                  $eventos  = null;
                  error_log("N3".$var);
                }
        
		            if (in_array("4".$var, $_SESSION['permisos'])) {
                  error_log("E4".$var);
                  $movimientos  = $query->queryJson("SELECT CONVERT(VARCHAR, ULTIMA_MODIFICACION, 103) AS desde, CARGO AS cargo, DEPARTAMENTO AS departamento, UNIDAD AS unidad FROM COLABORADOR_DATO_ESTRUCTURA WHERE COD_FUNC = $var ORDER BY ULTIMA_MODIFICACION DESC", "movimientos");
                } else {
                  $movimientos  = null;
                  error_log("N4".$var);
                }

		            if (in_array("5".$var, $_SESSION['permisos'])) {
                  error_log("E5".$var);
		              $dependencia  = $query->queryJson("SELECT COD_FUNC, PARENTESCO, NOMBRE_COMPLETO_DEP FROM COLABORADOR_DEPENDENCIA WHERE COD_FUNC = $var", "dependencia");
                } else {
                  $dependencia  = null;
                  error_log("N5".$var);
                }
		
                if (in_array("6".$var, $_SESSION['permisos'])) {
                  error_log("E6".$var);
                  $hobbies      = $query->queryJson("SELECT COD_FUNC, HOBBIE, OBSERVACION FROM colaborador_hobbies WHERE COD_FUNC = $var", "hobbies");
                } else {
                  $hobbies      = null;
                  error_log("N6".$var);
                }
				
				        //MODIFICAR OBTENER DATOS DOCUMENTOS EN CASO DE TEST
                if (in_array("7".$var, $_SESSION['permisos'])) {
                  error_log("E7".$var);
                  $documentos   = $query->queryJson("SELECT FUNC_TIPO, FUNC_DOCUMENTO, FUNC_PATH FROM COLABORADOR_DOCUMENTOS WHERE FUNC_CODIGO = $var ", "documentos");
                } else {
                  $documentos   = null;
                  error_log("N7".$var);
                }

                if (in_array("8".$var, $_SESSION['permisos'])) {
                  error_log("E8".$var);
                  $anotaciones  = $query->queryJson("SELECT FUNC_NRO_ANOTACION, FUNC_FECHA, FUNC_EVENTO, FUNC_OBSERVACION FROM COLABORADOR_ANOTACIONES WHERE FUNC_CODIGO = $var", "anotaciones");
                } else {
                  $anotaciones   = null;
                  error_log("N8".$var);
                }

                if (in_array("9".$var, $_SESSION['permisos'])) {
                  error_log("E9".$var);
                  $capacitaciones = $query->queryJson("SELECT FUNC_NRO_CAPACITACION, FUNC_EMPRESA, FUNC_CURSO, FUNC_ANHO, FUNC_MES, FUNC_CANT_HORA FROM COLABORADOR_CAPACITACIONES WHERE FUNC_CODIGO = $var", "capacitaciones");
                } else {
                  $capacitaciones = null;
                  error_log("N9".$var);
                }

                if (in_array("10".$var, $_SESSION['permisos'])) {
                  error_log("E10".$var);
                  $antlaborales = $query->queryJson("SELECT FUNC_NRO_ANTECEDENTE, FUNC_EMPRESA, FUNC_FECHA_DESDE, FUNC_FECHA_HASTA FROM COLABORADOR_ANTECEDENTE_LABORAL WHERE FUNC_CODIGO = $var", "antlaborales");
                } else {
				          //COMENTAR ESTO PARA PRUEBAS
                  $antlaborales = null;
                  error_log("N10".$var);
                }
				
				        //MODIFICAR OBTENER DATOS ENDS EN CASO DE TEST
				        if (in_array("11".$var, $_SESSION['permisos'])) {
                  error_log("E11".$var);
                  $ends   = $query->queryJson("SELECT CONVERT(DATE, FECHA, 103) as FECHA, NRO_EVENTO, COD_EVENTO, EVENTO, ITEM, ARCHIVO FROM COLABORADOR_ENDS WHERE COD_FUNC = $var ORDER BY FECHA DESC", "ends");
                  //$ends   = $query->queryJson("SELECT FORMAT(FECHA,'yyyy/MM/dd') as FECHA, NRO_EVENTO, COD_EVENTO, EVENTO, ITEM, ARCHIVO FROM COLABORADOR_ENDS WHERE COD_FUNC = $var ORDER BY FECHA DESC", "ends");
                } else {
                  $ends   = null;
                  error_log("N11".$var);
                }
		          } else {
                error_log("Permiso a colaborador $var denegado...");
                $logros         = null;
                $salario        = null;
                $eventos        = null;
                $movimientos    = null;
                $documentos     = null;
                $anotaciones    = null;
                $capacitaciones = null;
                $antlaborales   = null;
                $ends   		    = null;
              }
				
              if($academico == ""){
                $academico = null;
              }else {

              }
        
            if($antlaborales == ""){
              $antlaborales = null;
            }

            $arrayName = array('informacion'=>$info, 'logros'=>$logros, 'salario'=>$salario, 'eventos'=>$eventos, 'movimientos'=>$movimientos, 'documentos'=>$documentos, 'dependencia'=>$dependencia, 'hobbies'=>$hobbies, 'backups'=>$backups, 'academico'=>$academico, 'anotaciones'=>$anotaciones, 'capacitaciones'=>$capacitaciones, 'antlaborales'=>$antlaborales, 'ends'=>$ends);
              
			  
			  header('Content-Type: application/json');  
			  echo json_encode($arrayName);
			
				
			}
          }
          break;

        case 'selectGerencias':
          {
            $query      = new query();
            $gerencias  = $query->queryJson("SELECT CB1.COD_GERENCIA, CB1.GERENCIA AS GERENCIA, (SELECT COUNT(CB2.COD_FUNC) FROM COLABORADOR_BASICOS CB2 WHERE CB2.COD_GERENCIA = CB1.COD_GERENCIA) AS CANTIDAD, CB1.ORDEN_GERENCIA, (SELECT TOP 1 CB3.COD_FUNC FROM COLABORADOR_BASICOS CB3 WHERE CB3.COD_GERENCIA = CB1.COD_GERENCIA AND CB3.COD_SUPERIOR_INMEDIATO = (SELECT COD_FUNC FROM COLABORADOR_BASICOS WHERE COD_CARGO = 1146)) AS COD_FUNC FROM COLABORADOR_BASICOS CB1 WHERE CB1.COD_SUPERIOR_INMEDIATO = (SELECT COD_FUNC FROM COLABORADOR_BASICOS WHERE COD_CARGO = 1146) OR CB1.COD_GERENCIA != (SELECT COD_GERENCIA FROM COLABORADOR_BASICOS WHERE COD_CARGO =1146) GROUP BY CB1.GERENCIA, CB1.COD_GERENCIA, CB1.ORDEN_GERENCIA UNION SELECT CB1.COD_GERENCIA, CB1.GERENCIA AS GERENCIA, (SELECT COUNT(CB2.COD_FUNC) FROM COLABORADOR_BASICOS CB2) AS CANTIDAD, CB1.ORDEN_GERENCIA, (SELECT COD_FUNC FROM COLABORADOR_BASICOS WHERE COD_CARGO = 1146 ) COD_FUNC FROM COLABORADOR_BASICOS CB1 WHERE CB1.COD_GERENCIA = (SELECT COD_GERENCIA FROM COLABORADOR_BASICOS WHERE COD_CARGO = 1146) GROUP BY CB1.GERENCIA, CB1.COD_GERENCIA, CB1.ORDEN_GERENCIA ORDER BY CB1.ORDEN_GERENCIA", "gerencias");
		        $arrayName  = array('gerencias'=>$gerencias);
            header('Content-Type: application/json');
            echo json_encode($arrayName);
          }
          break;

        case 'selectSubGerencias':
          {
            $var          = $_GET["gerencia"];
            $var          = str_replace("'", "", $var);
            $var          = str_replace("-", "", $var);
            $var          = str_replace("=", "", $var);
            $query        = new query();
            $subgerencia  = $query->queryJson("SELECT DEPARTAMENTO FROM COLABORADOR_BASICOS WHERE GERENCIA = '$var' GROUP BY DEPARTAMENTO");
            $arrayName    = array('subgerencia'=>$subgerencia);
            header('Content-Type: application/json');
            echo json_encode($arrayName);
          }
          break;

        case 'selectGerencia':
          {
            $var        = $_GET["id"];
            $var        = str_replace("'", "", $var);
            $var        = str_replace("-", "", $var);
            $var        = str_replace("=", "", $var);
            $query      = new query();
            $gerente    =  $query->queryJson("SELECT COD_FUNC AS id, PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;0;'+USUARIO+';'+CONVERT(varchar(10), COD_FUNC) AS name, CARGO AS title FROM COLABORADOR_BASICOS WHERE COD_SUPERIOR_INMEDIATO = 580 AND COD_GERENCIA = $var"." ORDER BY POSICION_ORGANIGRAMA DESC");
            $hijo       =  $query->queryJson("SELECT COD_FUNC AS id, PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;0;'+USUARIO+';'+CONVERT(varchar(10), COD_FUNC) AS name, CARGO AS title FROM COLABORADOR_BASICOS WHERE COD_SUPERIOR_INMEDIATO = ".$gerente['id']." ORDER BY POSICION_ORGANIGRAMA DESC");
            print_r($gerente);
            $arrayName  = array('name'=>$gerente['name'], 'title'=>$gerente['title'], 'children'=>$hijo);
            header('Content-Type: application/json');
            echo json_encode($arrayName);
          }
          break;

        case 'selectRaiz':
          {
            if (isset($_GET['idGerencia']) AND $_GET['idGerencia'] != "31") {
              (new log())->registrar($_SESSION['nombre'], "Gerencias seleccionada", $_GET['gerencianame']);
              $ger      = $_GET['idGerencia'];
              $query    = new query();
              $datos    = $query->queryJson("SELECT COD_FUNC AS id, PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;0;'+USUARIO+';'+CONVERT(varchar(10), COD_FUNC)+';'+ANTIGUEDAD+';'+NOMBRE_Y_APELLIDO+';'+NRO_CEDULA+';'+GERENCIA+';'+SUPERIOR_INMEDIATO+';'+FECHA_INGRESO AS name, CARGO AS title, COD_GERENCIA AS gerencia FROM COLABORADOR_BASICOS WHERE COD_SUPERIOR_INMEDIATO = (SELECT COD_FUNC FROM COLABORADOR_BASICOS WHERE COD_CARGO = 1146) AND TRANSVERSAL = 0 AND COD_GERENCIA = $ger", "datos");
              $granjson = "";

              if(count($datos) > 1) {
                $super  = $query->queryJson("SELECT COD_FUNC AS id, PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;0;'+USUARIO+';'+CONVERT(varchar(10), COD_FUNC)+';'+ANTIGUEDAD+';'+NOMBRE_Y_APELLIDO+';'+NRO_CEDULA+';'+GERENCIA+';'+SUPERIOR_INMEDIATO+';'+FECHA_INGRESO AS name, CARGO AS title, COD_GERENCIA AS gerencia FROM COLABORADOR_BASICOS WHERE COD_FUNC = (SELECT COD_FUNC FROM COLABORADOR_BASICOS WHERE COD_CARGO = 1146 )", "datos");
                $datos  = $query->queryJson("SELECT COD_FUNC AS id, PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;'+CONVERT(varchar(10),((NIVEL_JERARQUIA * 50)))+';'+USUARIO+';'+CONVERT(varchar(10),COD_FUNC)+';'+ANTIGUEDAD+';'+NOMBRE_Y_APELLIDO+';'+NRO_CEDULA+';'+GERENCIA+';'+SUPERIOR_INMEDIATO+';'+FECHA_INGRESO AS name, CARGO AS title, COD_GERENCIA AS gerencia FROM COLABORADOR_BASICOS WHERE TRANSVERSAL = 0 AND COD_SUPERIOR_INMEDIATO = (SELECT COD_FUNC FROM COLABORADOR_BASICOS WHERE COD_CARGO = 1146) AND COD_GERENCIA = $ger"." ORDER BY POSICION_ORGANIGRAMA DESC", "datos");
                header('Content-Type: application/json');
                $super[0]['children']   = $datos;
                $super[0]['estructura'] = $query->queryJson("SELECT CB1.COD_JERARQUIA cod, CB1.JERARQUIA cargo, (SELECT COUNT(CB2.COD_JERARQUIA) FROM COLABORADOR_BASICOS CB2 WHERE CB2.COD_JERARQUIA = CB1.COD_JERARQUIA AND CB2.COD_GERENCIA = ".$ger." ) cantidad FROM COLABORADOR_BASICOS CB1 WHERE COD_GERENCIA = ".$ger." GROUP BY CB1.COD_JERARQUIA, CB1.JERARQUIA, CB1.NIVEL_JERARQUIA ORDER BY CB1.NIVEL_JERARQUIA", "estructura");
                echo json_encode($super[0]);
              } else {
                $datos[0]['children']   = $query->queryJson("SELECT COD_FUNC AS id, PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;'+CONVERT(varchar(10),((NIVEL_JERARQUIA * 50)))+';'+USUARIO+';'+CONVERT(varchar(10),COD_FUNC) +';'+ANTIGUEDAD+';'+NOMBRE_Y_APELLIDO+';'+NRO_CEDULA+';'+GERENCIA+';'+SUPERIOR_INMEDIATO+';'+FECHA_INGRESO AS name, CARGO AS title, (NIVEL_JERARQUIA * 100) AS nivel FROM COLABORADOR_BASICOS WHERE TRANSVERSAL = 0 AND CARGO IS NOT NULL AND COD_SUPERIOR_INMEDIATO =".$datos[0]['id']." ORDER BY POSICION_ORGANIGRAMA DESC", "childrens");
                $datos[0]['estructura'] = $query->queryJson("SELECT CB1.COD_JERARQUIA cod, CB1.JERARQUIA cargo, (SELECT COUNT(CB2.COD_JERARQUIA) FROM COLABORADOR_BASICOS CB2 WHERE CB2.COD_JERARQUIA = CB1.COD_JERARQUIA AND CB2.COD_GERENCIA = ".$datos[0]['gerencia']." ) cantidad FROM COLABORADOR_BASICOS CB1 WHERE COD_GERENCIA = ".$datos[0]['gerencia']." GROUP BY CB1.COD_JERARQUIA, CB1.JERARQUIA, CB1.NIVEL_JERARQUIA ORDER BY CB1.NIVEL_JERARQUIA", "estructura");
                header('Content-Type: application/json');
                echo json_encode($datos[0]);
              }
            } else {
              $query                  = new query();
              $datos                  = $query->queryJson("SELECT COD_FUNC AS id, PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;0;'+USUARIO+';'+CONVERT(varchar(10),COD_FUNC)+';'+ANTIGUEDAD+';'+NOMBRE_Y_APELLIDO+';'+NRO_CEDULA+';'+GERENCIA+';'+SUPERIOR_INMEDIATO+';'+FECHA_INGRESO AS name, CARGO AS title FROM COLABORADOR_BASICOS WHERE COD_FUNC = (SELECT COD_FUNC FROM COLABORADOR_BASICOS WHERE COD_CARGO = 1146)", "datos");
              $datos[0]['children']   = $query->queryJson("SELECT izquierda.COD_FUNC AS id, 
				izquierda.PRIMER_NOMBRE+' '+izquierda.PRIMER_APELLIDO+';'+
				izquierda.FOTO_TARGET+';false;'+
				CONVERT(varchar(10),((izquierda.NIVEL_JERARQUIA * 50)))+';'+
				izquierda.USUARIO+';'+CONVERT(varchar(10),izquierda.COD_FUNC)+';'+
				izquierda.ANTIGUEDAD+';'+izquierda.NOMBRE_Y_APELLIDO+';'+izquierda.NRO_CEDULA+';'+
				izquierda.GERENCIA+';'+izquierda.SUPERIOR_INMEDIATO+';'+izquierda.FECHA_INGRESO AS name, 
				izquierda.CARGO AS title, (izquierda.NIVEL_JERARQUIA * 100) AS nivel,
				izquierda.POSICION_ORGANIGRAMA
				FROM 
					(SELECT top 100 * FROM COLABORADOR_BASICOS 
						WHERE TRANSVERSAL = 0 AND COD_JERARQUIA in (11, 29, 36, 44) 
						AND CARGO IS NOT NULL 
						AND COD_SUPERIOR_INMEDIATO = ".$datos[0]['id']."
						AND POSICION_ORGANIGRAMA <> 'DERECHA'
						ORDER BY POSICION_ORGANIGRAMA DESC, NIVEL_JERARQUIA * 100 DESC ) izquierda
				UNION ALL
				SELECT derecha.COD_FUNC AS id, 
				derecha.PRIMER_NOMBRE+' '+derecha.PRIMER_APELLIDO+';'+
				derecha.FOTO_TARGET+';false;'+
				CONVERT(varchar(10),((derecha.NIVEL_JERARQUIA * 50)))+';'+
				derecha.USUARIO+';'+CONVERT(varchar(10),derecha.COD_FUNC)+';'+
				derecha.ANTIGUEDAD+';'+derecha.NOMBRE_Y_APELLIDO+';'+derecha.NRO_CEDULA+';'+
				derecha.GERENCIA+';'+derecha.SUPERIOR_INMEDIATO+';'+derecha.FECHA_INGRESO AS name, 
				derecha.CARGO AS title, (derecha.NIVEL_JERARQUIA * 100) AS nivel,
				derecha.POSICION_ORGANIGRAMA
				FROM 
					(SELECT top 100 * FROM COLABORADOR_BASICOS 
						WHERE TRANSVERSAL = 0 AND COD_JERARQUIA in (11, 29, 36, 44) 
						AND CARGO IS NOT NULL 
						AND COD_SUPERIOR_INMEDIATO = ".$datos[0]['id']."
						AND POSICION_ORGANIGRAMA = 'DERECHA'
						ORDER BY POSICION_ORGANIGRAMA DESC, NIVEL_JERARQUIA * 100 ASC ) derecha", "childrens");
              $datos[0]['estructura'] = $query->queryJson("SELECT CB1.COD_JERARQUIA cod, CB1.JERARQUIA cargo, (SELECT COUNT(CB2.COD_JERARQUIA) FROM COLABORADOR_BASICOS CB2 WHERE CB2.COD_JERARQUIA = CB1.COD_JERARQUIA) cantidad FROM COLABORADOR_BASICOS CB1 WHERE CB1.COD_JERARQUIA IS NOT NULL GROUP BY CB1.COD_JERARQUIA, CB1.JERARQUIA, CB1.NIVEL_JERARQUIA ORDER BY CB1.NIVEL_JERARQUIA", "estructura");
              header('Content-Type: application/json');
              echo json_encode($datos[0]);
            }
          }
          break;

        case 'selectTransversales':
          {
            $query = new query();
            $datos =  $query->queryJson("SELECT COD_FUNC AS id, PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;'+CONVERT(varchar(10),((NIVEL_JERARQUIA * 50)))+';'+USUARIO+';'+CONVERT(varchar(10),COD_FUNC)+';'+ANTIGUEDAD+';'+NOMBRE_Y_APELLIDO+';'+NRO_CEDULA+';'+GERENCIA+';'+SUPERIOR_INMEDIATO+';'+FECHA_INGRESO AS name, CARGO AS title, (NIVEL_JERARQUIA * 100) AS nivel,COD_SUPERIOR_INMEDIATO AS superior, POSICION_ORGANIGRAMA AS posicion FROM COLABORADOR_BASICOS WHERE TRANSVERSAL !=0 ORDER BY NIVEL_JERARQUIA, SUPERIOR_INMEDIATO DESC", "transversal");
            header('Content-Type: application/json');
            echo json_encode($datos);
          }
          break;
              
        case 'logregister':
          {
            $accion = $_GET['actividad'];
			      (new log())->registrar($_SESSION['nombre'], $accion, "SELECT NOMBRE_Y_APELLIDO FROM `colaboradores` WHERE cod_func = ".$_SESSION['ultimocid']." LIMIT 1", true);
          }
          break;

        default:
          break;
      }
    }
  }

  $class = new funciones();
  $class->switchFuncion();
?>