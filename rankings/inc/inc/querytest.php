<?php


try {
    $pdo = new \PDO(
        sprintf(
            "dblib:host=%s;dbname=%s",
            "190.128.229.38:9090",
            'SISTEMAA'
        ),
        "sumar",
        'carsa_2018'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "There was a problem connecting. " . $e->getMessage();
}
 
$query = $_GET['query'];
 
$statement = $pdo->prepare($query);
//$statement->bindValue(":username", "sanitizeduserinputusername", PDO::PARAM_STR);
$statement->execute();
 
$results = $statement->fetchAll(PDO::FETCH_ASSOC);
 
var_dump($results);



?>