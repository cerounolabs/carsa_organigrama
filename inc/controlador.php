<?php
session_start();
include "queryMySQL.php";
include "login.php";
include 'asignacion.php';
//include '../../inc/contador.php';
error_log("controlador ok");
class controlador
{



  public function PrimarySwitch($class='')
  {
    error_log("PrimarySwitch ok");
    $this->$class=$class;


    switch ($class)
    {
      case 'login':
      {

        $switchLogin = new switchLogin();
        $switchLogin->switchF($_POST['function']);

        error_log("switch login controlador ok");

      }
      break;
      case 'asignacion':
      {

        $switchAsignacion = new switchAsignacion();
        $switchAsignacion->switchF($_POST['function']);

        error_log("switch asignacion controlador ok");

      }
      break;
	  
	        case 'contador':
      {
/*
        $contador = new contador();
        $contador->contar();
*/
        error_log("switch asignacion contador ok");

      }
      break;



      default:
      echo "sin class";
      break;

    }


  }

}



//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
if(isset($_POST['class']) and isset($_POST['function']))
{

  $switchPrimario = new controlador();
  $switchPrimario ->PrimarySwitch($_POST['class']);


error_log(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>".$_POST['function']."-".$_POST['class']);

}
else
{
$arrayName = array('result' =>'error' ,'error'=>'SIN PARAMETROS DE REFERENCIA' );
header('Content-type: application/json');
echo json_encode($arrayName);
}










 ?>
