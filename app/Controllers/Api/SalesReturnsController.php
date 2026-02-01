<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\SalesReturnModel;
use App\Models\SalesReturnDetailModel;

class SalesReturnsController extends ResourceController
{
    protected $modelName = 'App\Models\SalesReturnModel';
    protected $format = 'json';

    public function index()
    {
        $returns = $this->model->findAll();
        return $this->respond([
            'status' => 'success',
            'data' => $returns
        ]);
    }

    public function show($id = null)
    {
        $return = $this->model->find($id);
        if (!$return) {
            return $this->failNotFound('Sales return not found');
        }

        $detailModel = new SalesReturnDetailModel();
        $items = $detailModel->where('return_id', $id)->findAll();

        return $this->respond([
            'status' => 'success',
            'data' => [
                'return' => $return,
                'items' => $items
            ]
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
            'message' => 'Sales return created successfully',
            'id' => $this->model->getInsertID()
        ]);
    }

    public function update($id = null)
    {
        $return = $this->model->find($id);
        if (!$return) {
            return $this->failNotFound('Sales return not found');
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (!$this->model->update($id, $data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Sales return updated successfully'
        ]);
    }

    public function delete($id = null)
    {
        $return = $this->model->find($id);
        if (!$return) {
            return $this->failNotFound('Sales return not found');
        }

        $this->model->delete($id);

        return $this->respondDeleted([
            'status' => 'success',
            'message' => 'Sales return deleted successfully'
        ]);
    }

    public function approve($id = null)
    {
        $return = $this->model->find($id);
        if (!$return) {
            return $this->failNotFound('Sales return not found');
        }

        $this->model->update($id, ['status' => 'Disetujui']);

        return $this->respond([
            'status' => 'success',
            'message' => 'Sales return approved successfully'
        ]);
    }
}
