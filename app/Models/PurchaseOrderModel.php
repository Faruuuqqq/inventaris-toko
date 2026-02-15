<?php
namespace App\Models;

use App\Entities\PurchaseOrder;
use CodeIgniter\Model;

class PurchaseOrderModel extends Model
{
    protected $table = 'purchase_orders';
    protected $primaryKey = 'id_po';
    protected $useAutoIncrement = true;
    protected $returnType = PurchaseOrder::class;
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';
    protected $allowedFields = [
        'nomor_po', 'tanggal_po', 'supplier_id', 'user_id',
        'total_amount', 'received_amount', 'status', 'notes'
    ];
    protected $useTimestamps = false;

    // Validation Rules
    protected $validationRules = [
        'nomor_po' => 'required|min_length[3]|max_length[50]|is_unique[purchase_orders.nomor_po,id_po,{id_po}]',
        'tanggal_po' => 'required|valid_date[Y-m-d]',
        'supplier_id' => 'required|integer|greater_than[0]',
        'user_id' => 'required|integer|greater_than[0]',
        'total_amount' => 'required|numeric|greater_than[0]',
        'received_amount' => 'required|numeric|greater_than_equal_to[0]',
        'status' => 'required|in_list[Draft,Dipesan,Diterima Sebagian,Diterima Semua,Dibatalkan]',
        'notes' => 'permit_empty|max_length[500]',
    ];

    protected $validationMessages = [
        'nomor_po' => [
            'required' => 'Nomor PO harus diisi',
            'is_unique' => 'Nomor PO sudah terdaftar',
        ],
        'tanggal_po' => [
            'required' => 'Tanggal PO harus diisi',
            'valid_date' => 'Format tanggal tidak valid (YYYY-MM-DD)',
        ],
        'supplier_id' => [
            'required' => 'Supplier harus dipilih',
            'integer' => 'Supplier tidak valid',
        ],
        'total_amount' => [
            'required' => 'Total PO harus diisi',
            'numeric' => 'Total harus berupa angka',
            'greater_than' => 'Total harus lebih dari 0',
        ],
        'status' => [
            'required' => 'Status harus dipilih',
            'in_list' => 'Status tidak valid',
        ],
    ];

    /**
     * Get purchase orders with filters for history view
     */
    public function getFilteredHistory($supplierId = null, $status = null, $startDate = null, $endDate = null)
    {
        $query = $this->select('purchase_orders.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id');

        if ($supplierId) {
            $query->where('purchase_orders.supplier_id', $supplierId);
        }

        if ($status) {
            $query->where('purchase_orders.status', $status);
        }

        if ($startDate) {
            $query->where('purchase_orders.tanggal_po >=', $startDate);
        }

        if ($endDate) {
            $query->where('purchase_orders.tanggal_po <=', $endDate);
        }

        return $query->orderBy('purchase_orders.tanggal_po', 'DESC')->asArray()->findAll();
    }

    /**
     * Get purchase orders with unpaid status
     */
    public function getUnpaid()
    {
        return $this->whereNotIn('status', ['Diterima Semua', 'Dibatalkan'])
                    ->orderBy('tanggal_po', 'ASC')
                    ->findAll();
    }

    /**
     * Update received amount
     */
    public function updateReceivedAmount($purchaseOrderId, $amount)
    {
        $purchaseOrder = $this->find($purchaseOrderId);
        if (!$purchaseOrder) {
            throw new \Exception('Purchase Order not found');
        }

        $newReceivedAmount = ($purchaseOrder->received_amount ?? 0) + $amount;
        $newStatus = 'Sebagian';

        if ($newReceivedAmount >= $purchaseOrder->total_amount) {
            $newStatus = 'Diterima Semua';
        }

        return $this->update($purchaseOrderId, [
            'received_amount' => $newReceivedAmount,
            'status' => $newStatus
        ]);
    }
    
    /**
     * Create purchase order with items
     */
    public function createPurchaseOrder($data, $items)
    {
        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // Create purchase order header
            $purchaseOrderId = $this->insert($data);

            // Create purchase order items
            $detailModel = new PurchaseOrderDetailModel();
            $detailModel->createPurchaseOrderItems($purchaseOrderId, $items);

            // Update stock if status is 'Diterima Semua'
            if (isset($data['status']) && $data['status'] === 'Diterima Semua') {
                $productModel = new ProductModel();
                $warehouseModel = new \App\Models\WarehouseModel();
                $defaultWarehouse = $warehouseModel->first();
                $warehouseId = $defaultWarehouse ? $defaultWarehouse->id : 1;

                foreach ($items as $item) {
                    $productModel->updateStock(
                        $item['product_id'],
                        $warehouseId,
                        $item['quantity'],
                        'IN',
                        $data['nomor_po'],
                        'Purchase Order ' . $data['nomor_po']
                    );
                }
            }

            $db->transComplete();
            return $purchaseOrderId;
        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }
}