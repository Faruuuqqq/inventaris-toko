<?php

namespace App\Services;

use App\Models\SalespersonModel;

class SalespersonDataService
{
    protected SalespersonModel $salespersonModel;

    public function __construct()
    {
        $this->salespersonModel = new SalespersonModel();
    }

    /**
     * Get data for INDEX page (all salespersons)
     */
    public function getIndexData(): array
    {
        $salespersons = $this->salespersonModel->asArray()->findAll();

        return [
            'salespersons' => $salespersons,
        ];
    }

    /**
     * Get data for CREATE page
     */
    public function getCreateData(): array
    {
        return [];
    }

    /**
     * Get data for EDIT page
     */
    public function getEditData(): array
    {
        return [];
    }

    /**
     * Get data for DETAIL page
     */
    public function getDetailData(int $salespersonId): array
    {
        $sales = $this->salespersonModel->find($salespersonId);

        if (!$sales) {
            return [];
        }

        return [
            'sales' => $sales,
        ];
    }
}
