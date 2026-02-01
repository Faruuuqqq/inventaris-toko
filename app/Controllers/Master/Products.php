<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ProductStockModel;
use CodeIgniter\Model;

class Products extends BaseCRUDController
{
    protected string $viewPath = 'master/products';
    protected string $routePath = '/master/products';
    protected string $entityName = 'Produk';
    protected string $entityNamePlural = 'Products';

    protected CategoryModel $categoryModel;
    protected ProductStockModel $productStockModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new CategoryModel();
        $this->productStockModel = new ProductStockModel();
    }

    protected function getModel(): Model
    {
        return new ProductModel();
    }

    protected function getStoreValidationRules(): array
    {
        return [
            'sku' => 'required|is_unique[products.sku]',
            'name' => 'required',
            'category_id' => 'required',
            'unit' => 'required',
            'price_buy' => 'required|numeric|greater_than[0]',
            'price_sell' => 'required|numeric|greater_than[0]',
            'min_stock_alert' => 'required|integer|greater_than_equal_to[0]',
        ];
    }

    protected function getUpdateValidationRules(int|string $id): array
    {
        return [
            'sku' => 'required|is_unique[products.sku,id,' . $id . ']',
            'name' => 'required',
            'category_id' => 'required',
            'unit' => 'required',
            'price_buy' => 'required|numeric|greater_than[0]',
            'price_sell' => 'required|numeric|greater_than[0]',
            'min_stock_alert' => 'required|integer|greater_than_equal_to[0]',
        ];
    }

    protected function getDataFromRequest(): array
    {
        return [
            'sku' => $this->request->getPost('sku'),
            'name' => $this->request->getPost('name'),
            'category_id' => $this->request->getPost('category_id'),
            'unit' => $this->request->getPost('unit'),
            'price_buy' => $this->request->getPost('price_buy'),
            'price_sell' => $this->request->getPost('price_sell'),
            'min_stock_alert' => $this->request->getPost('min_stock_alert'),
        ];
    }

    protected function getIndexData(): array
    {
        $products = $this->model
            ->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->findAll();

        // Add stock data to each product
        foreach ($products as $product) {
            $product->stock = $this->getProductTotalStock($product->id);
        }

        return $products;
    }

    protected function getAdditionalViewData(): array
    {
        $products = $this->getIndexData();
        $categories = $this->categoryModel->findAll();

        // Calculate statistics
        $totalStock = 0;
        $totalValue = 0;

        foreach ($products as $product) {
            $stock = $product->stock ?? 0;
            $totalStock += $stock;
            $totalValue += $product->price_sell * $stock;
        }

        return [
            'subtitle' => 'Kelola daftar produk dan kategori',
            'categories' => $categories,
            'totalProducts' => count($products),
            'totalCategories' => count($categories),
            'totalStock' => $totalStock,
            'totalValue' => $totalValue,
        ];
    }

    // Override index to use custom logic
    public function index()
    {
        try {
            $products = $this->getIndexData();
            $additionalData = $this->getAdditionalViewData();

            $data = array_merge([
                'title' => $this->entityName,
                'products' => $products,
            ], $additionalData);

            return view($this->viewPath . '/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Products index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data produk: ' . $e->getMessage());
        }
    }

    protected function afterStore($insertId): void
    {
        log_message('info', "Product created: ID {$insertId}, SKU: {$this->request->getPost('sku')}");
    }

    protected function afterUpdate($id): void
    {
        log_message('info', "Product updated: ID {$id}, SKU: {$this->request->getPost('sku')}");
    }

    protected function beforeDelete($id): void
    {
        $product = $this->model->find($id);
        if ($product) {
            log_message('info', "Product deleted: ID {$id}, SKU: {$product->sku}");
        }
    }

    private function getProductTotalStock($productId): int
    {
        $result = $this->productStockModel
            ->where('product_id', $productId)
            ->selectSum('quantity')
            ->first();
        return (int)($result->quantity ?? 0);
    }
}
