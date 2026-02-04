<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\ProductModel;
use App\Services\ProductDataService;
use CodeIgniter\Model;

class Products extends BaseCRUDController
{
    protected string $viewPath = 'master/products';
    protected string $routePath = '/master/products';
    protected string $entityName = 'Produk';
    protected string $entityNamePlural = 'Products';

    protected ProductDataService $dataService;

    public function __construct()
    {
        parent::__construct();
        $this->dataService = new ProductDataService();
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

    /**
     * Override index to use ProductDataService
     */
    public function index()
    {
        try {
            $data = array_merge(
                ['title' => 'Katalog Produk'],
                $this->dataService->getIndexData()
            );

            return view($this->viewPath . '/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Products index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data produk');
        }
    }

    /**
     * Override create to use ProductDataService
     */
    public function create()
    {
        if (!$this->checkStoreAccess()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $data = array_merge(
            [
                'title' => 'Tambah Produk',
                'subtitle' => 'Tambahkan produk baru ke katalog',
            ],
            $this->dataService->getCreateData()
        );

        return view($this->viewPath . '/create', $data);
    }

    /**
     * Override edit to use ProductDataService and pass 'product' variable
     */
    public function edit($id)
    {
        if (!$this->checkUpdateAccess($id)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $record = $this->model->find($id);

        if (!$record) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan');
        }

        $data = array_merge(
            [
                'title' => 'Edit Produk',
                'subtitle' => 'Ubah data produk',
                'product' => $record, // ← Using English variable name
            ],
            $this->dataService->getEditData()
        );

        return view($this->viewPath . '/edit', $data);
    }

    /**
     * Override detail to use ProductDataService
     */
    public function detail($id)
    {
        $detailData = $this->dataService->getDetailData($id);

        if (empty($detailData)) {
            return redirect()->to($this->routePath)->with('error', 'Produk tidak ditemukan');
        }

        $data = array_merge(
            [
                'title' => 'Detail Produk',
                'subtitle' => $detailData['product']->name,
            ],
            $detailData
        );

        return view($this->viewPath . '/detail', $data);
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
}
