<?php
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class ReportsController extends ResourceController
{
    protected $format = 'json';

    public function profitLoss()
    {
        $db = \Config\Database::connect();

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        // Get total sales
        $sales = $db->table('sales')
            ->selectSum('total_amount', 'total')
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->get()->getRowArray();

        // Get total purchases
        $purchases = $db->table('purchase_orders')
            ->selectSum('total_amount', 'total')
            ->where('tanggal_po >=', $startDate)
            ->where('tanggal_po <=', $endDate)
            ->get()->getRowArray();

        return $this->respond([
            'status' => 'success',
            'data' => [
                'period' => ['start' => $startDate, 'end' => $endDate],
                'sales' => (float) ($sales['total'] ?? 0),
                'purchases' => (float) ($purchases['total'] ?? 0),
                'gross_profit' => (float) (($sales['total'] ?? 0) - ($purchases['total'] ?? 0))
            ]
        ]);
    }

    public function cashFlow()
    {
        $db = \Config\Database::connect();

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        // Cash inflow from payments received
        $inflow = $db->table('payments')
            ->selectSum('amount', 'total')
            ->where('type', 'RECEIVABLE')
            ->where('payment_date >=', $startDate)
            ->where('payment_date <=', $endDate)
            ->get()->getRowArray();

        // Cash outflow from payments made
        $outflow = $db->table('payments')
            ->selectSum('amount', 'total')
            ->where('type', 'PAYABLE')
            ->where('payment_date >=', $startDate)
            ->where('payment_date <=', $endDate)
            ->get()->getRowArray();

        return $this->respond([
            'status' => 'success',
            'data' => [
                'period' => ['start' => $startDate, 'end' => $endDate],
                'inflow' => (float) ($inflow['total'] ?? 0),
                'outflow' => (float) ($outflow['total'] ?? 0),
                'net_cash_flow' => (float) (($inflow['total'] ?? 0) - ($outflow['total'] ?? 0))
            ]
        ]);
    }

    public function monthlySummary()
    {
        $db = \Config\Database::connect();

        $year = $this->request->getGet('year') ?? date('Y');

        $summary = [];
        for ($month = 1; $month <= 12; $month++) {
            $startDate = sprintf('%s-%02d-01', $year, $month);
            $endDate = date('Y-m-t', strtotime($startDate));

            $sales = $db->table('sales')
                ->selectSum('total_amount', 'total')
                ->where('created_at >=', $startDate)
                ->where('created_at <=', $endDate . ' 23:59:59')
                ->get()->getRowArray();

            $summary[] = [
                'month' => $month,
                'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
                'sales' => (float) ($sales['total'] ?? 0)
            ];
        }

        return $this->respond([
            'status' => 'success',
            'data' => [
                'year' => $year,
                'summary' => $summary
            ]
        ]);
    }

    public function productPerformance()
    {
        $db = \Config\Database::connect();

        $limit = $this->request->getGet('limit') ?? 10;

        $products = $db->table('sale_items si')
            ->select('p.id, p.name, p.sku, SUM(si.quantity) as total_qty, SUM(si.subtotal) as total_revenue')
            ->join('products p', 'p.id = si.product_id')
            ->groupBy('p.id')
            ->orderBy('total_revenue', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();

        return $this->respond([
            'status' => 'success',
            'data' => $products
        ]);
    }

    public function customerAnalysis()
    {
        $db = \Config\Database::connect();

        $limit = $this->request->getGet('limit') ?? 10;

        $customers = $db->table('sales s')
            ->select('c.id, c.name, c.code, COUNT(s.id) as total_transactions, SUM(s.total_amount) as total_spent')
            ->join('customers c', 'c.id = s.customer_id')
            ->groupBy('c.id')
            ->orderBy('total_spent', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();

        return $this->respond([
            'status' => 'success',
            'data' => $customers
        ]);
    }
}
