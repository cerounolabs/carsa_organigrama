<?php
    class log {
        public function registrar($usuario, $accion, $involucrado, $query=false){
            if($_SESSION['lastusuario'] == $usuario AND $_SESSION['lastaccion'] == $accion AND $_SESSION['lastinvolucrado'] == $involucrado) {
        
            } else {
                $conexion   = new conexionMySQL();
                $conn       = $conexion->conectar();
    
                if($query == true) {
                    $pre = $conn->query($involucrado);
                    
                    while($row = $pre->fetch_row()) {
                        $rows[]=$row[0];
                    }

                    $sql                        = "INSERT INTO `ORG_LOG`( `USUARIO`, `ACCION`, `INVOLUCRADO`) VALUES ('$usuario','$accion','".$rows[0]."')";
                    $result                     = $conn->query($sql); 
                    $_SESSION['lastusuario']    = $usuario;
                    $_SESSION['lastaccion']     = $accion;
                    $_SESSION['lastinvolucrado']= $involucrado;
                } else {
                    $sql                        = "INSERT INTO `ORG_LOG`( `USUARIO`, `ACCION`, `INVOLUCRADO`) VALUES ('$usuario','$accion','$involucrado')";
                    $conexion                   = new conexionMySQL();
                    $conn                       = $conexion->conectar();
                    $result                     = $conn->query($sql); 
                }
            }
        }

        public function consultar($fecha01, $fecha02) {  
            header('Content-Type: application/json');
            
            $fecha01    = date('Y-m-d 00:00:00', strtotime($fecha01));
            $fecha02    = date('Y-m-d 23:59:59', strtotime($fecha02));
            $sql        = "SELECT id, USUARIO, FECHA, ACCION, INVOLUCRADO FROM ORG_LOG WHERE FECHA >= '$fecha01' AND FECHA <= '$fecha02' ORDER BY FECHA DESC";
            $conexion   = new conexionMySQL();
            $conn       = $conexion->conectar();
            $result     = $conn->query($sql); 
    
            while($row = $result->fetch_row()) {
	            list($id, $usuario, $fecha, $accion, $involucrado)  = $row;
	            $rows[]                                             = Array("USUARIO"=>$usuario,"FECHA"=>date('Y-m-d', strtotime($fecha)),"ACCION"=>$accion,"INVOLUCRADO"=>$involucrado);
            }
    
            echo json_encode(Array("data"=>$rows));
        }
    }
?>