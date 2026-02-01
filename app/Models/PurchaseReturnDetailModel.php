<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseReturnDetailModel extends Model
{
    protected $table = 'purchase_return_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'return_id', 'product_id', 'quantity', 'price'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'return_id' => 'required|integer',
        'product_id' => 'required|integer',
        'quantity' => 'required|numeric|greater_than[0]',
        'price' => 'required|numeric|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'return_id' => [
            'required' => 'ID retur pembelian harus ada',
            'integer' => 'ID retur pembelian harus berupa angka'
        ],
        'product_id' => [
            'required' => 'Produk harus dipilih',
            'integer' => 'ID produk harus berupa angka'
        ],
        'quantity' => [
            'required' => 'Jumlah harus diisi',
            'numeric' => 'Jumlah harus berupa angka',
            'greater_than' => 'Jumlah harus lebih dari 0'
        ],
        'price' => [
            'required' => 'Harga harus diisi',
            'numeric' => 'Harga harus berupa angka',
            'greater_than_equal_to' => 'Harga tidak boleh kurang dari 0'
        ]
    ];

    public function getReturnDetails($purchaseReturnId)
    {
        return $this->select('purchase_return_items.*, products.name as product_name, products.sku, products.unit')
            ->join('products', 'products.id = purchase_return_items.product_id')
            ->where('purchase_return_items.return_id', $purchaseReturnId)
            ->findAll();
    }

    /**
     * Create purchase return items
     */
    public function createReturnItems($returnId, $items)
    {
        foreach ($items as $item) {
            $data = [
                'return_id' => $returnId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'] ?? 0
            ];

            $this->insert($data);
        }
    }
}