<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\SalespersonModel;
use App\Traits\ApiResponseTrait;
use CodeIgniter\Model;

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
        ];
    }

    protected function getIndexData(): array
    {
        return $this->model->asArray()->findAll();
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

    /**
     * Show salesperson detail page
     */
    public function detail($id)
    {
        $sales = $this->model->find($id);
        
        if (!$sales) {
            return redirect()->to($this->routePath)->with('error', 'Sales tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Sales',
            'subtitle' => $sales->name,
            'sales' => $sales,
        ];

        return view($this->viewPath . '/detail', $data);
    }
}
