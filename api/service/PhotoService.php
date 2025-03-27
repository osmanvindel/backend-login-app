<?php

class PhotoService
{

    public function __construct() {}

    public function getPhoto($id) {}
    
    // public function savePhoto(){
    //     $photoPath = '../photos/';
    //     if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    //         $fileTmpPath = $_FILES['image']['tmp_name'];
    //         $fileName = basename($_FILES['image']['name']);
    //         $fileSize = $_FILES['image']['size'];
    //         $fileType = $_FILES['image']['type'];

    //         // Generar un nombre único para evitar colisiones
    //         $newFileName = uniqid() . '_' . $fileName;

    //         // Mover el archivo al directorio de destino
    //         $destPath = $photoPath . $newFileName;
    //         if (move_uploaded_file($fileTmpPath, $destPath)) {
    //             echo json_encode(['success' => true, 'message' => 'Archivo subido correctamente']);
    //         } else {
    //             echo json_encode(['success' => false, 'message' => 'Error al subir el archivo']);
    //         }
    //     }
    //     else {
    //         echo "No se recibió ninguna imagen o hubo un error.";
    //     }
    // } 

    public function savePhoto($file) {
        // Directorio donde se guardarán las imágenes
        $uploadDir = '../photos/';
        // if (!is_dir($uploadDir)) {
        //     mkdir($uploadDir, 0777, true); // Crear el directorio si no existe
        // }

        // Generar un nombre único para evitar colisiones
        $newFileName = uniqid() . '_' . basename($file['name']);

        // Mover el archivo al directorio de destino
        $destPath = $uploadDir . $newFileName;
        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            return $newFileName; // Devolver el nombre del archivo guardado
        } else {
            return false; // Indicar que hubo un error
        }
    }
}
