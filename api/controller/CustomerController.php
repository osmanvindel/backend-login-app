<?php
require_once '../service/CustomerService.php';
header("Content-Type: application/json; charset=UTF-8");

$customerService = new CustomerService();

$httpMethod = $_SERVER['REQUEST_METHOD']; 
$pathRequest = $_SERVER['PATH_INFO'];    

if($httpMethod == 'POST' && $pathRequest == '/customer') {
    $data = json_decode(file_get_contents('php://input'), true); 

    if(validateJSON($data)) {
        header('Content-Type: application/json');
        http_response_code(400);   
        echo json_encode([
            'success: ' => false, 
            'message' => 'No se han enviado datos']);
        exit();
    }

    if (
        empty($data['dni']) ||
        empty($data['fullName']) ||
        empty($data['email']) ||
        empty($data['phoneNumber']) ||
        empty($data['address'])
    ) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Los campos son obligatorios'
        ]);
        exit();
    }

    //Validar email
    if(!validateEmail($data['email'])) { 
        header('Content-Type: application/json');
        http_response_code(400);   
        echo json_encode([
            'success' => false, 
            'message' => 'El email no es valido']);
        exit();
    }

    $customer = new Customer(
        $data['dni'], 
        $data['fullName'], 
        $data['email'], 
        $data['phoneNumber'], 
        $data['address'],
        $data['photo_url']
    );
        
    $result = $customerService->insertCustomer($customer);

    if(!$result) {
        http_response_code(400);   
        echo json_encode([
            'success' => false, 
            "message" => "No se pudo crear el cliente"
        ]);
        exit();
    }

    echo json_encode([
        'success' => true, 
        'message' => 'Rgistrado correctamente'
    ]);
}

if($httpMethod == 'GET' && $pathRequest == '/customers') {
    $customers = $customerService->getCustomers();
    echo json_encode($customers);
}

if($httpMethod == 'POST' && $pathRequest == '/delete') {
    $data = json_decode(file_get_contents('php://input'), true); 

    //echo "Data recibida en controller " .$data;

    if(!validateJSON($data)) {
        $result = $customerService->deleteCustomer($data['email']);
        if(!$result) {
            http_response_code(400);   
            echo json_encode([
                'success' => false, 
                "message" => "Correo del cliente a eliminar no valido"
            ]);
            exit();
        }
        http_response_code(200);
        echo json_encode([
            'success' => true, 
            'message' => 'Cliente eliminado'
        ]);
    } 
}

if ($httpMethod == 'POST' && $pathRequest == '/update') {
    $data = json_decode(file_get_contents('php://input'), true);

    //var_dump($data);

    if (validateJSON($data) || empty($data['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            "message" => "Datos inválidos o ID del cliente no proporcionado"
        ]);
        exit();
    }

    $result = $customerService->updateCustomer($data);
    if (!$result) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            "message" => "No se pudo actualizar el cliente"
        ]);
        exit();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Cliente actualizado correctamente'
    ]);
}


function validateJSON($data): bool {
    return empty($data);
}

function validateEmail($email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>