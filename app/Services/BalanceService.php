<?php

namespace App\Services;

use App\Models\SaleModel;
use App\Models\PurchaseOrderModel;
use App\Models\PaymentModel;
use App\Models\CustomerModel;
use App\Models\SupplierModel;

class BalanceService
{
    protected $saleModel;
    protected $purchaseOrderModel;
    protected $paymentModel;
    protected $customerModel;
    protected $supplierModel;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->purchaseOrderModel = new PurchaseOrderModel();
        $this->paymentModel = new PaymentModel();
        $this->customerModel = new CustomerModel();
        $this->supplierModel = new SupplierModel();
    }

    /**
     * Calculate customer receivable balance
     * receivable = total credit sales - total payments received
     * 
     * @param int $customerId
     * @return float
     */
    public function calculateCustomerReceivable($customerId)
    {
        // Get total of unpaid credit sales
        $creditSales = $this->saleModel
            ->selectSum('total_amount', 'total_sales')
            ->where('customer_id', $customerId)
            ->where('payment_type', 'CREDIT')
            ->where('payment_status !=', 'PAID')
            ->where('deleted_at', null)
            ->first();

        $totalSales = $creditSales['total_sales'] ?? 0;

        // Get total payments received for this customer
        $payments = $this->paymentModel
            ->selectSum('amount', 'total_payments')
            ->where('customer_id', $customerId)
            ->where('type', 'RECEIVABLE')
            ->where('deleted_at', null)
            ->first();

        $totalPayments = $payments['total_payments'] ?? 0;

        // Calculate balance
        $balance = max(0, $totalSales - $totalPayments);

        // Update customer record
        $this->customerModel->update($customerId, [
            'receivable_balance' => $balance,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $balance;
    }

    /**
     * Calculate supplier debt balance
     * debt = total unpaid purchases - total payments made
     * 
     * @param int $supplierId
     * @return float
     */
    public function calculateSupplierDebt($supplierId)
    {
        // Get total of unpaid purchases
        $purchases = $this->purchaseOrderModel
            ->selectSum('total_amount', 'total_purchases')
            ->where('supplier_id', $supplierId)
            ->where('payment_status !=', 'PAID')
            ->where('deleted_at', null)
            ->first();

        $totalPurchases = $purchases['total_purchases'] ?? 0;

        // Get total payments made to this supplier
        $payments = $this->paymentModel
            ->selectSum('amount', 'total_payments')
            ->where('supplier_id', $supplierId)
            ->where('type', 'PAYABLE')
            ->where('deleted_at', null)
            ->first();

        $totalPayments = $payments['total_payments'] ?? 0;

        // Calculate balance
        $balance = max(0, $totalPurchases - $totalPayments);

        // Update supplier record
        $this->supplierModel->update($supplierId, [
            'debt_balance' => $balance,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $balance;
    }

    /**
     * Reconcile customer balance - verify it's accurate
     * 
     * @param int $customerId
     * @return array ['is_balanced' => bool, 'difference' => float]
     */
    public function reconcileCustomerBalance($customerId)
    {
        $customer = $this->customerModel->find($customerId);
        if (!$customer) {
            return ['is_balanced' => false, 'difference' => 0, 'error' => 'Customer not found'];
        }

        $calculatedBalance = $this->calculateCustomerReceivable($customerId);
        $storedBalance = (float)$customer['receivable_balance'];
        
        $difference = abs($calculatedBalance - $storedBalance);
        $isBalanced = $difference < 0.01; // Allow small rounding difference

        return [
            'is_balanced' => $isBalanced,
            'difference' => $difference,
            'calculated' => $calculatedBalance,
            'stored' => $storedBalance
        ];
    }

    /**
     * Reconcile supplier balance - verify it's accurate
     * 
     * @param int $supplierId
     * @return array ['is_balanced' => bool, 'difference' => float]
     */
    public function reconcileSupplierBalance($supplierId)
    {
        $supplier = $this->supplierModel->find($supplierId);
        if (!$supplier) {
            return ['is_balanced' => false, 'difference' => 0, 'error' => 'Supplier not found'];
        }

        $calculatedBalance = $this->calculateSupplierDebt($supplierId);
        $storedBalance = (float)$supplier['debt_balance'];
        
        $difference = abs($calculatedBalance - $storedBalance);
        $isBalanced = $difference < 0.01; // Allow small rounding difference

        return [
            'is_balanced' => $isBalanced,
            'difference' => $difference,
            'calculated' => $calculatedBalance,
            'stored' => $storedBalance
        ];
    }

    /**
     * Get customer receivable summary
     * Shows customers with outstanding balance
     * 
     * @return array
     */
    public function getCustomerReceivableSummary()
    {
        return $this->customerModel
            ->where('receivable_balance >', 0)
            ->orderBy('receivable_balance', 'DESC')
            ->findAll();
    }

    /**
     * Get supplier debt summary
     * Shows suppliers with outstanding debt
     * 
     * @return array
     */
    public function getSupplierDebtSummary()
    {
        return $this->supplierModel
            ->where('debt_balance >', 0)
            ->orderBy('debt_balance', 'DESC')
            ->findAll();
    }

    /**
     * Calculate total receivable across all customers
     * 
     * @return float
     */
    public function getTotalReceivable()
    {
        $result = $this->customerModel
            ->selectSum('receivable_balance', 'total')
            ->first();

        return $result['total'] ?? 0;
    }

    /**
     * Calculate total debt across all suppliers
     * 
     * @return float
     */
    public function getTotalDebt()
    {
        $result = $this->supplierModel
            ->selectSum('debt_balance', 'total')
            ->first();

        return $result['total'] ?? 0;
    }
}
