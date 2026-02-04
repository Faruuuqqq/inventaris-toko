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

    protected function getModel(): ProductModel
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
            ->select('products.*, categories.name as category_name, COALESCE(SUM(ps.quantity), 0) as stock')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('product_stocks ps', 'ps.product_id = products.id', 'left')
            ->groupBy('products.id')
            ->findAll();

        return $products;
    }

    protected function getAdditionalViewData(): array
    {
        // Get all categories as array (not Entity objects)
        $categories = $this->categoryModel->asArray()->findAll();
        
        return [
            'categories' => $categories,
            'totalProducts' => $this->model->countAllResults(),
            'totalCategories' => count($categories),
            'lowStockCount' => 0, // TODO: Implement low stock count calculation from product_stocks table
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

    /**
     * Show product detail page
     */
    public function detail($id)
    {
        $product = $this->model->find($id);
        
        if (!$product) {
            return redirect()->to($this->routePath)->with('error', 'Produk tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Produk',
            'subtitle' => $product->name,
            'product' => $product,
            'totalStock' => $this->getProductTotalStock($id),
        ];

        return view($this->viewPath . '/detail', $data);
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
