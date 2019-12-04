<?php
header('Content-Type: application/json');
include "mssql.php";

$query = mssql_query("SELECT DESC_RANKING FROM COLABORADOR_RANKING WHERE COD_CATEGORIA = ".$_GET['CATEGORIA']." GROUP BY DESC_RANKING ORDER BY DESC_RANKING ");
$concat="[0]";
if (!mssql_num_rows($query)) 
{
    echo 'No records found';
}
else 
{
while ($row = mssql_fetch_array($query, MSSQL_NUM)) 
{
       $concat = $concat.","."[".$row[0]."]";
}
}
// Liberar de memoria el resultado de la consulta 
mssql_free_result($query);



$condiciones="";

if(isset($_GET['GERENCIAS']))
{
    $gerencias = $_GET['GERENCIAS'];
    $gerencias = explode(",",$gerencias);
    $aux= "AND COD_GERENCIA IN (";
    for ($i = 0; $i < count($gerencias); $i++) 
    {
        if(($i+1) == count($gerencias))
        {
             $aux= $aux.$gerencias[$i];
        }
        else
        {
            $aux= $aux.$gerencias[$i].","; 
        }
        
    }
    $aux=$aux.")";
    $condiciones = $condiciones." ".$aux;
}
if(isset($_GET['FECHAS']))
{
    
    $fecha = $_GET['FECHAS'];
    $fecha = explode(",",$fecha);
    $aux=" AND FECHA_PERIODO BETWEEN '".$fecha[0]."' AND '".$fecha[1]."'";
    
    $condiciones=$condiciones.$aux;
}
if(isset($_GET['DEPARTAMENTO']))
{
    $departamento = $_GET['DEPARTAMENTO'];
    $departamento = explode(",",$departamento);
    $aux= "AND COD_DEPARTAMENTO_AREA IN (";
    for ($i = 0; $i < count($departamento); $i++) 
    {
         $aux= $aux.$departamento[$i].",";
    }
    $aux=$aux.")";
    $condiciones = $condiciones." ".$aux;
}










$pivot = " 

select PivotTable.* 
from (

select  DESC_RANKING,SUM(CANT_RANKING) CANTIDAD ,(SELECT TOP 1 GERENCIA FROM COLABORADOR_BASICOS WHERE COD_GERENCIA = CR.COD_GERENCIA) AS [Gerencia o Departamento] FROM COLABORADOR_RANKING CR WHERE COD_CATEGORIA = ".$_GET['CATEGORIA']." $condiciones GROUP BY DESC_RANKING,COD_GERENCIA

) AS SourceTable 

PIVOT 
( 
max(SourceTable.CANTIDAD) 
FOR SourceTable.DESC_RANKING IN 
(
".$concat."
)
) AS PivotTable

";


//echo $pivot;

$pivot = mssql_query($pivot);
if (!mssql_num_rows($pivot)) 
{
    echo 'No records found';
}
else 
{
    $finalresul="";
    $fainalresult2="";
 while( $row =  mssql_fetch_array($pivot,MSSQL_ASSOC))
 {
     $aux=null;
     $aux2= Array("type"=>"bar","showInLegend"=>true,"name"=>utf8_encode($row['Gerencia o Departamento']),"color" =>"silver");
     
    unset($row["0"]);
     
     foreach ($row as $key => $value)
     {
         $aux[ utf8_encode($key)]=utf8_encode($value);
     
         
     }
     
     unset($row["Gerencia o Departamento"]);
      foreach ($row as $key => $value)
     {
      
         $aux2["dataPoints"][]= Array("y"=>$value,"label"=>utf8_encode($key));
         
     }
   //  unset($aux2["dataPoints"][0]);
   
     $finalresul[]= $aux;
     $finalresul2[]=$aux2;
 }

//print_r($finalresul);
echo json_encode(Array("fortable"=> $finalresul,"forchart"=>$finalresul2),JSON_UNESCAPED_UNICODE);

}
mssql_free_result($pivot);
?>