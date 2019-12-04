<?php
include "org_log.php";
class LoginFunciones
{

public function loginAdmin($usuario,$password)
{
$this->usuario =$usuario;
$this->password=$password;
$password = md5($password);
$sql="SELECT id,NOMBRE,USUARIO,SESSION,NIVEL FROM ORG_ADMIN WHERE USUARIO ='$usuario' AND PASSWORD = '$password'";
$consulta = new consulta();
$result = $consulta->queryArray($sql);
if($result[0]==null)
{
  $arrayName = array('result' =>'error','error'=>'Nombre de usuario y contraseña con coinciden');
  return json_encode($arrayName);
}
else
{
$_SESSION['id_usuario']=$result[0][0];
$_SESSION['nombre']=$result[0][1];
$_SESSION['usuario']=$result[0][2];
$_SESSION['session']=$result[0][3];
$_SESSION['nivel']=$result[0][4];
(new log())->registrar($_SESSION['nombre'],"Inicio de sesión ","administrador");

$_SESSION['admin']="true";
$sql2= "UPDATE ORG_ADMIN SET SESSION = NOW() WHERE id=".$_SESSION['id_usuario'];
$consulta = new consulta();
$consulta->query_simple($sql2);
$arrayName = array('result' =>'success');
return json_encode($arrayName);
}


}

public function loginUser($usuario,$password)
{
  $this->usuario =$usuario;
  $this->password=$password;
  $password = md5($password);
  $sql="SELECT id,NOMBRE_Y_APELLIDO,USUARIO,COD_FUNC,COD_GERENCIA,NIVEL_JERARQUIA,rankings FROM colaboradores WHERE USUARIO ='$usuario' AND PASSWORD ='$password'";
  $consulta = new consulta();
  $result = $consulta->queryArray($sql);
  if($result[0]==null)
  {

    $arrayName = array('result' =>'error','error'=>'Nombre de usuario y contraseña con coinciden');
    return json_encode($arrayName);

  }
  else
  {

    $_SESSION['id_usuario']=$result[0][0];
    $_SESSION['nombre']=$result[0][1];
    $_SESSION['usuario']=$result[0][2];
    $_SESSION['COD_FUNC']=$result[0][3];
	$_SESSION['rankings']=$result[0][6];
    (new log())->registrar($_SESSION['nombre'],"Inicio de sesión ","organigrama");
//    $arrayPermitidos[]=$result[0][3];
    $sql2="SELECT COD_FUNC,NOMBRE_Y_APELLIDO,NIVEL_JERARQUIA FROM colaboradores WHERE NIVEL_JERARQUIA > ".$result[0][5]." AND COD_GERENCIA = ".$result[0][4]." ORDER BY NIVEL_JERARQUIA";
   /* error_log($sql2);
    $resultado = $consulta->queryArray($sql2);
    for ($i=0; $i < count($resultado) ; $i++)
    {
    $arrayPermitidos[]=$resultado[$i][0];
    }
	
	*/
	
    $sql3="SELECT COD_COLABORADOR FROM ORG_RELACIONES WHERE COD_USUARIO = ".$result[0][0];
	$sql4="SELECT CONCAT(A.ID_PERMISO,R.COD_COLABORADOR) as permiso FROM ORG_ASIGNACIONES A INNER JOIN ORG_RELACIONES R ON R.ID = A.ID_ASIGNACION WHERE R.COD_USUARIO =".$result[0][0];
	
	
	$resultadopermisos = $consulta->queryArray($sql4);
	for ($i=0; $i < count($resultadopermisos) ; $i++)
    {
      $arraypermisos[]=$resultadopermisos[$i][0];
    }
	
    $resultado2 = $consulta->queryArray($sql3);
    for ($i=0; $i < count($resultado2) ; $i++)
    {
      $arrayPermitidos[]=$resultado2[$i][0];
    }
	

    $_SESSION['permitidos']= $arrayPermitidos;
	$_SESSION['permisos']=$arraypermisos;



    $arrayName = array('result' =>'success');
    return json_encode($arrayName);



  }


}

public function ChangePasswordAdmin($usuario,$password)
{
  $this->usuario=$usuario;
  $this->password=$password;
  $password=md5($password);
  $sql="UPDATE ORG_ADMIN SET PASSWORD = '$password'  WHERE id=".$usuario;
  $consulta = new consulta();
  $consulta->query_simple($sql);

    $arrayName = array('result' =>'success');
    return json_encode($arrayName);


}

public function ChangePasswordUser($usuario,$password)
{
  
   (new log())->registrar($_SESSION['nombre'],"Restauracion de contraseña","SELECT  NOMBRE_Y_APELLIDO  FROM `colaboradores` WHERE cod_func= $usuario limit 1",true);
  
  $this->usuario=$usuario;
  $this->password=$password;
  $password=md5($password);
  $sql="UPDATE colaboradores SET PASSWORD = '$password'  WHERE COD_FUNC=".$usuario;
  $sql2="UPDATE colaboradores SET resetpassword = true  WHERE COD_FUNC=".$usuario;
  $consulta = new consulta();
  $consulta->query_simple($sql);
  $consulta->query_simple($sql2);
  $arrayName = array('result' =>'success');
  return json_encode($arrayName);


}



public function ResetPasswordUser($usuario)
{
  
     (new log())->registrar($_SESSION['nombre'],"Restauracion de contraseña","SELECT  NOMBRE_Y_APELLIDO  FROM `colaboradores` WHERE cod_func= $usuario limit 1",true);

  
  $this->usuario=$usuario;
  $sql1="SELECT USUARIO FROM colaboradores where COD_FUNC=$usuario";
  $consulta = new consulta();
  $pass= $consulta->queryArray($sql1);
  
  $pass[0][0] =  str_replace(" ","",$pass[0][0]);
  error_log(">>>>>>>>>>>>>>>>>>>".$pass[0][0]."<<<<<<<<<<<<<<<<<<");
  $sql2= "UPDATE colaboradores SET PASSWORD = '".md5($pass[0][0])."'  WHERE COD_FUNC=$usuario";
  $sql3="UPDATE colaboradores SET resetpassword = false  WHERE COD_FUNC=".$usuario;
  $consulta->query_simple($sql2);
  $consulta->query_simple($sql3);
  error_log($sql2);
  $arrayName = array('result' =>'success');
  return json_encode($arrayName);
}

public function AllowRanking($usuario)
{
     (new log())->registrar($_SESSION['nombre'],"Acceso a rankings otorgado","SELECT  NOMBRE_Y_APELLIDO  FROM `colaboradores` WHERE cod_func= $usuario limit 1",true);

  $consulta = new consulta();
  $this->usuario=$usuario;
  $sql3="UPDATE colaboradores SET rankings = 1  WHERE COD_FUNC=".$usuario;
  $consulta->query_simple($sql3);
  error_log($sql2);
  $arrayName = array('result' =>'success');
  return json_encode($arrayName);
}
public function DeniedRanking($usuario)
{
   $this->usuario=$usuario;
     (new log())->registrar($_SESSION['nombre'],"Acceso a rankings eliminado","SELECT  NOMBRE_Y_APELLIDO  FROM `colaboradores` WHERE cod_func= $usuario limit 1",true);

 $consulta = new consulta();
  $sql3="UPDATE colaboradores SET rankings = 0  WHERE COD_FUNC=".$usuario;
  $consulta->query_simple($sql3);
  error_log($sql2);
  $arrayName = array('result' =>'success');
  return json_encode($arrayName);
}


public function logOut()
{

session_destroy();
$arrayName = array('result' =>'success');
return json_encode($arrayName);
}




}

/**
 *
 */
class switchLogin 
{

public function switchF($value="")
{
  switch ($value)
  {
    case 'loginAdmin':
    {
      $datos =  $_POST['data'];
      $class = new LoginFunciones();
      $result = $class ->loginAdmin($datos[0],$datos[1]);
      header('Content-type: application/json');
      echo $result;
    }
      break;

    case 'loginUser':
    {
      $datos =  $_POST['data'];
      $class = new LoginFunciones();
      $result = $class ->loginUser($datos[0],$datos[1]);
      header('Content-type: application/json');
      echo $result;
    }
      break;

    case 'ChangePasswordAdmin':
    {
      $datos =  $_POST['data'];
      $class = new LoginFunciones();
      $result = $class ->ChangePasswordAdmin($_SESSION['id_usuario'],$datos[0]);
      header('Content-type: application/json');
      echo $result;
    }
      break;
    case 'ChangePasswordUser':
    {
      $datos =  $_POST['data'];
      $class = new LoginFunciones();
      $result = $class ->ChangePasswordUser($_SESSION['COD_FUNC'],$datos[0]);
      header('Content-type: application/json');
      echo $result;
    }
      break;

	        case 'AllowRanking':
      {
        $datos =  $_POST['data'];
        $class = new LoginFunciones();
        $result = $class ->AllowRanking($datos[0]);
        header('Content-type: application/json');
        echo $result;
      }
        break;
		
		case 'DeniedRanking':
      {
        $datos =  $_POST['data'];
        $class = new LoginFunciones();
        $result = $class ->DeniedRanking($datos[0]);
        header('Content-type: application/json');
        echo $result;
      }
        break;

      case 'ResetPasswordUser':
      {
        $datos =  $_POST['data'];
        $class = new LoginFunciones();
        $result = $class ->ResetPasswordUser($datos[0]);
        header('Content-type: application/json');
        echo $result;
      }
        break;

    case 'logOut':
    {
      $class = new LoginFunciones();
      $result=$class->logOut();
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
