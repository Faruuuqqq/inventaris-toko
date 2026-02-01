<?php

namespace App\Controllers\Master;

use App\Controllers\BaseCRUDController;
use App\Models\SalespersonModel;
use CodeIgniter\Model;

class Salespersons extends BaseCRUDController
{
    protected string $viewPath = 'master/salespersons';
    protected string $routePath = '/master/salespersons';
    protected string $entityName = 'Sales';
    protected string $entityNamePlural = 'Salespersons';

    protected function getModel(): Model
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
}
