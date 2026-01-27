<?php
namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\SalespersonModel;

class Salespersons extends BaseController
{
    protected $salespersonModel;

    public function __construct()
    {
        $this->salespersonModel = new SalespersonModel();
    }

    public function index()
    {
        $salespersons = $this->salespersonModel->findAll();
        
        $data = [
            'title' => 'Sales',
            'subtitle' => 'Kelola data salesperson',
            'salespersons' => $salespersons,
        ];

        return view('layout/main', $data)->renderSection('content', view('master/salespersons/index', $data));
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

        // Create salesperson
        $this->salespersonModel->insert([
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
            'is_active' => 1,
        ]);

        return redirect()->to('/master/salespersons')->with('success', 'Sales berhasil ditambahkan');
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

        // Update salesperson
        $this->salespersonModel->update($id, [
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
        ]);

        return redirect()->to('/master/salespersons')->with('success', 'Sales berhasil diperbarui');
    }

    public function delete($id)
    {
        // Check if salesperson has sales transactions
        // Simplified for now
        $this->salespersonModel->delete($id);
        return redirect()->to('/master/salespersons')->with('success', 'Sales berhasil dihapus');
    }
}