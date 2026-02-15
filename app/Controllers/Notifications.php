<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotificationModel;
use App\Models\ProductStockModel;
use App\Models\CustomerModel;
use App\Models\SupplierModel;
use App\Models\SaleModel;
use App\Models\PurchaseOrderModel;

class Notifications extends BaseController
{
    protected $notificationModel;
    protected $productStockModel;
    protected $customerModel;
    protected $supplierModel;
    protected $saleModel;
    protected $poModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        $this->productStockModel = new ProductStockModel();
        $this->customerModel = new CustomerModel();
        $this->supplierModel = new SupplierModel();
        $this->saleModel = new SaleModel();
        $this->poModel = new PurchaseOrderModel();
    }

    /**
     * Get unread notifications count (for navbar badge)
     */
    public function getUnreadCount()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $userId = session()->get('user_id');
        $count = $this->notificationModel->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();

        return $this->response->setJSON(['count' => $count]);
    }

    /**
     * Get recent notifications (for dropdown)
     */
    public function getRecent()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $userId = session()->get('user_id');
        $notifications = $this->notificationModel->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        return $this->response->setJSON(['notifications' => $notifications]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $userId = session()->get('user_id');

        if ($id) {
            // Mark specific notification as read
            $this->notificationModel->where('id', $id)
                ->where('user_id', $userId)
                ->set(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')])
                ->update();
        } else {
            // Mark all as read
            $this->notificationModel->where('user_id', $userId)
                ->where('is_read', 0)
                ->set(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')])
                ->update();
        }

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Check and generate system notifications
     * Run this via cron job or when dashboard loads
     */
    public function checkSystemNotifications()
    {
        $this->checkLowStock();
        $this->checkOverdueReceivables();
        $this->checkOverduePayables();
        $this->checkPendingPOs();
        
        return $this->response->setJSON(['success' => true, 'message' => 'Notifications checked']);
    }

    /**
     * Check low stock and create notifications
     */
    private function checkLowStock()
    {
        $lowStockItems = $this->productStockModel
            ->select('product_stocks.*, products.name as product_name, products.sku, products.min_stock_alert')
            ->join('products', 'products.id = product_stocks.product_id')
            ->where('product_stocks.quantity <= products.min_stock_alert')
            ->findAll();

        foreach ($lowStockItems as $item) {
            // Check if notification already exists for this item (last 24 hours)
            $existing = $this->notificationModel
                ->where('type', 'low_stock')
                ->where('reference_id', $item['product_id'])
                ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
                ->first();

            if (!$existing) {
                $this->notificationModel->insert([
                    'user_id' => null, // Null means all users
                    'type' => 'low_stock',
                    'title' => 'Stok Menipis',
                    'message' => "Stok {$item['product_name']} ({$item['sku']}) tersisa {$item['quantity']} unit (min: {$item['min_stock_alert']})",
                    'reference_id' => $item['product_id'],
                    'reference_type' => 'product',
                    'link' => base_url('master/products'),
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    /**
     * Check overdue receivables and create notifications
     */
    private function checkOverdueReceivables()
    {
        $overdueSales = $this->saleModel
            ->select('sales.*, customers.name as customer_name, customers.phone')
            ->join('customers', 'customers.id = sales.customer_id')
            ->where('sales.payment_type', 'CREDIT')
            ->where('sales.payment_status !=', 'PAID')
            ->where('sales.due_date <', date('Y-m-d'))
            ->where('sales.due_date >=', date('Y-m-d', strtotime('-7 days'))) // Only recent overdue
            ->findAll();

        foreach ($overdueSales as $sale) {
            // Check if notification already exists
            $existing = $this->notificationModel
                ->where('type', 'overdue_receivable')
                ->where('reference_id', $sale['id'])
                ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
                ->first();

            if (!$existing) {
                $this->notificationModel->insert([
                    'user_id' => null,
                    'type' => 'overdue_receivable',
                    'title' => 'Piutang Jatuh Tempo',
                    'message' => "Invoice {$sale['invoice_number']} - {$sale['customer_name']} jatuh tempo " . date('d/m/Y', strtotime($sale['due_date'])) . " (Rp " . number_format($sale['total_amount'] - $sale['paid_amount'], 0, ',', '.') . ")",
                    'reference_id' => $sale['id'],
                    'reference_type' => 'sale',
                    'link' => base_url('finance/payments/receivable'),
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    /**
     * Check overdue payables and create notifications
     */
    private function checkOverduePayables()
    {
        $overduePOs = $this->poModel
            ->select('purchase_orders.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
            ->where('purchase_orders.payment_status !=', 'PAID')
            ->where('purchase_orders.tanggal_po <', date('Y-m-d', strtotime('-30 days'))) // PO older than 30 days
            ->where('purchase_orders.tanggal_po >=', date('Y-m-d', strtotime('-37 days'))) // Recent
            ->findAll();

        foreach ($overduePOs as $po) {
            $existing = $this->notificationModel
                ->where('type', 'overdue_payable')
                ->where('reference_id', $po['id_po'])
                ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
                ->first();

            if (!$existing) {
                $this->notificationModel->insert([
                    'user_id' => null,
                    'type' => 'overdue_payable',
                    'title' => 'Utang Jatuh Tempo',
                    'message' => "PO {$po['nomor_po']} - {$po['supplier_name']} (Rp " . number_format($po['total_amount'] - $po['paid_amount'], 0, ',', '.') . ")",
                    'reference_id' => $po['id_po'],
                    'reference_type' => 'purchase_order',
                    'link' => base_url('finance/payments/payable'),
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    /**
     * Check pending POs
     */
    private function checkPendingPOs()
    {
        $pendingPOs = $this->poModel
            ->select('purchase_orders.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
            ->where('purchase_orders.status', 'Dipesan')
            ->where('purchase_orders.tanggal_po <=', date('Y-m-d', strtotime('-7 days'))) // Ordered > 7 days ago
            ->where('purchase_orders.tanggal_po >=', date('Y-m-d', strtotime('-14 days'))) // But not too old
            ->findAll();

        foreach ($pendingPOs as $po) {
            $existing = $this->notificationModel
                ->where('type', 'pending_po')
                ->where('reference_id', $po['id_po'])
                ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
                ->first();

            if (!$existing) {
                $this->notificationModel->insert([
                    'user_id' => null,
                    'type' => 'pending_po',
                    'title' => 'PO Menunggu Penerimaan',
                    'message' => "PO {$po['nomor_po']} - {$po['supplier_name']} sudah {$po['days_pending']} hari menunggu",
                    'reference_id' => $po['id_po'],
                    'reference_type' => 'purchase_order',
                    'link' => base_url('transactions/purchases'),
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    /**
     * Get notification settings for current user
     */
    public function getSettings()
    {
        $userId = session()->get('user_id');
        $settings = $this->notificationModel->getUserSettings($userId);
        
        return $this->response->setJSON(['settings' => $settings]);
    }

    /**
     * Update notification settings
     */
    public function updateSettings()
    {
        $userId = session()->get('user_id');
        $settings = $this->request->getJSON(true);
        
        $this->notificationModel->updateUserSettings($userId, $settings);
        
        return $this->response->setJSON(['success' => true]);
    }
}
