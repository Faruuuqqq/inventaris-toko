<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseReturnModel extends Model
{
    protected $table = 'purchase_returns';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'no_retur', 'tanggal_retur', 'po_id', 'supplier_id',
        'alasan', 'status', 'total_retur'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'no_retur' => 'required|max_length[50]|is_unique[purchase_returns.no_retur,id,{id}]',
        'tanggal_retur' => 'required|valid_date',
        'po_id' => 'required|integer',
        'supplier_id' => 'required|integer',
        'status' => 'required|in_list[Pending,Disetujui,Ditolak]',
    ];

    protected $validationMessages = [
        'no_retur' => [
            'required' => 'Nomor retur harus diisi',
            'max_length' => 'Nomor retur maksimal 50 karakter',
            'is_unique' => 'Nomor retur sudah digunakan'
        ],
        'tanggal_retur' => [
            'required' => 'Tanggal retur harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'po_id' => [
            'required' => 'Purchase Order harus dipilih',
            'integer' => 'Purchase Order harus berupa ID yang valid'
        ],
        'supplier_id' => [
            'required' => 'Supplier harus dipilih',
            'integer' => 'Supplier harus berupa ID yang valid'
        ],
        'status' => [
            'required' => 'Status harus dipilih',
            'in_list' => 'Status tidak valid'
        ]
    ];

    public function getPurchaseReturnWithDetails($id)
    {
        return $this->select('purchase_returns.*, suppliers.name as supplier_name, suppliers.phone as supplier_phone')
            ->join('suppliers', 'suppliers.id = purchase_returns.supplier_id')
            ->where('purchase_returns.id', $id)
            ->first();
    }

    public function getPurchaseReturns($supplierId = null, $status = null, $startDate = null, $endDate = null)
    {
        $query = $this->select('purchase_returns.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_returns.supplier_id');

        if ($supplierId) {
            $query->where('purchase_returns.supplier_id', $supplierId);
        }

        if ($status) {
            $query->where('purchase_returns.status', $status);
        }

        if ($startDate) {
            $query->where('purchase_returns.tanggal_retur >=', $startDate);
        }

        if ($endDate) {
            $query->where('purchase_returns.tanggal_retur <=', $endDate);
        }

        return $query->orderBy('purchase_returns.tanggal_retur', 'DESC')->findAll();
    }

    public function generateReturnNumber(): string
    {
        $date = date('Ymd');
        $count = $this->where('DATE(tanggal_retur)', date('Y-m-d'))->countAllResults();
        return 'PR-' . $date . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }
}