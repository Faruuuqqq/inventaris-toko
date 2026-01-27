<?php
namespace App\Controllers\Info;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\SaleItemModel;
use App\Models\CustomerModel;
use App\Models\SalespersonModel;
use App\Models\SupplierModel;

class History extends BaseController
{
    protected $saleModel;
    protected $saleItemModel;
    protected $customerModel;
    protected $salespersonModel;
    protected $supplierModel;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->saleItemModel = new SaleItemModel();
        $this->customerModel = new CustomerModel();
        $this->salespersonModel = new SalespersonModel();
        $this->supplierModel = new SupplierModel();
    }

    public function sales()
    {
        $data = [
            'title' => 'Histori Penjualan',
            'subtitle' => 'Lihat riwayat transaksi penjualan',
            'customers' => $this->customerModel->findAll(),
        ];

        return view('layout/main', $data)->renderSection('content', view('info/history/sales', $data));
    }

    public function salesData()
    {
        $customerId = $this->request->getGet('customer_id');
        $paymentType = $this->request->getGet('payment_type');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $paymentStatus = $this->request->getGet('payment_status');

        $builder = $this->saleModel
            ->select('sales.*, customers.name as customer_name, salespersons.name as salesperson_name')
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

        $sales = $builder->orderBy('sales.created_at', 'DESC')->findAll(100);

        return $this->response->setJSON($sales);
    }

    public function purchases()
    {
        $data = [
            'title' => 'Histori Pembelian',
            'subtitle' => 'Lihat riwayat transaksi pembelian',
            'suppliers' => $this->supplierModel->findAll(),
        ];

        return view('layout/main', $data)->renderSection('content', view('info/history/purchases', $data));
    }

    public function purchasesData()
    {
        $supplierId = $this->request->getGet('supplier_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $status = $this->request->getGet('status');

        $db = \Config\Database::connect();
        $builder = $db->table('purchase_orders')
            ->select('purchase_orders.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id');

        if ($supplierId) {
            $builder->where('purchase_orders.supplier_id', $supplierId);
        }

        if ($status) {
            $builder->where('purchase_orders.status', $status);
        }

        if ($startDate) {
            $builder->where('purchase_orders.tanggal_po >=', $startDate);
        }

        if ($endDate) {
            $builder->where('purchase_orders.tanggal_po <=', $endDate);
        }

        $purchases = $builder->orderBy('purchase_orders.tanggal_po', 'DESC')->get()->getResultArray();

        return $this->response->setJSON($purchases);
    }

    public function returnSales()
    {
        $data = [
            'title' => 'Histori Retur Penjualan',
            'subtitle' => 'Lihat riwayat retur penjualan',
            'customers' => $this->customerModel->findAll(),
        ];

        return view('layout/main', $data)->renderSection('content', view('info/history/return-sales', $data));
    }

    public function salesReturnsData()
    {
        $customerId = $this->request->getGet('customer_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $status = $this->request->getGet('status');

        $db = \Config\Database::connect();
        $builder = $db->table('sales_returns')
            ->select('sales_returns.*, customers.name as customer_name')
            ->join('customers', 'customers.id = sales_returns.customer_id');

        if ($customerId) {
            $builder->where('sales_returns.customer_id', $customerId);
        }

        if ($status) {
            $builder->where('sales_returns.status', $status);
        }

        if ($startDate) {
            $builder->where('sales_returns.tanggal_retur >=', $startDate);
        }

        if ($endDate) {
            $builder->where('sales_returns.tanggal_retur <=', $endDate);
        }

        $returns = $builder->orderBy('sales_returns.tanggal_retur', 'DESC')->get()->getResultArray();

        return $this->response->setJSON($returns);
    }

    public function returnPurchases()
    {
        $data = [
            'title' => 'Histori Retur Pembelian',
            'subtitle' => 'Lihat riwayat retur pembelian',
            'suppliers' => $this->supplierModel->findAll(),
        ];

        return view('layout/main', $data)->renderSection('content', view('info/history/return-purchases', $data));
    }

    public function purchaseReturnsData()
    {
        $supplierId = $this->request->getGet('supplier_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $status = $this->request->getGet('status');

        $db = \Config\Database::connect();
        $builder = $db->table('purchase_returns')
            ->select('purchase_returns.*, suppliers.name as supplier_name')
            ->join('suppliers', 'suppliers.id = purchase_returns.supplier_id');

        if ($supplierId) {
            $builder->where('purchase_returns.supplier_id', $supplierId);
        }

        if ($status) {
            $builder->where('purchase_returns.status', $status);
        }

        if ($startDate) {
            $builder->where('purchase_returns.tanggal_retur >=', $startDate);
        }

        if ($endDate) {
            $builder->where('purchase_returns.tanggal_retur <=', $endDate);
        }

        $returns = $builder->orderBy('purchase_returns.tanggal_retur', 'DESC')->get()->getResultArray();

        return $this->response->setJSON($returns);
    }
}
