<?php
namespace App\Models;

use App\Entities\SalesReturn;
use CodeIgniter\Model;

class SalesReturnModel extends Model
{
    protected $table = 'sales_returns';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = SalesReturn::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'no_retur', 'tanggal_retur', 'sale_id', 'customer_id',
        'alasan', 'status', 'total_retur'
    ];
    protected $useTimestamps = false;

    /**
     * Get sales returns by status
     */
    public function getByStatus($status = null)
    {
        $builder = $this;

        if ($status) {
            $builder->where('status', $status);
        }

        return $builder->orderBy('tanggal_retur', 'DESC')->findAll();
    }

    /**
     * Get pending returns for approval
     */
    public function getPending()
    {
        return $this->where('status', 'Pending')
                    ->orderBy('tanggal_retur', 'ASC')
                    ->findAll();
    }

    /**
     * Approve sales return
     */
    public function approve($salesReturnId)
    {
        $salesReturn = $this->find($salesReturnId);
        if (!$salesReturn) {
            throw new \Exception('Sales Return not found');
        }

        if ($salesReturn->status !== 'Pending') {
            throw new \Exception('Sales Return is not pending');
        }

        return $this->update($salesReturnId, ['status' => 'Disetujui']);
    }

    /**
     * Process approved return (update stock)
     */
    public function processReturn($salesReturnId)
    {
        $db = \Config\Database::connect();

        try {
            $db->transStart();

            $salesReturn = $this->find($salesReturnId);
            if (!$salesReturn) {
                throw new \Exception('Sales Return not found');
            }

            if ($salesReturn->status !== 'Disetujui') {
                throw new \Exception('Sales Return is not approved');
            }

            // Get return items and update stock
            $detailModel = new SalesReturnDetailModel();
            $items = $detailModel->where('return_id', $salesReturnId)->findAll();
            $productModel = new ProductModel();
            $warehouseModel = new WarehouseModel();
            $defaultWarehouse = $warehouseModel->first();
            $warehouseId = $defaultWarehouse ? $defaultWarehouse->id : 1;

            foreach ($items as $item) {
                $productModel->updateStock(
                    $item->product_id ?? $item['product_id'],
                    $warehouseId,
                    $item->quantity ?? $item['quantity'],
                    'IN',
                    'Sales Return #' . $salesReturn->no_retur,
                    $salesReturn->alasan
                );
            }

            $db->transComplete();
            return true;

        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Generate return number
     */
    public function generateReturnNumber(): string
    {
        $date = date('Ymd');
        $count = $this->where('DATE(tanggal_retur)', date('Y-m-d'))->countAllResults();
        return 'SR-' . $date . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }
}