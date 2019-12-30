<?php
	include "conexionL.php";
	include "conexionMySQL.php";

	class query {
		public function queryJson($sql="",$funcion) {
			$cp1252_map = array(
				"\xc2\x80" => "\xe2\x82\xac", /* EURO SIGN */
				"\xc2\x82" => "\xe2\x80\x9a", /* SINGLE LOW-9 QUOTATION MARK */
				"\xc2\x83" => "\xc6\x92",     /* LATIN SMALL LETTER F WITH HOOK */
				"\xc2\x84" => "\xe2\x80\x9e", /* DOUBLE LOW-9 QUOTATION MARK */
				"\xc2\x85" => "\xe2\x80\xa6", /* HORIZONTAL ELLIPSIS */
				"\xc2\x86" => "\xe2\x80\xa0", /* DAGGER */
				"\xc2\x87" => "\xe2\x80\xa1", /* DOUBLE DAGGER */
				"\xc2\x88" => "\xcb\x86",     /* MODIFIER LETTER CIRCUMFLEX ACCENT */
				"\xc2\x89" => "\xe2\x80\xb0", /* PER MILLE SIGN */
				"\xc2\x8a" => "\xc5\xa0",     /* LATIN CAPITAL LETTER S WITH CARON */
				"\xc2\x8b" => "\xe2\x80\xb9", /* SINGLE LEFT-POINTING ANGLE QUOTATION */
				"\xc2\x8c" => "\xc5\x92",     /* LATIN CAPITAL LIGATURE OE */
				"\xc2\x8e" => "\xc5\xbd",     /* LATIN CAPITAL LETTER Z WITH CARON */
				"\xc2\x91" => "\xe2\x80\x98", /* LEFT SINGLE QUOTATION MARK */
				"\xc2\x92" => "\xe2\x80\x99", /* RIGHT SINGLE QUOTATION MARK */
				"\xc2\x93" => "\xe2\x80\x9c", /* LEFT DOUBLE QUOTATION MARK */
				"\xc2\x94" => "\xe2\x80\x9d", /* RIGHT DOUBLE QUOTATION MARK */
				"\xc2\x95" => "\xe2\x80\xa2", /* BULLET */
				"\xc2\x96" => "\xe2\x80\x93", /* EN DASH */
				"\xc2\x97" => "\xe2\x80\x94", /* EM DASH */
				"\xc2\x98" => "\xcb\x9c",     /* SMALL TILDE */
				"\xc2\x99" => "\xe2\x84\xa2", /* TRADE MARK SIGN */
				"\xc2\x9a" => "\xc5\xa1",     /* LATIN SMALL LETTER S WITH CARON */
				"\xc2\x9b" => "\xe2\x80\xba", /* SINGLE RIGHT-POINTING ANGLE QUOTATION*/
				"\xc2\x9c" => "\xc5\x93",     /* LATIN SMALL LIGATURE OE */
				"\xc2\x9e" => "\xc5\xbe",     /* LATIN SMALL LETTER Z WITH CARON */
				"\xc2\x9f" => "\xc5\xb8"      /* LATIN CAPITAL LETTER Y WITH DIAERESIS*/
			);
		
			$class = new conexion();
			$class->conectar();
			$query = mssql_query($sql);

			if (!mssql_num_rows($query)) {
    			error_log( 'No records found');
    			mssql_free_result($query);
			} else {
				$result = "";
				$query  = mssql_query($sql);
				
				while ($row = mssql_fetch_assoc($query)) {
					if($funcion == "datos") {
						$sqlMy		= "SELECT * FROM org_hijos WHERE COD_FUNC = ".$row['id'];
						$conexion 	= new conexionMySQL();
						$connMy 	= $conexion -> conectar();
						$resultMY 	= $connMy->query($sqlMy);
						$resultMY 	= $resultMY->fetch_array(MYSQLI_NUM);
						$name 		= explode(";",$row['name']);
		
						if(in_array($row['id'], $_SESSION['permitidos'])){
							if($resultMY[3] == "0") {
								$row['name'] = $name[0].";".$name[1].";".$name[2].";".$name[3].";".$name[4].";".$name[5].";".$name[6].";".$name[7];
							} else {
								$row['name']=$name[0]."(".$resultMY[3].");".$name[1].";".$name[2].";".$name[3].";".$name[4].";".$name[5].";".$name[6].";".$name[7];
							}

							$row['id'] 			= utf8_encode($row['id']);
							$row['name'] 		= utf8_encode($row['name']);
							$row['title'] 		= utf8_encode($row['title']);
							$row['children']	= null;
						} else {
							$row				= null;
						}
					}

					if($funcion == "childrens") {
						$sqlMy		= "SELECT * FROM org_hijos WHERE COD_FUNC = ".$row['id'];
						$conexion 	= new conexionMySQL();
						$connMy 	= $conexion -> conectar();
						$resultMY 	= $connMy->query($sqlMy);
						$resultMY 	= $resultMY->fetch_array(MYSQLI_NUM);
						$name 		= explode(";",$row['name']);
	
						if(in_array($row['id'], $_SESSION['permitidos'])) {
							if($resultMY[3] == "0") {
								$row['name']=$name[0].";".$name[1].";".$name[2].";".$name[3].";".$name[4].";".$name[5].";".$name[6].";".$name[7];
							} else {
								$row['name']=$name[0]."(".$resultMY[3].");".$name[1].";".$name[2].";".$name[3].";".$name[4].";".$name[5].";".$name[6].";".$name[7];
							}
							
							$row['id']	  	= utf8_encode($row['id']);
							$row['name']  	= utf8_encode($row['name']);
							$row['title'] 	= utf8_encode($row['title']);
							$row['nivel'] 	= utf8_encode($row['nivel']);
						} else {
							$row			= null;
						}
					}

					if($funcion == "transversal") {
						$sqlMy		= "SELECT * FROM org_hijos where COD_FUNC = ".$row['id'];
						$conexion 	= new  conexionMySQL();
						$connMy 	= $conexion -> conectar();
						$resultMY 	= $connMy->query($sqlMy);
						$resultMY 	= $resultMY->fetch_array(MYSQLI_NUM);
						$name		= explode(";",$row['name']);
	
						if(in_array($row['id'], $_SESSION['permitidos'])) {
							if($resultMY[3]=="0") {
								$row['name']=$name[0].";".$name[1].";".$name[2].";".$name[3].";".$name[4].";".$name[5].";".$name[6].";".$name[7];
							} else {
								$row['name']=$name[0]."(".$resultMY[3].");".$name[1].";".$name[2].";".$name[3].";".$name[4].";".$name[5].";".$name[6].";".$name[7];
							}
							
							$row['id'] 			= utf8_encode($row['id']);
							$row['name'] 		= utf8_encode($row['name']);
							$row['title'] 		= utf8_encode($row['title']);
							$row['nivel'] 		= utf8_encode($row['nivel']);
							$row['superior']	= utf8_encode($row['superior']);
						} else {
							$row				= null;
						}
					}

					if($funcion == "SelectChildrens") {
						$sqlMy		= "SELECT * FROM org_hijos WHERE COD_FUNC = ".$row['id'];
						$conexion	= new conexionMySQL();
						$connMy 	= $conexion -> conectar();
						$resultMY 	= $connMy->query($sqlMy);
						$resultMY 	= $resultMY->fetch_array(MYSQLI_NUM);
						$name 		= explode(";",$row['name']);
	
						if(in_array($row['id'], $_SESSION['permitidos'])) {
							if($resultMY[3] == "0") {
								$row['name']=$name[0].";".$name[1].";".$name[2].";".$name[3].";".$name[4].";".$name[5].";".$name[6].";".$name[7];
							} else {
								$row['name']=$name[0]."(".$resultMY[3].");".$name[1].";".$name[2].";".$name[3].";".$name[4].";".$name[5].";".$name[6].";".$name[7];
							}

							$row['id']		= strtr(utf8_encode($row['id']), $cp1252_map);
							$row['name'] 	= strtr(utf8_encode($row['name']), $cp1252_map);
							$row['title'] 	= strtr(utf8_encode($row['title']), $cp1252_map);
							$row['nivel'] 	= strtr(utf8_encode($row['nivel']), $cp1252_map);
						} else {
							$row 			= null;
						}
					}

					if($funcion == "gerencias") {
						if(in_array($row['COD_FUNC'], $_SESSION['permitidos'])) {
							$row['GERENCIA']		= utf8_encode($row['GERENCIA']);
 							$row['COD_GERENCIA']	= utf8_encode($row['COD_GERENCIA']);
						} else {
							$row					= null;
						}
					} else if($funcion == "diagrama") {
						error_log("diagrama");
 						$row['id']					 = utf8_encode($row['id']);
 						$row['title']				 = utf8_encode($row['title']);
 						$row['name']				 = utf8_encode($row['name']);
					} else if($funcion == "academico") {
						$row['ANTECEDENTE_ACADEMICO']= strtr(utf8_encode($row['ANTECEDENTE_ACADEMICO']), $cp1252_map);
					} else if($funcion == "academico2") {
						error_log("academico");
 						$row['GRADO_ACADEMICO']		 = utf8_encode($row['GRADO_ACADEMICO']);
 						$row['UNIVERSIDAD']			 = "";
 						$row['MASTERADO']			 = "";
 						$row['DIPLOMADO']			 = ""; 
 						$row['EDAD']				 = utf8_encode($row['EDAD']); 
 						$row['CAN_PER_DEP_ECO']		 = utf8_encode($row['CAN_PER_DEP_ECO']); 
 						$row['TIPO_VIVIENDA']		 = utf8_encode($row['TIPO_VIVIENDA']); 
 						$row['CAN_CONT_GASTOS']		 = utf8_encode($row['CAN_CONT_GASTOS']); 
						$row['MOV_PROPIA']			 = utf8_encode($row['MOV_PROPIA']);
						$row['NRO_CEDULA']			 = utf8_encode($row['NRO_CEDULA']);
						$row['COD_GERENCIA']		 = utf8_encode($row['COD_GERENCIA']);
						$row['GERENCIA']			 = utf8_encode($row['GERENCIA']);
						$row['SUPERIOR_INMEDIATO']	 = utf8_encode($row['SUPERIOR_INMEDIATO']);
						$row['FECHA_INGRESO']		 = utf8_encode($row['FECHA_INGRESO']); 
					} else if($funcion == "logros") {
						error_log("logros");
						$row['FECHA']				 = utf8_encode($row['PERIODO']);
 						$row['META']				 = utf8_encode($row['META']);
 						$row['RATIO']				 = utf8_encode($row['RATIO']);
 						$row['COLOR']				 = utf8_encode($row['COLOR']);
 						$row['COLOR_TEXTO']			 = utf8_encode($row['COLOR_TEXTO']);
 						$row['TIPO']				 = utf8_encode($row['TIPO']);
					} else if($funcion == "salario") {
						$row['id']					 = utf8_encode($row['id']);
 						$row['periodo']				 = utf8_encode($row['periodo']);
 						$row['fijo']				 = utf8_encode($row['fijo']);
 						$row['variable']			 = utf8_encode($row['variable']);
 						$row['total']				 = utf8_encode($row['total']);
						$row['aguinaldo']			 = utf8_encode($row['aguinaldo']);
						$row['aporte']				 = utf8_encode($row['aporte']);
					} else if($funcion == "eventos") {
						$row['codigo']				 = utf8_encode($row['codigo']);
						$row['falta']				 = utf8_encode($row['falta']);
						$row['mes']					 = utf8_encode($row['mes']);
						$row['ano']					 = utf8_encode($row['ano']);
						$row['total']				 = utf8_encode($row['total']);
						$row['color']				 = utf8_encode($row['color']);
					} else if($funcion == "dependencia") {
						error_log("dependencia");
						$row['COD_FUNC']			 = utf8_encode($row['COD_FUNC']);
						$row['PARENTESCO']			 = utf8_encode($row['PARENTESCO']);
						$row['NOMBRE_COMPLETO_DEP']	 = utf8_encode($row['NOMBRE_COMPLETO_DEP']);
 					} else if($funcion == "hobbies") {
						error_log("hobbies");
						$row['COD_FUNC']			 = strtr(utf8_encode($row['COD_FUNC']), $cp1252_map);
						$row['HOBBIE']				 = strtr(utf8_encode($row['HOBBIE']), $cp1252_map);
						$row['OBSERVACION']			 = strtr(utf8_encode($row['OBSERVACION']), $cp1252_map);
					} else if($funcion == "backups") {
 						error_log("hobbies");
						$row['COD_FUNC']			 = utf8_encode($row['COD_FUNC']);
						$row['TIPO']				 = utf8_encode($row['TIPO']);
						$row['CODIGO_BACKUP']		 = utf8_encode($row['CODIGO_BACKUP']);
						$row['NOMBRE_BACKUP']		 = utf8_encode($row['NOMBRE_BACKUP']); 
					} else if($funcion == "movimientos") {
						$row['desde']				 = utf8_encode($row['desde']);
						$row['cargo']				 = utf8_encode($row['cargo']);
						$row['departamento']		 = utf8_encode($row['departamento']);
						$row['unidad']				 = utf8_encode($row['unidad']);
					} else if($funcion == "documentos") {
						$row['FUNC_TIPO']			 = utf8_encode($row['FUNC_TIPO']);
						$row['FUNC_DOCUMENTO']		 = utf8_encode($row['FUNC_DOCUMENTO']);
						$row['FUNC_PATH']			 = utf8_encode($row['FUNC_PATH']);
					} else if($funcion == "anotaciones") {
						$row['FUNC_NRO_ANOTACION']	 = utf8_encode($row['FUNC_NRO_ANOTACION']);
						$row['FUNC_FECHA']		 	 = utf8_encode($row['FUNC_FECHA']);
						$row['FUNC_EVENTO']			 = utf8_encode($row['FUNC_EVENTO']);
						$row['FUNC_OBSERVACION']	 = utf8_encode($row['FUNC_OBSERVACION']);
					} else if($funcion == "capacitaciones") {
						$row['FUNC_NRO_CAPACITACION']= utf8_encode($row['FUNC_NRO_CAPACITACION']);
						$row['FUNC_EMPRESA']		 = utf8_encode($row['FUNC_EMPRESA']);
						$row['FUNC_CURSO']			 = utf8_encode($row['FUNC_CURSO']);
						$row['FUNC_ANHO']	 		 = utf8_encode($row['FUNC_ANHO']);
						$row['FUNC_MES']	 		 = utf8_encode($row['FUNC_MES']);
						$row['FUNC_CANT_HORA']	 	 = utf8_encode($row['FUNC_CANT_HORA']);
					} else if($funcion == "antlaborales") {
						$row['FUNC_NRO_ANTECEDENTE'] = utf8_encode($row['FUNC_NRO_ANTECEDENTE']);
						$row['FUNC_EMPRESA']		 = utf8_encode($row['FUNC_EMPRESA']);
						$row['FUNC_FECHA_DESDE']	 = utf8_encode($row['FUNC_FECHA_DESDE']);
						$row['FUNC_FECHA_HASTA']	 = utf8_encode($row['FUNC_FECHA_HASTA']);
					} else if($funcion == "ends") {
						/* MODIFICO PARA QUE LA URL SEA SOLO EL NOMBRE DEL ARCHIVO */
						$url_end = $row['ARCHIVO'];
						$url_end = str_replace('\\', '/', $url_end);						
						$url_end = basename($url_end);						
						
						$row['FECHA'] 				 = utf8_encode($row['FECHA']);
						$row['EVENTO']				 = utf8_encode($row['EVENTO']);
						$row['ARCHIVO']				 = utf8_encode($url_end);
						$row['NRO_EVENTO']	 		 = utf8_encode($row['NRO_EVENTO']);
					} else if($funcion =="estructura") {
						$row['cod']					 = utf8_encode($row['cod']);
						$row['cargo']				 = utf8_encode($row['cargo']);
						$row['cantidad']			 = utf8_encode($row['cantidad']);
					} else if($funcion =="listarcolaboradores") {
						$result[]					 = $row;
					}
					
					if($row != null) {
						$result[] = $row;
					}
				}

				mssql_free_result($query);
				return $result;
			}
		}
	}
?>