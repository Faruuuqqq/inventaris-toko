<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\CustomerModel;

class CustomersController extends ResourceController
{
    use ResponseTrait;
    
    protected $customerModel;
    
    public function __construct()
    {
        $this->customerModel = new CustomerModel();
    }
    
    public function index()
    {
        $customers = $this->customerModel->findAll();
        return $this->respond($customers);
    }
    
    public function show($id = null)
    {
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return $this->failNotFound('Customer not found');
        }
        return $this->respond($customer);
    }
    
    public function create()
    {
        $data = $this->request->getPost();
        $id = $this->customerModel->insert($data);
        return $this->respondCreated($id);
    }
    
    public function update($id = null)
    {
        $data = $this->request->getPost();
        $this->customerModel->update($id, $data);
        return $this->respond($data);
    }
    
    public function delete($id = null)
    {
        $this->customerModel->delete($id);
        return $this->respondDeleted();
    }
    
    public function receivable($id)
    {
        $customer = $this->customerModel->find($id);
        return $this->respond($customer);
    }
    
    public function creditLimit()
    {
        $customers = $this->customerModel->findAll();
        return $this->respond($customers);
    }
}
