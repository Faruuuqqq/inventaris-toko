<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\CustomerModel;
use CodeIgniter\Model;

class Customers extends BaseCRUDController
{
    protected string $viewPath = 'master/customers';
    protected string $routePath = '/master/customers';
    protected string $entityName = 'Customer';
    protected string $entityNamePlural = 'Customers';

    protected function getModel(): Model
    {
        return new CustomerModel();
    }

    protected function getStoreValidationRules(): array
    {
        return [
            'name' => 'required',
            'phone' => 'permit_empty',
            'address' => 'permit_empty',
            'credit_limit' => 'required|numeric',
        ];
    }

    protected function getDataFromRequest(): array
    {
        return [
            'code' => $this->request->getPost('code'),
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'credit_limit' => $this->request->getPost('credit_limit'),
        ];
    }

    protected function getIndexData(): array
    {
        return $this->model->asArray()->findAll();
    }
}
