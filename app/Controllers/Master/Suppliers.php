<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\SupplierModel;
use CodeIgniter\Model;

class Suppliers extends BaseCRUDController
{
    protected string $viewPath = 'master/suppliers';
    protected string $routePath = '/master/suppliers';
    protected string $entityName = 'Supplier';
    protected string $entityNamePlural = 'Suppliers';

    protected function getModel(): SupplierModel
    {
        return new SupplierModel();
    }

    protected function getStoreValidationRules(): array
    {
        return [
            'name' => 'required',
            'phone' => 'permit_empty',
        ];
    }

    protected function getDataFromRequest(): array
    {
        return [
            'code' => $this->request->getPost('code'),
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
        ];
    }

    /**
     * Show supplier detail page
     */
    public function detail($id)
    {
        $supplier = $this->model->find($id);
        
        if (!$supplier) {
            return redirect()->to($this->routePath)->with('error', 'Supplier tidak ditemukan');
        }

        $db = \Config\Database::connect();
        
        // Get recent purchase orders
        $recentPOs = $db->table('purchase_orders')
            ->select('id_po, nomor_po, tanggal_po, total_bayar, status')
            ->where('id_supplier', $id)
            ->orderBy('tanggal_po', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Get debt/unpaid balance
        $debtStatus = $db->table('purchase_orders')
            ->select('SUM(total_bayar - jumlah_dibayar) as total_debt, COUNT(*) as pending_count')
            ->where('id_supplier', $id)
            ->where('status !=', 'Dibatalkan')
            ->get()
            ->getRow();

        $totalDebt = $debtStatus->total_debt ?? 0;
        $pendingCount = $debtStatus->pending_count ?? 0;

        // Get statistics
        $stats = $db->table('purchase_orders')
            ->select('COUNT(*) as total_pos, SUM(total_bayar) as total_purchases, AVG(total_bayar) as avg_po')
            ->where('id_supplier', $id)
            ->get()
            ->getRow();

        $data = [
            'title' => 'Detail Supplier',
            'subtitle' => $supplier['name'],
            'supplier' => $supplier,
            'recentPOs' => $recentPOs,
            'totalDebt' => $totalDebt,
            'pendingCount' => $pendingCount,
            'stats' => $stats,
        ];

        return view($this->viewPath . '/detail', $data);
    }
}
