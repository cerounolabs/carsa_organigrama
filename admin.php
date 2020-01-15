<?php 
  session_start();

  if(isset($_SESSION['admin'])) {
	include 'inc/conexionMySQL.php';
    $fecMes   = date('Y-m');
    $fecAux   = date('Y-m-d', strtotime("{$fecMes} + 1 month"));
    $fecDesde = date('Y-m-01');
    $fecHasta = date('Y-m-d', strtotime("{$fecAux} - 1 day"));
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/check.css">
    <link rel="stylesheet" href="css/admin.css">

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <title>Adminitracion de accesos</title>
  </head>

  <body>
    <!-- Modal -->
    <div class="modal fade" id="modallog" role="dialog">
      <div class="modal-dialog" style="width:60%">
        <!-- Modal content-->
        <div class="modal-content " style="background:white;">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Registro de actividades</h4>
          </div>
          
          <div class="modal-body">
            <table class="table" style="width:100%">
              <tbody>
                <tr>
                  <td>Fecha Desde: <input type="date" id="desdeFilter" name="desdeFilter" value="<?php echo $fecDesde; ?>" onblur="getLog();"></td>
                  <td>Fecha Hasta: <input type="date" id="hastaFilter" name="hastaFilter" value="<?php echo $fecHasta; ?>" onblur="getLog();"></td>
                </tr>
              </tbody>
            </table>

		        <table class="table" id="logtable" style="width:100%">
              <thead>
                <tr>
                  <th>USUARIO</th>
                  <th>FECHA</th>
                  <th>ACCION</th>
		              <th>INVOLUCRADO</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

          <div class="modal-footer">
			      <button type="button" class="btn btn-primary downloadExcel" data-dismiss="modal">Descargar .xls</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <nav class="navbar navbar-inverse">
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
          <ul class="nav navbar-nav">
            <li class="active"><a href="admin.php">Administrador</a></li>
          </ul>
        
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#" data-toggle="modal" data-target="#modallog" ><span class=""></span>Registro de actividades</a></li>
            <li><a href="#" id = "salir"><span class="glyphicon glyphicon-log-in"></span> <?php echo $_SESSION['nombre']; ?></a></li>
<?php 
  if (isset($_SESSION['id_usuario'])) {
?>
            <li><a href="nuevacontrasenaAdmin.php"><span class="glyphicon glyphicon-lock"></span></a></li>
<?php 
  }
?>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid text-center">
      <h2>Panel de administracion de accesos por usuarios dentro del organigrama</h2>
      <div class="row content">
        <div class="col-sm-3 text-left">
          <h3>Usuarios activos</h3>
          <div class="input-group">
            <input type="text" class="form-control" id="nombreUsuario" placeholder="usuario...">
            <span class="input-group-btn">
              <button class="btn btn-default" id="buscar" type="button">Buscar</button>
              <button class="btn btn-default glyphicon glyphicon-remove" style="background-color:#ed365a;color:white" id="todos" type="button"></button>
            </span>
          </div>
          <div class="list-group ListUsuarios" style="max-height: 800px;overflow: scroll;"></div>
        </div>
    
        <div class="col-sm-6 text-left">
          <h3>Habilitacion de visualizacion de organigrama</h3>
          <div class="listaGerencias" style="max-height: 800px;overflow: scroll;"></div>
	        <div class="" style="float:right">
            <button type="button" class="btn btn-primary" id="marcartodos" name="button">Marcar todos</button>
            <button type="button" class="btn btn-primary" id="desmarcartodos" name="button">Desmarcar todos</button>
          </div>
        </div>

        <div class="col-sm-3 text-left">
          <h3>Visualizacion de datos habilitados</h3>
          <div class="divPermisos"></div>
		  <hr/>
		  <h3>Opciones de Organigrama</h3>
          <div class="divOpciones" style="display:none">
			<?php
				$conexion = new  conexionMySQL();
				$conn = $conexion ->conectar();
				$go = "select * from opciones order by nombre_opcion asc";
				$gpr = $conn->query($go);
				while($row = $gpr->fetch_array(MYSQLI_NUM)){
					$nombre_opcion = $row[1];
					$id_opcion = $row[0];
			?>
			<div class="checkbox" style="margin-top: 10px;"> 
				<label> 
					<input type="checkbox" name="opcion" value="<?php echo $id_opcion;?>"> 
					<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span><?php echo $nombre_opcion;?>
				</label> 
			</div>
			
			
			<?php
				}
			?>
		  </div>
        </div>
      </div>
    </div>

    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/jquery.table2excel.min.js"></script>
    <script src="js/configuraciones.js"></script>

    <script type="text/javascript" >
      $(document).on("click", ".downloadExcel", function() {
        $("#logtable").table2excel({
            exclude: ".excludeThisClass",
            name: "Worksheet Name",
            filename: "logs.xls" //do not include extension
        });
      });
	  
	  function g_opciones(){
		  var cod_usuario = $(".itemUsuario.list-group-item.active").data("func");
		  
		  if(cod_usuario > 0){
		  
		  $.ajax({
				url: "inc/opciones.php",
				type: "POST",
				dataType:'json',
				data: {o: 0, c : cod_usuario},
				success: function(data) {
					$(".divOpciones [type=checkbox]").prop("checked", false);	
					jQuery.each(data, function(index, item) {
						var id_opp = data[index].id_opcion;
						$('input:checkbox[name="opcion"][value="' + id_opp + '"]').prop('checked', true);
					});
					
					
				},complete: function(){
					$(".divOpciones").css('pointer-events','').css("opacity","1");
				}
			});
		  }
		  
	  }
	  
	  
	  $(".divOpciones [type=checkbox]").on("change",function(){
		  var id_usuario = $(".itemUsuario.list-group-item.active").data("func");
		  var id_opcion = $(this).val();
		  $(".divOpciones").css('pointer-events','none').css("opacity","0.4");
		  
		  if(id_usuario > 0 && id_opcion > 0){
		  $.ajax({
				url: "inc/opciones.php",
				type: "POST",
				dataType:'json',
				data: {o: id_opcion, c : id_usuario},
				success: function(data) {
					var status = data[0].status;
					var mensaje = data[0].mensaje;
					
					if(status == "success"){
						// alert("EXITO!: "+data[0].mensaje);
					}else {
						alert("ALERTA!: "+data[0].mensaje);
					}
					
					
				},complete: function(){
					$(".divOpciones").css('pointer-events','').css("opacity","1");
				}
			})
		  }else {
			alert("Algo fue mal!");	
		  }
		  
		  
	  })
	  
	  $(document).on("click",".itemUsuario",function(){
		  $(".divOpciones").fadeIn();
		  g_opciones();
	  })
	  
    </script>
   
    <script type="text/javascript">
      $(document).ready(function() {
        /* Custom filtering function which will search data in column four between two values */
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                var fechaActual = data[1];
                var desdeFilter = $('#desdeFilter').val();
                var hastaFilter = $('#hastaFilter').val();

                if (desdeFilter <= fechaActual && hastaFilter >= fechaActual) {
                    return true;
                }
                return false;
            }
        );

        $('#logtable thead tr').clone(true).appendTo('#logtable thead');
          $('#logtable thead tr:eq(1) th').each( function (i) {
            var title = $(this).text();
            
            $(this).html('<input type="text" placeholder="Filtrar '+title+'" />');
            $('input', this).on('keyup change', function() {
              if (table.column(i).search() !== this.value) {
                table
                  .column(i)
                  .search(this.value)
                  .draw();
              }
            });
        });

        var fecDes  = document.getElementById('desdeFilter').value;
        var fecHat  = document.getElementById('hastaFilter').value;
        var urlDat  = 'http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/inc/consultalog.php?var01='+fecDes+'&var02='+fecHat; 

        var table = $('#logtable').DataTable({
          "orderCellsTop": true,
          "fixedHeader": true,
          "info": true,
          "searching"	: true,
		      "paging"		: true,
          "ajax": {
              url: "http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/inc/consultalog.php",
              data: function(d){
                d.var01 = $('#desdeFilter').val();
                d.var02 = $('#hastaFilter').val();
              }
          },
          "columns": [
            {"data": "USUARIO"},
            {"data": "FECHA"},
            {"data": "ACCION"},
				    {"data":"INVOLUCRADO"}
          ]
        });

		    table.order([1, 'desc'])

        $('#desdeFilter, #hastaFilter').keyup(function() {
            table.draw();
        });
      });
    </script>

    <script type="text/javascript">
      $(document).on("click", "#todos", function() {
        $(".usergroup").fadeIn();
      });

      $(document).on("click", "#buscar", function() {
        var usuario = $("#nombreUsuario").val();
        usuario = usuario.toUpperCase();
        $(".usergroup").fadeOut();
        $(".user"+usuario).fadeIn();
      });

      $(document).on("click", "#marcartodos", function() {
        $(this).html('<img src="img/miniloading.gif" alt="Loading View" style="width: 25px;">');
        $(this).attr("disabled", true);
        var array=new Array(localStorage.getItem("idusuario"), "");
        var result = setValuesAjax(array, "inc/controlador.php", "marcartodos", "asignacion", function(result) {
          if (result.result == "success") {
            location.reload();
          } else {
            alert("Se produjo un error inesperado")
          }
        });
      });

      $(document).on("click", ".reset", function() {
        var id = $(this).data("usuario");
        var r  = confirm("¿Quiere restaurar la contraseña de este usuario?");
        if (r == true) {
          var array = new Array(id, "");
          var result= setValuesAjax(array, "inc/controlador.php", "ResetPasswordUser", "login", function(result) {
            if (result.result == "success") {
              alert("Restauracion de contraseña exitosa");
            } else {
              alert("Se produjo un error inesperado")
            }
          });
        } else {
        }
      });

      $(document).on("click", ".ranking", function() {
        var id  = $(this).data("usuario");
        var r   = confirm("¿Quiere permitir el acceso a los rankings a este usuario?");
        if (r == true) {
          var array = new Array(id, "");
          var result= setValuesAjax(array, "inc/controlador.php", "AllowRanking", "login", function(result) {
            if (result.result == "success") {
              alert("Permiso asignado!");
            } else {
              alert("Se produjo un error inesperado")
            }
          });
        } else {
        }
      });

      $(document).on("click",".quitranking", function() {
        var id  = $(this).data("usuario");
        var r   = confirm("¿Quiere quitar el acceso a los rankings a este usuario?");
        if (r == true) {
          var array = new Array(id, "");
          var result= setValuesAjax(array, "inc/controlador.php", "DeniedRanking", "login", function(result) {
            if (result.result == "success") {
              alert("Permiso retirado!");
              location.reload();
            } else {
              alert("Se produjo un error inesperado")
            }
          });
        } else {
        }
      });

      $(document).on("click", "#desmarcartodos", function() {
	      $(this).html('<img src="img/miniloading.gif" alt="Loading View" style="width: 25px;">');
	      $(this).attr("disabled",true);
        var array = new Array(localStorage.getItem("idusuario"), "");
        var result= setValuesAjax(array, "inc/controlador.php", "desmarcartodos", "asignacion", function(result) {
          if (result.result == "success") {
            location.reload();
          } else {
            alert("Se produjo un error inesperado")
          }
        });
      });

      $(document).on("click", "#salir", function() {
        var array = new Array(GetVal("#usuario"), GetVal("#pass"));
        var result= setValuesAjax(array, "inc/controlador.php", "logOut", "login", function(result) {
          if (result.result == "success") {
            window.location.replace("loginAdmin.html");
          } else {
            alert("Se produjo un error inesperado")
          }
        });
      });
    </script>

    <script type="text/javascript">
      function getLog() {
        $('#logtable').DataTable().clear();
        $('#logtable').DataTable().ajax.reload();
      }

      function cargarColaboradores() {
      }

      function desplegarhijos(func) {
        var divs= $('.accordion'+func+'>div').hide(); //Hide/close all containers
        var h2s = $('.accordion'+func+'>a').click(function () {
          var idS = func;
          var usuarioS = localStorage.getItem("idusuario");
          localStorage.setItem("idcolaborador", idS);
          var array = new Array(idS, usuarioS);
          var result= setValuesAjax(array, "inc/controlador.php", "selectAsignacion", "asignacion", function(result) {
            $(".itemPermiso").prop('checked', false);
            eachJson(result.moreData, function(json) {
              $(".permiso"+json.ID_PERMISO).prop("checked",true);
            })
          });
        
          if(!$(this).hasClass('listado')) {
            var id = $(this).data("id");
            var usuario = localStorage.getItem("idusuario");
            var array   = new Array(id,usuario);
            var result  = GetValueAjax(array, "inc/controlador.php", "ListarColaboradores", "asignacion", function(result) {
              if (result.result == "success") {
                eachJson(result.moreData, function(json) {
                  if (json.estado == "true") {
                    var html = '<div style="display:flex"><div class="checkbox" > <label> <input type="checkbox" class="checkGrupal" data-id= '+json.COD_FUNC+' value="10" checked> <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span></label> </div> <div class="accordionhijos accordion'+json.COD_FUNC+'"  data-id="'+json.COD_FUNC+'" style="background:white;padding:0px;width:100%">'
                    +'<a  class="gerenciaItem interDiv'+json.COD_FUNC+' list-group-item" data-id="'+json.COD_FUNC+'">'+json.NOMBRE_Y_APELLIDO+'</a>'
                    +''
                    +'<div class="acc_container container'+json.COD_FUNC+'" style="display: none;"></div></div></div>';
                  } else {
                    var html = '<div style="display:flex"><div class="checkbox" > <label> <input type="checkbox" class="checkGrupal" data-id= '+json.COD_FUNC+' value="10" > <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span></label> </div> <div class="accordionhijos accordion'+json.COD_FUNC+'"  data-id="'+json.COD_FUNC+'" style="background:white;padding:0px;width:100%">'
                    +'<a  class="gerenciaItem interDiv'+json.COD_FUNC+' list-group-item" data-id="'+json.COD_FUNC+'">'+json.NOMBRE_Y_APELLIDO+'</a>'
                    +''
                    +'<div class="acc_container container'+json.COD_FUNC+'" style="display: none;"></div></div></div>';
                  }
                
                  $(".container"+id).append(html);
                  desplegar(json.COD_FUNC);
                });

                $(".checkGrupal").change(function() {
                  var  id     = $(this).data("id");
                  var func    = $(this).data("func");
                  var usuario = localStorage.getItem("idusuario");
                  if(this.checked) {
                    var array   = new Array(id, usuario, 'agregar');
                    var result  = setValuesAjax(array, "../inc/nuevaAsignacion.php", "", "", function(result) {
                      if (result.result == "success") {
                      } else {
                        alert("Se produjo un error inesperado")
                      }
                    });
                  } else {
                    var array   = new Array(id, usuario, 'quitar');
                    var result  = setValuesAjax(array, "../inc/nuevaAsignacion.php", "", "", function(result) {
                      if (result.result == "success") {
                      } else {
                        alert("Se produjo un error inesperado")
                      }
                    });
                  }
                });

                $('.accordion'+id+'>a').addClass("listado")
              } else {
                alert("Se produjo un error inesperado")
              }
            });
          }
        
          h2s.not(this).removeClass('active')
          $(".colaboradorSelected").removeClass('colaboradorSelected');
          $(this).toggleClass('colaboradorSelected')
          divs.not($(this).next()).slideUp()
          $(this).next().slideToggle()
          return false; //Prevent the browser jump to the link anchor
        });
      }

      function desplegar(func) {
        var divs= $('.accordion'+func+'>div').hide(); //Hide/close all containers
        var h2s = $('.accordion'+func+'>a').click(function () {
          var idS     = func;
          var usuarioS= localStorage.getItem("idusuario");
          localStorage.setItem("idcolaborador",idS);
          var array = new Array(idS, usuarioS);
          var result= setValuesAjax(array, "inc/controlador.php", "selectAsignacion", "asignacion", function(result) {
            $(".itemPermiso").prop('checked', false);
            eachJson(result.moreData,function(json) {
              $(".permiso"+json.ID_PERMISO).prop("checked",true);
            })
          });

          if(!$(this).hasClass('listado')) {
            var id      = $(this).data("id");
            var usuario = localStorage.getItem("idusuario");
            var array   = new Array(id, usuario);
            var result  = GetValueAjax(array, "inc/controlador.php", "ListarColaboradores", "asignacion", function(result) {
              if (result.result == "success") {
                eachJson(result.moreData, function(json) {
                  if (json.estado == "true") {
                    var html = '<div style="display:flex"><div class="checkbox" > <label> <input type="checkbox" class="checkGrupal" data-id= '+json.COD_FUNC+' value="10" checked> <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span></label> </div> <div class="accordionhijos accordion'+json.COD_FUNC+'"  data-id="'+json.COD_FUNC+'" style="background:white;padding:0px;width:100%">'
                    +'<a  class="gerenciaItem interDiv'+json.COD_FUNC+' list-group-item" data-id="'+json.COD_FUNC+'">'+json.NOMBRE_Y_APELLIDO+'</a>'
                    +''
                    +'<div class="acc_container container'+json.COD_FUNC+'" style="display: none;"></div></div></div>';
                  } else {
                    var html = '<div style="display:flex"><div class="checkbox" > <label> <input type="checkbox" class="checkGrupal" data-id= '+json.COD_FUNC+' value="10" > <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span></label> </div> <div class="accordionhijos accordion'+json.COD_FUNC+'"  data-id="'+json.COD_FUNC+'" style="background:white;padding:0px;width:100%">'
                    +'<a  class="gerenciaItem interDiv'+json.COD_FUNC+' list-group-item" data-id="'+json.COD_FUNC+'">'+json.NOMBRE_Y_APELLIDO+'</a>'
                    +''
                    +'<div class="acc_container container'+json.COD_FUNC+'" style="display: none;"></div></div></div>';
                  }
                
                  $(".container"+id).append(html);
                  desplegarhijos(json.COD_FUNC);
                });
              
                $(".checkGrupal").change(function() {
                  var id      = $(this).data("id");
                  var func    = $(this).data("func");
                  var usuario = localStorage.getItem("idusuario");
                  
                  if(this.checked) {
                    var array = new Array(id, usuario, 'agregar');
                    var result= setValuesAjax(array, "../inc/nuevaAsignacion.php", "", "", function(result) {
                      if (result.result == "success") {
                      } else {
                        alert("Se produjo un error inesperado")
                      }
                    });
                  } else {
                    var array  = new Array(id,usuario,'quitar');
                    var result = setValuesAjax(array, "../inc/nuevaAsignacion.php", "", "", function(result) {
                      if (result.result == "success") {
                      } else {
                        alert("Se produjo un error inesperado")
                      }
                    });
                  }
                });
                $('.accordion'+id+'>a').addClass("listado")
              } else {
                alert("Se produjo un error inesperado")
              }
            });
          }

          h2s.not(this).removeClass('active')
          $(".colaboradorSelected").removeClass('colaboradorSelected');
          $(this).toggleClass('colaboradorSelected')
          divs.not($(this).next()).slideUp()
          $(this).next().slideToggle()
          return false; //Prevent the browser jump to the link anchor
        });
      }

      var result = GetValueAjax("", "inc/controlador.php", "ListarUsuarios", "asignacion", function(result) {
        if (result.result=="success") {
          eachJson(result.moreData,function(json) {
            var str = json.USUARIO; 
            if(str === null) {
	            str="Sin nombre de usuario";
            }
        
            json.USUARIO = str.replace(/ /g, ' ');
            if (json.rankings !="1") {
              var html =' <div class="input-group usergroup user'+json.USUARIO+'">'
              +'<a href="#" data-id="'+json.id+'" data-func="'+json.COD_FUNC+'"  class="'+json.USUARIO+' itemUsuario list-group-item ">'+json.USUARIO+'</a>'
              +'<span class="input-group-btn">'
              +'<button class ="btn btn-primary reset glyphicon glyphicon-lock" style="height:42px" data-usuario="'+json.COD_FUNC+'" ></button> <button class ="btn btn-primary ranking glyphicon glyphicon-stats" style="height:42px" data-usuario="'+json.COD_FUNC+'" ></button>< </span>'
              +'</div><!-- /input-group -->';  
            } else {
              var html =' <div class="input-group usergroup user'+json.USUARIO+'">'
              +'<a href="#" data-id="'+json.id+'" data-func="'+json.COD_FUNC+'"  class="'+json.USUARIO+' itemUsuario list-group-item ">'+json.USUARIO+'</a>'
              +'<span class="input-group-btn">'
              +'<button class ="btn btn-primary reset glyphicon glyphicon-lock" style="height:42px" data-usuario="'+json.COD_FUNC+'" ></button> <button class ="btn btn-success quitranking glyphicon glyphicon-stats" style="height:42px" data-usuario="'+json.COD_FUNC+'" ></button>< </span>'
              +'</div><!-- /input-group -->';  
            }
        
            $(".ListUsuarios").append(html);
          });

          $('.itemUsuario').click(function() {
			
            var result = setValuesAjax("", "inc/controlador.php", "selectPermisos", "asignacion", function(result) {
              $(".divPermisos").empty();
			  $(".divOpciones [type=checkbox]").prop("checked", false);
              eachJson(result.moreData, function(json) {
                var html='  <div class="checkbox" > <label> <input type="checkbox" class="permiso'+json.id+' itemPermiso" data-id= "'+json.id+'" value="10" > <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>'+json.nombre+'</label> </div>';
                $(".divPermisos").append(html);
              });

              $(".itemPermiso").change(function() {
                var id            = $(this).data("id");
                var usuario       = localStorage.getItem("idusuario");
                var colalaborador = localStorage.getItem("idcolaborador");
            
                if(this.checked) {
                  var array  = new Array(usuario, colalaborador, id);
                  var result = setValuesAjax(array, "inc/controlador.php", "insertAsignacion", "asignacion", function(result) {});
                } else {
                  var array = new Array(usuario,colalaborador,id);
                  var result = setValuesAjax(array, "inc/controlador.php", "deleteAsignacion", "asignacion", function(result) { });
                }
              });
            });

            $('.itemUsuario').removeClass("active");
            $(this).addClass("active");
      
            var id   = $(this).data("id");
            var func = $(this).data("func");
            localStorage.setItem("idusuario", id);
            localStorage.setItem("codfunc", func);
      
            $(".listaGerencias").empty();
      
            function toTitleCase(str) {
              return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
            }
      
            $.ajax({
              url:"inc/controlador.php",
              data:{
                class:"asignacion",
                function:"selectRaiz",
                usuario :localStorage.getItem("idusuario")
              },
              success: function(result) {
                for (var i = 0; i < result.moreData.length; i++) {
                  var coloborador = result.moreData[i].NOMBRE_Y_APELLIDO;
            
                  if(coloborador  == null ){
                    coloborador = "no definido"
                  }
          
                  coloborador = toTitleCase(coloborador);

                  if(result.moreData[i].estado == "true") {
                    var html = '<div style="display:flex"><div class="checkbox" > <label>   <input type="checkbox" class="checkGrupal" data-id= '+result.moreData[i].COD_FUNC+' value="10" checked> <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span></label> </div> <div class="accordionList accordion'+result.moreData[i].COD_FUNC+'"  data-id="'+result.moreData[i].COD_FUNC+'" style="background:white;padding:0px;width:100%">'
                    +'<a  class="gerenciaItem interDiv'+result.moreData[i].COD_FUNC+' list-group-item" data-id="'+result.moreData[i].COD_FUNC+'">'+coloborador+'</a>'
                    +''
                    +'<div class="acc_container container'+result.moreData[i].COD_FUNC+'" style="display: none;"></div></div></div>';
          
                    $(".listaGerencias").append(html);
                  } else {
                    var html = '<div style="display:flex"><div class="checkbox" > <label> <input type="checkbox" class="checkGrupal" data-id= '+result.moreData[i].COD_FUNC+' value="10" > <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span></label> </div> <div class="accordionList accordion'+result.moreData[i].COD_FUNC+'"  data-id="'+result.moreData[i].COD_FUNC+'" style="background:white;padding:0px;width:100%">'
                    +'<a  class="gerenciaItem interDiv'+result.moreData[i].COD_FUNC+' list-group-item" data-id="'+result.moreData[i].COD_FUNC+'">'+coloborador+'</a>'
                    +''
                    +'<div class="acc_container container'+result.moreData[i].COD_FUNC+'" style="display: none;"></div></div></div>';
           
                    $(".listaGerencias").append(html);
                  }

                  $('.accordion'+result.moreData[i].COD_GERENCIA).click(function() {
                    $(".accordionList").css("background","white")
                    $(this).css("background","#c59d4d");
                    var id = $(this).data("id");
                  });

                  $('.interDiv'+result.moreData[i].COD_GERENCIA).click(function() {
                    var id = $(this).data("id");
                    // CargaDiagrama(id);
                  });

                  $(".checkGrupal").change(function() {
                    var  id     = $(this).data("id");
                    var func    = $(this).data("func");
                    var usuario = localStorage.getItem("idusuario");
                
                    if (this.checked) {
                      var array = new Array(id, usuario, 'agregar');
                      var result= setValuesAjax(array, "../inc/nuevaAsignacion.php", "", "", function(result) {
                        if (result.result == "success") {
                        } else {
                          alert("Se produjo un error inesperado")
                        }
                      });
                    } else {
                      var array  = new Array(id, usuario, 'quitar');
                      var result = setValuesAjax(array, "../inc/nuevaAsignacion.php", "", "", function(result) {
                        if (result.result == "success") {
                        } else {
                          alert("Se produjo un error inesperado")
                        }
                      });
                    }
                  });
                }

                var divs = $('.accordionList>div').hide(); //Hide/close all containers
                var h2s  = $('.accordionList>a').click(function () {
                  var idS = $(this).data("id");
                  localStorage.setItem("idcolaborador", idS);
                  var usuarioS = localStorage.getItem("idusuario");
                  var array  = new Array(idS,usuarioS);
                  var result = setValuesAjax(array, "inc/controlador.php", "selectAsignacion", "asignacion", function(result) {
                    $(".itemPermiso").prop('checked', false);
                    eachJson(result.moreData, function(json) {
                      $(".permiso"+json.ID_PERMISO).prop('checked', true);
                    })
                  });

                  if(!$(this).hasClass('listado')) {
                    var id      = $(this).data("id");
                    var usuario = localStorage.getItem("idusuario");
                    var array   = new Array(id,usuario);
                    var result  = GetValueAjax(array, "inc/controlador.php", "ListarColaboradores", "asignacion", function(result) {
                      if (result.result == "success") {
                        eachJson(result.moreData, function(json) {
                          if (json.estado == "true") {
                            var html = '<div style="display:flex"><div class="checkbox" > <label> <input type="checkbox" class="checkGrupal" data-id= '+json.COD_FUNC+' value="10" checked> <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span></label> </div> <div class="accordionhijos accordion'+json.COD_FUNC+'"  data-id="'+json.COD_FUNC+'" style="background:white;padding:0px;width:100%">'
                            +'<a  class="gerenciaItem interDiv'+json.COD_FUNC+' list-group-item" data-id="'+json.COD_FUNC+'">'+json.NOMBRE_Y_APELLIDO+'</a>'
                            +''
                            +'<div class="acc_container container'+json.COD_FUNC+'" style="display: none;"></div></div></div>';
                          } else {
                            var html = '<div style="display:flex"><div class="checkbox" > <label> <input type="checkbox" class="checkGrupal" data-id= '+json.COD_FUNC+' value="10" > <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span></label> </div> <div class="accordionhijos accordion'+json.COD_FUNC+'"  data-id="'+json.COD_FUNC+'" style="background:white;padding:0px;width:100%">'
                            +'<a  class="gerenciaItem interDiv'+json.COD_FUNC+' list-group-item" data-id="'+json.COD_FUNC+'">'+json.NOMBRE_Y_APELLIDO+'</a>'
                            +''
                            +'<div class="acc_container container'+json.COD_FUNC+'" style="display: none;"></div></div></div>';
                          }
                    
                          $(".container"+id).append(html);
                          desplegar(json.COD_FUNC);
                        });
                  
                        $(".checkGrupal").change(function() {
                          var id      = $(this).data("id");
                          var func    = $(this).data("func");
                          var usuario = localStorage.getItem("idusuario");

                          if(this.checked) {
                            var array = new Array(id, usuario, 'agregar');
                            var result= setValuesAjax(array, "../inc/nuevaAsignacion.php", "", "", function(result) {
                              if (result.result == "success") {
                              } else {
                                alert("Se produjo un error inesperado")
                              }
                            });
                          } else {
                            var array  = new Array(id, usuario, 'quitar');
                            var result = setValuesAjax(array, "../inc/nuevaAsignacion.php", "", "", function(result) {
                              if (result.result == "success") {
                              } else {
                                alert("Se produjo un error inesperado")
                              }
                            });
                          }
                        });

                        $('.accordion'+id+'>a').addClass("listado")
                      } else {
                        alert("Se produjo un error inesperado")
                      }
                    });
                  }

                  h2s.not(this).removeClass('active')
                  $(".colaboradorSelected").removeClass('colaboradorSelected');
                  $(this).toggleClass('colaboradorSelected')
                  divs.not($(this).next()).slideUp()
                  $(this).next().slideToggle()
                  return false; //Prevent the browser jump to the link anchor
                });

                $(".checkGrupal").change(function() {
                  var id      = $(this).data("id");
                  var func    = $(this).data("func");
                  var usuario = localStorage.getItem("idusuario");

                  if(this.checked) {
                    var array = new Array(id,usuario,'agregar');
                    var result= setValuesAjax(array, "../inc/nuevaAsignacion.php", "", "", function(result) {
                      if (result.result == "success") {
                      } else {
                        alert("Se produjo un error inesperado")
                      }
                    });
                  } else {
                    var array  = new Array(id, usuario, 'quitar');
                    var result = setValuesAjax(array, "../inc/nuevaAsignacion.php", "", "", function(result) {
                      if (result.result == "success") {
                      } else {
                        alert("Se produjo un error inesperado")
                      }
                    });
                  }
                });
              },
              dataType:"json",
              type:"POST"
            });
          });
        } else {
          alert("Se produjo un error inesperado")
        }
      });
    </script>
  </body>
</html>
<?php
  } else {
    echo "Requiere permisos de administrador";
  }
?>
