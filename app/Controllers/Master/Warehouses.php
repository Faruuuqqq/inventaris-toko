<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\WarehouseModel;
use App\Services\WarehouseDataService;
use App\Traits\ApiResponseTrait;
use CodeIgniter\Model;

class Warehouses extends BaseCRUDController
{
    use ApiResponseTrait;
    
    protected string $viewPath = 'master/warehouses';
    protected string $routePath = '/master/warehouses';
    protected string $entityName = 'Gudang';
    protected string $entityNamePlural = 'Warehouses';

    protected WarehouseDataService $dataService;

    public function __construct()
    {
        parent::__construct();
        $this->dataService = new WarehouseDataService();
    }

    protected function getModel(): WarehouseModel
    {
        return new WarehouseModel();
    }

    protected function getStoreValidationRules(): array
    {
        return [
            'code' => 'required|is_unique[warehouses.code]',
            'name' => 'required',
            'address' => 'permit_empty',
        ];
    }

    protected function getUpdateValidationRules(int|string $id): array
    {
        return [
            'code' => 'required|is_unique[warehouses.code,id,' . $id . ']',
            'name' => 'required',
            'address' => 'permit_empty',
        ];
    }

    protected function getDataFromRequest(): array
    {
        return [
            'code' => $this->request->getPost('code'),
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address'),
        ];
    }

    /**
     * Override index to use WarehouseDataService
     */
    public function index()
    {
        try {
            $data = array_merge(
                ['title' => 'Daftar Gudang'],
                $this->dataService->getIndexData()
            );

            return view($this->viewPath . '/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Warehouses index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data gudang');
        }
    }

    /**
     * Override create to use WarehouseDataService
     */
    public function create()
    {
        if (!$this->checkStoreAccess()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $data = array_merge(
            [
                'title' => 'Tambah Gudang',
                'subtitle' => 'Tambahkan gudang baru',
            ],
            $this->dataService->getCreateData()
        );

        return view($this->viewPath . '/create', $data);
    }

    /**
     * Override edit to use WarehouseDataService and pass 'warehouse' variable
     */
    public function edit($id)
    {
        if (!$this->checkUpdateAccess($id)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $record = $this->model->find($id);

        if (!$record) {
            return redirect()->back()->with('error', 'Gudang tidak ditemukan');
        }

        $data = array_merge(
            [
                'title' => 'Edit Gudang',
                'subtitle' => 'Ubah data gudang',
                'warehouse' => $record,
            ],
            $this->dataService->getEditData()
        );

        return view($this->viewPath . '/edit', $data);
    }

    /**
     * Override detail to use WarehouseDataService
     */
    public function detail($id)
    {
        $detailData = $this->dataService->getDetailData($id);

        if (empty($detailData)) {
            return redirect()->to($this->routePath)->with('error', 'Gudang tidak ditemukan');
        }

        $data = array_merge(
            [
                'title' => 'Detail Gudang',
                'subtitle' => $detailData['gudang']->name,
            ],
            $detailData
        );

        return view($this->viewPath . '/detail', $data);
    }

    protected function beforeStore(array $data): array
    {
        $data['is_active'] = 1;
        return $data;
    }

    /**
     * AJAX: Get warehouse list for dropdown selection
     * Used in transaction forms
     */
    public function getList()
    {
        $warehouses = $this->model
            ->select('id, code, name, address')
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();
        
        return $this->respondData($warehouses);
    }
}
