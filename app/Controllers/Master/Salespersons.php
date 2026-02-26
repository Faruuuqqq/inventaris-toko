<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Helpers\PaginationHelper;
use App\Models\SalespersonModel;
use App\Traits\ApiResponseTrait;

class Salespersons extends BaseCRUDController
{
    use ApiResponseTrait;

    protected string $viewPath = 'master/salespersons';

    protected string $routePath = '/master/salespersons';

    protected string $entityName = 'Sales';

    protected string $entityNamePlural = 'Salespersons';

    protected function getModel(): SalespersonModel
    {
        return new SalespersonModel();
    }

    protected function getStoreValidationRules(): array
    {
        return [
            'name' => 'required',
            'phone' => 'permit_empty',
        ];
    }

    protected function getDataFromRequest(): array
    {
        return [
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address'),
        ];
    }

    protected function getListSelectFields(): string
    {
        return 'id, name, phone';
    }

    public function index()
    {
        try {
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = (int)($this->request->getGet('per_page') ?? 20);

            $params = PaginationHelper::getSafeParams($page, $perPage);
            $page = $params['page'];
            $perPage = $params['perPage'];

            $salespersons = $this->model->asArray()->paginate($perPage, 'default', $page);
            $pager = $this->model->pager;

            return view($this->viewPath . '/index', [
                'title' => 'Daftar Sales',
                'salespersons' => $salespersons,
                'pagination' => PaginationHelper::getPaginationLinks($pager, $perPage),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Salespersons index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data sales');
        }
    }

    public function create()
    {
        if (!$this->checkStoreAccess()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        return view($this->viewPath . '/create', [
            'title' => 'Tambah Sales',
            'subtitle' => 'Tambahkan sales baru',
        ]);
    }

    public function edit($id)
    {
        if (!$this->checkUpdateAccess($id)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $record = $this->model->find($id);

        if (!$record) {
            return redirect()->back()->with('error', 'Sales tidak ditemukan');
        }

        return view($this->viewPath . '/edit', [
            'title' => 'Edit Sales',
            'subtitle' => 'Ubah data sales',
            'salesperson' => $record,
        ]);
    }

    public function detail($id)
    {
        $sales = $this->model->find($id);

        if (!$sales) {
            return redirect()->to($this->routePath)->with('error', 'Sales tidak ditemukan');
        }

        return view($this->viewPath . '/detail', [
            'title' => 'Detail Sales',
            'subtitle' => $sales->name,
            'sales' => $sales,
        ]);
    }

    protected function beforeStore(array $data): array
    {
        $data['is_active'] = 1;
        return $data;
    }

    public function getList()
    {
        $salespersons = $this->model
            ->select('id, name, phone')
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        return $this->respondData($salespersons);
    }
}
