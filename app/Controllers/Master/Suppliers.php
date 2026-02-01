<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\SupplierModel;
use CodeIgniter\Model;

class Suppliers extends BaseCRUDController
{
    protected string $viewPath = 'master/suppliers';
    protected string $routePath = '/master/suppliers';
    protected string $entityName = 'Supplier';
    protected string $entityNamePlural = 'Suppliers';

    protected function getModel(): Model
    {
        return new SupplierModel();
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
            'code' => $this->request->getPost('code'),
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
        ];
    }
}
