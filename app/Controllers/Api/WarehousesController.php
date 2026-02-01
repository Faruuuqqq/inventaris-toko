<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\WarehouseModel;

class WarehousesController extends ResourceController
{
    protected $modelName = 'App\Models\WarehouseModel';
    protected $format = 'json';

    public function index()
    {
        $warehouses = $this->model->findAll();
        return $this->respond([
            'status' => 'success',
            'data' => $warehouses
        ]);
    }

    public function show($id = null)
    {
        $warehouse = $this->model->find($id);
        if (!$warehouse) {
            return $this->failNotFound('Warehouse not found');
        }
        return $this->respond([
            'status' => 'success',
            'data' => $warehouse
        ]);
    }

    public function create()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (!$this->model->insert($data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respondCreated([
            'status' => 'success',
            'message' => 'Warehouse created successfully',
            'id' => $this->model->getInsertID()
        ]);
    }

    public function update($id = null)
    {
        $warehouse = $this->model->find($id);
        if (!$warehouse) {
            return $this->failNotFound('Warehouse not found');
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (!$this->model->update($id, $data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Warehouse updated successfully'
        ]);
    }

    public function delete($id = null)
    {
        $warehouse = $this->model->find($id);
        if (!$warehouse) {
            return $this->failNotFound('Warehouse not found');
        }

        $this->model->delete($id);

        return $this->respondDeleted([
            'status' => 'success',
            'message' => 'Warehouse deleted successfully'
        ]);
    }
}
