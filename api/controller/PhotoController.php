<?php
require_once '../service/PhotoService.php';
//header("Content-Type: application/json; charset=UTF-8");
$httpMethod = $_SERVER['REQUEST_METHOD'];   //Obtener el metodo HTTP (GET, POST, PUT, DELETE)
$pathRequest = $_SERVER['PATH_INFO'];

$photoService = new PhotoService();

if ($httpMethod == 'POST' && $pathRequest == '/upload') {
    //var_dump($_FILES);
    if (isset($_FILES['image'])) {
        $filename = $photoService->savePhoto($_FILES['image']);

        if (!$filename) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al mover el archivo']);
            exit();
        }

        // Armar la ruta final que le vas a devolver al frontend
        $relativePath = '/photos/' . $filename;

        http_response_code(200);
        echo json_encode(['success' => true, 'path' => $relativePath]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No se recibió archivo']);
    }
}
