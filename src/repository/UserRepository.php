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
        $sql = "INSERT INTO users (name, email, `password`) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        $name = $user->getName();
        $email = $user->getEmail();
        $password = $user->getPassword();

        $stmt->bind_param("sss", $name, $email, $password); 
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

}
?>