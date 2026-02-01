<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class PurchaseReturnsController extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $db = \Config\Database::connect();
        $returns = $db->table('purchase_returns')->get()->getResultArray();

        return $this->respond([
            'status' => 'success',
            'data' => $returns
        ]);
    }

    public function show($id = null)
    {
        $db = \Config\Database::connect();
        $return = $db->table('purchase_returns')->where('id', $id)->get()->getRowArray();

        if (!$return) {
            return $this->failNotFound('Purchase return not found');
        }

        $items = $db->table('purchase_return_items')->where('return_id', $id)->get()->getResultArray();

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
        $db = \Config\Database::connect();
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $db->table('purchase_returns')->insert($data);

        return $this->respondCreated([
            'status' => 'success',
            'message' => 'Purchase return created successfully',
            'id' => $db->insertID()
        ]);
    }

    public function update($id = null)
    {
        $db = \Config\Database::connect();
        $return = $db->table('purchase_returns')->where('id', $id)->get()->getRowArray();

        if (!$return) {
            return $this->failNotFound('Purchase return not found');
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $db->table('purchase_returns')->where('id', $id)->update($data);

        return $this->respond([
            'status' => 'success',
            'message' => 'Purchase return updated successfully'
        ]);
    }

    public function delete($id = null)
    {
        $db = \Config\Database::connect();
        $return = $db->table('purchase_returns')->where('id', $id)->get()->getRowArray();

        if (!$return) {
            return $this->failNotFound('Purchase return not found');
        }

        $db->table('purchase_returns')->where('id', $id)->delete();

        return $this->respondDeleted([
            'status' => 'success',
            'message' => 'Purchase return deleted successfully'
        ]);
    }

    public function approve($id = null)
    {
        $db = \Config\Database::connect();
        $return = $db->table('purchase_returns')->where('id', $id)->get()->getRowArray();

        if (!$return) {
            return $this->failNotFound('Purchase return not found');
        }

        $db->table('purchase_returns')->where('id', $id)->update(['status' => 'Disetujui']);

        return $this->respond([
            'status' => 'success',
            'message' => 'Purchase return approved successfully'
        ]);
    }
}
