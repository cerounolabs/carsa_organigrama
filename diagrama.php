<?php
    session_start();

    if (isset($_SESSION['id_usuario'])) {
        include "inc/resetpassword.php";
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/check.css">
        <link rel="stylesheet" type="text/css" href="css/modal.css">
        <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="css/jquery.orgchart.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/tabs.css">
        <link rel="stylesheet" type="text/css" href="css/easy-autocomplete.css">
        <link rel="stylesheet" type="text/css" href="css/easy-autocomplete.min.css">
        <link rel="stylesheet" type="text/css" href="css/easy-autocomplete.themes.css">
        <link rel="stylesheet" type="text/css" href="css/easy-autocomplete.themes.min.css">
        <link rel="stylesheet" type="text/css" href="css/diagrama.css" media="screen">

        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/html2canvas.min.js"></script>
        <script type="text/javascript" src="js/jspdf.min.js"></script>
        <script type="text/javascript" src="js/jquery.easy-autocomplete.js"></script>
        <script type="text/javascript" src="js/jquery.easy-autocomplete.min.js"></script>

        <link rel="icon" href="">

        <title>Organigrama</title>
    </head>
    
    <body>
        <nav class="navbar navbar-inverse" style="border-radius:0px;    border-bottom: 17px solid;margin:0px">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"></a>
                </div>
                
                <div class="collapse navbar-collapse" id="myNavbar">
                    <ul class="nav navbar-nav" style="background:black">
                        <li class="active" style="padding:0px;"><a href="diagrama.php" style="background-color: #222222; font-size: 150%">Organigrama</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right" style="background: #222222;!important">
<?php 
    if ($_SESSION['rankings'] == "1") {
?>
                        <li style="padding:0px;background: #222222;!important"><a href="rankings"><span class="glyphicon glyphicon-stats"></span> Rankings</a></li>


<?php
	}
?>
                        <li style="padding:0px;background: #222222;!important"><a href="#" data-url="<?php if(isset($_SESSION['nombre'])){echo "diagrama.php";}else{echo "loginUser.php";} ?>" id="salir"><span class="glyphicon glyphicon-log-in"></span> <?php if(isset($_SESSION['nombre'])){echo "Cerrar sesión";}else{echo "Iniciar sesión";} ?></a></li>
<?php
    if (isset($_SESSION['id_usuario'])) {
?>
                        <li style="padding:0px;background: #222222;!important"><a  href="nuevacontrasena.php"><span class="glyphicon glyphicon-lock"></span> Cambiar contraseña</a></li>
<?php
    }
?>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid text-center" style="padding:0px">
            <div class="fullContent">
                <div class="divGrupos" >
                    <div class="background-theme" style="display: flex;height: 69px;">
                        <div id="gtitulo" style="width: 70%;">
                            <h2 class="h2Grupos">Gerencias </h2>
                        </div>
                        
                        <div id="arrows" style="width: 30%;">
                            <center>
                                <span>
                                    <i class="fa fa-arrow-left idissmising" aria-hidden="true" style="/* float: right; */color: white;/* background: aliceblue; */width: 30px;height: 30px;cursor: pointer;margin-top: 25%;font-size: 27px;"></i>
                                    <i class="fa fa-arrow-right idissmising" aria-hidden="true" style="display:none;color: white;  /* background: aliceblue; */width: 30px;height: 30px;cursor: pointer;margin-top: 25%;font-size: 27px;"></i>
                                </span>
                            </center>
                        </div>
                    </div>

                    <div class="contenidogrupo" style="overflow:auto;">
	                    <div class="" style="display:flex; padding-left:5px; margin:5px; padding-right:5px;">
                            <input type="text" name="" id="inputbuscarcolaborador" class="form-control" value="" placeholder="Nombre">
                            <button type="button" name="button" class="btn btnbuscarcolaborador background-theme" ><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>

                        <div class="">
                            <table id = "resumen" class="tableRes" style="display:none"></table>
                        </div>
                    </div>
                </div>

                <div id="chart-container2" class="divDiagrama" style="overflow-x:scroll;display:none" >
                    <center>
                        <img src="img/loading-animation.gif" alt="Smiley face" width="100" height="100" style="margin-top: 25%;">
                    </center>
                </div>

                <div id="chart-container" class="divDiagrama" style="overflow-x:scroll; ">
                    <span style="">
                        <i class="fa fa-sitemap" aria-hidden="true" style="margin-top: 25%;font-size:210px;color:#0000001f;"></i>
                        <br>
                        <span style="font-size: 25px;color: #bbb8b8;">Elija una categoria</span>
                    </span>
                </div>
            </div>

            <div class="modal-overlay">
                <div class="modal" style="width:auto; overflow-y:auto; max-height:100vh;">
                    <a class="close-modal">
                        <svg viewBox="0 0 20 20">
                            <path fill="#000000" d="M15.898,4.045c-0.271-0.272-0.713-0.272-0.986,0l-4.71,4.711L5.493,4.045c-0.272-0.272-0.714-0.272-0.986,0s-0.272,0.714,0,0.986l4.709,4.711l-4.71,4.711c-0.272,0.271-0.272,0.713,0,0.986c0.136,0.136,0.314,0.203,0.492,0.203c0.179,0,0.357-0.067,0.493-0.203l4.711-4.711l4.71,4.711c0.137,0.136,0.314,0.203,0.494,0.203c0.178,0,0.355-0.067,0.492-0.203c0.273-0.273,0.273-0.715,0-0.986l-4.711-4.711l4.711-4.711C16.172,4.759,16.172,4.317,15.898,4.045z"></path>
                        </svg>
                    </a>

                    <div class="modal-content" style="width: 100%;height: 100%;">
                        <center style="border:2px solid #464141;padding:10px;border-radius:12px;">
                            <div class="">
                                <div class="" style="width:100%">
                                    <div class="" style="height:80px;">
                                        <img src="img/logoCarsa.png" style="max-height: 100%;float:left;" alt="CARSA logo">
                                    </div>
                            
                                    <div class="imgProfile" ></div>
                                    <h2 id="nombre" style="font-size: 18px;"></h2>
                                </div>

                                <div class="" style="width:100%;text-align:left;">
                                    <div class="" style="height:auto !important;">
                                        <div class="" style="width:100%;min-height:auto;text-align:left;"  id="">
                                            <strong><label for="">&nbsp;</label> </strong> <label for="" id="infoCodigo" name="infoCodigo" style="text-transform:capitalize; display:none;"></label> <br>
                                            <strong><label for="">Organigrama:&nbsp;</label> </strong> <label for="" id="infoOrganigrama" name="infoOrganigrama" style="text-transform:capitalize;"></label> <br>
                                            <strong><label for="">Nro. C.I.:&nbsp;</label> </strong> <label for="" id="infoNroDocumento" name="infoNroDocumento" style="text-transform:capitalize;"></label> <br>
                                            <strong><label for="">Gerencia:&nbsp;</label> </strong> <label for="" id="infoGerencia" name="infoGerencia" style="text-transform:capitalize;"></label> <br>
                                            <strong><label for="" id="titSuperior">Superior:&nbsp;</label> </strong> <label for="" id="infoSuperior" name="infoSuperior" style="text-transform:capitalize;"></label> <br id="saltSuperior">
                                            <strong><label for="">Cargo:&nbsp;</label> </strong> <label for="" id="infoCargo" style="text-transform:capitalize;"></label> <br>
                                            <strong><label for="">Antig&uuml;edad:&nbsp;</label> </strong><label for="" id="infoantiguedad"></label><br>
                                            <strong><label for="">Fecha Ingreso:&nbsp;</label> </strong> <label for="" id="infoFechaIngreso" name="infoFechaIngreso" style="text-transform:capitalize;"></label> <br>
                                            <strong><label for="" id="titinfogrado">Antecedentes Acad&eacute;micos:&nbsp;</label></strong>
                                            
                                            <div for="" id="infogrado"></div>
                                            
											<strong><label for="" id="titinfolaboral">Antecedentes Laborales:</label></strong>
                                            
                                            <div for="" class="tablaAntLaborales"></div>
											
                                            <strong><label for="" style="display:none">Universidad:&nbsp;</label></strong><label style="display:none" for="" id="infouni"></label>
                                            <strong><label for="" style="display:none">Masterado:&nbsp;</label></strong><label for="" style="display: none" id="infomasterado"></label>
                                            <strong><label for="" style="display:none">Diplomado:&nbsp;</label></strong><label for="" style="display: none" id="infodiplomado"></label>
                                            <strong><label for="">Edad:&nbsp;</label></strong><label for="" id="infoedad"></label><br>
                                            <strong><label for="" id="titqbackup">Quien es su backup:&nbsp;</label></strong><label for="" id="infouni"></label><br>
                                    
                                            <div id="qbackup" >-</div>

                                            <strong>  <label for="" id="titdbackup">Backup de quien es: </label></strong><label for="" id="infouni"></label><br>
                                            
                                            <div id="dbackup" >-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <center><img src="img/miniloading.gif"  id= "miniloading" alt="Smiley face" height="42" width="42"></center>
                            
                            <div class="accordionM" style="background: white;padding:0px;border-radius: 10px;">
                                <h2 class="acc_trigger" style="margin:0px"><i class="fa fa-angle-down arow" aria-hidden="true" style="font-size:30px;cursor:pointer;"></i></h2>
                                <div class="acc_container">      
                                    <div>
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <div class="">
                                                    <div class="w3-bar w3-black">
                                                        <button class="w3-bar-item w3-button tablink background-tab" onclick="openTab(event, 'Productividad')">Productividad</button>
                                                        <button class="w3-bar-item w3-button tablink" onclick="openTab(event, 'Salario')">Salario</button>
                                                        <button class="w3-bar-item w3-button tablink" onclick="openTab(event, 'Disciplina')">Disciplina</button>
                                                        <button class="w3-bar-item w3-button tablink" onclick="openTab(event, 'Carrera')">Carrera</button>
                                                        <button class="w3-bar-item w3-button tablink" onclick="openTab(event, 'Documentos')">Documentos</button>
                                                        <button class="w3-bar-item w3-button tablink" onclick="openTab(event, 'Capacitaciones')">Capacitaciones</button>
                                                        <button class="w3-bar-item w3-button tablink" onclick="openTab(event, 'Anotaciones')">Anotaciones</button>
                                                        <button  style="display:none !important" class="w3-bar-item w3-button tablink" onclick="openTab(event, 'AntLaborales')">Antecedentes Laborales</button>
                                                        <button class="w3-bar-item w3-button tablink" onclick="openTab(event, 'Ficha')">Ficha personal</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="Productividad" class="w3-container w3-border city" style="height: 208px;overflow:auto;">
                                        <div class="filter" style="display:flex" >
                                            <input type="date" name="inilogros" id="inilogros" class="form-control" value="" placeholder="Desde">
                                            <input type="date" name="endlogros" id="endlogros" class="form-control" value="" placeholder="Hasta">
                                            <button type="button" name="button" class="btn btnFiltrarProductividad"><i class="fa fa-filter" aria-hidden="true"></i></button>
                                        </div>

                                        <table class="table2 tablaLogros">
                                            <strong>
                                                <thead>
                                                    <tr class="tr">	
                                                        <td class="td" style="text-align:center;"> Meses </td>
                                                        <td class="td" style="text-align:center;"> Meta </td>
                                                        <td class="td" style="text-align:center;"> Logrado </td>
                                                        <td class="td" style="text-align:center;"> Ratio </td>	
                                                    </tr>
                                                </thead>
                                            </strong>
                                        </table>
                                    </div>

                                    <div id="Salario" class="w3-container w3-border city" style="display:none ; height: 208px;overflow:auto;">
                                        <div class="filter" style="display:flex" >
                                            <input type="date" name="inisalario" id="inisalario" class="form-control" value="" placeholder="Desde">
                                            <input type="date" name="endsalario" id="endsalario" class="form-control" value="" placeholder="Hasta">
                                            <button type="button" name="button" class="btn btnFiltrarSalario"><i class="fa fa-filter" aria-hidden="true"></i></button>
                                        </div>

                                        <table class="table2 tablaSalario">
                                            <strong>
                                                <thead>
                                                    <tr class="tr">
                                                        <td class="td" style="text-align:center;"> Meses </td>
                                                        <td class="td" style="text-align:center;"> Salario Variable(SV) </td>
                                                        <td class="td" style="text-align:center;"> Salario Fijo(SF) </td>
                                                        <td class="td" style="text-align:center;"> Salario Total(ST) </td>
                                                        <td class="td" style="text-align:center;"> Aguinaldo </td>
                                                        <td class="td" style="text-align:center;"> IPS </td>
                                                    </tr>
                                                </thead>
                                            </strong>
                                        </table>
                                    </div>

                                    <div id="Disciplina" class="w3-container w3-border city" style="display:none; height:208px; overflow:auto;">
                                        <div class="filter" style="display:flex">
                                            <input type="date" name="inieventos" id="inieventos" class="form-control" value="" placeholder="Desde">
                                            <input type="date" name="endeventos" id="endeventos" class="form-control" value="" placeholder="Hasta">
                                            <button type="button" name="button" class="btn btnFiltrarEventos"><i class="fa fa-filter" aria-hidden="true"></i></button>
                                        </div>

                                        <table class="table2 tablaEventos">
                                            <thead>
                                                <tr>
                                                    <td class="td" style="text-align:center;">Licencias Justificadas/Injustificadas</td>
                                                    <td class="td" style="text-align:center;">C&oacute;digos</td>
                                                    <td class="td" style="text-align:center; display:none;">Fecha</td>
                                                    <td class="td" style="text-align:center;">Cantidad Total</td>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>

                                    <div id="Carrera" class="w3-container w3-border city" style="display:none;  height: 208px;overflow:auto;">
                                        <div class="filter" style="display:flex" >
                                            <input type="date" name="inimovimientos" id="inimovimientos" class="form-control" value="" placeholder="Desde">
                                            <input type="date" name="endmovimientos" id="endmovimientos" class="form-control" value="" placeholder="Hasta">
                                            <button type="button" name="button" class="btn btnFiltrarMovimientos"><i class="fa fa-filter" aria-hidden="true"></i></button>
                                        </div>

                                        <table class="table2 tablaMovimientos">
                                            <strong>
                                                <thead>
                                                    <tr class="tr">
                                                        <td class="td"> Desde </td>
                                                        <td class="td"> Cargo </td>
                                                        <td class="td"> Departamento/Oficina </td>
                                                        <td class="td" style="display:none;"> Unidad de Negocios </td>
                                                    </tr>
                                                </thead>
                                            </strong>
                                        </table>
                                    </div>

                                    <div id="Documentos" class="w3-container w3-border city" style="display:none; height:208px; overflow:auto;">
                                        <div class="filter" style="display:none">
                                            <input type="date" name="inidocumento" id="inidocumento" class="form-control" value="" placeholder="Desde">
                                            <input type="date" name="enddocumento" id="enddocumento" class="form-control" value="" placeholder="Hasta">
                                            <button type="button" name="button" class="btn btnFiltrarDocumentos"><i class="fa fa-filter" aria-hidden="true"></i></button>
                                        </div>

                                        <table class="table2 tablaDocumentos">
                                            <thead>
                                                <tr>
                                                    <td class="td"></td>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>

                                    <div id="Capacitaciones" class="w3-container w3-border city" style="display:none ; height: 208px;overflow:auto;">
                                        <div class="filter" style="display:none" >
                                            <input type="date" name="inicapacitacion" id="inicapacitacion" class="form-control" value="" placeholder="Desde">
                                            <input type="date" name="endcapacitacion" id="endcapacitacion" class="form-control" value="" placeholder="Hasta">
                                            <button type="button" name="button" class="btn btnFiltrarCapacitacion"><i class="fa fa-filter" aria-hidden="true"></i></button>
                                        </div>

                                        <table class="table2 tablaCapacitaciones">
                                            <strong>
                                                <thead>
                                                    <tr class="tr">
                                                        <td class="td" style="text-align:center;"> N&uacute;mero</td>
                                                        <td class="td" style="text-align:center;"> Empresa </td>
                                                        <td class="td" style="text-align:center;"> Curso </td>
                                                        <td class="td" style="text-align:center;"> A&ntilde;o </td>
                                                        <td class="td" style="text-align:center;"> Mes </td>
                                                        <td class="td" style="text-align:center;"> Horas </td>
                                                    </tr>
                                                </thead>
                                            </strong>
                                        </table>
                                    </div>

                                    <div id="Anotaciones" class="w3-container w3-border city" style="display:none ; height: 208px;overflow:auto;">
                                        <div class="filter" style="display:none" >
                                            <input type="date" name="inianotacion" id="inianotacion" class="form-control" value="" placeholder="Desde">
                                            <input type="date" name="endanotacion" id="endanotacion" class="form-control" value="" placeholder="Hasta">
                                            <button type="button" name="button" class="btn btnFiltrarAnotacion"><i class="fa fa-filter" aria-hidden="true"></i></button>
                                        </div>

                                        <table class="table2 tablaAnotaciones">
                                            <strong>
                                                <thead>
                                                    <tr class="tr">
                                                        <td class="td" style="text-align:center;"> N&uacute;mero</td>
                                                        <td class="td" style="text-align:center;"> Fecha </td>
                                                        <td class="td" style="text-align:center;"> Tipo </td>
                                                        <td class="td" style="text-align:center;"> Observaci&oacute;n </td>
                                                    </tr>
                                                </thead>
                                            </strong>
                                        </table>
                                    </div>

                                    <div id="AntLaborales" class="w3-container w3-border city" style="display:none !important; height: 208px;overflow:auto;">
                                        <div class="filter" style="display:none" >
                                            <input type="date" name="iniantlaboral" id="iniantlaboral" class="form-control" value="" placeholder="Desde">
                                            <input type="date" name="endantlaboral" id="endantlaboral" class="form-control" value="" placeholder="Hasta">
                                            <button type="button" name="button" class="btn btnFiltrarAntLaboral"><i class="fa fa-filter" aria-hidden="true"></i></button>
                                        </div>

                                        <table class="table2 tablaAntLaborales">
                                            <strong>
                                                <thead>
                                                    <tr class="tr">
                                                        <td class="td" style="text-align:center;"> N&uacute;mero </td>
                                                        <td class="td" style="text-align:center;"> Empresa </td>
                                                        <td class="td" style="text-align:center;"> Fecha Desde </td>
                                                        <td class="td" style="text-align:center;"> Fecha Hasta </td>
                                                    </tr>
                                                </thead>
                                            </strong>
                                        </table>
                                    </div>
                                    
                                    <div id="Ficha" class="w3-container w3-border city" style="display:none;  height: 208px;overflow:auto;">
                                        <div class="" id="datosdependencia" style="" >
                                            <center>
                                                <table class="table2">
                                                    <tr class="tr">
                                                        <td><label>Dependencia Familiar:</label></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr class="tr">
                                                        <td class="td"> Cantidad de personas que dependen econ&#243;micamente: </td>
                                                        <td class="td"> <label id="cant_depen">-</label></td>
                                                    </tr>
                                                    <tr class="tr">
                                                        <td class="td"> Cantidad de personas que contribuyen con los gastos: </td>
                                                        <td class="td"> <label id="cant_cont">-</label>	</td>
                                                    </tr>
                                                    <tr class="tr">
                                                        <td class="td"> Tipo de vivienda: </td>
                                                        <td class="td"> <label id="vivienda">-</label></td>
                                                    </tr>
                                                    <tr class="tr">
                                                        <td class="td"> Movilidad propia: </td>
                                                        <td class="td"> <label id="movilidad">-</label></td>
                                                    </tr>
                                                </table>
                                                
                                                <br>
                                            </center>
                                        </div>

                                        <table class="table2 tabladependencia">
                                            <strong>
                                                <thead>
                                                    <tr class="tr">
                                                        <td class="td">	Familiares Directos: </td>
                                                        <td class="td"> Nombre y apellido </td>
                                                    </tr>
                                                </thead>
                                            </strong>
                                        </table>
                                            
                                        <br>
                                        <table  class="table2 tablahobbies" style="display: none;" >
                                            <strong>
                                                <thead>
                                                    <tr class="tr">
                                                        <td class="td"> Hobbie </td>
                                                        <td class="td"> Observaci&#243;n</td>
                                                    </tr>
                                                </thead>
                                            </strong>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </center>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/jquery.mockjax.min.js"></script>
        <script type="text/javascript" src="js/jquery.orgchart.js"></script>
        <script type="text/javascript" src="js/configuraciones.js"></script>
        <script src="js/jquery.easy-autocomplete.js"></script>
        <script src="js/jquery.easy-autocomplete.min.js"></script>
        <script src="js/vistacolaborador.js"></script>
        <script src="js/logregister.js"></script>

        <script>
			$(document).on("click",".accordionList",function(){
				$(".contenidogrupo .background-accordionList").removeClass("seleccionado_boton");
				$(this).toggleClass("seleccionado_boton");
				
			})
		
            function jsUcfirst(string) {
                string = string.toLowerCase();
                string = string.replace("subgerente", "sub gerente");
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            function openTab(evt, cityName) {
                var i, x, tablinks;
                x = document.getElementsByClassName("city");
                
                for (i = 0; i < x.length; i++) {
                    x[i].style.display = "none";
                }
                
                tablinks = document.getElementsByClassName("tablink");
                
                for (i = 0; i < x.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" background-tab", "");
                }
                
                document.getElementById(cityName).style.display = "block";
                evt.currentTarget.className += " background-tab";
                registrar("tabla de "+cityName+" seleccionada");
            }
        </script>
        
        <script>
            $(".btnbuscarcolaborador").click(function(){
                var colaborador = $("#inputbuscarcolaborador").val();
                colaborador     = colaborador.split("-");
                cargartablas(localStorage.getItem("idcolaboradorbusqueda"));
            })

            var options = {
                url: "inc/colaboradores.php?listar=listar",
                getValue: "nombre",
                list: {
                    onClickEvent: function() {
                        var value       = $("#inputbuscarcolaborador").getSelectedItemData().codigo;
                        var codFunc     = $("#inputbuscarcolaborador").getSelectedItemData().codigo;
			            var cargo       = $("#inputbuscarcolaborador").getSelectedItemData().cargo;
			            var antiguedad  = $("#inputbuscarcolaborador").getSelectedItemData().antiguedad;
			            var img         = $("#inputbuscarcolaborador").getSelectedItemData().img;
			            var nombre      = $("#inputbuscarcolaborador").getSelectedItemData().nombre;
                        
			            localStorage.setItem("colaboradordatos",nombre+"||"+cargo+"||"+antiguedad+"||"+img+"||"+codFunc);
			            localStorage.setItem("idcolaboradorbusqueda",value);
                    },
                    match: {
                        enabled: true,
                        method: function(element, phrase) {
                            if (element.search(phrase) > -1) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                        /*method: function(element, phrase) {
                            return (element.lastIndexOf(phrase, 0) === 0);
                        }*/
                    }
                },
                theme: "square"
            };

            $("#inputbuscarcolaborador").easyAutocomplete(options);
            $("#inputbuscarcolaborador").addClass( "form-control" );
            $(".easy-autocomplete eac-square").css("width","100%");
        </script>
        
        <script type="text/javascript">
            function toTitleCase(str) {
                return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
            }
            
            function CargaDiagrama(url) {
                $("#chart-container").hide();
                $("#chart-container2").show();
                $("ul").css("border-right","0px");
                $("li").css("border-right","0px");

                $(this).css("color","#bbb");
                $(this).children().css("border-right","4px solid");
                $(this).children().css("background","#2c5c9e");
                $(this).css("border-right","4px solid");
                $(this).css("background","#2c5c9e");
                $(this).children("li").css("background","#2c5c9e");

                var json=$(this).data("json");

                $("#chart-container").empty();

                $(function() {
                    var contenido;
                    $.ajax({
                        url: url,
                        success: function(result) {
                            $("#resumen").html("");
                            var sum = 0;
                            
                            for (var i = 0; i < result.estructura.length; i++) {
                                sum = sum +parseInt(result.estructura[i].cantidad);
                                var html='<tr class= "tr"><td align="left" style="text-align: left;" ><span>'+result.estructura[i].cargo+'</span></td><td align="right"><span>'+result.estructura[i].cantidad+'</span></td></tr>';
                                $("#resumen").append(html);
                            }
                        
                            var htmlsum ='<tr class= "tr"><td align="left" style="text-align: left;" ><span><strong>Total</strong></span></td><td align="right"><span>'+sum+'</span></td></tr>';
                            $("#resumen").append(htmlsum);
                            $("#resumen").fadeIn();
                        
                            contenido = result;

                            $.mockjax({
                                url: '/orgchart/initdata',
                                responseTime: 1000,
                                contentType: 'application/json',
                                responseText:contenido
                            });

                            var ajaxURLs = {
                                'children': '/orgchart/children/',
                                'parent': '/orgchart/parent/',
                                'siblings': function(nodeData) {
                                    return '/orgchart/siblings/' + nodeData.id;
                                },
                                'families': function(nodeData) {
                                    return '/orgchart/families/' + nodeData.id;
                                }
                            };

                            var oc = $('#chart-container').orgchart({
                                'data' : contenido,
                                'ajaxURL': ajaxURLs,
                                'nodeContent': 'title'
                            });
                        
                            oc.init({ 
                                'data': contenido 
                            });

                            function toTitleCase(str) {
                                return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
                            }

                            setTimeout(function(){
                                $( ".node" ).each(function() {
                                    var cont = $(this).children("div.title").html();

                                    if(cont == null ){
                                        cont = "0;0;0;0;0;0;0;0;0"
                                    }

                                    var datos   = cont.split(";");
                                    var cargo   = $(this).children("div.content").html();
                                    var btnDown2= $(this).find(".bottomEdge");
                                
                                    $(this).attr("data-id",datos[5])

                                    if(cargo.length > 18) {
                                        var res1 = cargo;
                                        var res2 = "</div><div style='height:14px;font-size: 14px;color: rgba(0, 0, 0, 0.6);word-wrap: break-word;white-space: pre-wrap;'></div>"

                                        var res = res1+res2
                                        res     = res.toLowerCase();
                                        res     = res.substr(0,1).toUpperCase()+res.substr(1);
                                    } else {
                                        var res = cargo;
                                        res     = res.toLowerCase();
                                        res     = res.substr(0,1).toUpperCase()+res.substr(1);
                                    }

                                    var icon = "";
                                    
                                    if(btnDown2.length == 0 ){
                                        icon = '<i class="edge verticalEdge bottomEdge fa" style="position:relative"></i>';
                                    }

                                    if($(this).hasClass("node2")) {

                                    } else {
                                        var nombre      = datos[0].split(" ");
                                        var nombreDef   = toTitleCase( datos[0]);
                                        
                                        nombreDef   = "<strong>"+nombreDef+"</strong>";
                                        res         = res.toLowerCase();
                                        res         = res=res.substr(0,1).toUpperCase()+res.substr(1);
                                        res         = res.replace("&Amp;","")

                                        if (datos[2] === "true") {
                                            $(this).html("");
                                            $(this).removeClass("node node2")
                                            $(this).addClass("ghost");
                                            $(this).attr("data-id",datos[5])

                                            var img = datos[1].split("/");
                                            datos[1]= "http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/fotos/192.168.16.116:8081/"+img[(img.length-1)];

                                            var html = '<div style="width:50%;border-right: 3px solid #aaa;display: flex;">'
                                                +'<div style="width: 50%;"></div>'
                                                +'<div style="width: 50%;display: flex; ">'
                                                +'<div style="width: 63%;margin-left: 200px;">'
                                                +'<div class="node node2" data-id="'+datos[5]+'" style="float: right;"><div class="content background-border" style="height: 100%;border-radius: 13px;background: #fffcfc;border: 2px dashed #549bd9;">'
//                                                +'<div class="divSup infoContent background-theme" style="background:#aaa" data-json="'+datos[4]+'" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(cargo)+'">'
                                                +'<div class="divSup infoContent background-theme" style="background:#aaa" data-json="'+datos[4]+'" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(cargo)+'" data-codFunc="'+datos[5]+'">'
                                                +'<img src="'+datos[1]+'" alt="" height=61 width=65 class="img">'
                                                +'</div>'
                                                +'<div class="divInfer">'
                                                +'<div class="" style="height: 39%;"></div>'
                                                +'<div class="" style="height: 65%;">'
                                                +'<div style="height: 14px;"><spam class="name">'+nombreDef+'</spam></div><div style="height:14px; word-wrap:break-word; white-space:pre-wrap; text-transform:capitalize;" class="name">'+res+'</div>'
                                                +'</div></div></div></div></div></div>'
                                                +'<div style="width:30%;float:right;">'
                                                +'<br><br><div style="height: 2px; border-top: 6px #aaa; border-top-style: dashed;"></div></div></div></div> <div style="width:50%;border-left: 3px solid #aaa;"></div>';
                                            $(this).html(html);
                                        } else {
                                            var dato_seccion = $(".seleccionado_boton").data("id");
											
                                            if (datos[3] != "") {
                                                
                                                var img     = datos[1].split("/");
                                                datos[1]    = "http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/fotos/192.168.16.116:8081/"+img[(img.length-1)];
                                                var height  = datos[3];
                                                
                                            if(dato_seccion == 31 || dato_seccion == null){
													 if (datos[5] == 24 || datos[5] == 242 || datos[5] == 23 || datos[5] == 1318) {
													// height = 50;
													}
		   
													if (datos[5] == 2463 || datos[5] == 186 || datos[5] == 1957) {
														height = 200;
													}
													
													if( datos[5] == 1686 ){
														height = 250;
													}
													
													if ((datos[5] == 1956 || datos[5] == 2189) && height != 0) {
														height = 150;
													}
											}else {
													 if (datos[5] == 24 || datos[5] == 242 || datos[5] == 23 || datos[5] == 1318) {
														// height = 50;
													}

													if ((datos[5] == 1956 || datos[5] == 2189) && height != 0) {
														height = 150;
													}
												}

                                            var htmlJump= '<div class="var_hor" style="display: flex;  height: '+height+'px;"><did style="background: white;height: 100%;  width: 50%;border-right: 3px solid #aaaaaa;"></did>  <div style="background: white;height: 100%;  width: 50%;  border-left: 3px solid #aaaaaa;"> </div></div>';

                                            $(this).addClass( "node2" );
                                            $(this).html("");
                                            $(this).attr("data-id", datos[5])

                                            var html    ='<div class="content background-border" style="height: 100%;">'
//                                                +'<div class="divSup infoContent background-theme" data-json="'+datos[4]+'" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(cargo)+'">'
                                                +'<div class="divSup infoContent background-theme" data-json="'+datos[4]+'" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(cargo)+'" data-codFunc="'+datos[5]+'">'
                                                +'<img src="'+datos[1]+'" alt=""  height=61 width=65 class="img">'
                                                +'</div>'
                                                +'<div class="divInfer">'
                                                +'<div class="" style="height: 39%;"></div>'
                                                +'<div class="" style="height: 65%;">'
                                                +'<div style="height: 14px;"><spam class="name">'+nombreDef+'</spam></div><div style="height:14px; word-wrap:break-word; white-space:pre-wrap; text-transform:capitalize;" class="name">'+res+'</div>'
                                                +'</div></div></div>'+icon+'</div>';

                                            $(this).parent().prepend(htmlJump);
                                            $(this).html(html);
                                        } else {
                                            var img     = datos[1].split("/");
                                            datos[1]    = "http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/fotos/192.168.16.116:8081/"+img[(img.length-1)];

                                            $(this).addClass("node2");
                                            $(this).html("");
                                            $(this).attr("data-id",datos[5])

                                            var html ='<div class="content background-border" style="height: 100%;">'
//                                                +'<div class="divSup infoContent background-theme" data-json="'+datos[4]+'" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(cargo)+'">'
                                                +'<div class="divSup infoContent background-theme" data-json="'+datos[4]+'" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(cargo)+'" data-codFunc="'+datos[5]+'">'
                                                +'<img src="'+datos[1]+'" alt="" height=61 width=65 class="img">'
                                                +'</div>'
                                                +'<div class="divInfer">'
                                                +'<div class="" style="height: 39%;"></div>'
                                                +'<div class="" style="height: 65%;">'
                                                +'<div style="height: 14px;"><spam class="name">'+nombreDef+'</spam></div><div style="height:14px; word-wrap:break-word; white-space:pre-wrap; text-transform:capitalize;" class="name">'+res+'</div>'
                                                +'</div></div></div>'+icon+'</div>';

                                            $(this).html(html);
                                        }
                                    }
                                }
                            });

                            $.ajax({
                                url:"inc/funciones.php?funcion=selectTransversales",
                                success: function(result) {
                                    if(result != null) {
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

                                        if(result.informacion != null) {
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

                                        if(result.dependencia != null) {
                                            for (var i = 0; i < result.dependencia.length; i++) {
                                                var relacion    = result.dependencia[i].PARENTESCO;
                                                var nombre      = result.dependencia[i].NOMBRE_COMPLETO_DEP;
                                                var html        = '<tr class="tr"><td class="td">'+relacion+'</td><td class="td">'+nombre+'</td></tr>';
                                                $(".tabladependencia").append(html);
                                            }

                                        }
                                        
                                        if(result.academico != null) {
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
										if(result.antlaborales != null) {
                                            $(".tablaAntLaborales").empty();
                                            
                                            
                                            for (var i = 0; i < result.antlaborales.length; i++) {
                                                var nroAntLaboral = result.antlaborales[i].FUNC_NRO_ANTECEDENTE;
                                                var empAntLaboral = result.antlaborales[i].FUNC_EMPRESA;
                                                var desAntLaboral = result.antlaborales[i].FUNC_FECHA_DESDE;
                                                var hasAntLaboral = result.antlaborales[i].FUNC_FECHA_HASTA;

                                                if (i != (result.antlaborales.length - 1)) {
                                                    var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
                                                } else {
                                                     var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
                                                }
												$("#titinfolaboral").fadeIn();
                                                $(".tablaAntLaborales").append(html).fadeIn();
                                            }
                                        } else {
                                            $(".tablaAntLaborales").hide().empty();
                                            $("#titinfolaboral").hide();
                                        }

                                        if(result.hobbies != null) {
                                            for (var i = 0; i < result.hobbies.length; i++) {
                                                var hobbie      = result.hobbies[i].HOBBIE;
                                                var OBSERVACION = result.hobbies[i].OBSERVACION;
                                                var html        ='<tr class="tr"><td class="td">'+hobbie+'</td><td class="td">'+OBSERVACION+'</td></tr>';
                                                $(".tablahobbies").append(html);
                                                $(".tablahobbies").show();
                                            }
                                        } else {
                                            $(".tablahobbies").hide()
                                        }
                                        
                                        if(result.backups != null ) {
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

                                        var ultnivel = "";
                                        var ultid    = "";
                                        var ultsuper = "";
    
                                        for (var i = 0; i < result.length; i++) {
                                            var datos = result[i].name.split(";")
                                            
                                            if($("#"+result[i].superior).hasClass("transversal"+result[i].id)) {

                                            } else {
                                                var parent  = $("#"+result[i].superior).data("parent");
                                                var display = "";

                                                if(parent == undefined) {
                                                    display = "flex"
                                                } else {
                                                    display = "none"
                                                }

                                                var nombre   = datos[0].split(" ");
                                                var nombreDef= toTitleCase( datos[0]);
                                                nombreDef    = "<strong>"+nombreDef+"</strong>";
                                                var cargo    = result[i].title;

                                                if(cargo.length > 18) {
                                                    var res1 = cargo;
                                                    var res2 = "</div><div style='height:14px;font-size: 14px;color: rgba(0, 0, 0, 0.6);word-wrap: break-word;white-space: pre-wrap;'></div>"
                                                    var res  = res1+res2
                                                    res      = res.toLowerCase();
                                                    res      = res = res.substr(0,1).toUpperCase()+res.substr(1);
                                                } else {
                                                    var res = cargo;
                                                    res     = res.toLowerCase();
                                                    res     = res = res.substr(0,1).toUpperCase()+res.substr(1);
                                                    res     = res+"</div>";
                                                }

                                                if(ultnivel == result[i].nivel && ultsuper == result[i].superior) {
                                                    if(result[i].posicion == "DERECHA") {
                                                        var img  = datos[1].split("/");
                                                        datos[1] = "http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/fotos/192.168.16.116:8081/"+img[(img.length-1)];
                                                        
                                                        var html = ""
                                                            +'<div style="width: 10%;">'
                                                            +'<div style="height: 50%;  width: 100%;border-bottom: 3px solid #aaaaaa;">'
                                                            +'</div> <div style="  height: 50%;  width: 100%;border-top: 3px solid #aaaaaa;">'
                                                            +'</div>'
                                                            +'</div>'
                                                            +'<div style="width:90%">'
                                                            +'<div id="'+result[i].id+'" class="node node2" data-id="'+result[i].id+'" style="float:left;">'
                                                            +'<div class="content background-border">'
//                                                            +'<div class="divSup infoContent background-theme" data-json="" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(result[i].title)+'">'
                                                            +'<div class="divSup infoContent background-theme" data-json="" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(result[i].title)+'" data-codFunc="'+result[i].id+'">'
                                                            +'<img src="'+datos[1]+'" alt="" height="61" width="65" class="img">'
                                                            +'</div>'
                                                            +'<div class="divInfer">'
                                                            +'<div class="" style="height: 39%;">'
                                                            +'</div>'
                                                            +'<div class="" style="height: 65%;">'
                                                            +'<div style="height: 14px;">'
                                                            +'<spam class="name">'
                                                            +'<strong>'+nombreDef+'</strong>'
                                                            +'</spam>   </div>'
                                                            +'<div style="height:14px; word-wrap:break-word; white-space:pre-wrap; text-transform:capitalize;" class="name">'
                                                            +res
                                                            +'<div style="height:14px;font-size: 14px;color: Rgba(0, 0, 0, 0.6)">'
                                                            +'</div></div></div></div>'
                                                            +'</div>'
                                                            +'</div>'
                                                            +'</div> ';

                                                        $("#divderecha"+ultid).append(html);
                                                        $("#"+result[i].superior).addClass("transversal"+result[i].id);
                                                    } else {
                                                        var img  = datos[1].split("/");
                                                        datos[1] = "http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/fotos/192.168.16.116:8081/"+img[(img.length-1)];
                                                        var html = ""
                                                            +'<div style="width: 90%;">'
                                                            +'<div id="'+result[i].id+'" class="node node2" data-id="'+result[i].id+'" style="float: right;">'
                                                            +'<div class="content background-border">'
//                                                            +'<div class="divSup infoContent background-theme" data-json="TMASI" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(result[i].title)+'">'
                                                            +'<div class="divSup infoContent background-theme" data-json="TMASI" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(result[i].title)+'" data-codFunc="'+result[i].id+'">'
                                                            +'<img src="'+datos[1]+'" alt="" height="61" width="65" class="img">'
                                                            +'</div>'
                                                            +'<div class="divInfer">'
                                                            +'<div class="" style="height: 39%;">'
                                                            +'</div>'
                                                            +'<div class="" style="height: 65%;">'
                                                            +'<div style="height: 14px;">'
                                                            +'<spam class="name">'
                                                            +'<strong>'+nombreDef+'</strong>'
                                                            +'</spam>   </div>'
                                                            +'<div style="height:14px; word-wrap:break-word; white-space:pre-wrap; text-transform:capitalize;" class="name">'
                                                            +res
                                                            +'<div style="height:14px;font-size: 14px;color: Rgba(0, 0, 0, 0.6)">'
                                                            +'</div></div></div></div>'
                                                            +'</div></div>'
                                                            +'<div style="width:10%"><div style="height: 50%;  width: 100%;border-bottom: 3px solid #aaaaaa;">'
                                                            +'</div> <div style="height: 50%;  width: 100%;border-top: 3px solid #aaaaaa;">'
                                                            +'</div></div>';

                                                        $("#divizquierda"+ultid).append(html);
                                                        $("#"+result[i].superior).addClass("transversal"+result[i].id);
                                                    }
                                                } else {
                                                    if(result[i].posicion == "DERECHA") {
                                                        var img  = datos[1].split("/");
                                                        datos[1] = "http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/fotos/192.168.16.116:8081/"+img[(img.length-1)];
                                                        
                                                        var html =
                                                            '<div id="divTransversal'+result[i].superior+'" class="divTransversal'+result[i].superior+'" style="display:'+display+';/* display: none; */">'
                                                            +'<div id="divizquierda'+result[i].id+'"  style="width:50%;background: #ffffff;display: flex;border-right: 3px solid #aaaaaa;">'
                                                            +'</div><div id="divderecha'+result[i].id+'" style="width:50%;background:#ffffff;display: flex;border-left: 3px solid #aaaaaa;">'
                                                            +'<div style="width: 10%;">'

                                                            +'<div  style="height: 50%;  width: 100%;border-bottom: 3px solid #aaaaaa;display:flex">'
                                                            +'</div> <div style="  height: 50%;  width: 100%;border-top: 3px solid #aaaaaa;">'
                                                            +'</div>'
                                                            +'</div>'
                                                            +'<div style="width:90%">'
                                                            +'<div id="'+result[i].id+'" class="node node2" data-id="'+result[i].id+'" style="float:left;">'
                                                            +'<div class="content background-border">'
//                                                            +'<div class="divSup infoContent background-theme" data-json="" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(result[i].title)+'">'
                                                            +'<div class="divSup infoContent background-theme" data-json="" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(result[i].title)+'" data-codFunc="'+result[i].id+'">'
                                                            +'<img src="'+datos[1]+'" alt="" height="61" width="65" class="img">'
                                                            +'</div>'
                                                            +'<div class="divInfer">'
                                                            +'<div class="" style="height: 39%;">'
                                                            +'</div>'
                                                            +'<div class="" style="height: 65%;">'
                                                            +'<div style="height: 14px;">'
                                                            +'<spam class="name">'
                                                            +'<strong>'+nombreDef+'</strong>'
                                                            +'</spam>   </div>'
                                                            +'<div style="height:14px; word-wrap:break-word; white-space:pre-wrap; text-transform:capitalize;" class="name">'
                                                            +res
                                                            +'<div style="height:14px;font-size: 14px;color: Rgba(0, 0, 0, 0.6)">'
                                                            +'</div></div></div></div>'
                                                            +'</div>'
                                                            +'</div>'
                                                            +'</div> </div>';
                                                    } else {
                                                        var img = datos[1].split("/");
                                                        datos[1]= "http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/fotos/192.168.16.116:8081/"+img[(img.length-1)];

                                                        var html =
                                                            '<div id="divTransversal'+result[i].superior+'" class="divTransversal'+result[i].superior+'" style="display:'+display+';/* display: none; */">'
                                                            +'<div id="divizquierda'+result[i].id+'" style="width:50%;background: #ffffff;display: flex;border-right: 3px solid #aaaaaa;">'
                                                            +'<div style="width: 90%;">'
                                                            +'<div id="'+result[i].id+'" class="node node2" data-id="'+result[i].id+'" style="float: right;">'
                                                            +'<div class="content background-border">'
//                                                            +'<div class="divSup infoContent background-theme" data-json="TMASI" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(result[i].title)+'">'
                                                            +'<div class="divSup infoContent background-theme" data-json="TMASI" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(result[i].title)+'" data-codFunc="'+result[i].id+'">'
                                                            +'<img src="'+datos[1]+'" alt="" height="61" width="65" class="img">'
                                                            +'</div>'
                                                            +'<div class="divInfer">'
                                                            +'<div class="" style="height: 39%;">'
                                                            +'</div>'
                                                            +'<div class="" style="height: 65%;">'
                                                            +'<div style="height: 14px;">'
                                                            +'<spam class="name">'
                                                            +'<strong>'+nombreDef+'</strong>'
                                                            +'</spam>   </div>'
                                                            +'<div style="height:14px; word-wrap:break-word; white-space:pre-wrap; text-transform:capitalize;" class="name">'
                                                            +res
                                                            +'<div style="height:14px;font-size: 14px;color: Rgba(0, 0, 0, 0.6)">'
                                                            +'</div></div></div></div>'
                                                            +'</div></div>'
                                                            +'<div style="width:10%"><div style="height: 50%;  width: 100%;border-bottom: 3px solid #aaaaaa;">'
                                                            +'</div> <div style="  height: 50%;  width: 100%;border-top: 3px solid #aaaaaa;">'
                                                            +'</div></div></div><div id="divderecha'+result[i].id+'" style="width:50%;background: white;border-left: 3px solid #aaaaaa;display:flex">'
                                                            +'<br><br><br><br></div> </div>';
                                                    }

                                                    $("#"+result[i].superior).parent().append(html);
                                                    $("#"+result[i].superior).addClass("transversal"+result[i].id);
                                                }

                                                $("#"+result[i].id).hover(function() {
                                                    localStorage.setItem("idPersona",$(this).data("id"));
                                                    $(this).children().addClass('transition');
                                                }, function() {
                                                    $(".node2").children().removeClass('transition');
                                                });

                                                $("#"+result[i].id).on("click", function() {
                                                    elements.addClass('active');
                                                    $("#infoedad").html("");
                                                    $("#cant_depen").html("");
                                                    $("#cant_cont").html("");
                                                    $("#vivienda").html("");
                                                    $("#movilidad").html("");
                                                    $("#qbackup").html("");
                                                    $("#dbackup").html("");
                                                    $(".tabladependencia").empty();
                                                    $(".tablahobbies").empty();
                                                    $(".tabladependencia").append('<tr class="tr"><td class="td" style="color:black" >Familiares Directos</td><td class="td" style="color:black">Nombre y Apellido</td> </tr>')
                                                    $(".tablahobbies").append('<tr class="tr"><td class="td" style="color:black">Hobbie</td><td class="td" style="color:black">Observaci&#243;n</td> </tr>')
                                                    $(".tablaLogros").empty();
                                                    $(".tablaLogros").append('<thead style="background-color:#f5f5f5;"><tr class="tr"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Meses </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Tipo de Producción </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Meta </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Logrado </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Ratio </td></tr></thead>');
                                                    $("#infogrado").html("");
                                                    $("#infoNroDocumento").html("");
                                                    $("#infoGerencia").html("");
                                                    $("#infoOrganigrama").html("");
                                                    $("#infoSuperior").html("");
                                                    $("#infoFechaIngreso").html("");

                                                    $.ajax({
                                                        url:"inc/funciones.php?funcion=selectTablas&id="+localStorage.getItem("idPersona"),
                                                        success: function(result) {
                                                            $("#infogrado").html("");
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
                                                            $("#infoNroDocumento").html("");
                                                            $("#infoGerencia").html("");
                                                            $("#infoOrganigrama").html("");
                                                            $("#infoSuperior").html("");
                                                            $("#infoFechaIngreso").html("");

                                                            if(result.informacion != null) {
                                                                $("#infogrado").html(result.informacion[0].GRADO_ACADACADEMICO);
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


                                                            if(result.dependencia != null) {
                                                                for (var i = 0; i < result.dependencia.length; i++) {
                                                                    $(".tabladependencia").fadeIn();
                                                                    $("#datosdependencia").fadeIn();
                                                                    var relacion = result.dependencia[i].PARENTESCO;
                                                                    var nombre = result.dependencia[i].NOMBRE_COMPLETO_DEP;
                                                                    var html='<tr class="tr"><td class="td">'+relacion+'</td><td class="td">'+nombre+'</td></tr>';
                                                                    $(".tabladependencia").append(html);
                                                                }
                                                            } else {
                                                                $(".tabladependencia").fadeOut();
                                                                $("#datosdependencia").fadeOut()
                                                            }

                                                            if(result.academico != null) {
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
															if(result.antlaborales != null) {
															$(".tablaAntLaborales").empty();
															
															
															for (var i = 0; i < result.antlaborales.length; i++) {
																var nroAntLaboral = result.antlaborales[i].FUNC_NRO_ANTECEDENTE;
																var empAntLaboral = result.antlaborales[i].FUNC_EMPRESA;
																var desAntLaboral = result.antlaborales[i].FUNC_FECHA_DESDE;
																var hasAntLaboral = result.antlaborales[i].FUNC_FECHA_HASTA;

																if (i != (result.antlaborales.length - 1)) {
																	 var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
																} else {
																	 var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
																}
																$("#titinfolaboral").fadeIn();
																$(".tablaAntLaborales").append(html).fadeIn();
															}
														} else {
															$(".tablaAntLaborales").hide().empty();
															$("#titinfolaboral").hide();
														}
                                                            if(result.hobbies != null) {
                                                                for (var i = 0; i < result.hobbies.length; i++) {
                                                                    var hobbie      = result.hobbies[i].HOBBIE;
                                                                    var OBSERVACION = result.hobbies[i].OBSERVACION;
                                                                    var html        = '<tr class="tr"><td class="td">'+hobbie+'</td><td class="td">'+OBSERVACION+'</td></tr>';

                                                                    $(".tablahobbies").append(html);
                                                                    $(".tablahobbies").show();
                                                                }
                                                            } else {
                                                                $(".tablahobbies").hide()
                                                            }
                                                            
                                                            if(result.backups != null) {
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
                                                                    
                                                                    if (result.logros[i].COLOR=="ROJO") {
                                                                        color = "red";
                                                                        texto = "white";
                                                                    } else if (result.logros[i].COLOR=="VERDE") {
                                                                        color = "green";
                                                                        texto = "white";
                                                                    } else {
                                                                        color = "#eae70a";
                                                                        texto = "black";
                                                                    }
                                                                    
                                                                    if (i != (result.logros.length - 1)) {
                                                                        var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+result.logros[i].PERIODO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+result.logros[i].TIPO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+meta[0]+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+logro[0]+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; background-color:'+result.logros[i].COLOR+'; color:'+result.logros[i].COLOR_TEXTO+'">'+result.logros[i].RATIO+'%</td></tr>';
                                                                    } else {
                                                                        var html = '<tr class="tr"><td class="td" style="text-align:center;">'+result.logros[i].PERIODO+'</td><td class="td" style="text-align:center;">'+result.logros[i].TIPO+'</td><td class="td" style="text-align:center;">'+meta[0]+'</td><td class="td" style="text-align:center;">'+logro[0]+'</td><td class="td" style="text-align:center; background-color:'+result.logros[i].COLOR+'; color:'+result.logros[i].COLOR_TEXTO+'">'+result.logros[i].RATIO+'%</td></tr>';
                                                                    }

                                                                    $(".tablaLogros").append(html);

                                                                    promedioLogrado = promedioLogrado + parseInt(result.logros[i].RATIO);
                                                                }
                                                                
                                                                promedioLogrado = (promedioLogrado / result.logros.length);

                                                                var htmlPromedio= '<tr class="tr" style="background-color:#f5f5f5;"><td class="td" colspan="3"></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>Promedio</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+promedioLogrado.toFixed(2)+'%</strong></td></tr>';
                                                                
                                                                $(".tablaLogros").append(htmlPromedio);
                                                            } else {
                                                                $(".tablaLogros").empty();
                                                            }
                                                            
                                                            var fijo        = 0;
                                                            var variable    = 0;
                                                            var total       = 0
                                                            var aguinaldo   = 0;
                                                            var aporte      = 0;
                                                            
                                                            if (result.salario != null) {
                                                                $(".tablaSalario").empty();
                                                                $(".tablaSalario").append('<thead style="background-color:#f5f5f5;"><tr class="tr"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Meses</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Variable(SV)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Fijo(SF)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Total(ST)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Aguinaldo</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">IPS</td></tr></thead>');

                                                                for (var i = 0; i < result.salario.length; i++) {
                                                                    fijo        = fijo+ parseInt(result.salario[i].fijo);
                                                                    variable    = variable+parseInt(result.salario[i].variable);
                                                                    total       = total +parseInt(result.salario[i].total);
                                                                    aguinaldo   = aguinaldo +parseInt(result.salario[i].aguinaldo);
                                                                    aporte      = aporte +parseInt(result.salario[i].aporte);

                                                                    var VARIABLE    = result.salario[i].variable;
                                                                    var FIJO        = result.salario[i].fijo;
                                                                    var TOTAL       = result.salario[i].total;
                                                                    var AGUINALDO   = result.salario[i].aguinaldo;
                                                                    var APORTE      = result.salario[i].aporte;

                                                                    VARIABLE        = VARIABLE.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                                    VARIABLE        = VARIABLE.split('').reverse().join('').replace(/^[\.]/,'');
                                                                    FIJO            = FIJO.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                                    FIJO            = FIJO.split('').reverse().join('').replace(/^[\.]/,'');
                                                                    TOTAL           = TOTAL.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                                    TOTAL           = TOTAL.split('').reverse().join('').replace(/^[\.]/,'');
                                                                    AGUINALDO       = AGUINALDO.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                                    AGUINALDO       = AGUINALDO.split('').reverse().join('').replace(/^[\.]/,'');
                                                                    APORTE          = APORTE.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                                    APORTE          = APORTE.split('').reverse().join('').replace(/^[\.]/,'');
                                                                    
                                                                    
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

                                                                $(".tablaSalario").append('<tr class="tr" style="background-color:#f5f5f5;"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Promedios</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+variable+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+fijo+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+total+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+aguinaldo+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+aporte+'</strong></td></tr>');
                                                            } else {
                                                                $(".tablaSalario").empty();
                                                            }

                                                            if(result.eventos != null) {
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

                                                            if(result.documentos != null) {
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

                                                            if(result.capacitaciones != null) {
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

                                                            if(result.anotaciones != null) {
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

                                                            if(result.antlaborales != null) {
																$(".tablaAntLaborales").empty();
																
																
																for (var i = 0; i < result.antlaborales.length; i++) {
																	var nroAntLaboral = result.antlaborales[i].FUNC_NRO_ANTECEDENTE;
																	var empAntLaboral = result.antlaborales[i].FUNC_EMPRESA;
																	var desAntLaboral = result.antlaborales[i].FUNC_FECHA_DESDE;
																	var hasAntLaboral = result.antlaborales[i].FUNC_FECHA_HASTA;

																	if (i != (result.antlaborales.length - 1)) {
																		var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
																	} else {
																		 var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
																	}
																	$("#titinfolaboral").fadeIn();
																	$(".tablaAntLaborales").append(html).fadeIn();
																}
															} else {
																$(".tablaAntLaborales").hide().empty();
																$("#titinfolaboral").hide();
															}

                                                            if (result.movimientos != null ) {
                                                                var auxCargo = "";

                                                                $(".tablaMovimientos").empty();
                                                                $(".tablaMovimientos").append('<thead style="background-color:#f5f5f5;"><tr class="tr"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Desde </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">  Cargo </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">  Departamento/Oficina  </td></tr></thead>');

                                                                for (var i = 0; i < result.movimientos.length; i++) {
                                                                    if(result.movimientos[i].departamento != auxCargo) {
                                                                        if (i != (result.movimientos.length - 1)) {
                                                                            var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke; text-transform:capitalize;">'+result.movimientos[i].desde+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; text-transform:capitalize;">'+jsUcfirst(result.movimientos[i].cargo)+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; text-transform:capitalize;">'+jsUcfirst(result.movimientos[i].departamento)+'</td></tr>';
                                                                        } else {
                                                                            var html = '<tr class="tr"><td class="td" style="text-align:center; text-transform:capitalize;">'+result.movimientos[i].desde+'</td><td class="td" style="text-align:center; text-transform:capitalize;">'+jsUcfirst(result.movimientos[i].cargo)+'</td><td class="td" style="text-align:center; text-transform:capitalize;">'+jsUcfirst(result.movimientos[i].departamento)+'</td></tr>';
                                                                        }

                                                                        $(".tablaMovimientos").append(html);
                                                                        auxCargo = result.movimientos[i].departamento;
                                                                    }
                                                                }
                                                            } else {
                                                                $(".tablaMovimientos").empty();
                                                            }
                                                        },
                                                        dataType:"json",
                                                        type:"POST"
                                                    });

                                                    var codFunc         = $(this).children().children(".infoContent").data("codFunc");
                                                    var nombre          = $(this).children().children(".infoContent").data("name");
                                                    var img             = $(this).children().children(".infoContent").data("img");
                                                    var cargo           = $(this).children().children(".infoContent").data("cargo");
                                                    var jsonInfo        = $(this).children().children(".infoContent").data("json");
                                                    var str             = nombre;
                                                    var nombre          = str.split("-");
                                                    var antiguedad      = $(this).children().children(".infoContent").data("anti");
                                                    var fullname        = $(this).children().children(".infoContent").data("fullname");
                                                    
                                                    $("#nombre").html(toTitleCase(fullname));
                                                    $("#infoCodigo").html(codFunc);
                                                    $("#infoCargo").html(cargo);
                                                    $("#infoantiguedad").html(antiguedad);
                                                    $(".imgProfile").attr("style","height: 63%;background-image: url("+img+");background-color: #cccccc;background-repeat:no-repeat;background-size: 100% 115%;height: 201px;width:260px;border: 3px solid #c59d4c;border-radius: 17px;");
                                                    $(".divInfo").hide();
                                                    $("#"+jsonInfo).show();
                                                });
                                            }
                                            
                                            ultnivel    = result[i].nivel;
                                            ultid       = result[i].id;
                                            ultsuper    = result[i].superior;
                                        }
                                    }
                                },
                                
                                dataType:"json",
                                type:"POST"
                            });

                            $('.node2').on( "click", function() {
                                elements.addClass('active');
                                $("#miniloading").fadeIn();
                                $(".tablaMovimientos").empty();
                                $(".tablaSalario").empty();
                                $(".tablaLogros").empty();
                                $(".tablaEventos").empty();
                                $(".tablaDocumentos").empty();
                                $(".tablaCapacitaciones").empty();
                                $(".tablaAnotaciones").empty();
                                $(".tablaAntLaborales").empty();
                                $(".tabladependencia").empty();
                                $(".tablahobbies").empty();
                                $(".tabladependencia").append('<tr class="tr"><td class="td" style="color:black">Familiares Directos</td><td class="td" style="color:black">Nombre y Apellido</td> </tr>')
                                $(".tablahobbies").append('<tr class="tr"><td class="td" style="color:black">Hobbie</td><td class="td" style="color:black">Observaci&#243;n</td> </tr>')
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

                                $.ajax({//
                                    url:"inc/funciones.php?funcion=selectTablas&id="+localStorage.getItem("idPersona"),
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
                                        $("#infoCodigo").html(localStorage.getItem("idPersona"));

                                        if(result.informacion != null) {
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

                                        if(result.dependencia != null) {
                                            $(".tabladependencia").fadeIn();
                                            $("#datosdependencia").fadeIn();
                                            
                                            for (var i = 0; i < result.dependencia.length; i++) {
                                                var relacion= result.dependencia[i].PARENTESCO;
                                                var nombre  = result.dependencia[i].NOMBRE_COMPLETO_DEP;
                                                var html    = '<tr class="tr"><td class="td">'+relacion+'</td><td class="td">'+nombre+'</td></tr>';
                                                
                                                $(".tabladependencia").append(html);
                                            }
                                        }else{
                                            $(".tabladependencia").fadeOut()
                                            $("#datosdependencia").fadeOut()
                                        }

                                        if(result.academico != null) {
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
										if(result.antlaborales != null) {
                                            $(".tablaAntLaborales").empty();
                                            
                                            
                                            for (var i = 0; i < result.antlaborales.length; i++) {
                                                var nroAntLaboral = result.antlaborales[i].FUNC_NRO_ANTECEDENTE;
                                                var empAntLaboral = result.antlaborales[i].FUNC_EMPRESA;
                                                var desAntLaboral = result.antlaborales[i].FUNC_FECHA_DESDE;
                                                var hasAntLaboral = result.antlaborales[i].FUNC_FECHA_HASTA;

                                                if (i != (result.antlaborales.length - 1)) {
                                                     var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
                                                } else {
                                                     var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
                                                }
												$("#titinfolaboral").fadeIn();
                                                $(".tablaAntLaborales").append(html).fadeIn();
                                            }
                                        } else {
                                            $(".tablaAntLaborales").hide().empty();
                                            $("#titinfolaboral").hide();
                                        }
                                        if(result.hobbies != null) {
                                            for (var i = 0; i < result.hobbies.length; i++) {
                                                var hobbie      = result.hobbies[i].HOBBIE;
                                                var OBSERVACION = result.hobbies[i].OBSERVACION;
                                                var html        = '<tr class="tr"><td class="td">'+hobbie+'</td><td class="td">'+OBSERVACION+'</td></tr>';
                                                $(".tablahobbies").append(html);
                                                $(".tablahobbies").show();
                                            }
                                        } else {
                                            $(".tablahobbies").hide()
                                        }

                                        if(result.backups != null ) {
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

                                        var promedioLogrado=0;
                                        
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

                                                result.logros[i].COLOR  = result.logros[i].COLOR.replace(/ /g , "")
                                                
                                                if (result.logros[i].COLOR == "ROJO") {
                                                    color = "red";
                                                    texto = "white";
                                                } else if (result.logros[i].COLOR == "VERDE") {
                                                    color = "green";
                                                    texto = "white";
                                                } else {
                                                    color = "#eae70a";
                                                    texto = "black";
                                                }
                                                
                                                if (i != (result.logros.length - 1)) {
                                                    var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+result.logros[i].PERIODO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+result.logros[i].TIPO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+meta[0]+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+logro[0]+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; background-color:'+result.logros[i].COLOR+'; color:'+result.logros[i].COLOR_TEXTO+'">'+result.logros[i].RATIO+'%</td></tr>';
                                                } else {
                                                    var html = '<tr class="tr"><td class="td" style="text-align:center;">'+result.logros[i].PERIODO+'</td><td class="td" style="text-align:center;">'+result.logros[i].TIPO+'</td><td class="td" style="text-align:center;">'+meta[0]+'</td><td class="td" style="text-align:center;">'+logro[0]+'</td><td class="td" style="text-align:center; background-color:'+result.logros[i].COLOR+'; color:'+result.logros[i].COLOR_TEXTO+'">'+result.logros[i].RATIO+'%</td></tr>';
                                                }

                                                $(".tablaLogros").append(html);

                                                promedioLogrado = promedioLogrado + parseInt(result.logros[i].RATIO);
                                            }
                                            
                                            promedioLogrado = (promedioLogrado / result.logros.length);

                                            var htmlPromedio= '<tr class="tr" style="background-color:#f5f5f5;"><td class="td" colspan="3"></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>Promedio</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+promedioLogrado.toFixed(2)+'%</strong></td></tr>';

                                            $(".tablaLogros").append(htmlPromedio);
                                        } else {
                                            $(".tablaLogros").empty();
                                        }
                                        
                                        var fijo        = 0;
                                        var variable    = 0;
                                        var total       = 0
                                        var aguinaldo   = 0;
                                        var aporte      = 0;

                                        if (result.salario != null) {
                                            $(".tablaSalario").empty();
                                            $(".tablaSalario").append('<thead style="background-color:#f5f5f5;"><tr class="tr"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Meses</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Variable(SV)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Fijo(SF)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Total(ST)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Aguinaldo</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">IPS</td></tr></thead>');

                                            for (var i = 0; i < result.salario.length; i++) {
                                                fijo            = fijo+ parseInt(result.salario[i].fijo)
                                                variable        = variable+parseInt(result.salario[i].variable)
                                                total           = total +parseInt(result.salario[i].total)
                                                aguinaldo       = aguinaldo +parseInt(result.salario[i].aguinaldo)
                                                aporte          = aporte +parseInt(result.salario[i].aporte)

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

                                            $(".tablaSalario").append('<tr class="tr" style="background-color:#f5f5f5;"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Promedios</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+variable+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+fijo+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+total+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+aguinaldo+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+aporte+'</strong></td></tr>');
                                        } else {
                                            $(".tablaSalario").empty();
                                        }

                                        if(result.eventos != null) {
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

                                        if(result.documentos != null) {
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

                                        if(result.capacitaciones != null) {
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

                                        if(result.anotaciones != null) {
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

                                        if(result.antlaborales != null) {
                                            $(".tablaAntLaborales").empty();
                                            
                                            
                                            for (var i = 0; i < result.antlaborales.length; i++) {
                                                var nroAntLaboral = result.antlaborales[i].FUNC_NRO_ANTECEDENTE;
                                                var empAntLaboral = result.antlaborales[i].FUNC_EMPRESA;
                                                var desAntLaboral = result.antlaborales[i].FUNC_FECHA_DESDE;
                                                var hasAntLaboral = result.antlaborales[i].FUNC_FECHA_HASTA;

                                                if (i != (result.antlaborales.length - 1)) {
                                                     var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
                                                } else {
                                                     var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
                                                }
												$("#titinfolaboral").fadeIn();
                                                $(".tablaAntLaborales").append(html).fadeIn();
                                            }
                                        } else {
                                            $(".tablaAntLaborales").hide().empty();
                                            $("#titinfolaboral").hide();
                                        }

                                        if (result.movimientos != null ) {
                                            var auxCargo = "";

                                            $(".tablaMovimientos").empty();
                                            $(".tablaMovimientos").append('<thead style="background-color:#f5f5f5;"><tr class="tr"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Desde </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">  Cargo </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">  Departamento/Oficina  </td></tr></thead>');
                                            for (var i = 0; i < result.movimientos.length; i++) {
                                                if(result.movimientos[i].departamento != auxCargo) {
                                                    if (i != (result.movimientos.length - 1)) {
                                                        var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke; text-transform:capitalize;">'+result.movimientos[i].desde+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; text-transform:capitalize;">'+jsUcfirst(result.movimientos[i].cargo)+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; text-transform:capitalize;">'+jsUcfirst(result.movimientos[i].departamento)+'</td></tr>';
                                                    } else {
                                                        var html = '<tr class="tr"><td class="td" style="text-align:center; text-transform:capitalize;">'+result.movimientos[i].desde+'</td><td class="td" style="text-align:center; text-transform:capitalize;">'+jsUcfirst(result.movimientos[i].cargo)+'</td><td class="td" style="text-align:center; text-transform:capitalize;">'+jsUcfirst(result.movimientos[i].departamento)+'</td></tr>';
                                                    }

                                                    $(".tablaMovimientos").append(html);
                                                    auxCargo = result.movimientos[i].departamento;
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

                                var codFunc         = $(this).children().children(".infoContent").data("codFunc");
                                var nombre          = $(this).children().children(".infoContent").data("name");
                                var img             = $(this).children().children(".infoContent").data("img");
                                var cargo           = $(this).children().children(".infoContent").data("cargo");
                                var jsonInfo        = $(this).children().children(".infoContent").data("json");
                                var antiguedad      = $(this).children().children(".infoContent").data("anti");
                                var str             = nombre;
                                var nombre          = str.split("-");
                                var fullname        = $(this).children().children(".infoContent").data("fullname");

                                $("#nombre").html(toTitleCase(fullname));
                                $("#infoCodigo").html(codFunc);
                                $("#infoCargo").html(cargo);
                                $("#infoantiguedad").html(antiguedad);
                                $(".imgProfile").attr("style","height: 63%;background-image: url("+img+");background-color: #cccccc;background-repeat:no-repeat;background-size: 100% 115%;height: 201px;width:260px;border: 3px solid #c59d4c;border-radius: 17px;");
                                $(".divInfo").hide();
                                $("#"+jsonInfo).show();
                            });

                            $("#chart-container2").hide();
                            $("#chart-container").fadeIn();

                            $(document).ready(function(){
                                $('.node2').hover(function() {
                                    localStorage.setItem("idPersona",$(this).data("id"));
                                    $(this).children().addClass('transition');

                                }, function() {
                                    $(".node2").children().removeClass('transition');
                                });
                            });
                        }, 500);
                    }});
                });
            }

            CargaDiagrama("inc/funciones.php?funcion=selectRaiz");

            $.ajax({
                url:"inc/funciones.php?funcion=selectGerencias",
                success: function(result) {
                    var auxCant = 0;
                    for (var i = 0; i < result.gerencias.length; i++) {
                        if (auxCant < result.gerencias[i].CANTIDAD){
                            auxCant = result.gerencias[i].CANTIDAD;
                        }

                        var gerencia= result.gerencias[i].GERENCIA;
                        var porDepto= (result.gerencias[i].CANTIDAD * 100) / auxCant;

                        if(gerencia == null ){
                            gerencia = "no definido";
                        }

                        gerencia    = toTitleCase(gerencia); 
                        var html    = '<div class="accordionList accordion'+result.gerencias[i].COD_GERENCIA+' background-accordionList" data-conten="'+gerencia+'" data-id="'+result.gerencias[i].COD_GERENCIA+'" style="background: white;padding:10px;"><div style="width:100%;display:flex" class="interDiv'+result.gerencias[i].COD_GERENCIA+'" data-id="'+result.gerencias[i].COD_GERENCIA+'"><div style="width: 70%"><spam class="acc_trigger spanLis" style="margin:0px;color:black">'+gerencia+' ('+result.gerencias[i].CANTIDAD+') ('+porDepto.toFixed(2)+'%)</spam></div> <div style="width: 30%"><spam class="spamDown background-theme" ><i class="fa arow fa-angle-right" aria-hidden="true" style="font-size:30px;cursor:pointer;"></i></spam></div></div><div class="acc_container" style="display: none;"><br><br><br><br></div></div>';
                        
                        $(".contenidogrupo").append(html);
                        
                        var divs = $('.accordion>div>').hide();
                    
                        $('.accordion'+result.gerencias[i].COD_GERENCIA).click(function() {
                            localStorage.setItem("actualgerencia", $(this).data("conten"));
                            $(".accordionList").css("background", "white");
                            $(this).css("background", "#c59d4d");
                            
                            var id = $(this).data("id");
                            CargaDiagrama("inc/funciones.php?funcion=selectRaiz&idGerencia="+id+"&gerencianame="+localStorage.getItem("actualgerencia"));
                        });
                    
                        $('.interDiv'+result.gerencias[i].COD_GERENCIA).click(function() {
                            var id = $(this).data("id");
                        });
                    }
                },
                dataType:"json",
                type:"POST"
            });
        </script>
        
        <script type="text/javascript">
            $('.idissmising').click(function() {
                $(".contenidogrupo").hide();
                $(".divGrupos").addClass("grupooculto");
                $(".divGrupos").addClass("divGruposhide");
                $("#gtitulo").hide();
                $("#arrows").css("width","100%");
                $('.fa-arrow-right').css("margin-top","35%");
                $(".divDiagrama").css("width","96%");
                $(this).hide();
                $('.fa-arrow-right').show();
            });

            $('.fa-arrow-right').click(function() {
                $(".contenidogrupo").fadeIn();
                $(".divGrupos").removeClass("grupooculto");
                $(".divGrupos").removeClass("divGruposhide");
                $("#arrows").css("width","30%");
                $("#gtitulo").show();
                $(this).css("margin-top","25%");
                $(".divDiagrama").css("width","80%");
                $(this).hide();
                $('.fa-arrow-left').show();
            });
        </script>

        <script type="text/javascript">
            var elements = $('.modal-overlay, .modal');

            $('.node2').click(function() {
                elements.addClass('active');
            });
            
            $('.close-modal').click(function(){
                elements.removeClass('active');
            });
        </script>

        <script type="text/javascript">
            var divs = $('.accordionM>div').hide(); //Hide/close all containers

            var h2s  = $('.accordionM>h2').click(function () {
                h2s.not(this).removeClass('active')
                $(this).toggleClass('active')
                divs.not($(this).next()).slideUp()
                $(this).next().slideToggle()
                return false; //Prevent the browser jump to the link anchor
            });

            $('.btnFiltrarProductividad').on("click", function() {
                $("#miniloading").fadeIn();
                $(".tablaLogros").empty();
                $(".tablaLogros").append('<thead style="background-color:#f5f5f5;"><tr class="tr"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Meses </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Tipo de Producción </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Meta </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Logrado </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Ratio </td></tr></thead>');

                var ini             = $("#inilogros").val();
                var end             = $("#endlogros").val();
                var cod             = document.getElementById('infoCodigo').innerHTML;
                var JSONUrl         = "inc/funciones.php";

                var formattedDate1  = new Date(ini);
                var d1              = formattedDate1.getDate();
                var m1              = formattedDate1.getMonth();
                var y1              = formattedDate1.getFullYear();
                m1                  += 1;  // JavaScript months are 0-11

                var formattedDate2  = new Date(end);
                var d2              = formattedDate2.getDate();
                var m2              = formattedDate2.getMonth();
                var y2              = formattedDate2.getFullYear();
                d2                  += 1;
                m2                  += 1;  // JavaScript months are 0-11

                if(m1 < 9){
                    m1 = "0" + m1
                }

                if(m2 < 9){
                    m2 = "0" + m2
                }

                ini                 = (y1 + "-" + m1 + "-" + d1);
                end                 = (y2 + "-" + m2 + "-" + d2);

                $.ajax({
                    url: JSONUrl,
                    success: function(result) {
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

                                result.logros[i].COLOR  = result.logros[i].COLOR.replace(/ /g , "")

                                if (result.logros[i].COLOR == "ROJO") {
                                    color = "red";
                                    texto = "white";
                                } else if (result.logros[i].COLOR == "VERDE") {
                                    color = "green";
                                    texto = "white";
                                } else {
                                    color = "#eae70a";
                                    texto = "black";
                                }

                                if (i != (result.logros.length - 1)) {
                                    var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+result.logros[i].PERIODO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+result.logros[i].TIPO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+meta[0]+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+logro[0]+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; background-color:'+result.logros[i].COLOR+'; color:'+result.logros[i].COLOR_TEXTO+'">'+result.logros[i].RATIO+'%</td></tr>';
                                } else {
                                    var html = '<tr class="tr"><td class="td" style="text-align:center;">'+result.logros[i].PERIODO+'</td><td class="td" style="text-align:center;">'+result.logros[i].TIPO+'</td><td class="td" style="text-align:center;">'+meta[0]+'</td><td class="td" style="text-align:center;">'+logro[0]+'</td><td class="td" style="text-align:center; background-color:'+result.logros[i].COLOR+'; color:'+result.logros[i].COLOR_TEXTO+'">'+result.logros[i].RATIO+'%</td></tr>';
                                }
                                
                                $(".tablaLogros").append(html);
                                
                                promedioLogrado = promedioLogrado + parseInt(result.logros[i].RATIO);
                            }
                            
                            promedioLogrado = (promedioLogrado / result.logros.length);

                            var htmlPromedio= '<tr class="tr" style="background-color:#f5f5f5;"><td class="td" colspan="3"></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>Promedio</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0"><strong>'+promedioLogrado.toFixed(2)+'%</strong></td></tr>';
                            
                            $(".tablaLogros").append(htmlPromedio);
                        } else {
                            $(".tablaLogros").empty();
                        }

                        $("#miniloading").fadeOut();
                    },
                    data:{
                        dateIni:ini,
                        dateEnd:end,
                        consulta:"logros",
                        funcion:"selectTablas",
                        id:cod
                    },
                    dataType:"json",
                    type:"GET"
                });
            });
        
            $('.btnFiltrarSalario').on("click", function() {
                $("#miniloading").fadeIn();
                $(".tablaSalario").empty();
                $(".tablaSalario").append('<thead style="background-color:#f5f5f5;"><tr class="tr"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Meses</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Variable(SV)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Fijo(SF)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Total(ST)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Aguinaldo</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">IPS</td></tr></thead>');

                var ini             = $("#inisalario").val();
                var end             = $("#endsalario").val();
                var cod             = document.getElementById('infoCodigo').innerHTML;
                var JSONUrl         = "inc/funciones.php";

                var formattedDate1  = new Date(ini);
                var d1              = formattedDate1.getDate();
                var m1              =  formattedDate1.getMonth();
                var y1              = formattedDate1.getFullYear();
                m1                  += 1;  // JavaScript months are 0-11

                var formattedDate2  = new Date(end);
                var d2              = formattedDate2.getDate();
                var m2              =  formattedDate2.getMonth();
                var y2              = formattedDate2.getFullYear();
                d2                  += 1;
                m2                  += 1;  // JavaScript months are 0-11
                
                if(m1 < 9) {
                    m1 = "0"+m1
                }
        
                if(m2 < 9){
                    m2 = "0"+m2
                }

                ini                 = (y1 + "-" + m1 + "-" + d1);
                end                 = (y2 + "-" + m2 + "-" + d2);

                var fijo            = 0;
                var variable        = 0;
                var total           = 0
                var aguinaldo       = 0;
                var aporte          = 0;

                $.ajax( {
                    url: JSONUrl,
                    success: function(result) {
                        if (result.salario != null) {
                            for (var i = 0; i < result.salario.length; i++) {
                                fijo            = fijo+ parseInt(result.salario[i].fijo)
                                variable        = variable+parseInt(result.salario[i].variable)
                                total           = total +parseInt(result.salario[i].total)
                                aguinaldo       = aguinaldo +parseInt(result.salario[i].aguinaldo)
                                aporte          = aporte +parseInt(result.salario[i].aporte)
                                
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

                            $(".tablaSalario").append('<tr class="tr" style="background-color:#f5f5f5;"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Promedios</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+variable+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+fijo+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+total+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+aguinaldo+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+aporte+'</strong></td></tr>');
                        } else {
                            $(".tablaSalario").empty();
                        }

                        $("#miniloading").fadeOut();
                    },
                    data: {
                        dateIni:ini,
                        dateEnd:end,
                        consulta:"salario",
                        funcion:"selectTablas",
                        id:cod
                    },
                    dataType:"json",
                    type:"GET"
                });
            });
    
            $('.btnFiltrarEventos').on("click", function() {
                $("#miniloading").fadeIn();
                $(".tablaEventos").empty();
                $(".tablaEventos").html('<thead style="background-color:#f5f5f5;"><tr><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Licencias Justificadas/Injustificadas</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Códigos</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0; display:none;">Fecha</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Cantidad Total</td></tr></thead>');

                var ini             = $("#inieventos").val();
                var end             = $("#endeventos").val();
                var cod             = document.getElementById('infoCodigo').innerHTML;
                var JSONUrl         = "inc/funciones.php";

                var formattedDate1  = new Date(ini);
                var d1              = formattedDate1.getDate();
                var m1              = formattedDate1.getMonth();
                var y1              = formattedDate1.getFullYear();
                m1                  += 1;  // JavaScript months are 0-11

                var formattedDate2  = new Date(end);
                var d2              = formattedDate2.getDate();
                var m2              = formattedDate2.getMonth();
                var y2              = formattedDate2.getFullYear();
                d2                  += 1;
                m2                  += 1;  // JavaScript months are 0-11
                
                var auxEvenCod  = '';
                var auxEvenFal  = '';
                var auxEvenCol  = '';
                var auxEvenCan  = 0;
                var auxEvenBan  = true;
                var html        = '';

                if(m1 < 9){
                    m1 = "0"+m1
                }
                
                if(m2 < 9){
                    m2 = "0"+m2
                }

                ini = y1 + "-" + m1 + "-" + d1;
                end = y2 + "-" + m2 + "-" + d2;

                $.ajax({
                    url: JSONUrl,
                    success: function(result) {
                        if(result.eventos != null) {
                            for (var i = 0; i < result.eventos.length; i++) {
                                if(result.eventos[i].color == "ROJO"){
                                    result.eventos[i].color = "red";
                                } else if(result.eventos[i].color == "VERDE"){
                                    result.eventos[i].color = "green";
                                } else if(result.eventos[i].color == "AZUL"){
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

                        $("#miniloading").fadeOut();
                    },
                    data: {
                        dateIni:ini,
                        dateEnd:end,
                        consulta:"eventos",
                        funcion:"selectTablas",
                        id:cod
                    },
                    dataType:"json",
                    type:"GET"
                });
            });

            $('.btnFiltrarDocumentos').on("click", function() {
                $("#miniloading").fadeIn();
                $(".tablaDocumentos").empty();
                $(".tablaDocumentos").html('<thead style="background-color:#f5f5f5;"><tr><td class="td"></td></tr></thead>');

                var ini             = $("#inidocumento").val();
                var end             = $("#enddocumento").val();
                var cod             = document.getElementById('infoCodigo').innerHTML;
                var JSONUrl         = "inc/funciones.php";

                var formattedDate1  = new Date(ini);
                var d1              = formattedDate1.getDate();
                var m1              = formattedDate1.getMonth();
                var y1              = formattedDate1.getFullYear();
                m1                  += 1;  // JavaScript months are 0-11

                var formattedDate2  = new Date(end);
                var d2              = formattedDate2.getDate();
                var m2              = formattedDate2.getMonth();
                var y2              = formattedDate2.getFullYear();
                d2                  += 1;
                m2                  += 1;  // JavaScript months are 0-11

                if(m1 < 9){
                    m1 = "0"+m1
                }
                
                if(m2 < 9){
                    m2 = "0"+m2
                }

                ini = y1 + "-" + m1 + "-" + d1;
                end = y2 + "-" + m2 + "-" + d2;
        
                $.ajax({
                    url: JSONUrl,
                    success: function(result) {
                        if(result.documentos != null) {
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

                        $("#miniloading").fadeOut();
                    },
                    data: {
                        dateIni:ini,
                        dateEnd:end,
                        consulta:"documentos",
                        funcion:"selectTablas",
                        id:cod
                    },
                    dataType:"json",
                    type:"GET"
                });
            });

            $('.btnFiltrarCapacitaciones').on("click", function() {
                $("#miniloading").fadeIn();
                $(".tablaCapacitaciones").empty();
                $(".tablaCapacitaciones").html('<thead style="background-color:#f5f5f5;"><tr><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Número</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Empresa</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Curso</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Año</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Mes</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Horas</td></tr></thead>');

                var ini             = $("#inicapacitacion").val();
                var end             = $("#endcapacitacion").val();
                var cod             = document.getElementById('infoCodigo').innerHTML;
                var JSONUrl         = "inc/funciones.php";

                var formattedDate1  = new Date(ini);
                var d1              = formattedDate1.getDate();
                var m1              = formattedDate1.getMonth();
                var y1              = formattedDate1.getFullYear();
                m1                  += 1;  // JavaScript months are 0-11

                var formattedDate2  = new Date(end);
                var d2              = formattedDate2.getDate();
                var m2              = formattedDate2.getMonth();
                var y2              = formattedDate2.getFullYear();
                d2                  += 1;
                m2                  += 1;  // JavaScript months are 0-11

                if(m1 < 9){
                    m1 = "0"+m1
                }
                
                if(m2 < 9){
                    m2 = "0"+m2
                }

                ini = y1 + "-" + m1 + "-" + d1;
                end = y2 + "-" + m2 + "-" + d2;

                
                $.ajax({
                    url: JSONUrl,
                    success: function(result) {
                        if(result.capacitaciones != null) {
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

                        $("#miniloading").fadeOut();
                    },
                    data: {
                        dateIni:ini,
                        dateEnd:end,
                        consulta:"capacitaciones",
                        funcion:"selectTablas",
                        id:cod
                    },
                    dataType:"json",
                    type:"GET"
                });
            });

            $('.btnFiltrarAnotaciones').on("click", function() {
                $("#miniloading").fadeIn();
                $(".tablaAnotaciones").empty();
                $(".tablaAnotaciones").html('<thead style="background-color:#f5f5f5;"><tr><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Número</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Fecha</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Evento</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Observacion</td></tr></thead>');

                var ini             = $("#inianotacion").val();
                var end             = $("#endanotacion").val();
                var cod             = document.getElementById('infoCodigo').innerHTML;
                var JSONUrl         = "inc/funciones.php";

                var formattedDate1  = new Date(ini);
                var d1              = formattedDate1.getDate();
                var m1              = formattedDate1.getMonth();
                var y1              = formattedDate1.getFullYear();
                m1                  += 1;  // JavaScript months are 0-11

                var formattedDate2  = new Date(end);
                var d2              = formattedDate2.getDate();
                var m2              = formattedDate2.getMonth();
                var y2              = formattedDate2.getFullYear();
                d2                  += 1;
                m2                  += 1;  // JavaScript months are 0-11

                if(m1 < 9){
                    m1 = "0"+m1
                }
                
                if(m2 < 9){
                    m2 = "0"+m2
                }

                ini = y1 + "-" + m1 + "-" + d1;
                end = y2 + "-" + m2 + "-" + d2;
                    
                $.ajax({
                    url: JSONUrl,
                    success: function(result) {
                        if(result.anotaciones != null) {
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

                        $("#miniloading").fadeOut();
                    },
                    data: {
                        dateIni:ini,
                        dateEnd:end,
                        consulta:"anotaciones",
                        funcion:"selectTablas",
                        id:cod
                    },
                    dataType:"json",
                    type:"GET"
                });
            });
            
            $('.btnFiltrarAntLaboral').on("click", function() {
                $("#miniloading").fadeIn();
                $(".tablaAntLaborales").empty();
                

                var ini             = $("#iniantlaboral").val();
                var end             = $("#endantlaboral").val();
                var cod             = document.getElementById('infoCodigo').innerHTML;
                var JSONUrl         = "inc/funciones.php";

                var formattedDate1  = new Date(ini);
                var d1              = formattedDate1.getDate();
                var m1              = formattedDate1.getMonth();
                var y1              = formattedDate1.getFullYear();
                m1                  += 1;  // JavaScript months are 0-11

                var formattedDate2  = new Date(end);
                var d2              = formattedDate2.getDate();
                var m2              = formattedDate2.getMonth();
                var y2              = formattedDate2.getFullYear();
                d2                  += 1;
                m2                  += 1;  // JavaScript months are 0-11

                if(m1 < 9){
                    m1 = "0"+m1
                }
                
                if(m2 < 9){
                    m2 = "0"+m2
                }

                ini = y1 + "-" + m1 + "-" + d1;
                end = y2 + "-" + m2 + "-" + d2;

                                
                $.ajax({
                    url: JSONUrl,
                    success: function(result) {
                        if(result.antlaborales != null) {
                            for (var i = 0; i < result.antlaborales.length; i++) {
                                var nroAntLaboral = result.antlaborales[i].FUNC_NRO_ANTECEDENTE;
                                var empAntLaboral = result.antlaborales[i].FUNC_EMPRESA;
                                var desAntLaboral = result.antlaborales[i].FUNC_FECHA_DESDE;
                                var hasAntLaboral = result.antlaborales[i].FUNC_FECHA_HASTA;

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
                            $(".tablaAntLaborales").empty().hide();
							$("#titinfolaboral").hide();
                        }

                        $("#miniloading").fadeOut();
                    },
                    data: {
                        dateIni:ini,
                        dateEnd:end,
                        consulta:"antLaborales",
                        funcion:"selectTablas",
                        id:cod
                    },
                    dataType:"json",
                    type:"GET"
                });
            });

            $('.btnFiltrarMovimientos').on("click", function() {
                $("#miniloading").fadeIn();
                $(".tablaMovimientos").empty();
                $(".tablaMovimientos").append('<thead style="background-color:#f5f5f5;"><tr class="tr"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Desde </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">  Cargo </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">  Departamento/Oficina  </td></tr></thead>');

                var ini             = $("#inimovimientos").val();
                var end             = $("#endmovimientos").val();
                var cod             = document.getElementById('infoCodigo').innerHTML;
                var JSONUrl         = "inc/funciones.php";

                var formattedDate1  = new Date(ini);
                var d1              = formattedDate1.getDate();
                var m1              = formattedDate1.getMonth();
                var y1              = formattedDate1.getFullYear();
                m1                  += 1;  // JavaScript months are 0-11

                var formattedDate2  = new Date(end);
                var d2              = formattedDate2.getDate();
                var m2              = formattedDate2.getMonth();
                var y2              = formattedDate2.getFullYear();
                d2                  += 1;
                m2                  += 1;  // JavaScript months are 0-11

                if(m1 < 9){
                    m1 = "0"+m1
                }

                if(m2 < 9){
                    m2 = "0"+m2
                }

                ini = (y1 + "-" + m1 + "-" + d1);
                end = (y2 + "-" + m2 + "-" + d2);

                $.ajax({
                    url: JSONUrl,
                    success: function(result) {
                        if (result.movimientos != null ) {
                            var auxCargo = "";

                            for (var i = 0; i < result.movimientos.length; i++) {
                                if(result.movimientos[i].departamento != auxCargo) {
                                    if (i != (result.movimientos.length - 1)) {
                                        var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke; text-transform:capitalize;">'+result.movimientos[i].desde+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; text-transform:capitalize;">'+jsUcfirst(result.movimientos[i].cargo)+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; text-transform:capitalize;">'+jsUcfirst(result.movimientos[i].departamento)+'</td></tr>';
                                    } else {
                                        var html = '<tr class="tr"><td class="td" style="text-align:center; text-transform:capitalize;">'+result.movimientos[i].desde+'</td><td class="td" style="text-align:center; text-transform:capitalize;">'+jsUcfirst(result.movimientos[i].cargo)+'</td><td class="td" style="text-align:center; text-transform:capitalize;">'+jsUcfirst(result.movimientos[i].departamento)+'</td></tr>';
                                    }

                                    $(".tablaMovimientos").append(html);
                                    auxCargo = result.movimientos[i].departamento;
                                }
                            }
                        } else {
                            $(".tablaMovimientos").empty();
                        }
                        
                        $("#miniloading").fadeOut();
                    },
                    data: {
                        dateIni:ini,
                        dateEnd:end,
                        consulta:"movimientos",
                        funcion:"selectTablas",
                        id:cod
                    },
                    dataType:"json",
                    type:"GET"
                });
            });

            $('.acc_trigger').on("click", function() {
                if ($(".fa-angle-down")[0]) {
                    $(".arow").removeClass("fa-angle-down");
                    $(".arow").addClass("fa-angle-up")
                } else {
                    $(".arow").removeClass("fa-angle-up");
                    $(".arow").addClass("fa-angle-down")
                }
            });
        </script>

        <script type="text/javascript">
            $(document).on("click","#salir", function () {
                var url     = $(this).data("url");
                var array   = new Array(GetVal("#usuario"), GetVal("#pass"));
                var result  = setValuesAjax(array, "inc/controlador.php", "logOut", "login", function(result) {
                    if (result.result == "success") {
                        window.location.replace(url)
                    } else {
                        alert("Se produjo un error inesperado")
                    }
                });
            });
        </script>

        <script type="text/javascript">
            function getOrganigrama(codGerencia, nomGerencia){
				$(".contenidogrupo .background-accordionList").removeClass("seleccionado_boton");
                var elements = $('.modal-overlay, .modal');
                elements.removeClass('active');
                console.log('llego');
                CargaDiagrama("inc/funciones.php?funcion=selectRaiz&idGerencia="+codGerencia+"&gerencianame="+nomGerencia);
            
				$( document ).ajaxStop(function() {
				  setTimeout(function(){
					var contt = $(".orgchart td:first").find('.var_hor').length;
					if(contt > 0 ){
						$(".orgchart td:first").find('.var_hor').hide();
					}
				}, 500);
				});
				
				
				
				
				
			
			}
			
		
        </script>
    </body>
</html>
<?php
    } else {
        header("Location: loginUser.php");
        die();
    }
?>