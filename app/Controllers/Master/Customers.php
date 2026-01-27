<?php
namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\CustomerModel;

class Customers extends BaseController
{
    protected $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
    }

    public function index()
    {
        $customers = $this->customerModel->findAll();
        
        $data = [
            'title' => 'Customer',
            'subtitle' => 'Kelola data pelanggan',
            'customers' => $customers,
        ];

        return view('layout/main', $data)->renderSection('content', view('master/customers/index', $data));
    }

    public function store()
    {
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required',
            'phone' => 'permit_empty',
            'address' => 'permit_empty',
            'credit_limit' => 'required|numeric',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('errors', $validation->getErrors())->withInput();
        }

        // Create customer
        $this->customerModel->insert([
            'code' => $this->request->getPost('code'),
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'credit_limit' => $this->request->getPost('credit_limit'),
        ]);

        return redirect()->to('/master/customers')->with('success', 'Customer berhasil ditambahkan');
    }

    public function update($id)
    {
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required',
            'phone' => 'permit_empty',
            'address' => 'permit_empty',
            'credit_limit' => 'required|numeric',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('errors', $validation->getErrors())->withInput();
        }

        // Update customer
        $this->customerModel->update($id, [
            'code' => $this->request->getPost('code'),
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'credit_limit' => $this->request->getPost('credit_limit'),
        ]);

        return redirect()->to('/master/customers')->with('success', 'Customer berhasil diperbarui');
    }

    public function delete($id)
    {
        // Check if customer has transactions
        // Simplified for now
        $this->customerModel->delete($id);
        return redirect()->to('/master/customers')->with('success', 'Customer berhasil dihapus');
    }
}