
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>º</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
      
      <style>
      
      td
      {
        border:1px solid black;
		width:200px;
      }
      
      </style>
       
    </head>
    <body>

<form action="consultamssql.php" style="width:800px;" method="get">

SQL: <br> <textarea style="width:800px;" name="sql" id="" cols="30" rows="10"></textarea>

  <input type="submit" value="Submit">
</form>

    <?php
include "conexionL.php";
$class = new conexion ();
$class->conectar();
$sql=$_GET['sql'];
ECHO("CONSULTA:<br>".$sql."<br> Resultado:<br>");

try
{
$query = mssql_query($sql);
}
catch(Exception $e)
{
echo 'Excepción capturada: ',  $e->getMessage(), "\n";
}



if (!mssql_num_rows($query))
{
    echo( 'No records found');
}
else
{
$result="";
$query = mssql_query($sql);
$filas="";
while ($row = mssql_fetch_array($query))
{
  $columnas="";
  for($i=0;$i<count($row);$i++)
  {
	  $columnas = $columnas."<td>".$row[$i]."</td>";
  }
  $filas = $filas."<tr>".$columnas."</tr>";
  
}

echo "<table> <tbody>".$filas."</tbody> </table>";

}


	?>
    </body>
    </html>
