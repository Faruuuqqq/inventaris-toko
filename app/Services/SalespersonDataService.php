<?php

namespace App\Services;

use App\Models\SalespersonModel;
use App\Helpers\PaginationHelper;

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
        $salespersons = $this->salespersonModel->asArray()->paginate($perPage, 'default', $page);
        $pager = $this->salespersonModel->pager;

        return [
            'salespersons' => $salespersons,
            'pagination' => PaginationHelper::getPaginationLinks($pager, $perPage),
        ];
    }
}
