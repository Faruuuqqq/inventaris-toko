<?php
namespace App\Models;

use App\Entities\StockMutation;
use CodeIgniter\Model;

class StockMutationModel extends Model
{
    protected $table = 'stock_mutations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = StockMutation::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'product_id', 'warehouse_id', 'type', 'quantity',
        'current_balance', 'reference_number', 'notes'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Create stock mutation
     */
    public function createMutation($productId, $warehouseId, $type, $quantity, $currentBalance, $referenceNumber = null, $notes = null)
    {
        $data = [
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'type' => $type,
            'quantity' => $quantity,
            'current_balance' => $currentBalance,
            'reference_number' => $referenceNumber,
            'notes' => $notes
        ];

        return $this->insert($data);
    }

    /**
     * Get product stock in all warehouses
     */
    public function getProductStockAllWarehouses($productId)
    {
        return $this->select('stock_mutations.warehouse_id, warehouses.name as warehouse_name, SUM(CASE WHEN stock_mutations.type IN ("IN", "ADJUSTMENT_IN") THEN stock_mutations.quantity ELSE -stock_mutations.quantity END) as total_stock')
            ->join('warehouses', 'warehouses.id = stock_mutations.warehouse_id')
            ->where('stock_mutations.product_id', $productId)
            ->groupBy('stock_mutations.warehouse_id')
            ->findAll();
    }

    /**
      * Get product mutations
      */
    public function getProductMutations($productId, $warehouseId = null)
    {
        $builder = $this->where('product_id', $productId);

        if ($warehouseId) {
            $builder->where('warehouse_id', $warehouseId);
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Get stock for all products
     */
    public function getProductsStock()
    {
        return $this->select('product_id, SUM(quantity) as total_quantity')
            ->groupBy('product_id')
            ->findAll();
    }

    /**
     * Get all products with their stock information
     */
    public function getAllProductsStock()
    {
        return $this->select('products.id, products.sku, products.name, products.price_sell,
                              COALESCE(SUM(stock_mutations.quantity), 0) as total_stock')
                    ->join('products', 'products.id = stock_mutations.product_id', 'right')
                    ->groupBy('products.id, products.sku, products.name, products.price_sell')
                    ->findAll();
    }
}