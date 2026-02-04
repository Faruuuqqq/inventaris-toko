<?php

namespace App\Services;

use App\Models\SupplierModel;
use CodeIgniter\Database\BaseConnection;

class SupplierDataService
{
    protected SupplierModel $supplierModel;
    protected BaseConnection $db;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Get data for INDEX page (all suppliers)
     */
    public function getIndexData(): array
    {
        $suppliers = $this->supplierModel->asArray()->findAll();

        return [
            'suppliers' => $suppliers,
        ];
    }

    /**
     * Get data for CREATE page
     */
    public function getCreateData(): array
    {
        return [];
    }

    /**
     * Get data for EDIT page
     */
    public function getEditData(): array
    {
        return [];
    }

    /**
     * Get data for DETAIL page with stats and relationships
     */
    public function getDetailData(int $supplierId): array
    {
        $supplier = $this->supplierModel->find($supplierId);

        if (!$supplier) {
            return [];
        }

        // Get recent purchase orders
        $recentPOs = $this->db->table('purchase_orders')
            ->select('id_po, nomor_po, tanggal_po, total_amount, status')
            ->where('supplier_id', $supplierId)
            ->orderBy('tanggal_po', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Get debt/unpaid balance
        $debtStatus = $this->db->table('purchase_orders')
            ->select('SUM(total_amount - received_amount) as total_debt, COUNT(*) as pending_count')
            ->where('purchase_orders.supplier_id', $supplierId)
            ->where('purchase_orders.status !=', 'Dibatalkan')
            ->get()
            ->getRow();

        $totalDebt = $debtStatus->total_debt ?? 0;
        $pendingCount = $debtStatus->pending_count ?? 0;

        // Get statistics
        $stats = $this->db->table('purchase_orders')
            ->select('COUNT(*) as total_pos, SUM(total_amount) as total_purchases, AVG(total_amount) as avg_po')
            ->where('supplier_id', $supplierId)
            ->get()
            ->getRow();

        return [
            'supplier' => $supplier,
            'recentPOs' => $recentPOs,
            'totalDebt' => (int)$totalDebt,
            'pendingCount' => (int)$pendingCount,
            'stats' => $stats,
        ];
    }
}
