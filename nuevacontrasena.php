<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Modificar contraseña</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<style type="text/css">
    body {
        color: #999;
		background: #f5f5f5;
		font-family: 'Varela Round', sans-serif;
	}
	.form-control {
		box-shadow: none;
		border-color: #ddd;
	}
	.form-control:focus {
		border-color: #4aba70;
	}
	.login-form {
        width: 350px;
		margin: 0 auto;
		padding: 30px 0;
	}
    .login-form form {
        color: #434343;
		border-radius: 1px;
    	margin-bottom: 15px;
        background: #fff;
		border: 1px solid #f3f3f3;
        box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
        padding: 30px;
	}
	.login-form h4 {
		text-align: center;
		font-size: 22px;
        margin-bottom: 20px;
	}
    .login-form .avatar {
        color: #fff;
		margin: 0 auto 30px;
        text-align: center;
		width: 100px;
		height: 100px;
		border-radius: 50%;
		z-index: 9;
		background: #4aba70;
		padding: 15px;
		box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
	}
    .login-form .avatar i {
        font-size: 62px;
    }
    .login-form .form-group {
        margin-bottom: 20px;
    }
	.login-form .form-control, .login-form .btn {
		min-height: 40px;
		border-radius: 2px;
        transition: all 0.5s;
	}
	.login-form .close {
        position: absolute;
		top: 15px;
		right: 15px;
	}
	.login-form .btn {
		background: #4aba70;
		border: none;
		line-height: normal;
	}
	.login-form .btn:hover, .login-form .btn:focus {
		background: #42ae68;
	}
    .login-form .checkbox-inline {
        float: left;
    }
    .login-form input[type="checkbox"] {
        margin-top: 2px;
    }
    .login-form .forgot-link {
        float: right;
    }
    .login-form .small {
        font-size: 13px;
    }
    .login-form a {
        color: #4aba70;
    }
</style>
</head>
<body>
<div class="login-form">
    <form action="/examples/actions/confirmation.php" method="post">
		<div class="avatar" style="background:#b78d0f"><i class="glyphicon glyphicon-lock"></i></div>
    	<h4 class="modal-title">Cambio de Contraseña</h4>
        <div class="form-group">
            <input type="password" class="form-control" placeholder="Nueva contraseña" id="pass1"  required="required">
        </div>
        <div class="form-group">
            <input type="password" class="form-control" placeholder="Repita la contraseña" id="pass2" required="required">
        </div>
        <div class="form-group small clearfix">


        </div>
        <input type="button" class="btn btn-primary btn-block btn-lg btnLogin" style="background:#b78d0f" value="Guardar">
    </form>

</div>





<script src="js/configuraciones.js"></script>

<script type="text/javascript">


$(document).on("click",".btnLogin", function ()
{

  var pass1 = $("#pass1").val();
  var pass2 = $("#pass2").val();

if(pass1==pass2)
{

  var array=new Array(pass1,pass2);
  var result = setValuesAjax(array,"inc/controlador.php","ChangePasswordUser","login",function(result)

  {


    console.log(result);
  if (result.result=="success")
  {


   alert("La nueva contraseña ha sido guardada");
   window.location.replace("diagrama.php");


  }
  else
  {
alert(result.error)
  }


});
}
else
{
  
alert("Las contrase�as no coinciden");
}
});

</script>




</body>
</html>
