<?php
require_once '../../config/database.php';
require_once '../model/User.php';

//header("Content-Type: application/json; charset=UTF-8");

class UserRepository {


    public function __construct() {}

    public function getUser($email, $password): bool {
        global $conn;
        $sql = "SELECT `name` FROM users WHERE email = ? AND `password` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows() > 0) {
           // $conn->close();
            return true;
        }
        
       // $conn->close();
        return false;
    }

    public function insertUser($user): bool {
        global $conn;
        $sql = "INSERT INTO users (cedula, username, `name`, lastname, email, `password`) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        $cedula = $user->getCedula();
        $username = $user->getUsername();
        $name = $user->getName();
        $lastname = $user->getLastname();
        $email = $user->getEmail();
        $password = $user->getPassword();

        $stmt->bind_param("ssssss", $cedula, $username, $name, $lastname, $email, $password); 
        $stmt->execute();
        //$stmt->store_result();

        if($stmt->affected_rows > 0) {
           // $conn->close();
            return true;
        }
       // $conn->close();
        return false;
    }
    
    public function exists($email): bool {
        global $conn;
        $sql = "SELECT `name` FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0) {        
           // $conn->close();
            return true;
        }

       // $conn->close();
        return false;
    }

    public function getUserPassword($email) {
        global $conn;
        $sql = "SELECT `password` FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($password);
        $stmt->fetch();
        //$conn->close();
        return $password;   
    }

    public function blockUser($email) {
        global $conn;
        $sql = "UPDATE users SET isBlocked = 1 WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->affected_rows) {        
            return true;
        }
        return false;
    }

    public function unblockUser($email) {
        global $conn;
        $sql = "UPDATE users SET isBlocked = 0 WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->affected_rows) {        
            return true;
        }
        return false;
    }

    public function isBlocked($email) {
        global $conn;
        $sql = "SELECT name FROM users WHERE email = ? AND isBlocked = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0) return true;
        
        return false;
    }

    public function auditar($usuario, $evento, $fecha) {
        global $conn;
        $sql = "INSERT INTO bitacora_users (fecha, usuario, evento) VALUES (?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $fecha, $usuario, $evento);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->affected_rows) return true;
        
        return false;
    }

    public function addLoginLog($user_id, $browser, $ip, $device, $description) {
        global $conn;
        $sql = "INSERT INTO bitacora_login (user_id, browser, ip, device, `description`)
                VALUES(?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('issss',$user_id, $browser, $ip, $device, $description);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->affected_rows) return true;

        return false;
    }

    public function getUserId($email) {
        global $conn;
        $sql = "SELECT id FROM users WHERE email = ? AND isBlocked = 0";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($id);
        if($stmt->fetch()) return $id;
        
        return 0;
    }

    public function getUserList() {
        global $conn;
        $sql = "SELECT id, cedula, name, lastname, username, email, createdAt, isBlocked, isDeleted FROM users;";
        $stmt = $conn->prepare($sql);
        //$stmt->bind_param("isssii", $id, $username, $email, $createdAt, $isBlocked, $isDeleted);
        $stmt->execute();
        
        $result = $stmt->get_result(); // Obtener los resultados
        $filas = [];
    
        while ($fila = $result->fetch_assoc()) {
            $filas[] = $fila;
        }
    
        return $filas; // Devuelve un array con los datos
    }

    public function loadModules($id) {
        global $conn;
        $sql = "SELECT m.id, m.name, m.module_actionType 
                FROM modules m
                JOIN access_module am ON m.id = am.module_id
                JOIN users u ON am.user_id = u.id
                WHERE u.id = ?;";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $modulos = $result->fetch_all(MYSQLI_ASSOC);

        return $modulos;
    }

    public function getBitacoras($fecha) {
        global $conn;
        $sql = "SELECT usuario, evento, fecha FROM bitacora_users WHERE fecha = ?;";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $fecha);
        $stmt->execute();
        
        $result = $stmt->get_result(); // Obtener los resultados
        $filas = [];
    
        while ($fila = $result->fetch_assoc()) {
            $filas[] = $fila;
        }
    
        return $filas; // Devuelve un array con los datos
    }

    public function deleteUser($email) {
        global $conn;
        $sql = "UPDATE users SET isDeleted = 1 WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->affected_rows) {        
            return true;
        }
        return false;
    }

    public function updateUser($data) {
        global $conn;
    
        $fields = [];
        $values = [];
        
        // Definir los campos permitidos para actualizar
        $allowedFields = ['cedula', 'username', 'name', 'lastname', 'email', 'password'];
        
        foreach ($allowedFields as $field) {
            if (!empty($data[$field])) {  // Solo incluir si el campo no está vacío
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
    
        if (empty($fields)) {
            return false; // No hay campos para actualizar
        }
    
        $values[] = $data['id']; // Agregar ID al final para el WHERE
    
        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(str_repeat("s", count($values)), ...$values);
        
        $stmt->execute();
        
        return $stmt->affected_rows > 0;
    }
}
?>