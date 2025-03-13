<?php
require_once '../repository/UserRepository.php';
//require_once '../model/User.php';

//header("Content-Type: application/json; charset=UTF-8");

class UserService {

    private $userRepository = null; 

    public function __construct() {
        $this->userRepository = new UserRepository();
    }
    
    //Autenticar usuario
    public function userAuth($email, $password): bool {       
        if(!$this->userRepository->exists($email)) return false;

        //Verificar si el usuario no esta bloqueado
        if($this->userRepository->isBlocked($email)) return false;
        
        //Contiene la contraseña encriptada asociada al email evaluado previamente
        $encriptedPassword = $this->userRepository->getUserPassword($email);
    
        //Verificar si la contraseña enviada coincide con la contraseña encriptada
        if(password_verify($password, $encriptedPassword)) {
            return $this->userRepository->getUser($email, $encriptedPassword);
        }

        return false;
    }    

    //Insertar usuario
    public function addUser($user): bool {
        if($this->userRepository->exists($user->getEmail())) return false;

        //Encriptar contraseña
        $encriptedPassword = password_hash($user->getPassword(), PASSWORD_DEFAULT);
        //Reasignar contraseña encriptada
        $user->setPassword($encriptedPassword);

        //if($this->userRepository->insertUser($user)) return true;

        //Verdadero si se inserta el usuario
        return $this->userRepository->insertUser($user);

        return false;
    }

    public function blockUser($email): bool {
        $result =  $this->userRepository->exists($email);

        if($result) return $this->userRepository->blockUser($email);

        return false;
    }

    public function unblockUser($email): bool {
        $result =  $this->userRepository->exists($email);

        if($result) return $this->userRepository->unblockUser($email);

        return false;
    }

    public function auditar($usuario, $evento, $fecha): bool {
        return $this->userRepository->auditar($usuario, $evento, $fecha);
    }

    public function getUserId($email) {
        return $this->userRepository->getUserId($email);
    }

    public function addLoginLog($user_id, $browser, $ip, $device, $description): bool {
        return $this->userRepository->addLoginLog($user_id, $browser, $ip, $device, $description);
    }

    public function loadModulesByUser($id) {
        return $this->userRepository->loadModulesByUser($id);
    }

    public function getBitacoras($fecha) {
        return $this->userRepository->getBitacoras($fecha);
    }

    public function getUserList() {
        return $this->userRepository->getUserList();
    }

    public function deleteUser($email) {
        return $this->userRepository->deleteUser($email);
    }

    public function updateUser($data) {
        //Encriptar contraseña
        $encriptedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        //Reasignar contraseña encriptada
        $data['password'] = $encriptedPassword;

        return $this->userRepository->updateUser($data);
    }

    public function getModules() {
        return $this->userRepository->getModules();
    }

    public function grantUserAccess($id, $modules) {
        return $this->userRepository->grantAccess($id, $modules);
    }

    public function denyUserAccess($id, $modules) {
        return $this->userRepository->denyAccess($id, $modules);
    }
}

?>
