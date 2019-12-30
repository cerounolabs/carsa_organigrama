<?php
	include 'conexionMySQL.php';
	
	$return_arr = array();
	$conexion = new  conexionMySQL();
	$conn = $conexion ->conectar();
	
	$cod_usuario = $_POST["c"];
	$id_opcion = $_POST["o"];
	
	if(!ctype_digit($cod_usuario) || !ctype_digit($id_opcion)){
		exit("ERROR");
	}
	
	
	if($id_opcion == 0 && $cod_usuario > 0){
		
		$go = "select id_opcion from opciones_relaciones where cod_usuario = '$cod_usuario'";
		$gpr = $conn->query($go);
		while($row = $gpr->fetch_array(MYSQLI_NUM)){
			$row_array['id_opcion'] = $row[0];;
			array_push($return_arr,$row_array);
		}
		
	}else {

	
	$go = "select id from opciones_relaciones where cod_usuario = '$cod_usuario' and id_opcion = '$id_opcion'";
	$gpr = $conn->query($go);
	while($row = $gpr->fetch_array(MYSQLI_NUM)){
		$id_relacion = $row[0];
	}
		
		if(isset($id_relacion) && $id_relacion > 0 ){
			
			$dr = "DELETE FROM opciones_relaciones WHERE cod_usuario = '$cod_usuario' and id_opcion = '$id_opcion'";
			$gpr = $conn->query($dr);
			$check_delete = $conn->affected_rows;
			
			if($check_delete > 0){
				$row_array['status'] = "success";
				$row_array['mensaje'] = "Se removio la Opción";
			}else {
				$row_array['status'] = "error";
				$row_array['mensaje'] = "No se pudo remover la Opción";
			}
			
			array_push($return_arr,$row_array);
			
		}else {
			
			$ir = "INSERT INTO opciones_relaciones (cod_usuario, id_opcion) VALUES ('$cod_usuario', '$id_opcion');";
			
			$irr = $conn->query($ir);
			$check_insert = $conn->affected_rows;
				
				if(isset($check_insert) && $check_insert > 0){
					$row_array['status'] = "success";
					$row_array['mensaje'] = "Se añadio la Opción";
				}else {
					$row_array['status'] = "error";
					$row_array['mensaje'] = "No se pudo añadir la Opción";
				}
				array_push($return_arr,$row_array);
			
			
			
		}
		
	}
	
	
	
	
	
	echo json_encode($return_arr);
?>