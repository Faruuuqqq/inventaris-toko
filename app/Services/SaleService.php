<?php

namespace App\Services;

use App\Exceptions\CreditLimitExceededException;
use App\Exceptions\InsufficientStockException;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\SaleItemModel;
use App\Models\SaleModel;
use App\Models\WarehouseModel;

class SaleService
{
    protected SaleModel $saleModel;

    protected SaleItemModel $saleItemModel;

    protected ProductModel $productModel;

    protected CustomerModel $customerModel;

    protected WarehouseModel $warehouseModel;

    protected StockService $stockService;

    protected BalanceService $balanceService;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->saleItemModel = new SaleItemModel();
        $this->productModel = new ProductModel();
        $this->customerModel = new CustomerModel();
        $this->warehouseModel = new WarehouseModel();
        $this->stockService = new StockService();
        $this->balanceService = new BalanceService();
    }

    public function generateInvoiceNumber(): string
    {
        return 'INV-' . date('Ymd') . '-' . str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
    }

    public function processItems(array $items, int $warehouseId): array
    {
        $saleItemsData = [];
        $calculatedTotal = 0;

        foreach ($items as $item) {
            $product = $this->productModel->find($item['product_id']);

            if (!$product) {
                throw new \Exception("Produk ID {$item['product_id']} tidak ditemukan");
            }

            $qty = (int)$item['quantity'];
            if ($qty <= 0) {
                continue;
            }

            $price = (float)$product['price_sell'];
            $discount = (float)($item['discount'] ?? 0);

            $this->stockService->validateStock($product['id'], $warehouseId, $qty);

            $subtotal = ($price * $qty) - $discount;
            $calculatedTotal += $subtotal;

            $saleItemsData[] = [
                'product_id' => $product['id'],
                'quantity' => $qty,
                'price' => $price,
                'discount' => $discount,
                'subtotal' => $subtotal,
            ];
        }

        if (empty($saleItemsData)) {
            throw new \Exception('Tidak ada barang yang valid untuk dijual');
        }

        return [
            'items' => $saleItemsData,
            'total' => $calculatedTotal,
        ];
    }

    public function validateEntities(int $customerId, int $warehouseId): array
    {
        $customer = $this->customerModel->find($customerId);
        if (!$customer) {
            throw new \Exception('Customer tidak ditemukan');
        }

        $warehouse = $this->warehouseModel->find($warehouseId);
        if (!$warehouse) {
            throw new \Exception('Gudang tidak ditemukan');
        }

        return [$customer, $warehouse];
    }

    public function checkCreditLimit(int $customerId, float $newAmount): void
    {
        $customer = $this->customerModel->find($customerId);
        $currentReceivable = (float)($customer['receivable_balance'] ?? 0);
        $creditLimit = (float)($customer['credit_limit'] ?? 0);

        if ($currentReceivable + $newAmount > $creditLimit) {
            throw new CreditLimitExceededException(
                'Batas kredit akan terlampaui. Limit: ' . number_format($creditLimit, 0) .
                ', Outstanding: ' . number_format($currentReceivable, 0) .
                ', Penjualan baru: ' . number_format($newAmount, 0)
            );
        }
    }

    public function createSale(array $saleData): int
    {
        $saleId = $this->saleModel->insert($saleData);

        if (!$saleId) {
            throw new \Exception('Gagal membuat faktur penjualan');
        }

        return $saleId;
    }

    public function createSaleItems(int $saleId, array $saleItemsData, int $warehouseId, string $invoiceNumber): void
    {
        foreach ($saleItemsData as $sItem) {
            $sItem['sale_id'] = $saleId;
            $this->saleItemModel->insert($sItem);

            $this->stockService->deductStock(
                $sItem['product_id'],
                $warehouseId,
                $sItem['quantity'],
                'SALE',
                $saleId,
                "Penjualan $invoiceNumber"
            );
        }
    }

    public function createCashSale(int $customerId, int $warehouseId, array $items, ?int $salespersonId): array
    {
        $invoiceNumber = $this->generateInvoiceNumber();
        $userId = session()->get('id');

        [$customer, $warehouse] = $this->validateEntities($customerId, $warehouseId);

        $processed = $this->processItems($items, $warehouseId);

        $saleData = [
            'invoice_number' => $invoiceNumber,
            'customer_id' => $customerId,
            'salesperson_id' => $salespersonId,
            'warehouse_id' => $warehouseId,
            'user_id' => $userId,
            'total_amount' => $processed['total'],
            'paid_amount' => $processed['total'],
            'payment_type' => 'CASH',
            'payment_status' => 'PAID',
            'is_hidden' => 0,
        ];

        $saleId = $this->createSale($saleData);
        $this->createSaleItems($saleId, $processed['items'], $warehouseId, $invoiceNumber);

        return ['saleId' => $saleId, 'invoiceNumber' => $invoiceNumber];
    }

    public function createCreditSale(int $customerId, int $warehouseId, array $items, ?int $salespersonId, string $dueDate): array
    {
        $invoiceNumber = $this->generateInvoiceNumber();
        $userId = session()->get('id');

        [$customer, $warehouse] = $this->validateEntities($customerId, $warehouseId);

        $processed = $this->processItems($items, $warehouseId);

        $this->checkCreditLimit($customerId, $processed['total']);

        $saleData = [
            'invoice_number' => $invoiceNumber,
            'customer_id' => $customerId,
            'salesperson_id' => $salespersonId,
            'warehouse_id' => $warehouseId,
            'user_id' => $userId,
            'total_amount' => $processed['total'],
            'paid_amount' => 0,
            'due_date' => $dueDate,
            'payment_type' => 'CREDIT',
            'payment_status' => 'UNPAID',
            'is_hidden' => 0,
        ];

        $saleId = $this->createSale($saleData);
        $this->createSaleItems($saleId, $processed['items'], $warehouseId, $invoiceNumber);

        $this->balanceService->calculateCustomerReceivable($customerId);

        return ['saleId' => $saleId, 'invoiceNumber' => $invoiceNumber];
    }
}
