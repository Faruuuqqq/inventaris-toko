<?php

namespace App\Controllers\Info;

use App\Controllers\BaseController;
use App\Models\SaleModel;
use App\Models\ProductModel;
use App\Models\CategoryModel;

class Analytics extends BaseController
{
    protected $saleModel;
    protected $productModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->saleModel = new SaleModel();
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Advanced Analytics Dashboard
     */
    public function dashboard()
    {
        $dateFrom = $this->request->getGet('date_from') ?: date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?: date('Y-m-d');

        $db = \Config\Database::connect();

        // Calculate key metrics
        $stats = $this->calculateStats($dateFrom, $dateTo, $db);
        
        // Revenue by category
        $revenueByCategory = $this->getRevenueByCategory($dateFrom, $dateTo, $db);
        
        // Payment methods breakdown
        $paymentMethods = $this->getPaymentMethodsBreakdown($dateFrom, $dateTo, $db);
        
        // Top products
        $topProducts = $this->getTopProducts($dateFrom, $dateTo, $db);

        $data = [
            'title' => 'Analytics Dashboard',
            'subtitle' => 'Analisis mendalam terhadap penjualan, pendapatan, dan performa bisnis',
            'stats' => $stats,
            'revenueByCategory' => $revenueByCategory,
            'paymentMethods' => $paymentMethods,
            'topProducts' => $topProducts,
        ];

        return view('info/analytics/dashboard', $data);
    }

    /**
     * Calculate key statistics
     */
    private function calculateStats($dateFrom, $dateTo, $db)
    {
        // Current period stats
        $currentSales = $db->table('sales')
            ->where('tanggal_penjualan >=', $dateFrom)
            ->where('tanggal_penjualan <=', $dateTo)
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        $totalRevenue = array_sum(array_column($currentSales, 'total_penjualan'));
        $totalProfit = array_sum(array_column($currentSales, 'total_profit'));
        $totalTransactions = count($currentSales);
        $avgOrderValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Previous period for comparison (same duration)
        $daysDiff = (strtotime($dateTo) - strtotime($dateFrom)) / 86400;
        $prevDateTo = date('Y-m-d', strtotime($dateFrom . ' -1 day'));
        $prevDateFrom = date('Y-m-d', strtotime($prevDateTo . ' -' . $daysDiff . ' days'));

        $prevSales = $db->table('sales')
            ->where('tanggal_penjualan >=', $prevDateFrom)
            ->where('tanggal_penjualan <=', $prevDateTo)
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        $prevRevenue = array_sum(array_column($prevSales, 'total_penjualan'));
        $prevProfit = array_sum(array_column($prevSales, 'total_profit'));
        $prevTransactions = count($prevSales);
        $prevAvgOrderValue = $prevTransactions > 0 ? $prevRevenue / $prevTransactions : 0;

        // Calculate growth percentages
        $revenueGrowth = $prevRevenue > 0 ? (($totalRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;
        $profitGrowth = $prevProfit > 0 ? (($totalProfit - $prevProfit) / $prevProfit) * 100 : 0;
        $transactionGrowth = $prevTransactions > 0 ? (($totalTransactions - $prevTransactions) / $prevTransactions) * 100 : 0;
        $aovGrowth = $prevAvgOrderValue > 0 ? (($avgOrderValue - $prevAvgOrderValue) / $prevAvgOrderValue) * 100 : 0;

        return [
            'totalRevenue' => $totalRevenue,
            'totalProfit' => $totalProfit,
            'totalTransactions' => $totalTransactions,
            'avgOrderValue' => $avgOrderValue,
            'revenueGrowth' => round($revenueGrowth, 1),
            'profitGrowth' => round($profitGrowth, 1),
            'transactionGrowth' => round($transactionGrowth, 1),
            'aovGrowth' => round($aovGrowth, 1),
        ];
    }

    /**
     * Get revenue breakdown by category
     */
    private function getRevenueByCategory($dateFrom, $dateTo, $db)
    {
        $result = $db->query("
            SELECT 
                c.name as category_name,
                SUM(si.subtotal) as revenue,
                COUNT(DISTINCT s.id_sale) as transaction_count
            FROM sale_items si
            JOIN products p ON p.id = si.id_produk
            LEFT JOIN categories c ON c.id = p.category_id
            JOIN sales s ON s.id_sale = si.id_sale
            WHERE s.tanggal_penjualan >= ?
                AND s.tanggal_penjualan <= ?
                AND s.deleted_at IS NULL
            GROUP BY c.id, c.name
            ORDER BY revenue DESC
        ", [$dateFrom, $dateTo])->getResultArray();

        $totalRevenue = array_sum(array_column($result, 'revenue'));

        $categories = [];
        foreach ($result as $row) {
            $categories[] = [
                'name' => $row['category_name'] ?: 'Uncategorized',
                'revenue' => (float)$row['revenue'],
                'percentage' => $totalRevenue > 0 ? round(($row['revenue'] / $totalRevenue) * 100, 1) : 0,
                'transaction_count' => (int)$row['transaction_count']
            ];
        }

        return $categories;
    }

    /**
     * Get payment methods breakdown
     */
    private function getPaymentMethodsBreakdown($dateFrom, $dateTo, $db)
    {
        $result = $db->query("
            SELECT 
                tipe_penjualan,
                COUNT(*) as count,
                SUM(total_penjualan) as amount
            FROM sales
            WHERE tanggal_penjualan >= ?
                AND tanggal_penjualan <= ?
                AND deleted_at IS NULL
            GROUP BY tipe_penjualan
        ", [$dateFrom, $dateTo])->getResultArray();

        $totalAmount = array_sum(array_column($result, 'amount'));

        $methods = [];
        $icons = [
            'CASH' => [
                'label' => 'Tunai',
                'bgClass' => 'bg-success/10',
                'iconClass' => 'text-success',
                'barClass' => 'bg-success',
                'iconPath' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
            ],
            'CREDIT' => [
                'label' => 'Kredit',
                'bgClass' => 'bg-warning/10',
                'iconClass' => 'text-warning',
                'barClass' => 'bg-warning',
                'iconPath' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'
            ],
            'TRANSFER' => [
                'label' => 'Transfer',
                'bgClass' => 'bg-primary/10',
                'iconClass' => 'text-primary',
                'barClass' => 'bg-primary',
                'iconPath' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'
            ]
        ];

        foreach ($result as $row) {
            $type = $row['tipe_penjualan'];
            $iconData = $icons[$type] ?? $icons['CASH'];

            $methods[] = array_merge($iconData, [
                'type' => $type,
                'count' => (int)$row['count'],
                'amount' => (float)$row['amount'],
                'percentage' => $totalAmount > 0 ? round(($row['amount'] / $totalAmount) * 100, 1) : 0
            ]);
        }

        return $methods;
    }

    /**
     * Get top selling products
     */
    private function getTopProducts($dateFrom, $dateTo, $db)
    {
        $result = $db->query("
            SELECT 
                p.id,
                p.name,
                p.sku,
                SUM(si.quantity) as qty_sold,
                SUM(si.subtotal) as revenue,
                SUM(si.quantity * (si.harga_satuan - COALESCE(p.cost_price, 0))) as profit
            FROM sale_items si
            JOIN products p ON p.id = si.id_produk
            JOIN sales s ON s.id_sale = si.id_sale
            WHERE s.tanggal_penjualan >= ?
                AND s.tanggal_penjualan <= ?
                AND s.deleted_at IS NULL
            GROUP BY p.id, p.name, p.sku
            ORDER BY revenue DESC
            LIMIT 10
        ", [$dateFrom, $dateTo])->getResultArray();

        $totalRevenue = array_sum(array_column($result, 'revenue'));

        $products = [];
        foreach ($result as $row) {
            $products[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'sku' => $row['sku'],
                'qty_sold' => (int)$row['qty_sold'],
                'revenue' => (float)$row['revenue'],
                'profit' => (float)$row['profit'],
                'share' => $totalRevenue > 0 ? round(($row['revenue'] / $totalRevenue) * 100, 1) : 0
            ];
        }

        return $products;
    }
}
