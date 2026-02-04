<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\SupplierModel;
use App\Traits\ApiResponseTrait;
use CodeIgniter\Model;

class Suppliers extends BaseCRUDController
{
    use ApiResponseTrait;
    
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

    protected function getIndexData(): array
    {
        return $this->model->asArray()->findAll();
    }

    /**
     * AJAX: Get supplier list for dropdown/select2
     * Returns simplified supplier data for forms
     */
    public function getList()
    {
        $suppliers = $this->model
            ->select('id, code, name, phone')
            ->orderBy('name', 'ASC')
            ->findAll();
        
        return $this->respondData($suppliers);
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
            ->select('id_po, nomor_po, tanggal_po, total_amount, status')
            ->where('supplier_id', $id)
            ->orderBy('tanggal_po', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Get debt/unpaid balance
        $debtStatus = $db->table('purchase_orders')
            ->select('SUM(total_amount - received_amount) as total_debt, COUNT(*) as pending_count')
            ->where('purchase_orders.supplier_id', $id)
            ->where('purchase_orders.status !=', 'Dibatalkan')
            ->get()
            ->getRow();

        $totalDebt = $debtStatus->total_debt ?? 0;
        $pendingCount = $debtStatus->pending_count ?? 0;

        // Get statistics
        $stats = $db->table('purchase_orders')
            ->select('COUNT(*) as total_pos, SUM(total_amount) as total_purchases, AVG(total_amount) as avg_po')
            ->where('supplier_id', $id)
            ->get()
            ->getRow();

        $data = [
            'title' => 'Detail Supplier',
            'subtitle' => $supplier->name,
            'supplier' => $supplier,
            'recentPOs' => $recentPOs,
            'totalDebt' => $totalDebt,
            'pendingCount' => $pendingCount,
            'stats' => $stats,
        ];

        return view($this->viewPath . '/detail', $data);
    }
}
