<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Helpers\PaginationHelper;
use App\Models\WarehouseModel;
use App\Traits\ApiResponseTrait;

class Warehouses extends BaseCRUDController
{
    use ApiResponseTrait;

    protected string $viewPath = 'master/warehouses';

    protected string $routePath = '/master/warehouses';

    protected string $entityName = 'Gudang';

    protected string $entityNamePlural = 'Warehouses';

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

    protected function getListSelectFields(): string
    {
        return 'id, code, name, address';
    }

    public function index()
    {
        try {
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = (int)($this->request->getGet('per_page') ?? 20);

            $params = PaginationHelper::getSafeParams($page, $perPage);
            $page = $params['page'];
            $perPage = $params['perPage'];

            $warehouses = $this->model->asArray()->paginate($perPage, 'default', $page);
            $pager = $this->model->pager;

            return view($this->viewPath . '/index', [
                'title' => 'Daftar Gudang',
                'warehouses' => $warehouses,
                'pagination' => PaginationHelper::getPaginationLinks($pager, $perPage),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Warehouses index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data gudang');
        }
    }

    public function create()
    {
        if (!$this->checkStoreAccess()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        return view($this->viewPath . '/create', [
            'title' => 'Tambah Gudang',
            'subtitle' => 'Tambahkan gudang baru',
        ]);
    }

    public function edit($id)
    {
        if (!$this->checkUpdateAccess($id)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $record = $this->model->find($id);

        if (!$record) {
            return redirect()->back()->with('error', 'Gudang tidak ditemukan');
        }

        return view($this->viewPath . '/edit', [
            'title' => 'Edit Gudang',
            'subtitle' => 'Ubah data gudang',
            'warehouse' => $record,
        ]);
    }

    public function detail($id)
    {
        $gudang = $this->model->find($id);

        if (!$gudang) {
            return redirect()->to($this->routePath)->with('error', 'Gudang tidak ditemukan');
        }

        return view($this->viewPath . '/detail', [
            'title' => 'Detail Gudang',
            'subtitle' => $gudang->name,
            'gudang' => $gudang,
        ]);
    }

    protected function beforeStore(array $data): array
    {
        $data['is_active'] = 1;
        return $data;
    }

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
