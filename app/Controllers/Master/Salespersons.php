<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\SalespersonModel;
use App\Services\SalespersonDataService;
use App\Traits\ApiResponseTrait;
use CodeIgniter\Model;

class Salespersons extends BaseCRUDController
{
    use ApiResponseTrait;
    
    protected string $viewPath = 'master/salespersons';
    protected string $routePath = '/master/salespersons';
    protected string $entityName = 'Sales';
    protected string $entityNamePlural = 'Salespersons';

    protected SalespersonDataService $dataService;

    public function __construct()
    {
        parent::__construct();
        $this->dataService = new SalespersonDataService();
    }

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

    /**
     * Override index to use SalespersonDataService
     */
    public function index()
    {
        try {
            $page = (int)($this->request->getGet('page') ?? 1);
            $perPage = (int)($this->request->getGet('per_page') ?? 20);

            $data = array_merge(
                ['title' => 'Daftar Sales'],
                $this->dataService->getPaginatedData($page, $perPage)
            );

            return view($this->viewPath . '/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Salespersons index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data sales');
        }
    }

    /**
     * Override create to use SalespersonDataService
     */
    public function create()
    {
        if (!$this->checkStoreAccess()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $data = array_merge(
            [
                'title' => 'Tambah Sales',
                'subtitle' => 'Tambahkan sales baru',
            ],
            $this->dataService->getCreateData()
        );

        return view($this->viewPath . '/create', $data);
    }

    /**
     * Override edit to use SalespersonDataService and pass 'salesperson' variable
     */
    public function edit($id)
    {
        if (!$this->checkUpdateAccess($id)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses');
        }

        $record = $this->model->find($id);

        if (!$record) {
            return redirect()->back()->with('error', 'Sales tidak ditemukan');
        }

        $data = array_merge(
            [
                'title' => 'Edit Sales',
                'subtitle' => 'Ubah data sales',
                'salesperson' => $record,
            ],
            $this->dataService->getEditData()
        );

        return view($this->viewPath . '/edit', $data);
    }

    /**
     * Override detail to use SalespersonDataService
     */
    public function detail($id)
    {
        $detailData = $this->dataService->getDetailData($id);

        if (empty($detailData)) {
            return redirect()->to($this->routePath)->with('error', 'Sales tidak ditemukan');
        }

        $data = array_merge(
            [
                'title' => 'Detail Sales',
                'subtitle' => $detailData['sales']->name,
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
     * AJAX: Get salesperson list for dropdown selection
     * Used in sales forms
     */
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
