<?php
    $config = include __DIR__ . '/config.php';

    $conn = new mysqli(
        $config['HOST'],
        $config['USERNAME'],
        $config['PASSWORD'],
        $config['DB'],
        $config['PORT']
    );

    //Verificar la conexion
    if($conn->connect_error){
        die("Conexion fallida: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");
?>