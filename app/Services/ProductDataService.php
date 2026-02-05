<?php

namespace App\Services;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ProductStockModel;
use App\Helpers\PaginationHelper;

class ProductDataService
{
    protected ProductModel $productModel;
    protected CategoryModel $categoryModel;
    protected ProductStockModel $productStockModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->productStockModel = new ProductStockModel();
    }

    /**
     * Get data for INDEX page (with stats)
     * Returns: [
     *   'products' => [...],
     *   'categories' => [...],
     *   'totalProducts' => int,
     *   'totalCategories' => int,
     *   'totalStock' => int,
     *   'totalValue' => decimal,
     *   'lowStockCount' => int
     * ]
     */
    public function getIndexData(): array
    {
        $products = $this->productModel
            ->select('products.*, categories.name as category_name, COALESCE(SUM(ps.quantity), 0) as stock')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('product_stocks ps', 'ps.product_id = products.id', 'left')
            ->groupBy('products.id')
            ->findAll();

        $categories = $this->categoryModel->asArray()->findAll();

        // Calculate statistics
        $totalStock = 0;
        $totalValue = 0;

        foreach ($products as $product) {
            $stock = (int)($product->stock ?? 0);
            $totalStock += $stock;
            $buyPrice = (float)($product->price_buy ?? 0);
            $totalValue += ($stock * $buyPrice);
        }

        return [
            'products' => $products,
            'categories' => $categories,
            'totalProducts' => count($products),
            'totalCategories' => count($categories),
            'totalStock' => $totalStock,
            'totalValue' => $totalValue,
            'lowStockCount' => 0, // TODO: Implement low stock count
        ];
    }

    /**
     * Get data for CREATE page (minimal)
     * Returns: ['categories' => [...]]
     */
    public function getCreateData(): array
    {
        return [
            'categories' => $this->categoryModel->asArray()->findAll(),
        ];
    }

    /**
     * Get data for EDIT page (minimal)
     * Returns: ['categories' => [...]]
     */
    public function getEditData(): array
    {
        return [
            'categories' => $this->categoryModel->asArray()->findAll(),
        ];
    }

    /**
     * Get data for DETAIL page
     * Returns: ['product' => Entity, 'totalStock' => int]
     */
    public function getDetailData(int $productId): array
    {
        $product = $this->productModel->find($productId);

        if (!$product) {
            return [];
        }

        $result = $this->productStockModel
            ->where('product_id', $productId)
            ->selectSum('quantity')
            ->first();

        return [
            'product' => $product,
            'totalStock' => (int)($result->quantity ?? 0),
        ];
    }

    /**
     * Get paginated data for INDEX page
     * Returns: [
     *   'products' => [...], (paginated)
     *   'categories' => [...],
     *   'totalProducts' => int,
     *   'totalCategories' => int,
     *   'totalStock' => int (all warehouses),
     *   'totalValue' => decimal (all warehouses),
     *   'lowStockCount' => int,
     *   'pagination' => [
     *     'currentPage' => int,
     *     'totalPages' => int,
     *     'perPage' => int,
     *     'total' => int,
     *     'from' => int,
     *     'to' => int,
     *     ...
     *   ]
     * ]
     */
    public function getPaginatedData(?int $page = null, ?int $perPage = null): array
    {
        // Get safe pagination params
        $params = PaginationHelper::getSafeParams($page, $perPage);
        $page = $params['page'];
        $perPage = $params['perPage'];

        // Build query with joins for category and stock
        $query = $this->productModel
            ->select('products.*, categories.name as category_name, COALESCE(SUM(ps.quantity), 0) as stock')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('product_stocks ps', 'ps.product_id = products.id', 'left')
            ->groupBy('products.id');

        // Get paginated results
        $products = $query->paginate($perPage, 'default', $page);
        $pager = $this->productModel->pager;

        // Get categories for create/edit forms (not paginated)
        $categories = $this->categoryModel->asArray()->findAll();

        // Calculate statistics from ALL products (not just paginated)
        $allProducts = $this->productModel
            ->select('products.*, categories.name as category_name, COALESCE(SUM(ps.quantity), 0) as stock')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('product_stocks ps', 'ps.product_id = products.id', 'left')
            ->groupBy('products.id')
            ->findAll();

        $totalStock = 0;
        $totalValue = 0;

        foreach ($allProducts as $product) {
            $stock = (int)($product->stock ?? 0);
            $totalStock += $stock;
            $buyPrice = (float)($product->price_buy ?? 0);
            $totalValue += ($stock * $buyPrice);
        }

        return [
            'products' => $products,
            'categories' => $categories,
            'totalProducts' => count($allProducts),
            'totalCategories' => count($categories),
            'totalStock' => $totalStock,
            'totalValue' => $totalValue,
            'lowStockCount' => 0, // TODO: Implement low stock count
            'pagination' => PaginationHelper::getPaginationLinks($pager, $perPage),
        ];
    }

    /**
     * Get data for PDF EXPORT
     * Returns array of products with all necessary fields for export
     * Supports optional filters
     *
     * @param array $filters Optional filters (category_id, status, etc.)
     * @return array Array of products formatted for export
     */
    public function getExportData(array $filters = []): array
    {
        $query = $this->productModel
            ->select('products.sku, products.name, categories.name as category_name, products.unit, products.price_buy as purchase_price, products.price_sell as selling_price, COALESCE(SUM(ps.quantity), 0) as stock')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('product_stocks ps', 'ps.product_id = products.id', 'left')
            ->groupBy('products.id');

        // Apply filters if provided
        if (!empty($filters['category_id'])) {
            $query->where('products.category_id', $filters['category_id']);
        }

        if (isset($filters['status'])) {
            $query->where('products.status', $filters['status']);
        }

        // Return all matching products (no pagination)
        return $query->findAll();
    }

    /**
     * Get category by ID
     *
     * @param int $categoryId Category ID
     * @return object|null Category object or null if not found
     */
    public function getCategoryById(int $categoryId)
    {
        return $this->categoryModel->find($categoryId);
    }
}
