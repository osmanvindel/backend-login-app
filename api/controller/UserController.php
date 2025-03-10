<?php
require_once '../service/UserService.php';
header("Content-Type: application/json; charset=UTF-8");

$userService = new UserService();

$httpMethod = $_SERVER['REQUEST_METHOD'];   //Obtener el metodo HTTP (GET, POST, PUT, DELETE)
$pathRequest = $_SERVER['PATH_INFO'];       //Obtener la ruta de la peticion (Ejemplo -> /users)

//Ruta base: /api/controller/UserController.php
//Server a la escucha de cualquier peticion: php -S 0.0.0.0:3000

/*
    Autenticar usuario  
    -Endpoint: /login    
    -Metodo: POST
*/
if($httpMethod == 'POST' && $pathRequest == '/login') {
    //Obtener los datos enviados en el cuerpo de la peticion
    $data = json_decode(file_get_contents('php://input'), true); 

    //JSON vacio
    if(validateJSON($data)) {
        header('Content-Type: application/json');
        http_response_code(400);   
        echo json_encode([
            'success: ' => false, 
            'message' => 'No se han enviado datos']);
        exit();
    }  

    //Algun campo vacio
    if(empty($data['email']) || empty($data['password'])) {
        header('Content-Type: application/json');
        http_response_code(400);   
        echo json_encode([
            'success' => false, 
            'message' => 'Los campos email y password son obligatorios']);
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

    //Autenticar usuario 
    $result = $userService->userAuth($data['email'], $data['password']);
    
    //Usuario no existe
    if(!$result) {
        header('Content-Type: application/json');
        http_response_code(404);   
        echo json_encode([
            'success' => false,
            'message' => 'Credenciales incorrectas o usuario no existe'
        ]);
        exit();
    }

    //Login exitoso
    header('Content-Type: application/json');
    http_response_code(200);   
    echo json_encode([
        'success' => true, 
        'message' => 'Bienvenido'
    ]); 
}

/*
    Bitacora login
    -Endpoint: /user/bitacora-login
    -Metodo: POST
*/
if($httpMethod == 'POST' && $pathRequest == '/user/bitacora-login') {
    //Obtener los datos enviados en el cuerpo de la peticion
    $data = json_decode(file_get_contents('php://input'), true); 

    //JSON vacio
    if(validateJSON($data)) {
        header('Content-Type: application/json');
        http_response_code(400);   
        echo json_encode([
            'success: ' => false, 
            'message' => 'No se han enviado datos']);
            exit();
    } 

    $user_id = $data['user_id'];
    $browser = $data['browser'];
    $ip = $data['ip'];
    $device = $data['device'];
    $description = $data['description'];

    $result = $userService->addLoginLog($user_id, $browser, $ip, $device, $description);

    if(!$result) {
        header('Content-Type: application/json');
        http_response_code(404);   
        echo json_encode([
            'success' => false,
            'message' => 'Error en la peticion'
        ]);
        exit();
    }

    header('Content-Type: application/json');
    http_response_code(200);   
    echo json_encode([
        'success' => true, 
        'message' => 'Log registrado'
    ]); 
}

/*
    Obtener id usuario  
    -Endpoint: /user/id/{email}
    -Metodo: GET
*/
if ($httpMethod == 'GET' && preg_match('/^\/user\/id\/([^\/]+)$/', $pathRequest, $matches)) {
    //Obtener los datos enviados en la URL de la peticion
    $email = urldecode($matches[1]);

    $id = $userService->getUserId($email);
    
    echo json_encode(['id' => $id]);
}

/*
    Crear usuario  
    -Endpoint: /user    
    -Metodo: POST
*/
if($httpMethod == 'POST' && $pathRequest == '/signup') {
    //Obtener los datos enviados en el cuerpo de la peticion
    $data = json_decode(file_get_contents('php://input'), true); 

    //JSON vacio
    if(validateJSON($data)) {
        http_response_code(400);   
        echo json_encode(["error" => "No se han enviado datos"]);
        exit(); 
    }   

    //Campos vacios
    if(empty($data['name']) || empty($data['email']) || empty($data['password'])) {
        header('Content-Type: application/json');
        http_response_code(400);   
        echo json_encode([
            'success' => false, 
            'message' => 'Los campos son obligatorios']);
        exit();
    }

    //Validar nombre
    // if(!validateUserName($data['name'])) {
    //     header('Content-Type: application/json');
    //     http_response_code(400);   
    //     echo json_encode([
    //         'success' => false, 
    //         'message' => 'El usuario solo puede contener caracteres o simbolos especiales']);
    //     exit();
    // }

    //Validar email
    if(!validateEmail($data['email'])) { 
        header('Content-Type: application/json');
        http_response_code(400);   
        echo json_encode([
            'success' => false, 
            'message' => 'El email no es valido']);
        exit();
    }

    $user = new User(null, $data['cedula'], $data['username'], $data['name'], $data['lastname'], $data['email'], $data['password']);
    $result = $userService->addUser($user);

    if(!$result) {
        http_response_code(400);   
        echo json_encode([
            'success' => false, 
            "message" => "No se pudo crear el usuario"
        ]);
        exit();
    }

    echo json_encode([
        'success' => true, 
        'message' => 'Rgistrado correctamente'
    ]);
}

/*
    Bloquear usuario  
    -Endpoint: /block    
    -Metodo: POST
*/
if($httpMethod == 'POST' && $pathRequest == '/block') {
    $data = json_decode(file_get_contents('php://input'), true); 

    //echo "Data recibida en controller " .$data;

    if(!validateJSON($data)) {
        $result = $userService->blockUser($data);
        if(!$result) {
            http_response_code(400);   
            echo json_encode([
                'success' => false, 
                "message" => "Correo de usuario a bloquear no valido"
            ]);
            exit();
        }
        echo json_encode([
            'success' => true, 
            'message' => 'Usuario bloqueado'
        ]);
    } 
}

/*
    Validar usuario  
    -Endpoint: /unblock    
    -Metodo: POST
*/
if($httpMethod == 'POST' && $pathRequest == '/unblock') {
    $data = json_decode(file_get_contents('php://input'), true); 

    if(!validateJSON($data)) {
        $result = $userService->unblockUser($data['email']);
        if(!$result) {
            http_response_code(400);   
            echo json_encode([
                'success' => false, 
                "message" => "Correo de usuario a desbloquear no valido"
            ]);
            exit();
        }
        echo json_encode([
            'success' => true, 
            'message' => 'Usuario desbloqueado'
        ]);
    } 
}

/*
    Auditar login 
    -Endpoint: /auditar    
    -Metodo: POST
*/
if($httpMethod == 'POST' && $pathRequest == '/auditar') {
    $data = json_decode(file_get_contents('php://input'), true); 

    if(!validateJSON($data)) {

        $fecha = $data['fecha'];
        $usuario = $data['usuario'];
        $evento = $data['evento'];

        $result = $userService->auditar($usuario, $evento, $fecha);

        if(!$result) {
            http_response_code(400);   
            echo json_encode([
                'success' => false, 
                "message" => "Ocurrio un error al auditar"
            ]);
            exit();
        }
        echo json_encode([
            'success' => true, 
            'message' => 'Auditoria registrada'
        ]);
    } 
}

/*
    Obtener Modulos Usuario
    -Endpoint: /user/{id}/module
    -Metodo: GET
*/
if ($httpMethod == 'GET' && preg_match("/^\/user\/(\d+)\/module$/", $pathRequest, $matches)) {
    // Obtener el ID del usuario desde la URL
    $id = (int) $matches[1];

    try {
        // Cargar los módulos del usuario
        $modulos = $userService->loadModules($id);

        // Verificar si se encontraron módulos
        if (empty($modulos)) {
            http_response_code(404);
            echo json_encode(["error" => "No se encontraron módulos para el usuario con ID $id"]);
            exit;
        }

        // Devolver la respuesta en JSON
        header('Content-Type: application/json');
        echo json_encode($modulos);
    } catch (Exception $e) {
        // Manejo de errores en caso de fallo en la base de datos o servicio
        http_response_code(500);
        echo json_encode(["error" => "Error interno del servidor", "detalle" => $e->getMessage()]);
        exit;
    }
}

/*
    Obtener Bitacoras
    -Endpoint: /bitacoras/{fecha}
    -Metodo: GET
*/
if ($httpMethod == 'GET' && preg_match("/^\/bitacora\/(\d{4}-\d{2}-\d{2})$/", $pathRequest, $matches)) {
    // Obtener el ID del usuario desde la URL
    $fecha =  $matches[1];
    //echo 'Fecha recibida: ' .$fecha;

    $bitacoras = $userService->getBitacoras($fecha);

    echo json_encode($bitacoras);
}

/*
    Obtener Lista de Usuarios
    -Endpoint: /user-list
    -Metodo: GET
*/
if ($httpMethod == 'GET' && $pathRequest == '/user-list') {
    $userList = $userService->getUserList();
    echo json_encode($userList);
}

/*
    Eliminar (soft delete) Usuario  
    -Endpoint: /block    
    -Metodo: POST
*/
if($httpMethod == 'POST' && $pathRequest == '/delete') {
    $data = json_decode(file_get_contents('php://input'), true); 

    //echo "Data recibida en controller " .$data;

    if(!validateJSON($data)) {
        $result = $userService->deleteUser($data);
        if(!$result) {
            http_response_code(400);   
            echo json_encode([
                'success' => false, 
                "message" => "Correo de usuario a eliminar no valido"
            ]);
            exit();
        }
        echo json_encode([
            'success' => true, 
            'message' => 'Usuario eliminado'
        ]);
    } 
}

/*
    Actualizar Usuario  
    -Endpoint: /user/update    
    -Metodo: POST
*/
if ($httpMethod == 'POST' && $pathRequest == '/user/update') {
    $data = json_decode(file_get_contents('php://input'), true);

    var_dump($data);

    if (validateJSON($data) || empty($data['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            "message" => "Datos inválidos o ID de usuario no proporcionado"
        ]);
        exit();
    }

    $result = $userService->updateUser($data);
    if (!$result) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            "message" => "No se pudo actualizar el usuario"
        ]);
        exit();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Usuario actualizado correctamente'
    ]);
}

//Validaciones

function validateJSON($data): bool {
    return empty($data);
}

function validateUserName($name) {
    return preg_match("^[a-zA-Z0-9._-]{3,16}$", $name);
}

function validateEmail($email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>