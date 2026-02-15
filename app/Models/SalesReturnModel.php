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
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';
    protected $allowedFields = [
        'no_retur', 'tanggal_retur', 'sale_id', 'customer_id',
        'alasan', 'status', 'total_retur'
    ];
    protected $useTimestamps = false;

    // Validation Rules
    protected $validationRules = [
        'no_retur' => 'required|min_length[3]|max_length[50]|is_unique[sales_returns.no_retur,id,{id}]',
        'tanggal_retur' => 'required|valid_date[Y-m-d]',
        'sale_id' => 'required|integer|greater_than[0]',
        'customer_id' => 'required|integer|greater_than[0]',
        'alasan' => 'required|min_length[5]|max_length[500]',
        'status' => 'required|in_list[Pending,Diproses,Selesai,Dibatalkan]',
        'total_retur' => 'required|numeric|greater_than[0]',
    ];

    protected $validationMessages = [
        'no_retur' => [
            'required' => 'Nomor retur harus diisi',
            'is_unique' => 'Nomor retur sudah terdaftar',
        ],
        'tanggal_retur' => [
            'required' => 'Tanggal retur harus diisi',
            'valid_date' => 'Format tanggal tidak valid',
        ],
        'sale_id' => [
            'required' => 'Penjualan harus dipilih',
        ],
        'alasan' => [
            'required' => 'Alasan retur harus diisi',
            'min_length' => 'Alasan minimal 5 karakter',
        ],
        'total_retur' => [
            'required' => 'Total retur harus diisi',
            'greater_than' => 'Total retur harus lebih dari 0',
        ],
    ];

    /**
     * Get sales returns with filters for history view
     */
    public function getSalesReturns($customerId = null, $status = null, $startDate = null, $endDate = null)
    {
        $query = $this->select('sales_returns.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales_returns.customer_id');

        if ($customerId) {
            $query->where('sales_returns.customer_id', $customerId);
        }

        if ($status) {
            $query->where('sales_returns.status', $status);
        }

        if ($startDate) {
            $query->where('sales_returns.tanggal_retur >=', $startDate);
        }

        if ($endDate) {
            $query->where('sales_returns.tanggal_retur <=', $endDate);
        }

        return $query->orderBy('sales_returns.tanggal_retur', 'DESC')->asArray()->findAll();
    }

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