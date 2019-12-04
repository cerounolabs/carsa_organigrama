<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/login.css">

        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>

        <title>Inicio sesion administrador</title>
    </head>

    <body>
        <div class="login-form">
            <form action="/examples/actions/confirmation.php" method="post">
		        <div class="avatar background_default"><i class="glyphicon glyphicon-user"></i></div>
                
                <h4 class="modal-title">Ingrese su cuenta de administrador</h4>

                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Usuario" id="usuario" required="required">
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Contraseña" id="pass" required="required">
                </div>

                <div class="form-group small clearfix">
                    <label style="display:none" class="checkbox-inline"><input type="checkbox">Recordar</label>
                    <a style="display:none" href="#" class="forgot-link">¿Se te olvid&oacute; tu contrase&ntilde;a?</a>
                </div>

                <input type="button" class="btn btn-primary btn-block btn-lg btnLogin background_default" value="Login">
            </form>

            <div class="text-center small">Iniciar como <a href="loginUser.php">usuario</a></div>
        </div>

        <script src="js/configuraciones.js"></script>
        <script type="text/javascript">
            $(document).on("click", ".btnLogin", function () {
                var array   = new Array(GetVal("#usuario"), GetVal("#pass"));
                var result  = setValuesAjax(array, "inc/controlador.php", "loginAdmin", "login", function(result) {
                    if (result.result=="success") {
                        window.location.replace("admin.php");
                    } else {
                        alert(result.error)
                    }
                });
            });
        </script>
    </body>
</html>