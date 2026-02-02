<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\WarehouseModel;
use App\Traits\ApiResponseTrait;
use CodeIgniter\Model;

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
