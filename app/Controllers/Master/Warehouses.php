<?php
namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\WarehouseModel;

class Warehouses extends BaseController
{
    protected $warehouseModel;

    public function __construct()
    {
        $this->warehouseModel = new WarehouseModel();
    }

    public function index()
    {
        $warehouses = $this->warehouseModel->findAll();
        
        $data = [
            'title' => 'Gudang',
            'subtitle' => 'Kelola data gudang',
            'warehouses' => $warehouses,
        ];

        return view('layout/main', $data)->renderSection('content', view('master/warehouses/index', $data));
    }

    public function store()
    {
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'code' => 'required|is_unique[warehouses.code]',
            'name' => 'required',
            'address' => 'permit_empty',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('errors', $validation->getErrors())->withInput();
        }

        // Create warehouse
        $this->warehouseModel->insert([
            'code' => $this->request->getPost('code'),
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address'),
            'is_active' => 1,
        ]);

        return redirect()->to('/master/warehouses')->with('success', 'Gudang berhasil ditambahkan');
    }

    public function update($id)
    {
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'code' => 'required|is_unique[warehouses.code,id,'.$id.']',
            'name' => 'required',
            'address' => 'permit_empty',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('errors', $validation->getErrors())->withInput();
        }

        // Update warehouse
        $this->warehouseModel->update($id, [
            'code' => $this->request->getPost('code'),
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address'),
        ]);

        return redirect()->to('/master/warehouses')->with('success', 'Gudang berhasil diperbarui');
    }

    public function delete($id)
    {
        // Check if warehouse has stock
        // Simplified for now
        $this->warehouseModel->delete($id);
        return redirect()->to('/master/warehouses')->with('success', 'Gudang berhasil dihapus');
    }
}