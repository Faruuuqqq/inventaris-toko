<?php
namespace App\Models;

use App\Entities\Sale;
use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';
    protected $returnType = Sale::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'invoice_number', 'customer_id', 'warehouse_id', 'salesperson_id', 'user_id',
        'total_amount', 'due_date', 'paid_amount',
        'payment_type', 'payment_status', 'is_hidden',
        'kontra_bon_id'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    // GLOBAL SCOPE: Hide hidden sales from non-Owner users
    public function findAll(?int $limit = null, ?int $offset = 0)
    {
        $userRole = session()->get('role');

        if ($userRole !== 'OWNER') {
            $this->where('is_hidden', 0);
        }

        return parent::findAll($limit, $offset);
    }

    /**
     * Get customer sales
     */
    public function getCustomerSales($customerId, $status = null)
    {
        $builder = $this->where('customer_id', $customerId);

        if ($status) {
            $builder->where('payment_status', $status);
        }

        return $builder->orderBy('date', 'DESC')->findAll();
    }

    /**
     * Get unpaid sales for kontra bon
     */
    public function getUnpaidSales($customerId = null)
    {
        $builder = $this->where('payment_type', 'CREDIT')
                         ->where('payment_status !=', 'PAID');

        if ($customerId) {
            $builder->where('customer_id', $customerId);
        }

        return $builder->orderBy('date', 'ASC')->findAll();
    }

    /**
     * Get customer receivable (total unpaid amount)
     */
    public function getCustomerReceivable($customerId)
    {
        $result = $this->select('COALESCE(SUM(total_amount - paid_amount), 0) as receivable')
                       ->where('customer_id', $customerId)
                       ->where('payment_type', 'CREDIT')
                       ->where('payment_status !=', 'PAID')
                       ->first();

        return $result ? (float)$result->receivable : 0;
    }

    /**
     * Toggle hide status (only for OWNER)
     */
    public function toggleHide($saleId)
    {
        $sale = $this->find($saleId);

        if (!$sale) {
            throw new \Exception('Penjualan tidak ditemukan');
        }

        $newStatus = ($sale->is_hidden ?? 0) == 1 ? 0 : 1;

        return $this->update($saleId, ['is_hidden' => $newStatus]);
    }

    /**
     * Get all sales including hidden (for OWNER)
     */
    public function getAllSalesWithHidden($customerId = null, $paymentType = null, $startDate = null, $endDate = null, $paymentStatus = null)
    {
        $builder = $this->select('sales.*, customers.name as customer_name, salespersons.name as salesperson_name')
            ->join('customers', 'customers.id = sales.customer_id')
            ->join('salespersons', 'salespersons.id = sales.salesperson_id', 'left');

        if ($customerId) {
            $builder->where('sales.customer_id', $customerId);
        }

        if ($paymentType) {
            $builder->where('sales.payment_type', $paymentType);
        }

        if ($paymentStatus) {
            $builder->where('sales.payment_status', $paymentStatus);
        }

        if ($startDate) {
            $builder->where('sales.created_at >=', $startDate . ' 00:00:00');
        }

        if ($endDate) {
            $builder->where('sales.created_at <=', $endDate . ' 23:59:59');
        }

        // OWNER sees all, others only see non-hidden
        $userRole = session()->get('role');
        if ($userRole !== 'OWNER') {
            $builder->where('sales.is_hidden', 0);
        }

        return $builder->orderBy('sales.created_at', 'DESC')->findAll(100);
    }
}