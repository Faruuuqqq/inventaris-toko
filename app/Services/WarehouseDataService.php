<?php

namespace App\Services;

use App\Models\WarehouseModel;
use App\Helpers\PaginationHelper;

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

    /**
     * Get paginated data for INDEX page
     */
    public function getPaginatedData(?int $page = null, ?int $perPage = null): array
    {
        // Get safe pagination params
        $params = PaginationHelper::getSafeParams($page, $perPage);
        $page = $params['page'];
        $perPage = $params['perPage'];

        // Get paginated results
        $warehouses = $this->warehouseModel->asArray()->paginate($perPage, 'default', $page);
        $pager = $this->warehouseModel->pager;

        return [
            'warehouses' => $warehouses,
            'pagination' => PaginationHelper::getPaginationLinks($pager, $perPage),
        ];
    }
}
