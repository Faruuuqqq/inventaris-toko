<?php
namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;

class Products extends BaseController
{
    protected $productModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        try {
            $products = $this->productModel
                ->select('products.*, categories.name as category_name')
                ->join('categories', 'categories.id = products.category_id', 'left')
                ->findAll();

            $categories = $this->categoryModel->findAll();

            // Calculate statistics
            $totalProducts = count($products);
            $totalCategories = count($categories);
            $totalStock = 0;
            $totalValue = 0;

            foreach ($products as $product) {
                $totalStock += $this->getProductTotalStock($product['id']);
                $totalValue += $product['price_sell'] * $this->getProductTotalStock($product['id']);
            }

            $data = [
                'title' => 'Produk',
                'subtitle' => 'Kelola daftar produk dan kategori',
                'products' => $products,
                'categories' => $categories,
                'totalProducts' => $totalProducts,
                'totalCategories' => $totalCategories,
                'totalStock' => $totalStock,
                'totalValue' => $totalValue,
            ];

            return view('layout/main', $data)->renderSection('content', view('master/products/index', $data));
        } catch (\Exception $e) {
            log_message('error', 'Products index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data produk: ' . $e->getMessage());
        }
    }

    public function store()
    {
        try {
            // Validate input
            $validation = \Config\Services::validation();
            $validation->setRules([
                'sku' => 'required|is_unique[products.sku]',
                'name' => 'required',
                'category_id' => 'required',
                'unit' => 'required',
                'price_buy' => 'required|numeric|greater_than[0]',
                'price_sell' => 'required|numeric|greater_than[0]',
                'min_stock_alert' => 'required|integer|greater_than_equal_to[0]',
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                return redirect()->back()->with('errors', $validation->getErrors())->withInput();
            }

            // Create product
            $productId = $this->productModel->insert([
                'sku' => $this->request->getPost('sku'),
                'name' => $this->request->getPost('name'),
                'category_id' => $this->request->getPost('category_id'),
                'unit' => $this->request->getPost('unit'),
                'price_buy' => $this->request->getPost('price_buy'),
                'price_sell' => $this->request->getPost('price_sell'),
                'min_stock_alert' => $this->request->getPost('min_stock_alert'),
            ]);

            log_message('info', "Product created: ID {$productId}, SKU: {$this->request->getPost('sku')}");
            return redirect()->to('/master/products')->with('success', 'Produk berhasil ditambahkan');
        } catch (\Exception $e) {
            log_message('error', 'Product creation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan produk: ' . $e->getMessage())->withInput();
        }
    }

    public function update($id)
    {
        try {
            // Check if product exists
            $product = $this->productModel->find($id);
            if (!$product) {
                return redirect()->back()->with('error', 'Produk tidak ditemukan')->withInput();
            }

            // Validate input
            $validation = \Config\Services::validation();
            $validation->setRules([
                'sku' => 'required|is_unique[products.sku,id,'.$id.']',
                'name' => 'required',
                'category_id' => 'required',
                'unit' => 'required',
                'price_buy' => 'required|numeric|greater_than[0]',
                'price_sell' => 'required|numeric|greater_than[0]',
                'min_stock_alert' => 'required|integer|greater_than_equal_to[0]',
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                return redirect()->back()->with('errors', $validation->getErrors())->withInput();
            }

            // Update product
            $this->productModel->update($id, [
                'sku' => $this->request->getPost('sku'),
                'name' => $this->request->getPost('name'),
                'category_id' => $this->request->getPost('category_id'),
                'unit' => $this->request->getPost('unit'),
                'price_buy' => $this->request->getPost('price_buy'),
                'price_sell' => $this->request->getPost('price_sell'),
                'min_stock_alert' => $this->request->getPost('min_stock_alert'),
            ]);

            log_message('info', "Product updated: ID {$id}, SKU: {$this->request->getPost('sku')}");
            return redirect()->to('/master/products')->with('success', 'Produk berhasil diperbarui');
        } catch (\Exception $e) {
            log_message('error', 'Product update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui produk: ' . $e->getMessage())->withInput();
        }
    }

    public function delete($id)
    {
        try {
            // Check if product exists
            $product = $this->productModel->find($id);
            if (!$product) {
                return redirect()->back()->with('error', 'Produk tidak ditemukan');
            }

            // Delete product
            $this->productModel->delete($id);
            log_message('info', "Product deleted: ID {$id}, SKU: {$product['sku']}");
            return redirect()->to('/master/products')->with('success', 'Produk berhasil dihapus');
        } catch (\Exception $e) {
            log_message('error', 'Product deletion error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus produk: ' . $e->getMessage());
        }
    }

    private function getProductTotalStock($productId)
    {
        $productStockModel = new \App\Models\ProductStockModel();
        $result = $productStockModel->where('product_id', $productId)->selectSum('quantity')->first();
        return $result['quantity'] ?? 0;
    }
}