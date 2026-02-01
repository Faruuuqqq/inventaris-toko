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
        
        // Revenue trend data for charts
        $revenueTrend = $this->getRevenueTrend($dateFrom, $dateTo, $db);

        $data = [
            'title' => 'Analytics Dashboard',
            'subtitle' => 'Analisis mendalam terhadap penjualan, pendapatan, dan performa bisnis',
            'stats' => $stats,
            'revenueByCategory' => $revenueByCategory,
            'paymentMethods' => $paymentMethods,
            'topProducts' => $topProducts,
            'revenueTrend' => $revenueTrend,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
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
            ->where('created_at >=', $dateFrom)
            ->where('created_at <=', $dateTo . ' 23:59:59')
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        $totalRevenue = array_sum(array_column($currentSales, 'total_amount'));
        $totalProfit = array_sum(array_column($currentSales, 'total_profit'));
        $totalTransactions = count($currentSales);
        $avgOrderValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Previous period for comparison (same duration)
        $daysDiff = (strtotime($dateTo) - strtotime($dateFrom)) / 86400;
        $prevDateTo = date('Y-m-d', strtotime($dateFrom . ' -1 day'));
        $prevDateFrom = date('Y-m-d', strtotime($prevDateTo . ' -' . $daysDiff . ' days'));

        $prevSales = $db->table('sales')
            ->where('created_at >=', $prevDateFrom)
            ->where('created_at <=', $prevDateTo . ' 23:59:59')
            ->where('deleted_at', null)
            ->get()
            ->getResultArray();

        $prevRevenue = array_sum(array_column($prevSales, 'total_amount'));
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
                COUNT(DISTINCT s.id) as transaction_count
            FROM sale_items si
            JOIN products p ON p.id = si.product_id
            LEFT JOIN categories c ON c.id = p.category_id
            JOIN sales s ON s.id = si.sale_id
            WHERE s.created_at >= ?
                AND s.created_at <= ?
                AND s.deleted_at IS NULL
            GROUP BY c.id, c.name
            ORDER BY revenue DESC
        ", [$dateFrom, $dateTo . ' 23:59:59'])->getResultArray();

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
                payment_type,
                COUNT(*) as count,
                SUM(total_amount) as amount
            FROM sales
            WHERE created_at >= ?
                AND created_at <= ?
                AND deleted_at IS NULL
            GROUP BY payment_type
        ", [$dateFrom, $dateTo . ' 23:59:59'])->getResultArray();

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
            $type = $row['payment_type'];
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
                SUM(si.quantity * (si.price - COALESCE(p.cost_price, 0))) as profit
            FROM sale_items si
            JOIN products p ON p.id = si.product_id
            JOIN sales s ON s.id = si.sale_id
             WHERE s.created_at >= ?
                AND s.created_at <= ?
                AND s.deleted_at IS NULL
            GROUP BY p.id, p.name, p.sku
            ORDER BY revenue DESC
            LIMIT 10
        ", [$dateFrom, $dateTo . ' 23:59:59'])->getResultArray();

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

    /**
     * Get revenue trend data for charts
     */
    private function getRevenueTrend($dateFrom, $dateTo, $db)
    {
        $daysDiff = (strtotime($dateTo) - strtotime($dateFrom)) / 86400;
        
        // Determine grouping (daily, weekly, monthly)
        if ($daysDiff <= 31) {
            // Daily grouping
            $groupBy = 'DATE(s.created_at)';
            $dateFormat = '%Y-%m-%d';
        } elseif ($daysDiff <= 90) {
            // Weekly grouping
            $groupBy = 'YEARWEEK(s.created_at)';
            $dateFormat = '%Y-W%v';
        } else {
            // Monthly grouping
            $groupBy = 'DATE_FORMAT(s.created_at, "%Y-%m")';
            $dateFormat = '%Y-%m';
        }
        
        $result = $db->query("
            SELECT 
                DATE_FORMAT(s.created_at, ?) as period_label,
                {$groupBy} as period,
                SUM(s.total_amount) as revenue,
                SUM(s.total_profit) as profit,
                COUNT(*) as transactions
            FROM sales s
            WHERE s.created_at >= ?
                AND s.created_at <= ?
                AND s.deleted_at IS NULL
            GROUP BY period
            ORDER BY period ASC
        ", [$dateFormat, $dateFrom, $dateTo . ' 23:59:59'])->getResultArray();
        
        return $result;
    }

    /**
     * Export Analytics Dashboard to CSV
     */
    public function exportDashboard()
    {
        $dateFrom = $this->request->getGet('date_from') ?: date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?: date('Y-m-d');

        $db = \Config\Database::connect();

        // Get all analytics data
        $stats = $this->calculateStats($dateFrom, $dateTo, $db);
        $revenueByCategory = $this->getRevenueByCategory($dateFrom, $dateTo, $db);
        $paymentMethods = $this->getPaymentMethodsBreakdown($dateFrom, $dateTo, $db);
        $topProducts = $this->getTopProducts($dateFrom, $dateTo, $db);
        
        // Set response headers for CSV download
        $filename = 'analytics_export_' . date('Y-m-d_His') . '.csv';
        $this->response->setHeader('Content-Type', 'text/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->setHeader('Expires', '0');
        
        // Open output stream
        $output = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Section 1: Summary Statistics
        fputcsv($output, ['ANALYTICS DASHBOARD EXPORT']);
        fputcsv($output, ['Period: ' . $dateFrom . ' to ' . $dateTo]);
        fputcsv($output, ['']);
        
        fputcsv($output, ['KEY METRICS']);
        fputcsv($output, ['Metric', 'Value', 'Growth (%)']);
        fputcsv($output, [
            'Total Revenue', 
            'Rp ' . number_format($stats['totalRevenue'], 0, ',', '.'),
            $stats['revenueGrowth'] . '%'
        ]);
        fputcsv($output, [
            'Total Profit', 
            'Rp ' . number_format($stats['totalProfit'], 0, ',', '.'),
            $stats['profitGrowth'] . '%'
        ]);
        fputcsv($output, [
            'Total Transactions', 
            number_format($stats['totalTransactions'], 0, ',', '.'),
            $stats['transactionGrowth'] . '%'
        ]);
        fputcsv($output, [
            'Avg Order Value', 
            'Rp ' . number_format($stats['avgOrderValue'], 0, ',', '.'),
            $stats['aovGrowth'] . '%'
        ]);
        fputcsv($output, ['']);
        
        // Section 2: Revenue by Category
        if (!empty($revenueByCategory)) {
            fputcsv($output, ['REVENUE BY CATEGORY']);
            fputcsv($output, ['Category', 'Revenue (Rp)', 'Percentage (%)', 'Transactions']);
            foreach ($revenueByCategory as $cat) {
                fputcsv($output, [
                    $cat['name'],
                    number_format($cat['revenue'], 0, ',', '.'),
                    $cat['percentage'] . '%',
                    $cat['transaction_count']
                ]);
            }
            fputcsv($output, ['']);
        }
        
        // Section 3: Payment Methods
        if (!empty($paymentMethods)) {
            fputcsv($output, ['PAYMENT METHODS']);
            fputcsv($output, ['Method', 'Count', 'Amount (Rp)', 'Percentage (%)']);
            foreach ($paymentMethods as $method) {
                fputcsv($output, [
                    $method['label'],
                    $method['count'],
                    number_format($method['amount'], 0, ',', '.'),
                    $method['percentage'] . '%'
                ]);
            }
            fputcsv($output, ['']);
        }
        
        // Section 4: Top 10 Products
        if (!empty($topProducts)) {
            fputcsv($output, ['TOP 10 PRODUCTS']);
            fputcsv($output, ['Product Name', 'SKU', 'Qty Sold', 'Revenue (Rp)', 'Profit (Rp)', 'Share (%)']);
            foreach ($topProducts as $product) {
                fputcsv($output, [
                    $product['name'],
                    $product['sku'],
                    $product['qty_sold'],
                    number_format($product['revenue'], 0, ',', '.'),
                    number_format($product['profit'], 0, ',', '.'),
                    $product['share'] . '%'
                ]);
            }
        }
        
        fclose($output);
        return $this->response;
    }
}
