<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\WarehouseModel;
use App\Services\WarehouseDataService;

class WarehousesController extends ResourceController
{
    protected WarehouseModel $modelName = 'App\Models\WarehouseModel';
    protected string $format = 'json';

    protected WarehouseDataService $dataService;

    public function __construct()
    {
        $this->dataService = new WarehouseDataService();
    }

    public function index()
    {
        $data = $this->dataService->getIndexData();
        return $this->respond([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function show($id = null)
    {
        $detailData = $this->dataService->getDetailData($id);
        if (empty($detailData)) {
            return $this->failNotFound('Gudang tidak ditemukan');
        }
        return $this->respond([
            'status' => 'success',
            'data' => $detailData
        ]);
    }

    public function create()
    {
        $model = new WarehouseModel();
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (!$model->insert($data)) {
            return $this->failValidationErrors($model->errors());
        }

        return $this->respondCreated([
            'status' => 'success',
            'message' => 'Gudang berhasil dibuat',
            'id' => $model->getInsertID()
        ]);
    }

    public function update($id = null)
    {
        $model = new WarehouseModel();
        $warehouse = $model->find($id);
        if (!$warehouse) {
            return $this->failNotFound('Gudang tidak ditemukan');
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (!$model->update($id, $data)) {
            return $this->failValidationErrors($model->errors());
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Gudang berhasil diperbarui'
        ]);
    }

    public function delete($id = null)
    {
        $model = new WarehouseModel();
        $warehouse = $model->find($id);
        if (!$warehouse) {
            return $this->failNotFound('Gudang tidak ditemukan');
        }

        $model->delete($id);

        return $this->respondDeleted([
            'status' => 'success',
            'message' => 'Gudang berhasil dihapus'
        ]);
    }
}
