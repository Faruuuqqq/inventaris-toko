<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\SupplierModel;

class SuppliersController extends ResourceController
{
    use ResponseTrait;
    
    protected $supplierModel;
    
    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
    }
    
    public function index()
    {
        $suppliers = $this->supplierModel->findAll();
        return $this->respond($suppliers);
    }
    
    public function show($id = null)
    {
        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return $this->failNotFound('Supplier not found');
        }
        return $this->respond($supplier);
    }
    
    public function create()
    {
        $data = $this->request->getPost();
        $id = $this->supplierModel->insert($data);
        return $this->respondCreated($id);
    }
    
    public function update($id = null)
    {
        $data = $this->request->getPost();
        $this->supplierModel->update($id, $data);
        return $this->respond($data);
    }
    
    public function delete($id = null)
    {
        $this->supplierModel->delete($id);
        return $this->respondDeleted();
    }
}
