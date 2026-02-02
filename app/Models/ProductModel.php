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
    protected $updatedField = 'updated_at';

    // Validation Rules
    protected $validationRules = [
        'sku' => 'required|min_length[2]|max_length[50]|is_unique[products.sku,id,{id}]',
        'name' => 'required|min_length[2]|max_length[200]',
        'category_id' => 'required|integer',
        'unit' => 'required|max_length[20]',
        'price_buy' => 'required|numeric|greater_than[0]',
        'price_sell' => 'required|numeric|greater_than[0]',
        'min_stock_alert' => 'required|integer|greater_than_equal_to[0]',
    ];

    protected $validationMessages = [
        'sku' => [
            'required' => 'SKU harus diisi',
            'is_unique' => 'SKU sudah digunakan',
            'min_length' => 'SKU minimal 2 karakter',
        ],
        'name' => [
            'required' => 'Nama produk harus diisi',
            'min_length' => 'Nama produk minimal 2 karakter',
        ],
        'category_id' => [
            'required' => 'Kategori harus dipilih',
        ],
        'unit' => [
            'required' => 'Satuan harus diisi',
        ],
        'price_buy' => [
            'required' => 'Harga beli harus diisi',
            'greater_than' => 'Harga beli harus lebih dari 0',
        ],
        'price_sell' => [
            'required' => 'Harga jual harus diisi',
            'greater_than' => 'Harga jual harus lebih dari 0',
        ],
        'min_stock_alert' => [
            'required' => 'Batas stok minimum harus diisi',
            'greater_than_equal_to' => 'Batas stok minimum tidak boleh negatif',
        ],
    ];

    /**
     * Update stock for a product in a specific warehouse
     * Creates a stock mutation record automatically
     *
     * @param int $productId
     * @param int $warehouseId
     * @param int $quantity Positive for IN, Negative for OUT
     * @param string $type IN, OUT, ADJUSTMENT_IN, ADJUSTMENT_OUT, TRANSFER
     * @param string|null $referenceType SALE, PURCHASE, RETURN_SALE, RETURN_PURCHASE, ADJUSTMENT
     * @param int|null $referenceId
     * @param string|null $notes
     * @return bool
     */
    public function updateStock($productId, $warehouseId, $quantity, $type, $referenceType = null, $referenceId = null, $notes = null)
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
            if ($quantity < 0 && $newBalance < 0) {
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
                'reference_number' => $referenceType ? "{$referenceType}-{$referenceId}" : null,
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