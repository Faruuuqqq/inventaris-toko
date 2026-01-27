<?php
namespace App\Models;

use App\Entities\Product;
use App\Entities\ProductStock;
use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = Product::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'sku', 'name', 'category_id', 'unit',
        'price_buy', 'price_sell', 'min_stock_alert'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    // Relationships
    protected $with = ['category'];

    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    public function stocks()
    {
        return $this->hasMany(ProductStockModel::class, 'product_id');
    }

    /**
     * Update stock for a product in a specific warehouse
     * Creates a stock mutation record automatically
     *
     * @param int $productId
     * @param int $warehouseId
     * @param int $quantity Positive for IN, Negative for OUT
     * @param string $type IN, OUT, ADJUSTMENT_IN, ADJUSTMENT_OUT, TRANSFER
     * @param string|null $referenceNumber Invoice number, etc.
     * @param string|null $notes
     * @return bool
     */
    public function updateStock($productId, $warehouseId, $quantity, $type, $referenceNumber = null, $notes = null)
    {
        $db = \Config\Database::connect();

        try {
            $db->transStart();

            // Get or create stock record
            $stockModel = new \App\Models\ProductStockModel();
            $stock = $stockModel->where([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId
            ])->first();

            if (!$stock) {
                // Create new stock record
                $stockModel->insert([
                    'product_id' => $productId,
                    'warehouse_id' => $warehouseId,
                    'quantity' => 0,
                ]);
                $currentBalance = 0;
            } else {
                $currentBalance = $stock['quantity'];
            }

            // Calculate new balance
            $newBalance = $currentBalance + $quantity;

            // Check if stock is sufficient for OUT operations
            if (in_array($type, ['OUT', 'ADJUSTMENT_OUT']) && $newBalance < 0) {
                throw new \Exception('Stok tidak mencukupi');
            }

            // Update stock
            if ($stock) {
                $stockModel->update($stock['id'], ['quantity' => $newBalance]);
            } else {
                $stockModel->insert([
                    'product_id' => $productId,
                    'warehouse_id' => $warehouseId,
                    'quantity' => $newBalance,
                ]);
            }

            // Log mutation
            $mutationModel = new \App\Models\StockMutationModel();
            $mutationModel->insert([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'type' => $type,
                'quantity' => $quantity,
                'current_balance' => $newBalance,
                'reference_number' => $referenceNumber,
                'notes' => $notes,
            ]);

            $db->transComplete();

            return $db->transStatus();
        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Get current stock for a product in all warehouses
     */
    public function getStockInAllWarehouses($productId)
    {
        $stockModel = new \App\Models\ProductStockModel();
        $warehouseModel = new \App\Models\WarehouseModel();

        $stocks = $stockModel->where('product_id', $productId)->findAll();

        $result = [];
        foreach ($stocks as $stock) {
            $warehouse = $warehouseModel->find($stock['warehouse_id']);
            $result[] = [
                'warehouse' => $warehouse['name'],
                'warehouse_code' => $warehouse['code'],
                'quantity' => $stock['quantity'],
            ];
        }

        return $result;
    }
}