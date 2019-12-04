<?php
header('Content-Type: application/json');
include "model/consultasMapper.php";
include "../../../inc/conexionMySQL.php";
include "../../../inc/org_log.php";
session_start();
if(isset($_GET['consulta']))
{
switch ($_GET['consulta']) 
{
    case 'rankings':
        {
        $result = (new consultas )->fetchRankings();
        echo json_encode($result);
        }
        break;
    case 'gerencias':
        {
        $result = (new consultas )->fetchGerencias();
        echo json_encode($result);
        }
        break;
    case 'departamentos':
        {
        if(isset($_GET['gerencia']))
        {
        $result = (new consultas )->fetchDepartamentos($_GET['gerencia']);
        echo json_encode($result);             
        }
        else
        {
        echo json_encode(Array("error"=>"sin parametro de gerencia")); 
        }
 
        }
        break;
    case 'datosranking':
        {
        if(isset($_GET['filtros']) and isset($_GET['ranking']) and isset($_GET['top']) and isset($_GET['orden']) and isset($_GET['nombreranking']))
        {
        error_log($_GET['filtros']);  
      
        $filtros = explode(",",$_GET['filtros']);
        
       
        foreach ($filtros as $key => $value)
        {
             if($value != "")
        {
         $val=explode("||",$value);
         list($gerencia,$departamento,$fecha,$titulo)=$val;
         
           (new log())->registrar($_SESSION['nombre'],"Consulta rakings de ".$_GET['nombreranking']." realizada",$titulo." ".$fecha,false);
         
         $row[]=Array("gerencia"=>$gerencia,"departamento"=>$departamento,"fecha"=>$fecha,"titulo"=>$titulo);
		 
		 error_log(print_r($row,true));
        }    
        }
        $result = (new consultas )->consultarRanking($row,$_GET['ranking'],$_GET['top'],$_GET['orden']);
       // echo $result;
        
        
        
        
        
        for ($r = 0; $r < count($result['datos']); $r++) 
        {
             $row = $result['datos'][$r];
    
     $aux=null;
     $aux2= Array("type"=>"bar","showInLegend"=>true,"name"=>$row['Filtro']);
     
    unset($row["0"]);
     
     foreach ($row as $key => $value)
     {
         $aux[ $key]=$value;
     }
     
     unset($row["Gerencia o Departamento"]);
     unset($row["Filtro"]);
      foreach ($row as $key => $value)
     {
         $aux2["dataPoints"][]= Array("y"=>(int)$value,"label"=>$key);
     }
   //  unset($aux2["dataPoints"][0]);
   
     $finalresul[]= $aux;
     $finalresul2[]=$aux2;
    }
        
     //print_r($finalresul);
    echo json_encode(Array("fortable"=> $finalresul,"forchart"=>$finalresul2,'fortop'=>$result['top']),JSON_UNESCAPED_UNICODE);   
        
        
        
        
        
        }
        else
        {
          echo json_encode(Array("error"=>"sin parametro de para generar datos","parametros"=>print_r($_GET,TRUE)));   
        }
        }
        break;
        
    default:
        echo json_encode(Array("error"=>"Consulta ".$_GET['consulta']." no disponible"));
        break;
}
    
}
else
{
    
    echo json_encode(Array("error"=>"sin parametro de consulta"));
    
}




?>