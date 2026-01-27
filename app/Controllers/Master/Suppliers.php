<?php
namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\SupplierModel;

class Suppliers extends BaseController
{
    protected $supplierModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
    }

    public function index()
    {
        $suppliers = $this->supplierModel->findAll();
        
        $data = [
            'title' => 'Supplier',
            'subtitle' => 'Kelola data supplier',
            'suppliers' => $suppliers,
        ];

        return view('layout/main', $data)->renderSection('content', view('master/suppliers/index', $data));
    }

    public function store()
    {
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required',
            'phone' => 'permit_empty',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('errors', $validation->getErrors())->withInput();
        }

        // Create supplier
        $this->supplierModel->insert([
            'code' => $this->request->getPost('code'),
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
        ]);

        return redirect()->to('/master/suppliers')->with('success', 'Supplier berhasil ditambahkan');
    }

    public function update($id)
    {
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required',
            'phone' => 'permit_empty',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('errors', $validation->getErrors())->withInput();
        }

        // Update supplier
        $this->supplierModel->update($id, [
            'code' => $this->request->getPost('code'),
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
        ]);

        return redirect()->to('/master/suppliers')->with('success', 'Supplier berhasil diperbarui');
    }

    public function delete($id)
    {
        // Check if supplier has transactions
        // Simplified for now
        $this->supplierModel->delete($id);
        return redirect()->to('/master/suppliers')->with('success', 'Supplier berhasil dihapus');
    }
}