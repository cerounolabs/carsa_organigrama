

<?php session_start();

if (isset($_SESSION['id_usuario']))
{
include "../inc/resetpassword.php";

if($_SESSION['rankings'] == "1")
{

include "../inc/org_log.php";
 (new log())->registrar($_SESSION['nombre'],"Ingreso al panel de rankings","",false);
}
else
{
header("Location: ../diagrama.php");
echo 	$_SESSION['rankings'];
}

}
else
{

  header("Location: ../loginUser.php");
  die();


}
 ?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Ranking de colaboradores</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.css" rel="stylesheet">
    
    <style type="text/css">
        .filtroitem{
            background: #dca332d4;color: white;padding: 5px;margin: 10px;border-radius: 24px;
        }
        
        .filtroitem :hobber {
            
             background: #f44336;
        }
    </style>

</head>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <?php include "sidebar.php";?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->


                    <?php include "topbar.php"?>
                    <!-- Begin Page Content -->
                    <div class="container-fluid">

                        <!-- Page Heading -->
                      

                        <!-- Content Row -->
                        <div class="row">







                        </div>

                        <!-- Content Row -->

                        <div class="row">


                        </div>

                        <!-- Content Row -->
                        <div class="row">


                            <div class="col-lg-12 mb-4">

                                <!-- Illustrations -->
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0  font-weight-bold text-primary" id="rankingtitulo" ></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center">

                                            <div>
                                                <div style="display:flex" >


                                                    <div style="width:50%;padding:10px" >


                                                        <label for="" style="float:left" >Gerencias</label>

                                                        <select name="" class="form-control" id="selectGerencia">
                                                        
                                                        </select>



                                                        <label for="" style="float:left" >Departamento</label>
                                                        <select name="" class="form-control" id="selectDepartamento">

                                                            

                                                        </select>

                                                        <labe style="float:left" >Filtros</labe>
                                                        <br>


                                                        <div id="filtros">



                                                        </div>

                                                    </div>


                                                    <div style="width:50%;padding:10px">

                                                        <label for="" style="float:left">Desde</label>
                                                        <input type="date" name="" id="inputdesde" class="form-control"/>

                                                        <label for="" style="float:left">Hasta</label>
                                                        <input type="date" name="" id="inputhasta" class="form-control"/>
                                                        <br>
                                                        <button class="btn btn-secondary" id="agregarfiltro">Agregar filtro</button>
                                                    </div>
                                                    <br><br>
                                                </div>

                                                    <div style="width:100%;display:flex">
                                                        <div style="width:30%;padding:10px">
                                                             <label for="" style="float:left" >Top</label>
                                                        <select name="" class="form-control" id="selectTop">

                                                            <option value="5">5</option>
                                                            <option value="10">10</option>
                                                            <option value="20">20</option>
                                                            <option value="30">30</option>
                                                            <option value="40">40</option>
                                                            <option value="50">50</option>
                                                            <option value="60">60</option>
                                                            <option value="70">70</option>
                                                            <option value="80">80</option>
                                                            <option value="90">90</option>
                                                            <option value="100">100</option>

                                                        </select>
                                                        </div>
                                                        <div style="width:30%;padding:10px">
                                                                     <label for="" style="float:left" >Criterio</label>
                                                        <select name="" class="form-control" id="selectCriterio">

                                                            <option value="DESC">Orden Descendente</option>
                                                            <option value="ASC">Orden Ascendente</option>

                                                        </select>
                                                        </div>
                                                        <div style="width:30%;margin-top:40px">
                                                            <button class="btn btn-primary" id="consultar" >Consultar </button>
                                                        </div>
                                                        <div style="width:10%">
                                                            
                                                        </div>
                                                        
                                                    </div>
                                                    <br>
                                                    <div style="width:100%;">
                                                        
                                                         <div style="width:100%">
                                                            <div id="chartContainerTop" style="height: 400px; width: 100%;"></div>
                                                        </div>
                                                       
                                                       
                                                      
                                                    </div>
                                                    
                                                    
<br> <br> <br> <br>
                                                <div id="chartContainer" style="height: 370px; width: 100%; margin: 0px auto;"></div>
                                                <br> <br>
                                                <div class="card shadow mb-4">
                                                    <div class="card-header py-3">
                                                        <h6 class="m-0 font-weight-bold text-primary">Tabla de resultados</h6>
                                                          <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        
                                                        <button class="btn btn-primary downloadExcel"><i class="fas fa-download  fa-sm text-white-50"></i> Dercargar.xls</button>
                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                                <thead>
                                                                <tr>
                                                              
                                                                </tr>
                                                                </thead>
                                                                <tfoot>
                                                                <tr>
                                                            
                                                                </tr>
                                                                </tfoot>
                                                                <tbody>
                                                         

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- End of Main Content -->

                                        <!-- Footer -->
                                        <footer class="sticky-footer bg-white">
                                            <div class="container my-auto">
                                                <div class="copyright text-center my-auto">
                                                    
                                                </div>
                                            </div>
                                        </footer>
                                        <!-- End of Footer -->

                                    </div>
                                    <!-- End of Content Wrapper -->

                                </div>
                                <!-- End of Page Wrapper -->

                                <!-- Scroll to Top Button-->
                                <a class="scroll-to-top rounded" href="#page-top">
                                    <i class="fas fa-angle-up"></i>
                                </a>

                                <!-- Logout Modal-->
                                <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">/div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                                    <a class="btn btn-primary" href="login.html">Logout</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bootstrap core JavaScript-->
                                    <script src="vendor/jquery/jquery.min.js"></script>
                                    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

                                    <!-- Core plugin JavaScript-->
                                    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

                                    <!-- Custom scripts for all pages-->
                                    <script src="js/sb-admin-2.min.js"></script>

                                    <!-- Page level plugins -->
                                    <script src="vendor/chart.js/Chart.min.js"></script>

                                    <!-- Page level custom scripts -->
                                    <script src="js/demo/chart-area-demo.js"></script>
                                    <script src="js/demo/chart-pie-demo.js"></script>
                                    <script src="js/canvasjs/canvasjs.min.js"></script>
                                    <script src="js/jquery.table2excel.min.js"></script>
</body>
<script type="text/javascript">
    

</script>

<script type="text/javascript">
    window.onload = function () 
    {
        $("#rankingtitulo").html("Ranking de "+localStorage.getItem("rankingtitulo"))
    }
</script>

<script type="text/javascript">
    
        $.ajax( {
        type: "get",
        url:"inc/inc/consultasController.php",      
        beforeSend: function(request) 
		{
        request.setRequestHeader("Accept", "application/json"),
        request.setRequestHeader("Content-Type", "application/json")
        },
		data:{consulta:"rankings"},
        success: function( response )
        {
          
		for(var i=0 ; i< response.length;i++)
		{
		    
		    var html = '<a class="collapse-item rankingitem" href="#" data-id="'+response[i].ID+'" data-ranking="'+response[i].CATEGORIA+'" style="white-space: pre-line;">'+response[i].CATEGORIA+'</a>';
		    $("#collapseRContent").append(html)     
		   
		    
		}
		
		$(".rankingitem").click(function()
		{
		
		localStorage.setItem("rankingid",$(this).data("id"))
		localStorage.setItem("rankingtitulo",$(this).data("ranking"))
		
		$("#rankingtitulo").html("Ranking de "+localStorage.getItem("rankingtitulo"));
		    
		});
           
        },
        error(jqXHR, exception)
        {

        console.log("No de pudo recuperar los elementos del formulario "+exception) 

        }
    });
    
    $("#selectGerencia").append("<option value > Cargando gerencias...</option>")
    
            $.ajax( {
        type: "get",
        url:"inc/inc/consultasController.php",      
        beforeSend: function(request) 
		{
        request.setRequestHeader("Accept", "application/json"),
        request.setRequestHeader("Content-Type", "application/json")
        },
		data:{consulta:"gerencias"},
        success: function( response )
        {
          $("#selectGerencia").empty("")
          $("#selectGerencia").append("<option value selected>Elija una gerencia</option>")
		for(var i=0 ; i< response.length;i++)
		{
		    
		    var html = '<option value="'+response[i].CODIGO+'" >'+response[i].GERENCIA+'</option>';
		    $("#selectGerencia").append(html)     
		   
		    
		}
		$("#selectGerencia").append("<option value='0' >Todas las gerencias</option>")
		
		$("#selectGerencia").change(function()
		{
		$("#selectDepartamento").empty("")

		
		
        var gerenciaid = $(this).val();
        
        if(gerenciaid !== "0")
        {
            $("#selectDepartamento").append("<option value>Cargando departamentos...</option>")
        $.ajax( {
        type: "get",
        url:"inc/inc/consultasController.php",      
        beforeSend: function(request) 
		{
        request.setRequestHeader("Accept", "application/json"),
        request.setRequestHeader("Content-Type", "application/json")
        },
		data:{consulta:"departamentos",gerencia:gerenciaid},
        success: function( response )
        {
          $("#selectDepartamento").empty("")
          $("#selectDepartamento").append("<option value selected>Elija una departamento</option>")
		for(var i=0 ; i< response.length;i++)
		{
		    
		  var html = '<option value="'+response[i].CODIGO+'" >'+response[i].DEPARTAMENTO+'</option>';
		  $("#selectDepartamento").append(html)    
		   
		}
		
		$("#selectDepartamento").change(function()
		{
		

		    
		});
           
        },
        error(jqXHR, exception)
        {

        console.log("No de pudo recuperar los elementos del formulario "+exception) 

        }
    });
    
}
		    
		});
           
        },
        error(jqXHR, exception)
        {

        console.log("No de pudo recuperar los elementos del formulario "+exception) 

        }
    });
    
</script>

<script type="text/javascript" >
    function converdate(date)
    {
        
var today = new Date(date);
var dd = today.getDate()+1;
var mm = today.getMonth() + 1; //January is 0!

var yyyy = today.getFullYear();
if (dd < 10) {
  dd = '0' + dd;
} 
if (mm < 10) {
  mm = '0' + mm;
} 
var today = dd + '/' + mm + '/' + yyyy;
 return today;       
    }
    
    $("#agregarfiltro").click(function(){
        
       var gerencia = $("#selectGerencia").val();
       var departamento = $("#selectDepartamento").val();
       var desde = $("#inputdesde").val();
       var hasta = $("#inputhasta").val();
       
       var gerenciatitulo = $("#selectGerencia :selected").text();
       var departamentotitulo = $("#selectDepartamento :selected").text();
       var titulo=""
       if(departamentotitulo != "" && departamento !="")
       {
           
           titulo= gerenciatitulo+"-"+departamentotitulo
           
       }
       else
       {
           titulo = gerenciatitulo
       }
       
       
       if (gerencia != "")
       {
           
           if(desde != "" && hasta =="")
           {
               alert("Defina el campo Hasta para continuar")
           }
          else if(hasta != "" && desde =="")
           {
               alert("Defina el campo Desde para continuar")
           }
           else
           {var fecha=""
              if(desde != "" && hasta !="")
              {

                  fecha=converdate(desde)+"*"+converdate(hasta)
              }
               var html='<label class="filtroitem" data-titulo="'+titulo+'" data-gerencia="'+gerencia+'" data-departamento="'+departamento+'" data-fecha="'+fecha+'" > '+titulo+' <spam></spam><br><spam>'+desde+" "+hasta+'</spam> </label>'
               $("#filtros").append(html);
               
               $(".filtroitem").click(function()
               {
                   $(this).remove();
               });
               
               $(".filtroitem").hover(function()
               {
                   
                   $(this).css("background","#e91e63")
                   $(this).css("cursor","pointer")
                   
               }, function() {
                    
                    $(this).css("background","#dca332d4")
                })
           }
           
       }
       else
       {
           
           alert("Seleccionar una gerencia es obligatorio")
           
       }
       
    })
    
    
    $("#consultar").click(function()
    {
        if(localStorage.getItem("rankingtitulo")!=null)
        {
            
        
        
        $(this).html("Consultando...");
        $(this).attr("disabled",true);
        
     $(".table-bordered thead").empty()
     $(".table-bordered tbody").empty()
        
    var ranking = localStorage.getItem("rankingid");
    var top = $("#selectTop").val();
    var orden = $("#selectCriterio").val();
    var filtro = "";
    var nombreranking= localStorage.getItem("rankingtitulo");
    $('.filtroitem').each(function(i, obj) 
    {
    filtro = filtro+","+$(this).data("gerencia")+"||"+$(this).data("departamento")+"||"+$(this).data("fecha")+"||"+$(this).data("titulo")
    });
    
    console.log(filtro)
    console.log(ranking)
    console.log(orden)
    console.log(top)
  
     
     
        $.ajax({
            type: "get",
            url: "inc/inc/consultasController.php",
            data:
                {
                consulta:"datosranking",
                filtros:filtro,
                ranking:ranking,    
                top:top,
                orden:orden,
                nombreranking:nombreranking
                
                },
            success: function (data, text) {
  $("#consultar").html("Consultar");
$("#consultar").attr("disabled",false);
                console.log(data)
                    console.log(data)
                
            var tableheader=""
            for(key in data.fortable[0])
            {
            console.log(key)
            tableheader=tableheader+'<th>'+key+'</th>'
            
            }
            
            $(".table-bordered thead").append('<tr>'+tableheader+'</tr>')
        
           
             for(key in data.fortable)
            {
                 var tablerows = ""
                for(key2 in data.fortable[key])
                {
            console.log(key)
            if (data.fortable[key][key2]===null)
            {
                data.fortable[key][key2]="0"
            }
            tablerows=tablerows+'<th>'+data.fortable[key][key2]+'</th>' 
                }
           $(".table-bordered tbody").append('<tr>'+tablerows+'</tr>')
            }
            
            
            
            
            
                var chart = new CanvasJS.Chart("chartContainer", {
                    animationEnabled: true,
                    exportEnabled:true,
                    title:{
                        text: localStorage.getItem("rankingtitulo")
                    },
                    axisY: {
                        title: "Cantidad"
                    },
                    legend: {
                        cursor:"pointer",
                        itemclick : toggleDataSeries
                    },
                    toolTip: {
                        shared: true,
                        content: toolTipFormatter
                    },
                    data: data.forchart
                });
                chart.render();
                
                
                
                
    var chart2 = new CanvasJS.Chart("chartContainerTop", {
	exportEnabled: true,
	animationEnabled: true,
	title:{
		text: "Top"
	},
	legend:{
		cursor: "pointer",
		itemclick: explodePie
	},
	data: [{
		type: "pie",
		showInLegend: true,
		toolTipContent: "{label}: <strong>{y}</strong>",
		indexLabel: "{label} - {porcentaje}%",
		legendText: "{label}:{y}",
		dataPoints: data.fortop
	}]
});
chart2.render();


function explodePie (e) {
	if(typeof (e.dataSeries.dataPoints[e.dataPointIndex].exploded) === "undefined" || !e.dataSeries.dataPoints[e.dataPointIndex].exploded) {
		e.dataSeries.dataPoints[e.dataPointIndex].exploded = true;
	} else {
		e.dataSeries.dataPoints[e.dataPointIndex].exploded = false;
	}
	e.chart.render();

}
                
                
        function toolTipFormatter(e) {
            var str = "";
            var total = 0 ;
            var str3;
            var str2 ;
            for (var i = 0; i < e.entries.length; i++){
                var str1 = "<span style= 'color:"+e.entries[i].dataSeries.color + "'>" + e.entries[i].dataSeries.name + "</span>: <strong>"+  e.entries[i].dataPoint.y + "</strong> <br/>" ;
                total = e.entries[i].dataPoint.y + total;
                str = str.concat(str1);
            }
            str2 = "<strong>" + e.entries[0].dataPoint.label + "</strong> <br/>";
            str3 = "<span style = 'color:Tomato'>Total: </span><strong>" + total + "</strong><br/>";
            return (str2.concat(str)).concat(str3);
        }

        function toggleDataSeries(e) {
            if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                e.dataSeries.visible = false;
            }
            else {
                e.dataSeries.visible = true;
            }
            chart.render();
        }

        
            },
            error: function (request, status, error)
            {
                $("#consultar").html("Consultar");
                $("#consultar").attr("disabled",false);
                alert("Sin resultados")
                console.log("Se produjo un error al recuperar los datos de autocompletado");
                console.log(request.responseText);
            }
        });
     
        
        }
        else
        {
            
            alert("Seleccione primero un tipo de ranking")
            
        }
    })
    
</script>


<script type="text/javascript" >
    
    $(document).on("click", ".downloadExcel", function ()
  {

$("#dataTable").table2excel({
    exclude: ".excludeThisClass",
    name: "Worksheet Name",
    filename: "tabladeresultados.xls" //do not include extension
});

  });
    
</script>

</html>
