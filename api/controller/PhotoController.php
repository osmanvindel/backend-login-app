<?php
require_once '../service/PhotoService.php';
//header("Content-Type: application/json; charset=UTF-8");
$httpMethod = $_SERVER['REQUEST_METHOD'];   //Obtener el metodo HTTP (GET, POST, PUT, DELETE)
$pathRequest = $_SERVER['PATH_INFO']; 

$photoService = new PhotoService();

if($httpMethod == 'POST' && $pathRequest == '/upload') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Inicializar el servicio
        $photoService = new PhotoService();

        // Llamar al método savePhoto() pasando el archivo
        $result = $photoService->savePhoto($_FILES['image']);

        // Devolver la respuesta
        if(!$result){
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al subir el archivo']);
            exit();
        }        

        if ($result) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Archivo subido correctamente', 'path' => $result]);            
            //exit();
        } 
    } else {
        http_response_code(400);
        echo "No se recibió ninguna imagen o hubo un error.";
    }
}
?>