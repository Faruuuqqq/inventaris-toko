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