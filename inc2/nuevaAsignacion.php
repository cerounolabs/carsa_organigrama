 <?php
include "conexionL.php";
include "queryMySQL.php";
include "org_log.php";
session_start();
class contador
{
public function contar($func='',$usuario,$accion)
{
  $conexion = new  conexionMySQL();
  $connMy = $conexion -> conectar();


$class = new conexion ();
$conn=$class->conectar();
if($accion =="agregar")
{
  $consulta = new consulta();
  $sqlXL="INSERT INTO `ORG_RELACIONES`(`COD_USUARIO`, `FECHA`, `COD_COLABORADOR`, `COD_ADMIN`) VALUES ($usuario,now(),".$func.",1)";
  $usuario = $consulta->queryArray("SELECT NOMBRE_Y_APELLIDO FROM  `colaboradores` WHERE id =".$usuario);
  $colaborador = $consulta->queryArray("SELECT NOMBRE_Y_APELLIDO FROM  `colaboradores` WHERE COD_FUNC =".$func);

(new log())->registrar($_SESSION['nombre'],"Acceso al colaborador ".$colaborador[0][0]." otorgado",$usuario[0][0],false);
  

}
else
{

$sqlXL="DELETE FROM ORG_RELACIONES WHERE  COD_USUARIO =  $usuario  AND COD_COLABORADOR = ".$func;
$consulta = new consulta();
$usuario = $consulta->queryArray("SELECT NOMBRE_Y_APELLIDO FROM  `colaboradores` WHERE id =".$usuario);
$colaborador = $consulta->queryArray("SELECT NOMBRE_Y_APELLIDO FROM  `colaboradores` WHERE COD_FUNC =".$func);

(new log())->registrar($_SESSION['nombre'],"Acceso al colaborador ".$colaborador[0][0]." eliminado",$usuario[0][0],false);
  

}

error_log(">>>>>>>>>>>>>>".$sqlXL);
    $resultMY = $connMy->query($sqlXL);



$conta = 0;
$sql5="SELECT CB1.COD_FUNC,CB1.COD_SUPERIOR_INMEDIATO ,(SELECT COUNT(CB2.COD_FUNC) FROM COLABORADOR_BASICOS CB2 WHERE CB2.COD_SUPERIOR_INMEDIATO = CB1.COD_FUNC ) AS HIJOS FROM COLABORADOR_BASICOS CB1 WHERE CB1.COD_FUNC=$func  ORDER BY CB1.COD_FUNC";
$query5 = mssql_query($sql5);
while( $row5 = mssql_fetch_assoc($query5))
{
	
error_log('>>>>>>>>>>>'. $row5['COD_FUNC']);	
$conta = $conta +1;
$sql="SELECT CB1.COD_FUNC,CB1.COD_SUPERIOR_INMEDIATO ,(SELECT COUNT(CB2.COD_FUNC) FROM COLABORADOR_BASICOS CB2 WHERE CB2.COD_SUPERIOR_INMEDIATO = CB1.COD_FUNC ) AS HIJOS FROM COLABORADOR_BASICOS CB1
where CB1.COD_FUNC = ".$row5['COD_FUNC'];
$query =  mssql_query( $sql );
if( $query === false)
{
    //die( print_r( sqlsrv_errors(), true) );
}
else
{
while( $row =   mssql_fetch_assoc($query))
{
$result[]=$row;
}
$hijos=null;
$a = "null";
while ($a == "null")
{

$aux = array();

  $cont=0;
for ($i=0; $i < COUNT($result) ; $i++)
{
  $sql2="SELECT CB1.COD_FUNC,CB1.COD_SUPERIOR_INMEDIATO ,(SELECT COUNT(CB2.COD_FUNC) FROM COLABORADOR_BASICOS CB2 WHERE CB2.COD_SUPERIOR_INMEDIATO = CB1.COD_FUNC ) AS HIJOS,CB1.NOMBRE_Y_APELLIDO FROM COLABORADOR_BASICOS CB1 where CB1.COD_SUPERIOR_INMEDIATO =".$result[$i]['COD_FUNC'];
  $query2= mssql_query( $sql2);
  while( $row = mssql_fetch_assoc($query2))
  {
  $hijos[]=$row['COD_FUNC'];
  $aux[$cont]['COD_FUNC']=$row['COD_FUNC'];
  $cont=$cont+1;
  
  
  
    if($accion =="agregar")
  {

    $sqlXL="INSERT INTO `ORG_RELACIONES`(`COD_USUARIO`, `FECHA`, `COD_COLABORADOR`, `COD_ADMIN`,`NOMBRE_Y_APELLIDO`) VALUES ($usuario,now(),".$row['COD_FUNC'].",1)";


(new log())->registrar($_SESSION['nombre'],"Acceso al colaborador ".$row['NOMBRE_Y_APELLIDO']." otorgado",$usuario[0][0],false);
  }
  else
  {

    $sqlXL="DELETE FROM ORG_RELACIONES WHERE  COD_USUARIO =  $usuario AND COD_COLABORADOR = ".$row['COD_FUNC'];

(new log())->registrar($_SESSION['nombre'],"Acceso al colaborador ".$row['NOMBRE_Y_APELLIDO']." eliminado",$usuario[0][0],false);

  }
    $resultMY = $connMy->query($sqlXL);

  
  
  
  }
}
if (empty($aux))
{
$a=null;
}
else
{
$result=array();
$result = $aux;
}
}
}
$row5['totalhijos']=COUNT($hijos);
$final[]=$row5;
if($row5['COD_SUPERIOR_INMEDIATO']==NULL){$row5['COD_SUPERIOR_INMEDIATO']="NULL";}


//$connMy->query($sqlMy2);

}
header('Content-Type: application/json');
$arrayName = array('result' =>'success','moreData'=>null );
echo json_encode($arrayName );
}
}
$datos =$_POST['data'];
$class = new contador();
$class->contar($datos[0],$datos[1],$datos[2]);



 ?>
