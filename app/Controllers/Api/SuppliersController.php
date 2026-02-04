<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\SupplierModel;
use App\Services\SupplierDataService;

class SuppliersController extends ResourceController
{
    use ResponseTrait;
    
    protected SupplierModel $supplierModel;
    protected SupplierDataService $dataService;
    
    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->dataService = new SupplierDataService();
    }
    
    public function index()
    {
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = (int)($this->request->getGet('per_page') ?? 20);

        $data = $this->dataService->getPaginatedData($page, $perPage);
        return $this->respond($data);
    }
    
    public function show($id = null)
    {
        $detailData = $this->dataService->getDetailData($id);
        if (empty($detailData)) {
            return $this->failNotFound('Supplier tidak ditemukan');
        }
        return $this->respond($detailData);
    }
    
    public function create()
    {
        $data = $this->request->getPost();
        
        if (!$this->supplierModel->validate($data)) {
            return $this->failValidationErrors($this->supplierModel->errors());
        }
        
        $id = $this->supplierModel->insert($data);
        return $this->respondCreated(['id' => $id]);
    }
    
    public function update($id = null)
    {
        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return $this->failNotFound('Supplier tidak ditemukan');
        }
        
        $data = $this->request->getPost();
        
        if (!$this->supplierModel->update($id, $data)) {
            return $this->failValidationErrors($this->supplierModel->errors());
        }
        
        return $this->respond(['message' => 'Supplier berhasil diperbarui']);
    }
    
    public function delete($id = null)
    {
        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return $this->failNotFound('Supplier tidak ditemukan');
        }
        
        $this->supplierModel->delete($id);
        return $this->respondDeleted(['message' => 'Supplier berhasil dihapus']);
    }
}
