<?php

namespace App\Services;

use App\Models\UserModel;
use App\Helpers\PaginationHelper;

class UserDataService
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Get data for INDEX page (all users)
     */
    public function getIndexData(): array
    {
        $users = $this->userModel->asArray()->orderBy('created_at', 'DESC')->findAll();

        return [
            'users' => $users,
            'subtitle' => 'Kelola pengguna sistem (Owner only)',
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
    public function getDetailData(int $userId): array
    {
        $pengguna = $this->userModel->find($userId);

        if (!$pengguna) {
            return [];
        }

        return [
            'pengguna' => $pengguna,
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

        // Get paginated results (ordered by created_at DESC)
        $users = $this->userModel
            ->asArray()
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage, 'default', $page);
        $pager = $this->userModel->pager;

        return [
            'users' => $users,
            'subtitle' => 'Kelola pengguna sistem (Owner only)',
            'pagination' => PaginationHelper::getPaginationLinks($pager, $perPage),
        ];
    }
}
