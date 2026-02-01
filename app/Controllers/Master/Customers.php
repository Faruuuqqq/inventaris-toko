<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\CustomerModel;
use CodeIgniter\Model;

class Customers extends BaseCRUDController
{
    protected string $viewPath = 'master/customers';
    protected string $routePath = '/master/customers';
    protected string $entityName = 'Customer';
    protected string $entityNamePlural = 'Customers';

    protected function getModel(): Model
    {
        return new CustomerModel();
    }

    protected function getStoreValidationRules(): array
    {
        return [
            'name' => 'required',
            'phone' => 'permit_empty',
            'address' => 'permit_empty',
            'credit_limit' => 'required|numeric',
        ];
    }

    protected function getDataFromRequest(): array
    {
        return [
            'code' => $this->request->getPost('code'),
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'credit_limit' => $this->request->getPost('credit_limit'),
        ];
    }

    protected function getIndexData(): array
    {
        return $this->model->asArray()->findAll();
    }

    /**
     * Show customer detail page
     */
    public function detail($id)
    {
        $customer = $this->model->find($id);
        
        if (!$customer) {
            return redirect()->to($this->routePath)->with('error', 'Customer tidak ditemukan');
        }

        $db = \Config\Database::connect();
        
        // Get recent sales transactions
        $recentSales = $db->table('sales')
            ->select('id_sale, nomor_faktur, tanggal_penjualan, total_penjualan, status_pembayaran')
            ->where('id_customer', $id)
            ->orderBy('tanggal_penjualan', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Get credit usage
        $totalCredit = $db->table('sales')
            ->selectSum('sisa_pembayaran')
            ->where('id_customer', $id)
            ->where('status_pembayaran !=', 'PAID')
            ->get()
            ->getRow();

        $creditUsed = $totalCredit->sisa_pembayaran ?? 0;
        $creditLimit = $customer['credit_limit'] ?? 0;
        $creditAvailable = $creditLimit - $creditUsed;
        $creditPercentage = $creditLimit > 0 ? ($creditUsed / $creditLimit) * 100 : 0;

        // Get statistics
        $stats = $db->table('sales')
            ->select('COUNT(*) as total_transactions, SUM(total_penjualan) as total_sales, AVG(total_penjualan) as avg_sale')
            ->where('id_customer', $id)
            ->get()
            ->getRow();

        $data = [
            'title' => 'Detail Customer',
            'subtitle' => $customer['name'],
            'customer' => $customer,
            'recentSales' => $recentSales,
            'creditUsed' => $creditUsed,
            'creditLimit' => $creditLimit,
            'creditAvailable' => $creditAvailable,
            'creditPercentage' => min($creditPercentage, 100),
            'stats' => $stats,
        ];

        return view($this->viewPath . '/detail', $data);
    }
}
