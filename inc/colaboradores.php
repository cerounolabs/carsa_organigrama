<?php
    /*PRODUCCION*/
//    include "../../inc/query.php";

    /*TESTING*/
    include "../../inctesting/query.php";

    session_start();
    
    switch ($_GET["listar"]) {
        case 'listar':
            {
                $SQL    = "SELECT COD_FUNC, NOMBRE_Y_APELLIDO AS nombre, CARGO AS cargo, FOTO_TARGET AS img, ANTIGUEDAD AS antiguedad, NRO_CEDULA AS documento, GERENCIA AS gerencia, SUPERIOR_INMEDIATO AS superior, FECHA_INGRESO AS FechaIngreso FROM COLABORADOR_BASICOS";
                $query  = new query();
                $datos  = $query->queryJson($SQL,"");
                $rows   = "";
                $cont   = 0;
                
                foreach ($datos as $key => $value) {
                    $nombre_busqueda= strtoupper(strtolower(utf8_encode($value['nombre'])));
                    $nombre_busqueda= str_replace('Á', 'A', $nombre_busqueda);
                    $nombre_busqueda= str_replace('É', 'E', $nombre_busqueda);
                    $nombre_busqueda= str_replace('Í', 'I', $nombre_busqueda);
                    $nombre_busqueda= str_replace('Ó', 'O', $nombre_busqueda);
                    $nombre_busqueda= str_replace('Ú', 'U', $nombre_busqueda);

                    $nombre_cargo   = strtoupper(strtolower(utf8_encode($value['cargo'])));
                    $nombre_cargo   = str_replace('Á', 'A', $nombre_cargo);
                    $nombre_cargo   = str_replace('É', 'E', $nombre_cargo);
                    $nombre_cargo   = str_replace('Í', 'I', $nombre_cargo);
                    $nombre_cargo   = str_replace('Ó', 'O', $nombre_cargo);
                    $nombre_cargo   = str_replace('Ú', 'U', $nombre_cargo);

                    $nombre_busqueda = $nombre_busqueda.' | '.$nombre_cargo;

                    $rows[] = Array("codigo"=>$value['COD_FUNC'], "nombre"=>utf8_encode($value['nombre']), "cargo"=>utf8_encode(ucwords(strtolower($value['cargo']))), "img"=>utf8_encode($value['img']), "antiguedad"=>utf8_encode($value['antiguedad']), "nomDocumento"=>utf8_encode($value['documento']), "nomGerencia"=>utf8_encode($value['gerencia']), "nomSuperior"=>utf8_encode($value['superior']), "nomFechaIngreso"=>utf8_encode($value['fechaIngreso']), "nombre_busqueda"=>$nombre_busqueda);
                }

                header('Content-Type: application/json');
                echo json_encode($rows);
            }

            break;

        case 'datoscolaborador':
            {
                $SQL    = "SELECT CARGO AS cargo, FOTO_TARGET AS img, ANTIGUEDAD AS antiguedad, NOMBRE_Y_APELLIDO AS nombre, NRO_CEDULA AS documento, GERENCIA AS gerencia, SUPERIOR_INMEDIATO AS superior, FECHA_INGRESO AS FechaIngreso FROM COLABORADOR_BASICOS WHERE COD_FUNC = ".$_GET['idcolaborador'];
                $query  = new query();
                $datos  = $query->queryJson($SQL,"");
                $rows   = "";
                $cont   = 0;
                
                foreach ($datos as $key => $value) {
                    $rows[] = Array("nombre"=>utf8_encode($value['nombre']), "img"=>utf8_encode($value['img']), "cargo"=>utf8_encode(ucwords(strtolower($value['cargo']))), "antiguedad"=>utf8_encode($value['antiguedad']), "nomDocumento"=>utf8_encode($value['documento']), "nomGerencia"=>utf8_encode($value['gerencia']), "nomSuperior"=>utf8_encode($value['superior']), "nomFechaIngreso"=>utf8_encode($value['fechaIngreso']));  
                }

                header('Content-Type: application/json');
                echo json_encode($rows);
            }

            break;
    }
?>