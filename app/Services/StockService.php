<?php

namespace App\Services;

use App\Models\ProductStockModel;
use App\Models\StockMutationModel;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidTransactionException;

class StockService
{
    protected $productStockModel;
    protected $stockMutationModel;

    public function __construct()
    {
        $this->productStockModel = new ProductStockModel();
        $this->stockMutationModel = new StockMutationModel();
    }

    /**
     * Deduct stock from warehouse
     * Used for: Sales, Purchase Returns
     * 
     * @param int $productId
     * @param int $warehouseId
     * @param int $quantity
     * @param string $type (SALE, RETURN_OUT, etc)
     * @param int|null $referenceId (id of sale/return record)
     * @param string $notes
     * @throws InsufficientStockException
     * @return bool
     */
    public function deductStock($productId, $warehouseId, $quantity, $type = 'SALE', $referenceId = null, $notes = '')
    {
        // Validate inputs
        if (!$productId || !$warehouseId || $quantity <= 0) {
            throw new InvalidTransactionException("Data stok tidak valid");
        }

        // Check current stock
        $currentStock = $this->getAvailableStock($productId, $warehouseId);
        
        if ($currentStock < $quantity) {
            throw new InsufficientStockException(
                "Stok tidak cukup untuk produk ini. Tersedia: {$currentStock}, Diminta: {$quantity}"
            );
        }

        // Get current stock before deduction
        $stockBefore = $currentStock;

        // Update product_stocks
        $this->productStockModel
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->update(['quantity' => $currentStock - $quantity]);

        // Log to stock movements
        $this->logStockMovement(
            $productId,
            $warehouseId,
            0,                          // qty_in
            $quantity,                  // qty_out
            $stockBefore - $quantity,   // balance_after
            $type,
            $referenceId,
            $notes
        );

        return true;
    }

    /**
     * Add stock to warehouse
     * Used for: Purchases, Sales Returns
     * 
     * @param int $productId
     * @param int $warehouseId
     * @param int $quantity
     * @param string $type (PURCHASE, RETURN_IN, etc)
     * @param int|null $referenceId
     * @param string $notes
     * @throws InvalidTransactionException
     * @return bool
     */
    public function addStock($productId, $warehouseId, $quantity, $type = 'PURCHASE', $referenceId = null, $notes = '')
    {
        // Validate inputs
        if (!$productId || !$warehouseId || $quantity <= 0) {
            throw new InvalidTransactionException("Data stok tidak valid");
        }

        // Get current stock
        $currentStock = $this->getAvailableStock($productId, $warehouseId);
        $newStock = $currentStock + $quantity;

        // Update product_stocks
        $this->productStockModel
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->update(['quantity' => $newStock]);

        // Log to stock movements
        $this->logStockMovement(
            $productId,
            $warehouseId,
            $quantity,                  // qty_in
            0,                          // qty_out
            $newStock,                  // balance_after
            $type,
            $referenceId,
            $notes
        );

        return true;
    }

    /**
     * Get available stock for product in warehouse
     * 
     * @param int $productId
     * @param int $warehouseId
     * @return int
     */
    public function getAvailableStock($productId, $warehouseId)
    {
        $stock = $this->productStockModel
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();

        return $stock ? (int)$stock['quantity'] : 0;
    }

    /**
     * Validate if stock is available (without deducting)
     * 
     * @param int $productId
     * @param int $warehouseId
     * @param int $quantity
     * @throws InsufficientStockException
     * @return bool
     */
    public function validateStock($productId, $warehouseId, $quantity)
    {
        $available = $this->getAvailableStock($productId, $warehouseId);
        
        if ($available < $quantity) {
            throw new InsufficientStockException(
                "Stok tidak cukup untuk produk ini. Tersedia: {$available}, Diminta: {$quantity}"
            );
        }

        return true;
    }

    /**
     * Log stock movement to track history
     * 
     * @param int $productId
     * @param int $warehouseId
     * @param int $qtyIn
     * @param int $qtyOut
     * @param int $balanceAfter
     * @param string $type
     * @param int|null $referenceId
     * @param string $notes
     * @return bool
     */
    protected function logStockMovement($productId, $warehouseId, $qtyIn, $qtyOut, $balanceAfter, $type, $referenceId = null, $notes = '')
    {
        return $this->stockMutationModel->insert([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'type' => $type,                        // IN, OUT, ADJUSTMENT
            'qty_in' => $qtyIn,
            'qty_out' => $qtyOut,
            'balance_after' => $balanceAfter,
            'reference_id' => $referenceId,
            'reference_type' => $this->getRefType($type), // SALE, PURCHASE, RETURN, etc
            'notes' => $notes,
            'created_by' => session()->get('id') ?? 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get reference type based on movement type
     * 
     * @param string $type
     * @return string
     */
    protected function getRefType($type)
    {
        $map = [
            'SALE' => 'SALE',
            'RETURN_OUT' => 'RETURN',
            'PURCHASE' => 'PURCHASE',
            'RETURN_IN' => 'RETURN',
            'ADJUSTMENT' => 'ADJUSTMENT'
        ];
        
        return $map[$type] ?? 'OTHER';
    }

    /**
     * Get stock movement history for a product
     * 
     * @param int $productId
     * @param int|null $warehouseId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getMovementHistory($productId, $warehouseId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->stockMutationModel
            ->where('product_id', $productId);

        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }

        if ($startDate) {
            $builder->where('created_at >=', $startDate . ' 00:00:00');
        }

        if ($endDate) {
            $builder->where('created_at <=', $endDate . ' 23:59:59');
        }

        return $builder->orderBy('created_at', 'ASC')->findAll();
    }
}
