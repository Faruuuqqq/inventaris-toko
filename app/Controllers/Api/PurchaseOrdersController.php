<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\PurchaseOrderModel;
use App\Models\PurchaseOrderDetailModel;

class PurchaseOrdersController extends ResourceController
{
    protected $modelName = 'App\Models\PurchaseOrderModel';
    protected $format = 'json';

    public function index()
    {
        $orders = $this->model->findAll();
        return $this->respond([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    public function show($id = null)
    {
        $order = $this->model->find($id);
        if (!$order) {
            return $this->failNotFound('Purchase order not found');
        }

        $detailModel = new PurchaseOrderDetailModel();
        $items = $detailModel->where('po_id', $id)->findAll();

        return $this->respond([
            'status' => 'success',
            'data' => [
                'order' => $order,
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
            'message' => 'Purchase order created successfully',
            'id' => $this->model->getInsertID()
        ]);
    }

    public function update($id = null)
    {
        $order = $this->model->find($id);
        if (!$order) {
            return $this->failNotFound('Purchase order not found');
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (!$this->model->update($id, $data)) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Purchase order updated successfully'
        ]);
    }

    public function delete($id = null)
    {
        $order = $this->model->find($id);
        if (!$order) {
            return $this->failNotFound('Purchase order not found');
        }

        $this->model->delete($id);

        return $this->respondDeleted([
            'status' => 'success',
            'message' => 'Purchase order deleted successfully'
        ]);
    }

    public function receive($id = null)
    {
        $order = $this->model->find($id);
        if (!$order) {
            return $this->failNotFound('Purchase order not found');
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        // Update order status
        $this->model->update($id, [
            'status' => 'Diterima Semua',
            'received_amount' => $order['total_amount']
        ]);

        return $this->respond([
            'status' => 'success',
            'message' => 'Purchase order received successfully'
        ]);
    }
}
