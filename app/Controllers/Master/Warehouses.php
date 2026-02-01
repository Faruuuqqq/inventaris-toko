<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\WarehouseModel;
use CodeIgniter\Model;

class Warehouses extends BaseCRUDController
{
    protected string $viewPath = 'master/warehouses';
    protected string $routePath = '/master/warehouses';
    protected string $entityName = 'Gudang';
    protected string $entityNamePlural = 'Warehouses';

    protected function getModel(): Model
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
}
