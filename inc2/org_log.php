<?php


class log 
{

public function registrar($usuario,$accion,$involucrado,$query=false)
{
    
    if($_SESSION['lastusuario']==$usuario and $_SESSION['lastaccion']==$accion and $_SESSION['lastinvolucrado']==$involucrado)
    {
        
    }
    else
    {
    $conexion = new conexionMySQL();
    $conn = $conexion->conectar();
    if($query == true)
    {

        $pre = $conn->query($involucrado);
        while($row = $pre->fetch_row())
        {
            $rows[]=$row[0];
        }  

    $sql="INSERT INTO `ORG_LOG`( `USUARIO`, `ACCION`, `INVOLUCRADO`) VALUES ('$usuario','$accion','".$rows[0]."')";
    $result = $conn->query($sql); 
    
    $_SESSION['lastusuario']=$usuario;
    $_SESSION['lastaccion']=$accion;
    $_SESSION['lastinvolucrado']=$involucrado;
    }
    
    
    
    else
    {
        $sql="INSERT INTO `ORG_LOG`( `USUARIO`, `ACCION`, `INVOLUCRADO`) VALUES ('$usuario','$accion','$involucrado')";
        $conexion = new conexionMySQL();
        $conn = $conexion->conectar();
        $result = $conn->query($sql); 
    }
    }

}

public function consultar()
{  
    header('Content-Type: application/json');
    $sql="SELECT `id`, `USUARIO`, `FECHA`, `ACCION`, `INVOLUCRADO` FROM `ORG_LOG` ORDER BY FECHA DESC";
    $conexion = new conexionMySQL();
    $conn = $conexion->conectar();
    $result = $conn->query($sql); 
    while($row = $result->fetch_row())
        {

	list($id,$usuario,$fecha,$accion,$involucrado)=$row;
	
	$rows[]=   Array("USUARIO"=>$usuario,"FECHA"=>$fecha,"ACCION"=>$accion,"INVOLUCRADO"=>$involucrado);
        }
   echo json_encode(Array("data"=>$rows));
}


}




?>