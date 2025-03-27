<?php
require_once '../repository/CustomerRepository.php';

class CustomerService {
    private $customerRepository = null;

public function __construct() {
    $this->customerRepository = new CustomerRepository();
}

public function getCustomers() {
    return $this->customerRepository->getCustomers();
}

public function getCustomerByEmail($email) {}

public function insertCustomer($customer) {
    if($this->customerRepository->exists($customer->getEmail())) return false;

    return $this->customerRepository->insertCustomer($customer);

    return false;
}

public function updateCustomer($customer) {
    return $this->customerRepository->updateCustomer($customer);   
}

public function deleteCustomer($customer) {
    return $this->customerRepository->deleteCustomer($customer);
}
}

?>