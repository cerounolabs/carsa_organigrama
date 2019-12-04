
function MostrarMonedas(callback) {
  $.ajax({
    url:"inc/controlador.php",
    success:function(rows) {
      var primaria ="";

      if (rows != null) {
        $.each(rows, function(i, row) {
          if (row.primaria == "t")  {
            primaria = "true";
            var html ='<tr style="border: 2px solid #30a5ff;"><td style="width: 20%;">'+row.simbolo+'</td><td id="tdValor'+row.id+'" >'+row.valor+'</td><td id="tdMin'+row.id+'">'+row.unid_menor+'</td><td></td><td><center><spam class= "glyphicon glyphicon-ok"></spam></center></td></tr>';
            $(html).appendTo("#tablaMonedas");
          } else {
            var html ='<tr><td style="width: 20%;">'+row.simbolo+'</td><td id="tdValor'+row.id+'">'+row.valor+'</td><td id="tdMin'+row.id+'">'+row.unid_menor+'</td><td><button data-id="'+row.id+'" type="button" class="btnEdit btn btn-primary glyphicon glyphicon-pencil" name="button"></button></td><td class="definePrimary" data-id="'+row.id+'" ></td></tr>';
            $(html).appendTo("#tablaMonedas");
          }
        });
      } else {
        var primaria ="";
      }

      if(primaria != "true") {
        $(".definePrimary").html('<button type="button" data-id="2" class="btnSavePrimary btn btn-primary glyphicon glyphicon-ok" style="background-color: #30a5ff;border-color:#30a5ff;" name="button"></button>')
        callback(primaria);
      } else {
        callback(primaria);
      }
    },
    data:{
      class:"switchMonedas",
      function:"ConsultarMonedas",
    },
    dataType:"json",
    type:"POST"
  });
}

function serializador(elemento) {
  var result = $(elemento).serializeArray();
  alert(result)
  return result;
}

function eachJson(data, callback) {
  $.each(data, function(i, item) {
    callback(data[i])
  })
}

function GetVal(elemento) {
  var result = $(elemento).val();
  return result
}

function GetHTML(elemento) {
  var result = $(elemento).html();
  return result
}

function SetHtml(elemento, dato) {
  $(elemento).html(dato);
}

function click(elemento, callback) {
  $(document).on("click", elemento, function () {
    callback(this);
  });
}

function GetValueAjax(datos, url, funcion, clase, callback) {
  $.ajax({
    url:url,
    success:function(result) {
      callback(result);
    },
    data: {
      class:clase,
      function:funcion,
      data:datos
    },
    error: function (jqXHR, exception) {
      var msg = '';
      
      if (jqXHR.status === 0) {
        msg = 'Not connect.\n Verify Network.';
      } else if (jqXHR.status == 404) {
        msg = "URL no encontrada (ಠ_ಠ) " + url;
      } else if (jqXHR.status == 500) {
        msg = "Error interno de servidor: ¯\_(ツ)_/¯";
      } else if (exception === 'parsererror') {
        msg = 'Requested JSON parse failed.';
      } else if (exception === 'timeout') {
        msg = 'Time out error.';
      } else if (exception === 'abort') {
        msg = 'Ajax request aborted.';
      } else {
        msg = 'Uncaught Error.\n' + jqXHR.responseText;
      }
      
      alert(msg)
    },
    dataType:"json",
    type:"POST"
  });
}


function setValuesAjax(datos, url, funcion, clase, callback) {
  $.ajax({
    url:url,
    success:function(result) {
      callback(result)
    },
    data: {
      class:clase,
      function:funcion,
      data:datos
    },
    error: function (jqXHR, exception) {
      var msg = '';
      
      if (jqXHR.status === 0) {
        msg = 'Not connect.\n Verify Network.';
      } else if (jqXHR.status == 404) {
        msg = "La URL no funciona (ಠ_ಠ) " + url;
      } else if (jqXHR.status == 500) {
        msg = "Error interno de servidor: ¯\_(ツ)_/¯";
      } else if (exception === 'parsererror') {
        msg = 'Requested JSON parse failed.';
      } else if (exception === 'timeout') {
        msg = 'Time out error.';
      } else if (exception === 'abort') {
        msg = 'Ajax request aborted.';
      } else {
        msg = 'Uncaught Error.\n' + jqXHR.responseText;
      }

      alert(msg)
    },
    dataType:"json",
    type:"POST"
  });
}