<?php

namespace App\Services;

use App\Models\CustomerModel;
use App\Helpers\PaginationHelper;
use CodeIgniter\Database\BaseConnection;

class CustomerDataService
{
    protected CustomerModel $customerModel;
    protected BaseConnection $db;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Get data for INDEX page (all customers)
     */
    public function getIndexData(): array
    {
        $customers = $this->customerModel->asArray()->findAll();

        return [
            'customers' => $customers,
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
     * Get data for DETAIL page with stats and relationships
     */
    public function getDetailData(int $customerId): array
    {
        $customer = $this->customerModel->find($customerId);

        if (!$customer) {
            return [];
        }

        // Get recent sales transactions (last 10)
        $recentSales = $this->db->table('sales')
            ->select('id_sale, nomor_faktur, tanggal_penjualan, total_penjualan, status_pembayaran')
            ->where('id_customer', $customerId)
            ->orderBy('tanggal_penjualan', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Get credit usage
        $totalCredit = $this->db->table('sales')
            ->selectSum('sisa_pembayaran')
            ->where('id_customer', $customerId)
            ->where('status_pembayaran !=', 'PAID')
            ->get()
            ->getRow();

        $creditUsed = $totalCredit->sisa_pembayaran ?? 0;
        $creditLimit = $customer->credit_limit ?? 0;
        $creditAvailable = $creditLimit - $creditUsed;
        $creditPercentage = $creditLimit > 0 ? ($creditUsed / $creditLimit) * 100 : 0;

        // Get statistics
        $stats = $this->db->table('sales')
            ->select('COUNT(*) as total_transactions, SUM(total_penjualan) as total_sales, AVG(total_penjualan) as avg_sale')
            ->where('id_customer', $customerId)
            ->get()
            ->getRow();

        return [
            'customer' => $customer,
            'recentSales' => $recentSales,
            'creditUsed' => (int)$creditUsed,
            'creditLimit' => (int)$creditLimit,
            'creditAvailable' => (int)$creditAvailable,
            'creditPercentage' => min($creditPercentage, 100),
            'stats' => $stats,
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
        $customers = $this->customerModel->asArray()->paginate($perPage, 'default', $page);
        $pager = $this->customerModel->pager;

        return [
            'customers' => $customers,
            'pagination' => PaginationHelper::getPaginationLinks($pager, $perPage),
        ];
    }

    /**
     * Get data for PDF EXPORT
     * Returns array of customers with all necessary fields for export
     * Supports optional filters
     *
     * @param array $filters Optional filters (status, etc.)
     * @return array Array of customers formatted for export
     */
    public function getExportData(array $filters = []): array
    {
        $query = $this->customerModel->asArray();

        // Apply filters if provided
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Return all matching customers (no pagination)
        return $query->findAll();
    }
}
