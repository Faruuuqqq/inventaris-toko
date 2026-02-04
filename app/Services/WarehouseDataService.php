<?php

namespace App\Services;

use App\Models\WarehouseModel;

class WarehouseDataService
{
    protected WarehouseModel $warehouseModel;

    public function __construct()
    {
        $this->warehouseModel = new WarehouseModel();
    }

    /**
     * Get data for INDEX page (all warehouses)
     */
    public function getIndexData(): array
    {
        $warehouses = $this->warehouseModel->asArray()->findAll();

        return [
            'warehouses' => $warehouses,
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
    public function getDetailData(int $warehouseId): array
    {
        $gudang = $this->warehouseModel->find($warehouseId);

        if (!$gudang) {
            return [];
        }

        return [
            'gudang' => $gudang,
        ];
    }
}
