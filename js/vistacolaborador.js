function cargartablas(idcolaborador) {
    var cdatos  = localStorage.getItem("colaboradordatos");
    cdatos      = cdatos.split("||");
    var img     = cdatos[3].split("/");
    img         = "http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/fotos/192.168.16.116:8081/"+img[(img.length-1)];
    var data1   = jsUcfirst(cdatos[1]); 
    var data4   = cdatos[4];
    var data5   = cdatos[5]; 
    var data6   = cdatos[6]; 
    var data7   = cdatos[7]; 
    var data8   = cdatos[8]; 

    
    $("#nombre").html(toTitleCase(cdatos[0]));
    $("#infoCodigo").html(data4);
    $("#infoCargo").html(data1);
    $(".imgProfile").attr("style","height: 63%;background-image: url("+img+");background-color: #cccccc;background-repeat:no-repeat;background-size: 100% 115%;height: 201px;width:260px;border: 3px solid #c59d4c;border-radius: 17px;"); 
    $("#infoantiguedad").html(cdatos[2]);

    var elements = $('.modal-overlay, .modal');        
    elements.addClass('active');
    
    $("#miniloading").fadeIn();
    $(".tablaMovimientos").empty();
    $(".tablaSalario").empty();
    $(".tablaLogros").empty();
    $(".tablaEventos").empty();
    $(".tabladependencia").empty();
    $(".tablahobbies").empty();
    $(".tablaDocumentos").empty();
    $(".tablaEnds").empty();
    $(".tablaCapacitaciones").empty();
    $(".tablaAnotaciones").empty();
    $(".tablaAntLaborales").empty();
    $(".tabladependencia").append('<tr class="tr"><td class="td" style="color:black;">Familiares Directos</td><td class="td" style="color:black;">Nombre y Apellido</td> </tr>');
    $(".tablahobbies").append('<tr class="tr"><td class="td" style="color:black;">Hobbie</td><td class="td" style="color:black;">Observaci&#243;n</td> </tr>');
    $("#infoedad").html("");
    $("#cant_depen").html("");
    $("#cant_cont").html("");
    $("#vivienda").html("");
    $("#movilidad").html("");
    $("#qbackup").html("");
    $("#dbackup").html("");
    $("#infogrado").html("");
    $("#infoNroDocumento").html("");
    $("#infoGerencia").html("");
    $("#infoOrganigrama").html("");
    $("#infoSuperior").html("");
    $("#infoFechaIngreso").html("");
    $(".tablaLogros").append('<thead style="background-color:#f5f5f5;"><tr class="tr"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Meses </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Tipo de Producción </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Meta </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Logrado </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Ratio </td></tr></thead>');

    $.ajax({
        url:"inc/funciones.php?funcion=selectTablas&id="+idcolaborador,
        success: function(result) {
            $("#miniloading").fadeOut();
            $("#infogrado").html("");
            $("#infoNroDocumento").html("");
            $("#infoGerencia").html("");
            $("#infoOrganigrama").html("");
            $("#infoSuperior").html("");
            $("#infoFechaIngreso").html("");
            $("#infouni").html("");
            $("#infomasterado").html("");
            $("#infodiplomado").html("");
            $("#infoedad").html("");
            $("#cant_depen").html("");
            $("#cant_cont").html("");
            $("#vivienda").html("");
            $("#movilidad").html("");
            $("#qbackup").html("");
            $("#dbackup").html("");

            if (result.informacion != null) {
                $("#infogrado").html(result.informacion[0].GRADO_ACADEMICO);
                $("#infouni").html(result.informacion[0].UNIVERSIDAD);
                $("#infomasterado").html(result.informacion[0].MASTERADO);
                $("#infodiplomado").html(result.informacion[0].DIPLOMADO);
                $("#infoedad").html(result.informacion[0].EDAD);
                $("#cant_depen").html(result.informacion[0].CAN_PER_DEP_ECO);
                $("#cant_cont").html(result.informacion[0].CAN_CONT_GASTOS);
                $("#vivienda").html(result.informacion[0].TIPO_VIVIENDA);
                $("#movilidad").html(result.informacion[0].MOV_PROPIA);
                $("#infoNroDocumento").html(result.informacion[0].NRO_CEDULA);
                $("#infoGerencia").html(jsUcfirst(result.informacion[0].GERENCIA));
                $("#infoFechaIngreso").html(result.informacion[0].FECHA_INGRESO);

                var nomGer = "'"+result.informacion[0].GERENCIA+"'";
                var verOrganigrama = '<button type="button" onclick="getOrganigrama(' + result.informacion[0].COD_GERENCIA + ', '+ nomGer +');">Ver</button>';
                $("#infoOrganigrama").html(verOrganigrama);

                if (result.informacion[0].SUPERIOR_INMEDIATO != ''){
                    document.getElementById("titSuperior").style.display = "";
                    document.getElementById("saltSuperior").style.display = "";
                    document.getElementById("infoSuperior").style.display = "";
                    $("#titSuperior").html('Superior: ');
                    $("#infoSuperior").html(jsUcfirst(result.informacion[0].SUPERIOR_INMEDIATO));
                } else {
                    document.getElementById("titSuperior").style.display = "none";
                    document.getElementById("saltSuperior").style.display = "none";
                    document.getElementById("infoSuperior").style.display = "none";
                    $("#titSuperior").html('');
                    $("#infoSuperior").html('');
                }
            }

            if (result.dependencia != null) {
                $(".tabladependencia").fadeIn();
                $("#datosdependencia").fadeIn();

                for (var i = 0; i < result.dependencia.length; i++) {
                    var relacion= result.dependencia[i].PARENTESCO;
                    var nombre  = result.dependencia[i].NOMBRE_COMPLETO_DEP;
                    var html    = '<tr class="tr"><td class="td">'+relacion+'</td><td class="td">'+nombre+'</td></tr>';
                    $(".tabladependencia").append(html);
                }
            } else {
                $(".tabladependencia").fadeOut()
                $("#datosdependencia").fadeOut()
            }

            if (result.academico != null) {
                document.getElementById("titinfogrado").style.display = "";

                for (var i = 0; i < result.academico.length; i++) {
                    var cursado = result.academico[i].ANTECEDENTE_ACADEMICO;
                    cursado     = cursado.replace("/", " / ");
                    cursado     = cursado.replace("/culminado", " / culminado");
                    cursado     = cursado.replace("/en", " / en");
                    cursado     = cursado.replace("/proceso", " / proceso");
                    var html    = '<spam style="text-transform:capitalize;">'+cursado+'</spam><br>';
                    $("#infogrado").append(html);
                }
            } else {
                document.getElementById("titinfogrado").style.display = "none";
            }

            if (result.hobbies != null) {
                for (var i = 0; i < result.hobbies.length; i++) {
                    var hobbie      = result.hobbies[i].HOBBIE;
                    var OBSERVACION = result.hobbies[i].OBSERVACION;
                    var html        = '<tr class="tr"><td class="td">'+hobbie+'</td><td class="td">'+OBSERVACION+'</td></tr>';
                    $(".tablahobbies").append(html);
                    $(".tablahobbies").show();
                }
            } else {
                $(".tablahobbies").hide();
            }

            if (result.documentos != null) {
                for (var i = 0; i < result.documentos.length; i++) {
                    var tipoDoc     = result.documentos[i].FUNC_TIPO;
                    var nombreDoc   = result.documentos[i].FUNC_DOCUMENTO;
                    var pathDoc     = result.documentos[i].FUNC_PATH;

                    if (i != (result.documentos.length - 1)) {
                        var html = '<tr class="tr"><td class="td" style="text-align:left; border-bottom-color:whitesmoke;"><a href="http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/documentos/192.168.16.116:8080/'+pathDoc+'" target="_blank"> '+nombreDoc+' </a></td></tr>';
                    } else {
                        var html = '<tr class="tr"><td class="td" style="text-align:left;"><a href="http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/documentos/192.168.16.116:8080/'+pathDoc+'" target="_blank"> '+nombreDoc+' </a></td></tr>';
                    }

                    $(".tablaDocumentos").append(html);
                    $(".tablaDocumentos").show();
                }
            } else {
                $(".tablaDocumentos").hide();
            }
			
															if(result.ends != null) {
                                                                $(".tablaEnds").empty();
                                                                $(".tablaEnds").html('<thead style="background-color:#f5f5f5;"><tr><td class="td"></td></tr></thead>');

                                                                for (var i = 0; i < result.ends.length; i++) {
                                                                    var fechaEnd     = result.ends[i].FECHA + " - ";
                                                                    var eventoEnd   = result.ends[i].EVENTO;
                                                                    var pathEnd     = result.ends[i].ARCHIVO;
                                                                    var nroEnd     = result.ends[i].NRO_EVENTO;
																	
																	var fechaEnd = fechaEnd.replace(" 00:00:00.000"," ");
																	var fechaEnd = fechaEnd.replace("000","");
																	
                                                                    if (i != (result.ends.length - 1)) {
                                                                        var html = '<tr data-nro="'+nroEnd+'" class="tr"><td class="td" style="text-align:left; border-bottom-color:whitesmoke;"><a href="http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/ends/192.168.16.116:8082/'+pathEnd+'" target="_blank"> '+fechaEnd+eventoEnd+' </a></td></tr>';
                                                                    } else {
                                                                        var html = '<tr data-nro="'+nroEnd+'" class="tr"><td class="td" style="text-align:left;"><a href="http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/ends/192.168.16.116:8082/'+pathEnd+'" target="_blank"> '+fechaEnd+eventoEnd+' </a></td></tr>';
                                                                    }

                                                                   
																	
                                                                }
																
																$(".tablaEnds").append(html);
																	var seen = {};
																	$('.tablaEnds tr').each(function() {
																	  var txt = $(this).text();
																	  var numero = $(this).data("nro");
																	  if (seen[txt]){
																		$(this).css("display","none");
																		$(this).addClass("repetido_"+numero);
																	  }else{
																		seen[txt] = true;
																		$(this).addClass("primero_"+numero);
																		$(this).addClass("only");
																	  }
																	});
                                                            } else {
                                                                $(".tablaEnds").empty();
                                                            }
			

            if (result.capacitaciones != null) {
                for (var i = 0; i < result.capacitaciones.length; i++) {
                    var nroCapacitacion = result.capacitaciones[i].FUNC_NRO_CAPACITACION;
                    var empCapacitacion = result.capacitaciones[i].FUNC_EMPRESA;
                    var curCapacitacion = result.capacitaciones[i].FUNC_CURSO;
                    var anoCapacitacion = result.capacitaciones[i].FUNC_ANHO;
                    var mesCapacitacion = result.capacitaciones[i].FUNC_MES;
                    var horCapacitacion = result.capacitaciones[i].FUNC_CANT_HORA;

                    if (i != (result.capacitaciones.length - 1)) {
                        var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+nroCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+empCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+curCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+anoCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+mesCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+horCapacitacion+'</td></tr>';
                    } else {
                        var html = '<tr class="tr"><td class="td" style="text-align:center;">'+nroCapacitacion+'</td><td class="td" style="text-align:center;">'+empCapacitacion+'</td><td class="td" style="text-align:center;">'+curCapacitacion+'</td><td class="td" style="text-align:center;">'+anoCapacitacion+'</td><td class="td" style="text-align:center;">'+mesCapacitacion+'</td><td class="td" style="text-align:center;">'+horCapacitacion+'</td></tr>';
                    }

                    $(".tablaCapacitaciones").append(html);
                    $(".tablaCapacitaciones").show();
                }
            } else {
                $(".tablaCapacitaciones").hide();
            }

            if (result.anotaciones != null) {
                for (var i = 0; i < result.anotaciones.length; i++) {
                    var nroAnotacion = result.anotaciones[i].FUNC_NRO_ANOTACION;
                    var fecAnotacion = result.anotaciones[i].FUNC_FECHA;
                    var eveAnotacion = result.anotaciones[i].FUNC_EVENTO;
                    var obsAnotacion = result.anotaciones[i].FUNC_OBSERVACION;

                    if (i != (result.anotaciones.length - 1)) {
                        var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+nroAnotacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+fecAnotacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+eveAnotacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+obsAnotacion+'</td></tr>';
                    } else {
                        var html = '<tr class="tr"><td class="td" style="text-align:center;">'+nroAnotacion+'</td><td class="td" style="text-align:center;">'+fecAnotacion+'</td><td class="td" style="text-align:center;">'+eveAnotacion+'</td><td class="td" style="text-align:center;">'+obsAnotacion+'</td></tr>';
                    }

                    $(".tablaAnotaciones").append(html);
                    $(".tablaAnotaciones").show();
                }
            } else {
                $(".tablaAnotaciones").hide();
            }

            if (result.antlaborales != null) {
                for (var i = 0; i < result.antlaborales.length; i++) {
                    var nroAntLaboral = result.antlaborales[i].FUNC_NRO_ANTECEDENTE;
                    var empAntLaboral = result.antlaborales[i].FUNC_EMPRESA;
                    var desAntLaboral = result.antlaborales[i].FUNC_FECHA_DESDE;
                    var hasAntLaboral = result.antlaborales[i].FUNC_FECHA_HASTA;
					var empAntLaboral = sentenceCase(empAntLaboral);
                    if (i != (result.antlaborales.length - 1)) {
                        var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
                    } else {
                         var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
                    }

                    $(".tablaAntLaborales").append(html);
                    $(".tablaAntLaborales").show();
					$("#titinfolaboral").fadeIn();
                }
            } else {
                $(".tablaAntLaborales").hide().empty();
				$("#titinfolaboral").hide();
            }

            if (result.backups != null ) {
                document.getElementById("titqbackup").style.display = "none";
                document.getElementById("titdbackup").style.display = "none";

                for (var i = 0; i < result.backups.length; i++) {
                    if(result.backups[i].TIPO == "1") {
                        document.getElementById("titqbackup").style.display = "";
                        $("#qbackup").append('<spam>-'+toTitleCase(result.backups[i].NOMBRE_BACKUP)+'</spam><br>');
                    } else {
                        document.getElementById("titdbackup").style.display = "";
                        $("#dbackup").append('<spam>-'+toTitleCase(result.backups[i].NOMBRE_BACKUP)+'</spam><br>');
                    }
                }
            } else {
                document.getElementById("titqbackup").style.display = "none";
                document.getElementById("titdbackup").style.display = "none";
            }

            var promedioLogrado = 0;
            
            if (result.logros != null) {
                for (var i = 0; i < result.logros.length; i++) {
                    var meta                = result.logros[i].META.split(".");
                    var logro               = result.logros[i].LOGRADO.split(".");
                    
                    meta[0]                 = meta[0].toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                    meta[0]                 = meta[0].split('').reverse().join('').replace(/^[\.]/,'');
                    logro[0]                = logro[0].toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                    logro[0]                = logro[0].split('').reverse().join('').replace(/^[\.]/,'');
                    
                    var color               = "";
                    var texto               = "";

                    result.logros[i].COLOR  = result.logros[i].COLOR.replace(/ /g , "");
                    
                    if (result.logros[i].COLOR == "ROJO") {
                        color   = "red";
                        texto   = "white";
                    } else if (result.logros[i].COLOR == "VERDE"){ 
                        color   = "green";
                        texto   = "white";
                    } else {
                        color   = "#eae70a";
                        texto   = "black";
                    }

                    if (i != (result.logros.length - 1)) {
                        var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+result.logros[i].PERIODO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+result.logros[i].TIPO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+meta[0]+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+logro[0]+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; background-color:'+result.logros[i].COLOR+'; color:'+result.logros[i].COLOR_TEXTO+'">'+result.logros[i].RATIO+'%</td></tr>';
                    } else {
                        var html = '<tr class="tr"><td class="td" style="text-align:center;">'+result.logros[i].PERIODO+'</td><td class="td" style="text-align:center;">'+result.logros[i].TIPO+'</td><td class="td" style="text-align:center;">'+meta[0]+'</td><td class="td" style="text-align:center;">'+logro[0]+'</td><td class="td" style="text-align:center; background-color:'+result.logros[i].COLOR+'; color:'+result.logros[i].COLOR_TEXTO+'">'+result.logros[i].RATIO+'%</td></tr>';
                    }
                    
                    $(".tablaLogros").append(html);

                    promedioLogrado = promedioLogrado + parseInt(result.logros[i].RATIO);
                }

                promedioLogrado  = (promedioLogrado / result.logros.length);

                var htmlPromedio = '<tr class="tr" style="background-color:#f5f5f5;"><td class="td" colspan="3"></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>Promedio:</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0"><strong>'+promedioLogrado.toFixed(2)+'%</strong></td></tr>';

                $(".tablaLogros").append(htmlPromedio);
            } else {
                $(".tablaLogros").empty();
            }
            
            var fijo        = 0;
            var variable    = 0;
            var total       = 0;
            var aguinaldo   = 0;
            var aporte      = 0;
            
            if (result.salario != null) {
                $(".tablaSalario").empty();
                $(".tablaSalario").append('<thead style="background-color:#f5f5f5;"><tr class="tr"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Meses</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Variable(SV)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Fijo(SF)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Total(ST)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Aguinaldo</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">IPS</td></tr></thead>');

                for (var i = 0; i < result.salario.length; i++) {
                    fijo            = fijo + parseInt(result.salario[i].fijo);
                    variable        = variable + parseInt(result.salario[i].variable);
                    total           = total + parseInt(result.salario[i].total);
                    aguinaldo       = aguinaldo + parseInt(result.salario[i].aguinaldo);
                    aporte          = aporte + parseInt(result.salario[i].aporte);

                    var VARIABLE    = result.salario[i].variable;
                    var FIJO        = result.salario[i].fijo;
                    var TOTAL       = result.salario[i].total;
                    var AGUINALDO   = result.salario[i].aguinaldo;
                    var APORTE      = result.salario[i].aporte;

                    AGUINALDO       = AGUINALDO.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                    AGUINALDO       = AGUINALDO.split('').reverse().join('').replace(/^[\.]/,'');
                    APORTE          = APORTE.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                    APORTE          = APORTE.split('').reverse().join('').replace(/^[\.]/,'');
                    VARIABLE        = VARIABLE.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                    VARIABLE        = VARIABLE.split('').reverse().join('').replace(/^[\.]/,'');
                    FIJO            = FIJO.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                    FIJO            = FIJO.split('').reverse().join('').replace(/^[\.]/,'');
                    TOTAL           = TOTAL.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                    TOTAL           = TOTAL.split('').reverse().join('').replace(/^[\.]/,'');

                    if (i != (result.salario.length - 1)) {
                        var html = '<tr class="tr"> <td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+result.salario[i].periodo+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+VARIABLE+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+FIJO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+TOTAL+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+AGUINALDO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+APORTE+'</td></tr>';
                    } else {
                        var html = '<tr class="tr"> <td class="td" style="text-align:center;">'+result.salario[i].periodo+'</td><td class="td" style="text-align:center;">'+VARIABLE+'</td><td class="td" style="text-align:center;">'+FIJO+'</td><td class="td" style="text-align:center;">'+TOTAL+'</td><td class="td" style="text-align:center;">'+AGUINALDO+'</td><td class="td" style="text-align:center;">'+APORTE+'</td></tr>';
                    }

                    $(".tablaSalario").append(html);
                }

                total       = (total/result.salario.length);
                variable    = (variable/result.salario.length);
                fijo        = (fijo/result.salario.length);
                fijo        = fijo.toFixed(0);
                variable    = variable.toFixed(0);
                total       = total.toFixed(0);
                fijo        = fijo.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                fijo        = fijo.split('').reverse().join('').replace(/^[\.]/,'');
                variable    = variable.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                variable    = variable.split('').reverse().join('').replace(/^[\.]/,'');
                total       = total.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                total       = total.split('').reverse().join('').replace(/^[\.]/,'');
                aguinaldo   = aguinaldo.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                aguinaldo   = aguinaldo.split('').reverse().join('').replace(/^[\.]/,'');
                aporte      = aporte.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                aporte      = aporte.split('').reverse().join('').replace(/^[\.]/,'');

                $(".tablaSalario").append('<tr class="tr" style="background-color:#f5f5f5;"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Promedios:</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+variable+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+fijo+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+total+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+aguinaldo+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+aporte+'</strong></td></tr>');
            } else {
                $(".tablaSalario").empty();
            }

            if (result.eventos != null) {
                $(".tablaEventos").empty();
                $(".tablaEventos").html('<thead style="background-color:#f5f5f5;"><tr><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Licencias Justificadas/Injustificadas</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Códigos</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0; display:none;">Fecha</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Cantidad Total</td></tr></thead>');
                
                var auxEvenCod  = '';
                var auxEvenFal  = '';
                var auxEvenCol  = '';
                var auxEvenCan  = 0;
                var auxEvenBan  = true;
                var html        = '';
                
                for (var i = 0; i < result.eventos.length; i++) {
                    if(result.eventos[i].color == "ROJO") {
                        result.eventos[i].color = "red";
                    } else if(result.eventos[i].color == "VERDE") {
                        result.eventos[i].color = "green";
                    } else if(result.eventos[i].color == "AZUL") {
                        result.eventos[i].color = "blue";
                    }

                    if (auxEvenBan === true ){
                        auxEvenCod  = result.eventos[i].codigo;
                        auxEvenFal  = result.eventos[i].falta;
                        auxEvenCol  = result.eventos[i].color;
                        auxEvenCan  = 0;
                        auxEvenBan  = false;
                    }

                    if (i != (result.eventos.length - 1)) {
                        if (result.eventos[i].codigo != auxEvenCod){
                            var html    = '<tr><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+auxEvenFal+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; color:'+auxEvenCol+'">'+auxEvenCod+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; display:none;">'+result.eventos[i].mes+"-"+result.eventos[i].ano+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+auxEvenCan+'</td></tr>';
                            $(".tablaEventos").append(html);
                            auxEvenCod  = result.eventos[i].codigo;
                            auxEvenFal  = result.eventos[i].falta;
                            auxEvenCol  = result.eventos[i].color;
                            auxEvenCan  = Number(result.eventos[i].total);
                        } else {
                            auxEvenCan  = auxEvenCan + Number(result.eventos[i].total);
                        }
                    } else {
                        var html    = '';

                        if (result.eventos[i].codigo != auxEvenCod){
                            html        = '<tr><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+auxEvenFal+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; color:'+auxEvenCol+'">'+auxEvenCod+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; display:none;">'+result.eventos[i].mes+"-"+result.eventos[i].ano+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+auxEvenCan+'</td></tr>';
                            auxEvenCod  = result.eventos[i].codigo;
                            auxEvenFal  = result.eventos[i].falta;
                            auxEvenCol  = result.eventos[i].color;
                            auxEvenCan  = Number(result.eventos[i].total);
                        }

                        html        = html + '<tr><td class="td" style="text-align:center;">'+auxEvenFal+'</td><td class="td" style="text-align:center; color:'+auxEvenCol+'">'+auxEvenCod+'</td><td class="td" style="text-align:center; display:none;">'+result.eventos[i].mes+"-"+result.eventos[i].ano+'</td><td class="td" style="text-align:center;">'+auxEvenCan+'</td></tr>';

                        $(".tablaEventos").append(html);
                    }
                }
            } else {
                $(".tablaEventos").empty();
            }

            if (result.documentos != null) {
                $(".tablaDocumentos").empty();
                $(".tablaDocumentos").html('<thead style="background-color:#f5f5f5;"><tr><td class="td"></td></tr></thead>');

                for (var i = 0; i < result.documentos.length; i++) {
                    var tipoDoc     = result.documentos[i].FUNC_TIPO;
                    var nombreDoc   = result.documentos[i].FUNC_DOCUMENTO;
                    var pathDoc     = result.documentos[i].FUNC_PATH;

                    if (i != (result.documentos.length - 1)) {
                        var html = '<tr class="tr"><td class="td" style="text-align:left; border-bottom-color:whitesmoke;"><a href="http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/documentos/192.168.16.116:8080/'+pathDoc+'" target="_blank"> '+nombreDoc+' </a></td></tr>';
                    } else {
                        var html = '<tr class="tr"><td class="td" style="text-align:left;"><a href="http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/documentos/192.168.16.116:8080/'+pathDoc+'" target="_blank"> '+nombreDoc+' </a></td></tr>';
                    }

                    $(".tablaDocumentos").append(html);
                }
            } else {
                $(".tablaDocumentos").empty();
            }
			
															if(result.ends != null) {
                                                                $(".tablaEnds").empty();
                                                                $(".tablaEnds").html('<thead style="background-color:#f5f5f5;"><tr><td class="td"></td></tr></thead>');

                                                                for (var i = 0; i < result.ends.length; i++) {
                                                                    var fechaEnd     = result.ends[i].FECHA + " - ";
                                                                    var eventoEnd   = result.ends[i].EVENTO;
                                                                    var pathEnd     = result.ends[i].ARCHIVO;
																	var nroEnd     = result.ends[i].NRO_EVENTO;
																	
																	var fechaEnd = fechaEnd.replace(" 00:00:00.000"," ");
																	var fechaEnd = fechaEnd.replace("000","");
																	
																	
																	
																	
                                                                    if (i != (result.ends.length - 1)) {
                                                                        var html = '<tr data-nro="'+nroEnd+'" class="tr"><td class="td" style="text-align:left; border-bottom-color:whitesmoke;"><a href="http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/ends/192.168.16.116:8082/'+pathEnd+'" target="_blank"> '+fechaEnd+eventoEnd+' </a></td></tr>';
                                                                    } else {
                                                                        var html = '<tr data-nro="'+nroEnd+'" class="tr"><td class="td" style="text-align:left;"><a href="http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/ends/192.168.16.116:8082/'+pathEnd+'" target="_blank"> '+fechaEnd+eventoEnd+' </a></td></tr>';
                                                                    }

                                                                    $(".tablaEnds").append(html);
																	
																	
                                                                }
																var seen = {};
																	$('.tablaEnds tr').each(function() {
																	  var txt = $(this).text();
																	  var numero = $(this).data("nro");
																	  if (seen[txt]){
																		$(this).css("display","none"); 
																		$(this).addClass("repetido_"+numero);
																	  }else{
																		seen[txt] = true;
																		$(this).addClass("primero_"+numero);
																		$(this).addClass("only");
																	  }
																	});
                                                            } else {
                                                                $(".tablaEnds").empty();
                                                            }

            if (result.capacitaciones != null) {
                $(".tablaCapacitaciones").empty();
                $(".tablaCapacitaciones").html('<thead style="background-color:#f5f5f5;"><tr><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Número</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Empresa</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Curso</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Año</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Mes</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Horas</td></tr></thead>');
                
                for (var i = 0; i < result.capacitaciones.length; i++) {
                    var nroCapacitacion = result.capacitaciones[i].FUNC_NRO_CAPACITACION;
                    var empCapacitacion = result.capacitaciones[i].FUNC_EMPRESA;
                    var curCapacitacion = result.capacitaciones[i].FUNC_CURSO;
                    var anoCapacitacion = result.capacitaciones[i].FUNC_ANHO;
                    var mesCapacitacion = result.capacitaciones[i].FUNC_MES;
                    var horCapacitacion = result.capacitaciones[i].FUNC_CANT_HORA;

                    if (i != (result.capacitaciones.length - 1)) {
                        var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+nroCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+empCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+curCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+anoCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+mesCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+horCapacitacion+'</td></tr>';
                    } else {
                        var html = '<tr class="tr"><td class="td" style="text-align:center;">'+nroCapacitacion+'</td><td class="td" style="text-align:center;">'+empCapacitacion+'</td><td class="td" style="text-align:center;">'+curCapacitacion+'</td><td class="td" style="text-align:center;">'+anoCapacitacion+'</td><td class="td" style="text-align:center;">'+mesCapacitacion+'</td><td class="td" style="text-align:center;">'+horCapacitacion+'</td></tr>';
                    }

                    $(".tablaCapacitaciones").append(html);
                }
            } else {
                $(".tablaCapacitaciones").empty();
            }

            if (result.anotaciones != null) {
                $(".tablaAnotaciones").empty();
                $(".tablaAnotaciones").html('<thead style="background-color:#f5f5f5;"><tr><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Número</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Fecha</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Evento</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Observación</td></tr></thead>');
                
                for (var i = 0; i < result.anotaciones.length; i++) {
                    var nroAnotacion = result.anotaciones[i].FUNC_NRO_ANOTACION;
                    var fecAnotacion = result.anotaciones[i].FUNC_FECHA;
                    var eveAnotacion = result.anotaciones[i].FUNC_EVENTO;
                    var obsAnotacion = result.anotaciones[i].FUNC_OBSERVACION;

                    if (i != (result.anotaciones.length - 1)) {
                        var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+nroAnotacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+fecAnotacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+eveAnotacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+obsAnotacion+'</td></tr>';
                    } else {
                        var html = '<tr class="tr"><td class="td" style="text-align:center;">'+nroAnotacion+'</td><td class="td" style="text-align:center;">'+fecAnotacion+'</td><td class="td" style="text-align:center;">'+eveAnotacion+'</td><td class="td" style="text-align:center;">'+obsAnotacion+'</td></tr>';
                    }

                    $(".tablaAnotaciones").append(html);
                }
            } else {
                $(".tablaAnotaciones").empty();
            }

            if (result.antlaborales != null) {
                $(".tablaAntLaborales").empty();
                // $(".tablaAntLaborales").html('<thead style="background-color:#f5f5f5;"><tr><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Número</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Empresa</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Fecha Desde</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Fecha Hasta</td></tr></thead>');
                
                for (var i = 0; i < result.antlaborales.length; i++) {
                    var nroAntLaboral = result.antlaborales[i].FUNC_NRO_ANTECEDENTE;
                    var empAntLaboral = result.antlaborales[i].FUNC_EMPRESA;
                    var desAntLaboral = result.antlaborales[i].FUNC_FECHA_DESDE;
                    var hasAntLaboral = result.antlaborales[i].FUNC_FECHA_HASTA;
					var empAntLaboral = sentenceCase(empAntLaboral);
                    if (i != (result.antlaborales.length - 1)) {
                         var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
                    } else {
                        var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
                    }

                    $(".tablaAntLaborales").append(html);
                    $(".tablaAntLaborales").show();
					$("#titinfolaboral").fadeIn();
                }
            } else {
                $(".tablaAntLaborales").hide().empty();
				$("#titinfolaboral").hide();
            }

            if (result.movimientos != null ){
                var auxCargo = "";
                
                $(".tablaMovimientos").empty();
                $(".tablaMovimientos").append('<thead style="background-color:#f5f5f5;"><tr class="tr"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Desde</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Cargo</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Departamento/Oficina</td></tr></thead>');

                for (var i = 0; i < result.movimientos.length; i++) {
                    if(result.movimientos[i].cargo != auxCargo) {
                        if (i != (result.movimientos.length - 1)) {
                            var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke; text-transform:capitalize;">'+result.movimientos[i].desde+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; text-transform:capitalize;">'+jsUcfirst(result.movimientos[i].cargo)+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; text-transform:capitalize;">'+jsUcfirst(result.movimientos[i].cargo)+'</td></tr>';
                        } else {
                            var html = '<tr class="tr"><td class="td" style="text-align:center; text-transform:capitalize;">'+result.movimientos[i].desde+'</td><td class="td" style="text-align:center; text-transform:capitalize;">'+jsUcfirst(result.movimientos[i].cargo)+'</td><td class="td" style="text-align:center; text-transform:capitalize;">'+jsUcfirst(result.movimientos[i].cargo)+'</td></tr>';
                        }

                        $(".tablaMovimientos").append(html);
                        auxCargo = result.movimientos[i].cargo;
                    }
                }
            } else {
                $(".tablaMovimientos").empty();
            }
            $("#miniloading").fadeOut();

        },
        dataType:"json",
        type:"POST"
    });
}