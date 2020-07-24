<?php
  include "query.php";
  include "org_log.php";
  
  class funciones {
    function switchFuncion() {
      switch ($_GET["funcion"]) {
        case 'SelectChildrens':
        {
          if ( isset($_GET["id"] )) {
            $var = $_GET["id"];
            error_log($_GET["id"]);
        
            if($var == "undefined" ) {

          $query = new query();
          $result= $query->queryJson("select COD_FUNC AS id ,PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;'+CONVERT(varchar(10),((NIVEL_JERARQUIA * 50)))+';'+USUARIO+';'+CONVERT(varchar(10),COD_FUNC) +';'+ANTIGUEDAD+';'+NOMBRE_Y_APELLIDO AS name ,CARGO AS title, (NIVEL_JERARQUIA * 100) AS nivel  from  COLABORADOR_BASICOS where TRANSVERSAL =0 AND COD_SUPERIOR_INMEDIATO =580","SelectChildrens");
          header('Content-Type: application/json');
          $arrayName = array('children' => $result);
          echo json_encode($arrayName);
        }
        else
        {
          $var = str_replace("'","",$var);
          $var = str_replace("-","",$var);
          $var = str_replace("=","",$var);
          $query = new query();
//          $result= $query->queryJson("select COD_FUNC AS id ,PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;'+CONVERT(varchar(10),((NIVEL_JERARQUIA * 50)))+';'+USUARIO+';'+CONVERT(varchar(10),COD_FUNC)  +';'+ANTIGUEDAD+';'+NOMBRE_Y_APELLIDO AS name ,CARGO AS title, (NIVEL_JERARQUIA * 100) AS nivel  from  COLABORADOR_BASICOS where TRANSVERSAL =0 AND COD_SUPERIOR_INMEDIATO =$var","SelectChildrens");
          $result= $query->queryJson("SELECT COD_FUNC AS id, RTRIM(LTRIM(PRIMER_NOMBRE)) + ' ' + RTRIM(LTRIM(PRIMER_APELLIDO)) + ';' + FOTO_TARGET + ';false;' + CONVERT(varchar(10),((NIVEL_JERARQUIA * 50))) + ';' + RTRIM(LTRIM(USUARIO)) + ';' + CONVERT(varchar(10),COD_FUNC) + ';' + ANTIGUEDAD + ';' + NOMBRE_Y_APELLIDO AS name, CARGO AS title, (NIVEL_JERARQUIA * 100) AS nivel FROM COLABORADOR_BASICOS WHERE TRANSVERSAL = 0 AND COD_SUPERIOR_INMEDIATO = $var ORDER BY (NIVEL_JERARQUIA * 100) DESC","SelectChildrens");
          header('Content-Type: application/json');
		  //print_r($result);
          $arrayName = array('children' => $result);
          echo json_encode($arrayName);
        }

        }
        else
        {


        }

      }
      break;

      case 'selectTablas':


      {
        $var = $_GET["id"];
        $var = str_replace("'","",$var);
        $var = str_replace("-","",$var);
        $var = str_replace("=","",$var);


        if(isset($_GET['dateIni']) and isset($_GET['dateEnd']) and isset($_GET['consulta']))
        {   $query = new query();
          $fechaInicio = $_GET['dateIni'];
          $fechaFin = $_GET['dateEnd'];
        header('Content-Type: application/json');
          switch ($_GET['consulta'])
          {
              case 'logros':

              {
              $logros      = $query->queryJson("SELECT MES+'-'+CONVERT(varchar(10),ANO) AS FECHA , META,CONVERT(varchar(20),LOGRADO) as LOGRADO,RATIO,COLOR  FROM COLABORADOR_LOGROS WHERE FECHA BETWEEN '$fechaInicio' AND '$fechaFin' AND COD_FUNC = $var order by convert(datetime, FECHA) desc ","logros");
              $arrayName = array('logros' => $logros );
              echo json_encode($arrayName);
              }
              break;

              case 'salario':
              {
              $salario     = $query->queryJson("SELECT COD_FUNC as id ,PERIODO as periodo , SALARIO_BRUTO_FIJO as fijo, SALARIO_BRUTO_VARIABLE as variable ,(SALARIO_BRUTO_FIJO+SALARIO_BRUTO_VARIABLE)as total FROM COLABORADOR_SALARIOS WHERE CONVERT(date, PERIODO,103) BETWEEN '$fechaInicio' and '$fechaFin' and COD_FUNC = $var","salario");
              $arrayName = array('salario' => $salario );
              echo json_encode($arrayName);
              }
              break;

              case 'eventos':
              {

              $eventos= $query->queryJson("SELECT COD_FALTA as codigo ,DESC_FALTA as falta,MES as mes,ANHO as ano,TOTAL_CONCEPTO as total, COLOR as color  FROM COLABORADOR_FALTAS_EVENTOS WHERE  CONVERT(DATE, CONVERT(VARCHAR(20), ANHO) + '-'+CONVERT (VARCHAR (20),MES )+'-'+'01') BETWEEN '$fechaInicio' AND '$fechaFin' AND COD_FUNC = $var ","eventos");
              error_log (print_r($eventos,true));
              $arrayName = array('eventos' => $eventos);
              echo json_encode($arrayName);
              }
              break;

              case 'movimientos':
              {
              $movimientos = $query->queryJson(" SELECT  CONVERT(VARCHAR,ULTIMA_MODIFICACION, 103) as desde ,CARGO as cargo ,DEPARTAMENTO as departamento,UNIDAD as unidad FROM COLABORADOR_DATO_ESTRUCTURA where CONVERT(date,ULTIMA_MODIFICACION,103) BETWEEN '$fechaInicio' AND '$fechaFin'  AND COD_FUNC = $var","movimientos");
              $arrayName = array('movimientos' => $movimientos );
              echo json_encode($arrayName);
              }
              break;

              case 'documentos':
                {
                  $documentos   = $query->queryJson("SELECT FUNC_TIPO, FUNC_DOCUMENTO, FUNC_PATH FROM COLABORADOR_DOCUMENTOS WHERE FUNC_CODIGO = $var", "documentos");
                  $arrayName    = array('documentos' => $documentos );
                  echo json_encode($arrayName);
                }
                break;

              case 'anotaciones':
                {
                  $anotaciones  = $query->queryJson("SELECT FUNC_NRO_ANOTACION, FUNC_FECHA, FUNC_EVENTO, FUNC_OBSERVACION FROM COLABORADOR_ANOTACIONES WHERE FUNC_CODIGO = $var", "anotaciones");
                  $arrayName    = array('anotaciones' => $anotaciones );
                  echo json_encode($arrayName);
                }
                break;

              case 'capacitaciones':
                {
                  $capacitaciones = $query->queryJson("SELECT FUNC_NRO_CAPACITACION, FUNC_EMPRESA, FUNC_CURSO, FUNC_ANHO, FUNC_MES, FUNC_CANT_HORA FROM COLABORADOR_CAPACITACIONES WHERE FUNC_CODIGO = $var", "capacitaciones");
                  $arrayName      = array('capacitaciones' => $capacitaciones );
                  echo json_encode($arrayName);
                }
                break;

            default:
              // code...
            break;
          }





        }
        else
        {
        $query = new query();
        $info        = $query->queryJson("SELECT GRADO_ACADEMICO,UNIVERSIDAD,MASTERADO,DIPLOMADO FROM COLABORADOR_BASICOS WHERE COD_FUNC= $var","academico");
        $logros      = $query->queryJson("SET LANGUAGE Spanish; SELECT TOP 6 MES+'-'+CONVERT(varchar(10),ANO) AS FECHA , META,CONVERT(varchar(20),LOGRADO) as LOGRADO,RATIO,COLOR  FROM COLABORADOR_LOGROS WHERE  COD_FUNC = $var ORDER BY convert(datetime, FECHA) desc","logros");
         $salario     = $query->queryJson(" SELECT TOP 6 COD_FUNC as id ,PERIODO as periodo , SALARIO_BRUTO_FIJO as fijo, SALARIO_BRUTO_VARIABLE as variable ,(SALARIO_BRUTO_FIJO+SALARIO_BRUTO_VARIABLE)as total FROM COLABORADOR_SALARIOS WHERE  COD_FUNC = $var ORDER BY CONVERT(DATE, PERIODO) DESC","salario");
        $eventos= $query->queryJson(" SELECT TOP 6 COD_FALTA as codigo ,DESC_FALTA as falta,MES as mes,ANHO as ano,TOTAL_CONCEPTO as total, COLOR as color  FROM COLABORADOR_FALTAS_EVENTOS WHERE  COD_FUNC =$var ORDER BY CONVERT(DATE, CONVERT(VARCHAR(20), ANHO) + '-'+CONVERT (VARCHAR (20),MES )+'-'+'01')  DESC ","eventos");
       $movimientos = $query->queryJson(" SELECT TOP 6  CONVERT(VARCHAR,ULTIMA_MODIFICACION, 103) as desde ,CARGO as cargo ,DEPARTAMENTO as departamento,UNIDAD as unidad FROM COLABORADOR_DATO_ESTRUCTURA where COD_FUNC = $var order by ULTIMA_MODIFICACION ","movimientos");
       $documentos   = $query->queryJson("SELECT FUNC_TIPO, FUNC_DOCUMENTO, FUNC_PATH FROM COLABORADOR_DOCUMENTOS WHERE FUNC_CODIGO = $var","documentos");
       $anotaciones   = $query->queryJson("SELECT FUNC_NRO_ANOTACION, FUNC_FECHA, FUNC_EVENTO, FUNC_OBSERVACION FROM COLABORADOR_ANOTACIONES WHERE FUNC_CODIGO = $var", "anotaciones");
       $capacitaciones   = $query->queryJson("SELECT FUNC_NRO_CAPACITACION, FUNC_EMPRESA, FUNC_CURSO, FUNC_ANHO, FUNC_MES, FUNC_CANT_HORA FROM COLABORADOR_CAPACITACIONES WHERE FUNC_CODIGO = $var", "capacitaciones");
       $arrayName = array('informacion' => $info, 'logros' => $logros, 'salario' => $salario, 'eventos' => $eventos, 'movimientos' => $movimientos, 'documentos' => $documentos, 'anotaciones' => $anotaciones, 'capacitaciones' => $capacitaciones); 
       //$arrayName = array('informacion'=>$info,'logros' => $logros,'salario'=>$salario,'eventos'=>$eventos,'movimientos'=>$movimientos,'documentos'=>$documentos),'anotaciones'=>$anotaciones),'capacitaciones'=>$capacitaciones);
        header('Content-Type: application/json');
        echo json_encode($arrayName);
       }
      } 

      break;

      case 'selectGerencias':
        {

          $query = new query();
          $gerencias =  $query->queryJson(" select COD_GERENCIA, GERENCIA,ORDEN_GERENCIA from COLABORADOR_BASICOS where COD_SUPERIOR_INMEDIATO = (SELECT COD_FUNC FROM COLABORADOR_BASICOS WHERE COD_JERARQUIA = 10) OR COD_GERENCIA = 31  GROUP BY GERENCIA,COD_GERENCIA,ORDEN_GERENCIA  ORDER BY ORDEN_GERENCIA ","gerencias");     
		  $arrayName = array('gerencias'=>$gerencias);
          header('Content-Type: application/json');
          echo json_encode($arrayName);

        }
        break;

        case 'selectSubGerencias':
          {
            $var = $_GET["gerencia"];
            $var = str_replace("'","",$var); 
            $var = str_replace("-","",$var);
            $var = str_replace("=","",$var);
            $query = new query();
            $subgerencia =  $query->queryJson("SELECT DEPARTAMENTO FROM COLABORADOR_BASICOS WHERE GERENCIA = '$var' GROUP BY DEPARTAMENTO");
            $arrayName = array('subgerencia'=>$subgerencia);
            header('Content-Type: application/json');
            echo json_encode($arrayName);

          }
          break;

          case 'selectGerencia':
          {

            $var = $_GET["id"];
            $var = str_replace("'","",$var);
            $var = str_replace("-","",$var);
            $var = str_replace("=","",$var);
            $query = new query();
            $gerente =  $query->queryJson("SELECT COD_FUNC AS id ,PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;0;'+USUARIO+';'+CONVERT(varchar(10),COD_FUNC) AS name ,CARGO AS title FROM COLABORADOR_BASICOS  WHERE COD_SUPERIOR_INMEDIATO = 580 AND  COD_GERENCIA = $var");
            $hijo    =  $query->queryJson("SELECT COD_FUNC AS id ,PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;0;'+USUARIO+';'+CONVERT(varchar(10),COD_FUNC) AS name ,CARGO AS title FROM COLABORADOR_BASICOS  WHERE COD_SUPERIOR_INMEDIATO = ".$gerente['id']);

            print_r($gerente);

            $arrayName = array('name' => $gerente['name'] ,'title'=>$gerente['title'], 'children' => $hijo );
            header('Content-Type: application/json');
            echo json_encode($arrayName);

          }
            # code...
            break;


            case 'selectRaiz':
            {
				(new log())->registrar($_SESSION['nombre'],"Gerencias seleccionada",$_GET['gerencianame']);
                if (isset($_GET['idGerencia']) and $_GET['idGerencia'] != "31" )
              {
                
                $ger = $_GET['idGerencia'];
				
				
				
                $query = new query();
                $datos =  $query->queryJson("select COD_FUNC AS id ,PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;0;'+USUARIO+';'+CONVERT(varchar(10),COD_FUNC)+';'+ANTIGUEDAD+';'+NOMBRE_Y_APELLIDO AS name ,CARGO AS title , COD_GERENCIA as gerencia from  COLABORADOR_BASICOS where  TRANSVERSAL =0 and COD_SUPERIOR_INMEDIATO = (select COD_FUNC from COLABORADOR_BASICOS WHERE COD_JERARQUIA = 10 ) AND COD_GERENCIA = $ger ","datos");
                
                $granjson="";
             
                if(count($datos)>1)
                {
                  
                  $super=$query->queryJson("select  COD_FUNC AS id , PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;0;'+USUARIO+';'+CONVERT(varchar(10),COD_FUNC)+';'+ANTIGUEDAD+';'+NOMBRE_Y_APELLIDO AS name ,CARGO AS title , COD_GERENCIA as gerencia from  COLABORADOR_BASICOS where COD_FUNC = (select COD_FUNC from COLABORADOR_BASICOS WHERE COD_JERARQUIA = 10 )","datos");

                  $datos =  $query->queryJson("select COD_FUNC AS id ,PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;'+CONVERT(varchar(10),((NIVEL_JERARQUIA * 50)))+';'+USUARIO+';'+CONVERT(varchar(10),COD_FUNC)+';'+ANTIGUEDAD+';'+NOMBRE_Y_APELLIDO AS name ,CARGO AS title , COD_GERENCIA as gerencia from  COLABORADOR_BASICOS where  TRANSVERSAL =0 and COD_SUPERIOR_INMEDIATO = (select COD_FUNC from COLABORADOR_BASICOS WHERE COD_JERARQUIA = 10 ) AND COD_GERENCIA = $ger ","datos");
                

                  header('Content-Type: application/json');
                  $super[0]['children']=$datos;
                  $super[0]['estructura']= $query->queryJson(" SELECT CB1.COD_JERARQUIA cod ,CB1.JERARQUIA cargo ,(SELECT COUNT(CB2.COD_JERARQUIA) FROM COLABORADOR_BASICOS CB2 WHERE CB2.COD_JERARQUIA = CB1.COD_JERARQUIA AND CB2.COD_GERENCIA = ".$ger." ) cantidad FROM COLABORADOR_BASICOS CB1 WHERE  COD_GERENCIA = ".$ger." GROUP BY CB1.COD_JERARQUIA,CB1.JERARQUIA","estructura");

                  echo json_encode($super[0]);

                }
                else 
                {
                $datos[0]['children'] =  $query->queryJson("select COD_FUNC AS id ,PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;'+CONVERT(varchar(10),((NIVEL_JERARQUIA * 50)))+';'+USUARIO+';'+CONVERT(varchar(10),COD_FUNC) +';'+ANTIGUEDAD+';'+NOMBRE_Y_APELLIDO AS  name ,CARGO AS title, (NIVEL_JERARQUIA * 100) AS nivel  from  COLABORADOR_BASICOS where TRANSVERSAL =0 AND CARGO IS NOT NULL AND COD_SUPERIOR_INMEDIATO =".$datos[0]['id'],"childrens");
                $datos[0]['estructura']= $query->queryJson(" SELECT CB1.COD_JERARQUIA cod ,CB1.JERARQUIA cargo ,(SELECT COUNT(CB2.COD_JERARQUIA) FROM COLABORADOR_BASICOS CB2 WHERE CB2.COD_JERARQUIA = CB1.COD_JERARQUIA AND CB2.COD_GERENCIA = ".$datos[0]['gerencia']." ) cantidad FROM COLABORADOR_BASICOS CB1 WHERE  COD_GERENCIA = ".$datos[0]['gerencia']." GROUP BY CB1.COD_JERARQUIA,CB1.JERARQUIA","estructura");
             
                header('Content-Type: application/json');
                echo json_encode($datos[0]);
                }
              }
              else
            {


              $query = new query();
              $datos =  $query->queryJson("select COD_FUNC AS id ,PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;0;'+USUARIO+';'+CONVERT(varchar(10),COD_FUNC)+';'+ANTIGUEDAD+';'+NOMBRE_Y_APELLIDO AS name ,CARGO AS title  from  COLABORADOR_BASICOS where COD_FUNC = (select COD_FUNC WHERE COD_JERARQUIA = 10 ) ","datos");
              $datos[0]['children'] =  $query->queryJson("select COD_FUNC AS id ,PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;'+CONVERT(varchar(10),((NIVEL_JERARQUIA * 50)))+';'+USUARIO+';'+CONVERT(varchar(10),COD_FUNC)+';'+ANTIGUEDAD+';'+NOMBRE_Y_APELLIDO AS name ,CARGO AS title, (NIVEL_JERARQUIA * 100) AS nivel  from  COLABORADOR_BASICOS where TRANSVERSAL =0 AND CARGO IS NOT NULL AND COD_SUPERIOR_INMEDIATO =".$datos[0]['id'],"childrens");
              $datos[0]['estructura']= $query->queryJson(" SELECT CB1.COD_JERARQUIA cod ,CB1.JERARQUIA cargo ,(SELECT COUNT(CB2.COD_JERARQUIA) FROM COLABORADOR_BASICOS CB2 WHERE CB2.COD_JERARQUIA = CB1.COD_JERARQUIA  ) cantidad FROM COLABORADOR_BASICOS CB1 WHERE  CB1.COD_JERARQUIA IS NOT NULL GROUP BY CB1.COD_JERARQUIA,CB1.JERARQUIA","estructura");

              header('Content-Type: application/json');
              echo json_encode($datos[0]);

            }
            }
              # code...
              break;

              case 'selectTransversales':
              {

                $query = new query();
                $datos =  $query->queryJson("SELECT COD_FUNC AS id ,PRIMER_NOMBRE+' '+PRIMER_APELLIDO+';'+FOTO_TARGET+';false;'+CONVERT(varchar(10),((NIVEL_JERARQUIA * 50)))+';'+USUARIO+';'+CONVERT(varchar(10),COD_FUNC)+';'+ANTIGUEDAD+';'+NOMBRE_Y_APELLIDO   AS name ,CARGO AS title, (NIVEL_JERARQUIA * 100) AS nivel,COD_SUPERIOR_INMEDIATO AS superior  from  COLABORADOR_BASICOS where TRANSVERSAL !=0 ","transversal");
                header('Content-Type: application/json');
                echo json_encode($datos);
              }
              break;

    default:
      # code...
      break;
  }

  }





}


$class = new funciones();
$class->switchFuncion();










 ?>
