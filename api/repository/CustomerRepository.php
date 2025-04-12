<?php
require_once '../../config/database.php';
require_once '../model/Customer.php';

class CustomerRepository {
    public function __construct() {}

    public function insertCustomer($customer) {
        global $conn;
        $sql = "INSERT INTO customers( dni, name, email, phone, address, photo_url) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        $dni = $customer->getDni();
        $name = $customer->getName();
        $email = $customer->getEmail();
        $phone = $customer->getPhone();
        $address = $customer->getAddress();
        $photo_url = $customer->getPhotoUrl();

        $stmt->bind_param('ssssss', $dni, $name, $email, $phone, $address, $photo_url); 
        $stmt->execute();
        //$stmt->store_result();

        if($stmt->affected_rows > 0) {
           // $conn->close();
            return true;
        }
       // $conn->close();
        return false;
    }

    public function updateCustomer($data) {
        global $conn;
    
        $fields = [];
        $values = [];
        
        // Definir los campos permitidos para actualizar
        $allowedFields = ['dni', 'name', 'email', 'phone', 'address'];
        
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
    
        $sql = "UPDATE customers SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(str_repeat("s", count($values)), ...$values);
        
        $stmt->execute();
        
        return $stmt->affected_rows > 0;
    }

    public function deleteCustomer($email) {
        global $conn;
        $sql = "UPDATE customers SET isDeleted = 1 WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->affected_rows) {        
            return true;
        }
        return false;
    }

    public function getCustomers() {
        global $conn;
        $sql = "SELECT id, dni, name, email, phone, address, isDeleted FROM customers;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $result = $stmt->get_result(); // Obtener los resultados
        $filas = [];
    
        while ($fila = $result->fetch_assoc()) {
            $filas[] = $fila;
        }
    
        return $filas; // Devuelve un array con los datos
    }

    public function getCustomerByEmail($email) {}

    public function exists($email): bool {
        global $conn;
        $sql = "SELECT `name` FROM customers WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0) {        
            return true;
        }

        return false;
    }
}
?>