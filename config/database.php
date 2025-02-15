<?php
    //Credenciales de conexion
    $HOST = "localhost";
    $USERNAME = "root";
    $PASSWORD = "";
    $DB = "usuarios_prog_movil2";

    //Conexion a la base de datos
    $conn = new mysqli($HOST, $USERNAME, $PASSWORD, $DB);

    //Verificar la conexion
    if($conn->connect_error){
        die("Conexion fallida: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");
?>