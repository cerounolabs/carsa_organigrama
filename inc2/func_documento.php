<?php
    ini_set('mssql.charset', 'UTF-8');

    $msconnect = mssql_connect("192.168.16.10", "sumar", "carsa_2018");

    if(!$msconnect || !mssql_select_db("BDPRODUC", $msconnect)) {
        echo 'MSSQL error: '.mssql_get_last_message();
    } else {
        $mssql      = "SELECT * FROM COLABORADOR_DOCUMENTOS";
        $msquery    = mssql_query($mssql);
        $msfile     = fopen("func_documento.txt", "w");

	while ($row = mssql_fetch_assoc($msquery)){
       		fwrite($msfile, $row['FUNC_PATH'].PHP_EOL);
        }

        fclose($msfile);
        mssql_free_result($msquery);
    }

    mssql_close($msconnect);
?>
