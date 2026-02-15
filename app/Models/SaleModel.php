<?php
namespace App\Models;

use App\Entities\Sale;
use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';
    protected $returnType = Sale::class;
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';
    protected $allowedFields = [
        'invoice_number', 'customer_id', 'warehouse_id', 'salesperson_id', 'user_id',
        'total_amount', 'due_date', 'paid_amount',
        'payment_type', 'payment_status', 'is_hidden',
        'kontra_bon_id',
        'delivery_number', 'delivery_date', 'delivery_address', 'delivery_notes', 'delivery_driver_id'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation Rules
    protected $validationRules = [
        'invoice_number' => 'required|min_length[5]|max_length[50]|is_unique[sales.invoice_number,id,{id}]',
        'customer_id' => 'required|integer|greater_than[0]',
        'warehouse_id' => 'required|integer|greater_than[0]',
        'salesperson_id' => 'permit_empty|integer|greater_than_equal_to[0]',
        'user_id' => 'required|integer|greater_than[0]',
        'total_amount' => 'required|numeric|greater_than_equal_to[0]',
        'due_date' => 'permit_empty|valid_date[Y-m-d]',
        'paid_amount' => 'required|numeric|greater_than_equal_to[0]',
        'payment_type' => 'required|in_list[CASH,CREDIT]',
        'payment_status' => 'required|in_list[UNPAID,PARTIAL,PAID]',
        'is_hidden' => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'invoice_number' => [
            'required' => 'Nomor invoice harus diisi',
            'is_unique' => 'Nomor invoice sudah terdaftar',
        ],
        'customer_id' => [
            'required' => 'Pelanggan harus dipilih',
            'integer' => 'Pelanggan tidak valid',
        ],
        'warehouse_id' => [
            'required' => 'Gudang harus dipilih',
        ],
        'total_amount' => [
            'required' => 'Total penjualan harus diisi',
            'numeric' => 'Total harus berupa angka',
            'greater_than_equal_to' => 'Total tidak boleh negatif',
        ],
        'payment_type' => [
            'required' => 'Jenis pembayaran harus dipilih',
            'in_list' => 'Jenis pembayaran tidak valid',
        ],
    ];

     /**
      * GLOBAL SCOPE: Automatically filter hidden sales from query results
      *
      * This is a security measure to prevent non-OWNER users from seeing hidden sales in history.
      * Hidden sales (is_hidden = 1) are only visible to OWNER role users.
      *
      * Implementation Detail:
      * - Whenever findAll() is called, this method adds WHERE is_hidden = 0 if user is not OWNER
      * - OWNER can see all sales including hidden ones (for audit/recovery)
      * - Other roles (ADMIN, GUDANG, SALES) never see hidden sales, even if queried directly
      *
      * Security Benefit:
      * - Prevents accidental exposure of hidden transactions
      * - Ensures hidden sales don't appear in reports/exports for non-owner users
      * - Clear separation between OWNER visibility and staff visibility
      *
      * @param int|null $limit Optional result limit
      * @param int $offset Query offset
      * @return mixed Array of Sale entities
      */
     public function findAll(?int $limit = null, ?int $offset = 0)
     {
         $userRole = session()->get('role');

         // Only OWNER can see hidden sales. All other roles must filter them out.
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

        return $builder->orderBy('created_at', 'DESC')->findAll();
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

        return $builder->orderBy('created_at', 'ASC')->findAll();
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
      * Toggle sale visibility (hide/unhide from transaction history)
      *
      * SECURITY NOTE: This method performs the actual data update but does NOT check permissions.
      * Permission validation MUST happen in the Controller layer before calling this method.
      * Only users with OWNER role are allowed to hide sales.
      *
      * Business Logic:
      * - Hidden sales are excluded from history queries via global scope (findAll)
      * - Hidden sales are still in database and can be restored by owner
      * - Hiding sales is a sensitive operation (hides revenue) - only OWNER can do this
      *
      * Permission Chain:
      * 1. UI: Only OWNER role sees the hide button
      * 2. Controller: OWNER role check happens in Sales::toggleHide()
      * 3. Model: This method executes the toggle (assumes permission already checked)
      *
      * Why OWNER Only?
      * - Sales hiding is a sensitive financial operation
      * - Only business owner should decide what appears in reports
      * - Prevents accidental data hiding by staff (ADMIN, GUDANG, SALES roles)
      * - Maintains data integrity and audit trail
      *
      * @param int $saleId The ID of the sale to toggle
      * @return bool True if update was successful
      * @throws \Exception If sale with given ID is not found
      *
      * @see \App\Controllers\Transactions\Sales::toggleHide() - Permission check
      * @see \App\Controllers\Info\History::toggleSaleHide() - AJAX endpoint
      * @see findAll() - Global scope that automatically filters hidden sales
      */
     public function toggleHide($saleId)
     {
         $sale = $this->find($saleId);

         if (!$sale) {
             throw new \Exception('Penjualan tidak ditemukan');
         }

         // Toggle: 0 (visible) ↔ 1 (hidden)
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