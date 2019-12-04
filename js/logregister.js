function registrar(actividad) {
    $.ajax({
        url:"inc/funciones.php",
        success: function(resultc) {
            console.log(resultc);
        },
        data:{
            actividad:actividad,
            funcion:"logregister"
        },
        dataType:"json",
        type:"get"
    });
}