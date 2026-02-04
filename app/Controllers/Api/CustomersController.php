<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\CustomerModel;
use App\Services\CustomerDataService;

class CustomersController extends ResourceController
{
    use ResponseTrait;
    
    protected CustomerModel $customerModel;
    protected CustomerDataService $dataService;
    
    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->dataService = new CustomerDataService();
    }
    
    public function index()
    {
        $data = $this->dataService->getIndexData();
        return $this->respond($data);
    }
    
    public function show($id = null)
    {
        $detailData = $this->dataService->getDetailData($id);
        if (empty($detailData)) {
            return $this->failNotFound('Customer tidak ditemukan');
        }
        return $this->respond($detailData);
    }
    
    public function create()
    {
        $data = $this->request->getPost();
        
        // Validate using model
        if (!$this->customerModel->validate($data)) {
            return $this->failValidationErrors($this->customerModel->errors());
        }
        
        $id = $this->customerModel->insert($data);
        return $this->respondCreated(['id' => $id]);
    }
    
    public function update($id = null)
    {
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return $this->failNotFound('Customer tidak ditemukan');
        }
        
        $data = $this->request->getPost();
        
        if (!$this->customerModel->update($id, $data)) {
            return $this->failValidationErrors($this->customerModel->errors());
        }
        
        return $this->respond(['message' => 'Customer berhasil diperbarui']);
    }
    
    public function delete($id = null)
    {
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return $this->failNotFound('Customer tidak ditemukan');
        }
        
        $this->customerModel->delete($id);
        return $this->respondDeleted(['message' => 'Customer berhasil dihapus']);
    }
    
    public function receivable($id)
    {
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return $this->failNotFound('Customer tidak ditemukan');
        }
        return $this->respond($customer);
    }
    
    public function creditLimit()
    {
        $customers = $this->customerModel->findAll();
        return $this->respond($customers);
    }
}
