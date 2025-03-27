<?php
class Customer {
    
    private $name;
    private $dni;
    private $email;
    private $phone;
    private $address;

    public function __construct($dni, $name, $email, $phone, $address){
        $this->dni = $dni;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }    

    public function getDni(){
        return $this->dni;
    }   

    public function setDni($dni){    
        $this->dni = $dni;
    }

    public function getEmail(){    
        return $this->email;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    public function getPhone(){     
        return $this->phone;
    }   

    public function setPhone($phone){        
        $this->phone = $phone;
    }   

    public function getAddress(){        
        return $this->address;
    }

    public function setAddress($address){       
        $this->address = $address;
    }

    
    public function jsonSerialize(): mixed {
        return [
            'name' => $this->name,
            'dni' => $this->dni,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address
        ];
    }

    
}
?>