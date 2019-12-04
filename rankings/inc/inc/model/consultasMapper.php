<?php
include "mssql.php";
class consultas 
{

function newQuery($sql,$utf8=true)
{
    
$queryResult = mssql_query($sql);   
if (!mssql_num_rows($queryResult)) 
{
    error_log('No records found');
    return 'No records found';
}
else 
{
$result="";    
while($row =  mssql_fetch_array($queryResult,MSSQL_ASSOC))
{
     $aux="";
     foreach ($row as $key => $value)
     {
         if($utf8)
         {
         $aux[ utf8_encode($key)]=utf8_encode($value);
         }
         else
         {
          $aux[$key]=$value;   
         }
     }
     $result[] = $aux;
}
mssql_free_result($queryResult); 
return $result;
}
  
    
}

function fetchRankings()
{
$sql="SELECT COD_CATEGORIA AS ID ,DESCRIPCION_CATEGORIA AS CATEGORIA FROM COLABORADOR_CATEGORIA_RANKING";    
return $this->newQuery($sql,true);
}
function fetchGerencias()
{
$sql="SELECT GERENCIA,COD_GERENCIA AS CODIGO FROM COLABORADOR_BASICOS GROUP BY GERENCIA,COD_GERENCIA";    
return $this->newQuery($sql,true);
}
function fetchDepartamentos($gerencia)
{
$sql="SELECT DEPARTAMENTO,COD_DEPARTAMENTO_AREA AS CODIGO FROM COLABORADOR_BASICOS WHERE COD_GERENCIA = $gerencia GROUP BY DEPARTAMENTO,COD_DEPARTAMENTO_AREA";    
return $this->newQuery($sql,true);
}
function rankingsColumnas($ranking,$top,$orden)
{
$sql="SELECT DESC_RANKING,sum(CANT_RANKING) AS SUMA FROM COLABORADOR_RANKING WHERE COD_CATEGORIA = $ranking GROUP BY DESC_RANKING ORDER BY sum(CANT_RANKING) $orden";
$resultado = $this->newQuery($sql,false);
$concat = "[0]";
     foreach ($resultado as $key => $value)
     {
      $concat=$concat.","."[".$value['DESC_RANKING']."]";
     }
return $concat; 
}
function rankingsTop($ranking,$top,$orden,$departamento)
{
$sql="SELECT TOP $top DESC_RANKING,sum(CANT_RANKING) AS SUMA FROM COLABORADOR_RANKING WHERE COD_CATEGORIA = $ranking $departamento GROUP BY DESC_RANKING ORDER BY sum(CANT_RANKING) $orden";
error_log($sql);
$resultado = $this->newQuery($sql,true);
$aux="";
$total = 0;
 foreach ($resultado as $key => $value)
     {
     $total = $total + (int)$value['SUMA'];
     }
     
     
 foreach ($resultado as $key => $value)
     {
      $aux[]=Array("y"=>(int)$value['SUMA'],"label"=>$value['DESC_RANKING'],"porcentaje"=>round((((int)$value['SUMA']*100)/$total),0));
     }

return $aux;
}
function consultarRanking($datos,$categoria="0",$top,$orden)
{
 $columnas = $this->rankingsColumnas($categoria,$top,$orden); 
 $sql="";
 $auxtg="";
 for ($d = 0; $d < count($datos); $d++) 
{
 
$condiciones="";

if($datos[$d]['gerencia'] != "" and $datos[$d]['gerencia']!="0" )
{
    $gerencias = explode(" ",$datos[$d]['gerencia']);
    $aux= "AND COD_GERENCIA IN (";
    for ($i = 0; $i < count($gerencias); $i++) 
    {
        if(($i+1) == count($gerencias))
        {
             $auxtg[]=$gerencias[$i];
             $aux= $aux.$gerencias[$i];
        }
        else
        {
            $auxtg[]=$gerencias[$i];
            $aux= $aux.$gerencias[$i].","; 
        }
        
    }
 
    $aux=$aux.")";
    $condiciones = $condiciones." ".$aux;
   
}
else
{
    
    $datos[$d][titulo]="";
    
}



if($datos[$d]['fecha'] != "")
{
    $fecha = explode("*",$datos[$d]['fecha']);
 //   $aux=" AND FECHA_PERIODO BETWEEN '".$fecha[0]."' AND '".$fecha[1]."'";
    
$aux=" AND
cast (convert(date,
RIGHT(fecha_periodo,4)+'-'+RIGHT( LEFT(fecha_periodo,5), 2)+'-' +LEFT(fecha_periodo,2)
) AS DATE) 
BETWEEN 
CONVERT(DATE,'".$fecha[0]."',103)
AND
CONVERT(DATE,'".$fecha[1]."',103)
";
    
    $condiciones=$condiciones.$aux;
}
if($datos[$d]['departamento'] != "" and $datos[$d]['departamento'] !="null")
{
    $departamento = explode(" ",$datos[$d]['departamento']);
    $aux= "AND COD_DEPARTAMENTO_AREA IN (";
    for ($i = 0; $i < count($departamento); $i++) 
    {
   
        if(($i+1) == count($departamento))
        {
             $auxtd[]=$departamento[$i];
             $aux= $aux.$departamento[$i];
        }
        else
        {
             $auxtd[]=$departamento[$i];
             $aux= $aux.$departamento[$i].","; 
        }
    }
    $aux=$aux.")";
    $condiciones = $condiciones." ".$aux;
}
    
if(count($datos)==($d+1))
{
    
    if($datos[$d][titulo]!="")
    {
    
  $sql= $sql."select PivotTable.* from (
SELECT DESC_RANKING,SUM(CANT_RANKING) CANTIDAD ,(SELECT TOP 1 GERENCIA FROM COLABORADOR_BASICOS WHERE COD_GERENCIA = CR.COD_GERENCIA) AS [Gerencia o Departamento],'".utf8_decode($datos[$d][titulo])."' AS Filtro FROM COLABORADOR_RANKING CR WHERE COD_CATEGORIA = $categoria $condiciones GROUP BY DESC_RANKING,COD_GERENCIA
) AS SourceTable PIVOT (max(SourceTable.CANTIDAD) FOR SourceTable.DESC_RANKING IN (".$columnas.")) AS PivotTable";  
}else
{
      $sql= $sql."select PivotTable.* from (
SELECT DESC_RANKING,SUM(CANT_RANKING) CANTIDAD ,(SELECT TOP 1 GERENCIA FROM COLABORADOR_BASICOS WHERE COD_GERENCIA = CR.COD_GERENCIA) AS [Gerencia o Departamento],(SELECT TOP 1 GERENCIA FROM COLABORADOR_BASICOS WHERE COD_GERENCIA = CR.COD_GERENCIA) AS Filtro FROM COLABORADOR_RANKING CR WHERE COD_CATEGORIA = $categoria $condiciones GROUP BY DESC_RANKING,COD_GERENCIA
) AS SourceTable PIVOT (max(SourceTable.CANTIDAD) FOR SourceTable.DESC_RANKING IN (".$columnas.")) AS PivotTable"; 
    
}
    
    
}
else
{
      if($datos[$d][titulo]!="")
    {
$sql= $sql."select PivotTable.* from (
SELECT DESC_RANKING,SUM(CANT_RANKING) CANTIDAD ,(SELECT TOP 1 GERENCIA FROM COLABORADOR_BASICOS WHERE COD_GERENCIA = CR.COD_GERENCIA) AS [Gerencia o Departamento],'".utf8_decode($datos[$d][titulo])."' AS Filtro FROM COLABORADOR_RANKING CR WHERE COD_CATEGORIA = $categoria $condiciones GROUP BY DESC_RANKING,COD_GERENCIA
) AS SourceTable PIVOT (max(SourceTable.CANTIDAD) FOR SourceTable.DESC_RANKING IN (".$columnas.")) AS PivotTable"." UNION ALL ";

    }
    else
    {
        $sql= $sql."select PivotTable.* from (
SELECT DESC_RANKING,SUM(CANT_RANKING) CANTIDAD ,(SELECT TOP 1 GERENCIA FROM COLABORADOR_BASICOS WHERE COD_GERENCIA = CR.COD_GERENCIA) AS [Gerencia o Departamento],(SELECT TOP 1 GERENCIA FROM COLABORADOR_BASICOS WHERE COD_GERENCIA = CR.COD_GERENCIA)  AS Filtro FROM COLABORADOR_RANKING CR WHERE COD_CATEGORIA = $categoria $condiciones GROUP BY DESC_RANKING,COD_GERENCIA
) AS SourceTable PIVOT (max(SourceTable.CANTIDAD) FOR SourceTable.DESC_RANKING IN (".$columnas.")) AS PivotTable"." UNION ALL ";
    }
    }
     
}

if(is_array($auxtg))
{
$tgerencias = "AND COD_DEPARTAMENTO_AREA IN (".implode(",",$auxtg).")";
error_log($tgerencias);    
}
else
{
    $tgerencias="";
}
if(is_array($auxtd))
{
$tdepartamento = "AND COD_DEPARTAMENTO_AREA IN (".implode(",",$auxtd).")";
error_log($tdepartamento);    
}
else
{
    $tdepartamento="";
}


$top = $this->rankingsTop($categoria,$top,$orden,$tdepartamento); 
error_log($sql);
return Array('datos'=>$this->newQuery($sql,true),'top'=>$top);
//return $sql;
}



}
?>