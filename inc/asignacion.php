<?php

class asignacion
{


public function ListarColaboradores($value='',$usuario)
{
$sql="SELECT id, COD_FUNC, NOMBRE_Y_APELLIDO,CARGO ,'false' as estado FROM colaboradores WHERE COD_SUPERIOR_INMEDIATO = $value AND COD_FUNC NOT IN( SELECT COD_COLABORADOR FROM ORG_RELACIONES where COD_USUARIO = $usuario AND COD_SUPERIOR_INMEDIATO=$value) UNION
    SELECT c.id,c.COD_FUNC,c.NOMBRE_Y_APELLIDO,c.CARGO , 'true' as estado FROM colaboradores c inner join ORG_RELACIONES r on r.COD_COLABORADOR = c.COD_FUNC WHERE r.COD_USUARIO = $usuario AND c.COD_SUPERIOR_INMEDIATO =$value  ORDER BY NOMBRE_Y_APELLIDO";
$consulta = new consulta();
$result = $consulta->query_json($sql);
$arrayName = array('result' =>'success','moreData'=>json_decode($result));
return json_encode($arrayName);
}

public function ListarUsuarios()
{

$sql="SELECT USUARIO,COD_FUNC,id,rankings from colaboradores ORDER BY USUARIO";
$consulta = new consulta();
$result = $consulta->query_json($sql);
$arrayName = array('result' =>'success','moreData'=>json_decode($result));
return json_encode($arrayName);

}

public function NuevaRelacion($usuario,$colaborador,$admin)
{

$usuario = $consulta->queryArray("SELECT NOMBRE_Y_APELLIDO FROM  `colaboradores` WHERE id =".$usuario);
$colaborador = $consulta->queryArray("SELECT NOMBRE_Y_APELLIDO FROM  `colaboradores` WHERE COD_FUNC =".$colaborador);

(new log())->registrar($_SESSION['nombre'],"Acceso al colaborador ".$colaborador[0][0]." otorgado",$usuario[0][0],false);
  
$sql="INSERT INTO ORG_RELACIONES (COD_USUARIO,FECHA,COD_COLABORADOR,COD_ADMIN)VALUES($usuario,now(),$colaborador,$admin)";
$consulta = new consulta();
$result=$consulta->query_simple($sql);
$arrayName = array('result' =>'success');
return json_encode($arrayName);
}

public function EliminarRelacion($usuario,$colaborador)
{
  
$usuario = $consulta->queryArray("SELECT NOMBRE_Y_APELLIDO FROM  `colaboradores` WHERE id =".$usuario);
$colaborador = $consulta->queryArray("SELECT NOMBRE_Y_APELLIDO FROM  `colaboradores` WHERE COD_FUNC =".$colaborador);

(new log())->registrar($_SESSION['nombre'],"Acceso al colaborador ".$colaborador[0][0]." eliminado",$usuario[0][0],false);
  
$sql="DELETE FROM ORG_RELACIONES WHERE COD_USUARIO = $usuario AND COD_COLABORADOR = $colaborador";
$consulta = new consulta();
$result=$consulta->query_simple($sql);
$arrayName = array('result' =>'success');
return json_encode($arrayName);
}

public function NuevaRelacionGrupo($gerencia,$usuario,$admin)
{
$sql="SELECT COD_FUNC FROM colaboradores WHERE COD_GERENCIA = $gerencia ";
$consulta = new consulta();
$result=$consulta->queryArray($sql);
for ($i=0; $i < count($result) ; $i++)
{
$sql="INSERT INTO ORG_RELACIONES (COD_USUARIO,FECHA,COD_COLABORADOR,COD_ADMIN)VALUES($usuario,now(),".$result[$i][0].",$admin)";
$result2=$consulta->query_simple($sql);
}
$arrayName = array('result' =>'success');
return json_encode($arrayName);
}

public function EliminarRelacionGrupo($gerencia,$usuario)
{
$sql="SELECT COD_FUNC FROM colaboradores WHERE COD_GERENCIA = $gerencia ";
$consulta = new consulta();

$result=$consulta->queryArray($sql);




for ($i=0; $i < count($result) ; $i++)
{
$sql="DELETE FROM ORG_RELACIONES WHERE COD_USUARIO = $usuario AND COD_COLABORADOR = ".$result[$i][0];

$result2=$consulta->query_simple($sql);
}
$arrayName = array('result' =>'success');
return json_encode($arrayName);
}

public function selectPermisos($value='')
{
$sql="SELECT id,nombre from ORG_PERMISOS";
$consulta = new consulta();
$result = $consulta->query_assoc($sql);
$arrayName = array('result' =>'success','moreData'=>$result);
return json_encode($arrayName);
}

public function selectRaiz($usuario)
{
$sql ="SELECT id, COD_FUNC, NOMBRE_Y_APELLIDO,CARGO ,'false' as estado FROM colaboradores WHERE COD_CARGO=1146 AND COD_FUNC NOT IN( SELECT COD_COLABORADOR FROM ORG_RELACIONES where COD_USUARIO = $usuario AND COD_CARGO=1146) UNION SELECT c.id,c.COD_FUNC,c.NOMBRE_Y_APELLIDO,c.CARGO , 'true' as estado FROM colaboradores c inner join ORG_RELACIONES r on r.COD_COLABORADOR = c.COD_FUNC WHERE r.COD_USUARIO =$usuario AND c.COD_CARGO=1146 ORDER BY NOMBRE_Y_APELLIDO";
$consulta = new consulta();
$result = $consulta->query_assoc($sql);
$arrayName = array('result' =>'success','moreData'=>$result);
return json_encode($arrayName);
}


public function selectAsignacion($usuario='',$colaborador)
{

$sql="SELECT id,ID_ASIGNACION,ID_PERMISO FROM ORG_ASIGNACIONES where ID_ASIGNACION=(SELECT id FROM ORG_RELACIONES where cod_usuario = $usuario and cod_colaborador =$colaborador)";
$consulta = new consulta();
$result = $consulta->query_assoc($sql);
$arrayName = array('result' =>'success','moreData'=>$result);
return json_encode($arrayName);

}


public function deleteAsignacion($usuario,$colaborador,$permiso)
{
  $consulta = new consulta();
  
  $usuariol = $consulta->queryArray("SELECT NOMBRE_Y_APELLIDO FROM  `colaboradores` WHERE id =".$usuario);
$colaboradorl = $consulta->queryArray("SELECT NOMBRE_Y_APELLIDO FROM  `colaboradores` WHERE COD_FUNC =".$colaborador);
$permisol =$consulta->queryArray("SELECT NOMBRE FROM `ORG_PERMISOS` WHERE ID=$permiso");

(new log())->registrar($_SESSION['nombre'],"Permiso de ".$permisol[0][0]." del colaborador ".$colaboradorl[0][0]." eliminado",$usuariol[0][0],false);

  
  $sql1="SELECT id FROM ORG_RELACIONES WHERE cod_usuario=$usuario and cod_colaborador=$colaborador";
  $result = $consulta->query_assoc($sql1);

  if($result[0]['id']!= null)
  {
  $sql2="DELETE FROM `ORG_ASIGNACIONES` WHERE ID_ASIGNACION= ".$result[0]['id']." AND ID_PERMISO=  $permiso";
  error_log($sql2);
  $result2=$consulta->query_simple($sql2);
  $arrayName = array('result' =>'success');

  }
  else
  {
  $arrayName = array('result'=>'error','error'=>'No existe relacion');
  }

  return json_encode($arrayName);
}

public function insertAsignacion($usuario,$colaborador,$permiso)
{
$consulta = new consulta();


$usuariol = $consulta->queryArray("SELECT NOMBRE_Y_APELLIDO FROM  `colaboradores` WHERE id =".$usuario);
$colaboradorl = $consulta->queryArray("SELECT NOMBRE_Y_APELLIDO FROM  `colaboradores` WHERE COD_FUNC =".$colaborador);
$permisol =$consulta->queryArray("SELECT NOMBRE FROM `ORG_PERMISOS` WHERE ID=$permiso");

(new log())->registrar($_SESSION['nombre'],"Permiso de ".$permisol[0][0]." del colaborador ".$colaboradorl[0][0]." otorgado",$usuariol[0][0],false);


$sql1="SELECT id FROM ORG_RELACIONES WHERE cod_usuario=$usuario and cod_colaborador=$colaborador";
$result = $consulta->query_assoc($sql1);
if($result[0]['id']!= null)
{
$sql2="INSERT INTO `ORG_ASIGNACIONES`(ID_ASIGNACION, ID_PERMISO) VALUES (".$result[0]['id'].",$permiso)";
error_log($sql2);
$result2=$consulta->query_simple($sql2);
$arrayName = array('result' =>'success');

}
else
{
$arrayName = array('result'=>'error','error'=>'No existe relacion');
}

return json_encode($arrayName);
}
public function ListarGerencias($value='')
{

$sql ="select COD_GERENCIA, GERENCIA,ORDEN_GERENCIA from colaboradores where COD_SUPERIOR_INMEDIATO = (SELECT COD_FUNC FROM colaboradores WHERE COD_CARGO = 1146) OR COD_GERENCIA = 31 GROUP BY GERENCIA,COD_GERENCIA,ORDEN_GERENCIA ORDER BY ORDEN_GERENCIA";
$consulta = new consulta();
$result = $consulta->query_assoc($sql);

for ($i=0; $i < count($result) ; $i++)
{

$sql2 = "SELECT COUNT(COD_FUNC),'total' FROM colaboradores WHERE COD_GERENCIA =".$result[$i]['COD_GERENCIA']."  UNION SELECT COUNT(r.COD_USUARIO),'asignado' FROM ORG_RELACIONES r inner join colaboradores c on c.COD_FUNC = r.COD_COLABORADOR WHERE c.COD_GERENCIA = ".$result[$i]['COD_GERENCIA']."  AND  r.COD_USUARIO =".$value;
$compar  = $consulta->queryArray($sql2);
if($compar[0][0]== $compar[1][0])
{
  $result[$i]['estado']="true";
}
else
{
  $result[$i]['estado']="false";
}



}

$arrayName = array('result' =>'success','moreData'=>$result);

return json_encode($arrayName);

}




public function marcartodos($usuario='',$admin)
{
$sql="SELECT COD_FUNC FROM colaboradores";
$consulta = new consulta();
$result = $consulta->query_assoc($sql);
for ($i=0; $i < COUNT($result) ; $i++)
{
  $sql="INSERT INTO ORG_RELACIONES (COD_USUARIO,FECHA,COD_COLABORADOR,COD_ADMIN)VALUES($usuario,now(),".$result[$i]['COD_FUNC'].",$admin)";
  $consulta->query_simple($sql);
}
$arrayName = array('result' =>'success' , 'moreData'=>COUNT($result));
return json_encode($arrayName);
}


public function desmarcartodos($usuario='')
{
$sql="DELETE FROM ORG_RELACIONES WHERE COD_USUARIO=$usuario";
$consulta = new consulta();
$result = $consulta->query_simple($sql);
$arrayName = array('result' =>'success' , 'moreData'=>null);
return json_encode($arrayName);
}



}


class switchAsignacion
{

public function switchF($value='')
{

switch ($value) {
    case 'ListarColaboradores':
      {
        $datos = $_POST['data'];
        $class= new asignacion();
        $result= $class->ListarColaboradores($datos[0],$datos[1]);
        echo $result;
      }
      break;

    case 'ListarUsuarios':
      {
        $class= new asignacion();
        $result= $class->ListarUsuarios();
        echo $result;
      }
      break;
    case 'NuevaRelacion':
      {
        $datos = $_POST['data'];
        $class= new asignacion();
        $result= $class->NuevaRelacion($datos[1],$datos[0],$_SESSION['id_usuario']);
        header('Content-type: application/json');
        echo $result;
      }
      break;

    case 'EliminarRelacion':
        {
        $datos = $_POST['data'];
        $class= new asignacion();
        $result= $class->EliminarRelacion($datos[1],$datos[0]);
        header('Content-type: application/json');
        echo $result;
        }
        break;

    case 'NuevaRelacionGrupo':
        {
        $datos = $_POST['data'];
        $class= new asignacion();
        $result= $class->NuevaRelacionGrupo($datos[0],$datos[1],$_SESSION['id_usuario']);
        header('Content-type: application/json');
        echo $result;
        }
        break;

    case 'EliminarRelacionGrupo':
        {
        $datos = $_POST['data'];
        $class= new asignacion();
        $result= $class->EliminarRelacionGrupo($datos[0],$datos[1]);
        header('Content-type: application/json');
        echo $result;
        }
        break;

    case 'ListarGerencias':
        {

        $class= new asignacion();
        $result= $class->ListarGerencias($_POST['usuario']);
        header('Content-type: application/json');

		      error_log($result);
        echo $result;
        }
        break;
    case 'selectRaiz':
        {
        $class= new asignacion();
        $result= $class->selectRaiz($_POST['usuario']);
        header('Content-type: application/json');
        echo $result;
        }
        break;

    case 'selectPermisos':
        {
        $class= new asignacion();
        $result= $class->selectPermisos();
        header('Content-type: application/json');
        echo $result;
        }
        break;

    case 'selectAsignacion':
        {
        $datos = $_POST['data'];
        $class= new asignacion();
        $result= $class->selectAsignacion($datos[1],$datos[0]);
        header('Content-type: application/json');
        echo $result;
        }
        break;


    case 'insertAsignacion':
        {
        $datos = $_POST['data'];
        $class= new asignacion();
        $result= $class->insertAsignacion($datos[0],$datos[1],$datos[2]);
        header('Content-type: application/json');
        echo $result;
        }
        break;


    case 'deleteAsignacion':
        {
        $datos = $_POST['data'];
        $class= new asignacion();
        $result= $class->deleteAsignacion($datos[0],$datos[1],$datos[2]);
        header('Content-type: application/json');
        echo $result;
        }
        break;
		
		

    case 'marcartodos':
        {
        $datos = $_POST['data'];
        $class= new asignacion();
        $result= $class->marcartodos($datos[0],$_SESSION['id_usuario']);
        header('Content-type: application/json');
        echo $result;
        }
        break;

    case 'desmarcartodos':
        {
        $datos = $_POST['data'];
        $class= new asignacion();
        $result= $class->desmarcartodos($datos[0]);
        header('Content-type: application/json');
        echo $result;
        }
        break;
		
		
		

  default:
    # code...
    break;
}



}

}







 ?>
