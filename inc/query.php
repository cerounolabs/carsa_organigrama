<?php
    include "conexion.php";

    class query {
        public function queryJson($sql = "", $funcion) {
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

            $class  = new conexion ();
            $conn   = $class->conectar();
            error_log(">>>>>>>>>>>>>>>>>>>>".$sql);

            $query  = sqlsrv_query($conn, $sql);
            
            if ($query === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                $result = "";

                while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
                    if ($funcion == "datos") {
                        $row['id']                  = utf8_encode($row['id']);
                        $row['name']                = utf8_encode($row['name']);
                        $row['title']               = utf8_encode($row['title']);
                        $row['children']            = null;
                    }

                    if ($funcion == "childrens") {
                        $row['id']                  = utf8_encode($row['id']);
                        $row['name']                = utf8_encode($row['name']);
                        $row['title']               = utf8_encode($row['title']);
                        $row['nivel']               = utf8_encode($row['nivel']);
                    }

                    if ($funcion == "transversal") {
                        $row['id']                  = utf8_encode($row['id']);
                        $row['name']                = utf8_encode($row['name']);
                        $row['title']               = utf8_encode($row['title']);
                        $row['nivel']               = utf8_encode($row['nivel']);
                        $row['superior']            = utf8_encode($row['superior']);
                    }

                    if ($funcion == "SelectChildrens") {
                        $row['id']                  = strtr(utf8_encode($row['id']), $cp1252_map);
                        $row['name']                = strtr(utf8_encode($row['name']), $cp1252_map);
                        $row['title']               = strtr(utf8_encode($row['title']), $cp1252_map);
                        $row['nivel']               = strtr(utf8_encode($row['nivel']), $cp1252_map);
                    }

                    if ($funcion == "gerencias") {
                        $row['GERENCIA']            = utf8_encode($row['GERENCIA']);
                        $row['COD_GERENCIA']        = utf8_encode($row['COD_GERENCIA']);

                    } else if ($funcion == "diagrama") {
                        error_log("diagrama");
                        $row['id']                  = utf8_encode($row['id']);
                        $row['title']               = utf8_encode($row['title']);
                        $row['name']                = utf8_encode($row['name']);

                    } else if ($funcion == "academico") {
                        error_log("academico");
                        $row['GRADO_ACADEMICO']     = utf8_encode($row['GRADO_ACADEMICO']);
                        $row['UNIVERSIDAD']         = utf8_encode($row['UNIVERSIDAD']);
                        $row['MASTERADO']           = utf8_encode($row['MASTERADO']);
                        $row['DIPLOMADO']           = utf8_encode($row['DIPLOMADO']);

                    } else if ($funcion == "logros") {
                        error_log("logros");
                        $row['FECHA']               = utf8_encode($row['FECHA']);
                        $row['META']                = utf8_encode($row['META']);
                        $row['RATIO']               = utf8_encode($row['RATIO']);
                        $row['COLOR']               = utf8_encode($row['COLOR']);

                    } else if ($funcion == "dependencia") {
                        error_log("dependencia");
                        $row['COD_FUNC']            = utf8_encode($row['COD_FUNC']);
                        $row['PARENTESCO']          = utf8_encode($row['PARENTESCO']);
                        $row['NOMBRE_COMPLETO_DEP'] = utf8_encode($row['NOMBRE_COMPLETO_DEP']);

                    } else if ($funcion == "hobbies") {
                        error_log("hobbies");
                        $row['COD_FUNC']            = utf8_encode($row['COD_FUNC']);
                        $row['HOBBIE']              = utf8_encode($row['HOBBIE']);
                        $row['OBSERVACION']         = utf8_encode($row['OBSERVACION']);

                    } else if ($funcion == "backups") {
                        error_log("hobbies");
                        $row['COD_FUNC']            = utf8_encode($row['COD_FUNC']);
                        $row['TIPO']                = utf8_encode($row['TIPO']);
                        $row['CODIGO_BACKUP']       = utf8_encode($row['CODIGO_BACKUP']);
                        $row['NOMBRE_BACKUP']       = utf8_encode($row['NOMBRE_BACKUP']);

                    } else if ($funcion == "salario") {
                        $row['id']                  = utf8_encode($row['id']);
                        $row['periodo']             = utf8_encode($row['periodo']);
                        $row['fijo']                = utf8_encode($row['fijo']);
                        $row['variable']            = utf8_encode($row['variable']);
                        $row['total']               = utf8_encode($row['total']);

                    } else if ($funcion == "eventos") {
                        $row['codigo']              = utf8_encode($row['codigo']);
                        $row['falta']               = utf8_encode($row['falta']);
                        $row['mes']                 = utf8_encode($row['mes']);
                        $row['ano']                 = utf8_encode($row['ano']);
                        $row['total']               = utf8_encode($row['total']);
                        $row['color']               = utf8_encode($row['color']);

                    } else if($funcion == "movimientos") {
                        $row['desde']               = utf8_encode($row['desde']);
                        $row['cargo']               = utf8_encode($row['cargo']);
                        $row['departamento']        = utf8_encode($row['departamento']);
                        $row['unidad']              = utf8_encode($row['unidad']);

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

                    } else if($funcion == "estructura") {
                        $row['cod']                 = utf8_encode($row['cod']);
                        $row['cargo']               = utf8_encode($row['cargo']);
                        $row['cantidad']            = utf8_encode($row['cantidad']);
                    }

                    $result[] = $row;
                }

                return $result;
            }

            sqlsrv_free_stmt($query);
            sqlsrv_close($conn);
        }
    }
?>
