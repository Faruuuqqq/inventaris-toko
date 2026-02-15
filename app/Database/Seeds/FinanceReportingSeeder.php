<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FinanceReportingSeeder extends Seeder
{
    public function run()
    {
        echo "\n💰 Creating Financial Reporting Data...\n";
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Get data for contra bons
            $customers = $db->table('customers')->select('id, name, credit_limit')->get()->getResultArray();
            $unpaidSales = $db->table('sales')
                ->select('id, invoice_number, customer_id, total_amount, created_at, due_date')
                ->where('payment_type', 'CREDIT')
                ->where('payment_status', 'UNPAID')
                ->get()
                ->getResultArray();
            
            // Group unpaid sales by customer
            $customerSales = [];
            foreach ($unpaidSales as $sale) {
                if (!isset($customerSales[$sale['customer_id']])) {
                    $customerSales[$sale['customer_id']] = [];
                }
                $customerSales[$sale['customer_id']][] = $sale;
            }
            
            echo "   Creating Contra Bons for customers with unpaid invoices...\n";
            
            // Create Contra Bons (Credit Notes)
            $cbCount = 0;
            $invoiceCounter = 1000;
            
            foreach ($customerSales as $customerId => $sales) {
                // Only create contra bon if customer has 3+ unpaid sales
                if (count($sales) < 3) {
                    continue;
                }
                
                $cbCount++;
                $invoiceCounter++;
                
                $customer = null;
                foreach ($customers as $c) {
                    if ($c['id'] == $customerId) {
                        $customer = $c;
                        break;
                    }
                }
                
                // Calculate total amount
                $totalAmount = 0;
                foreach ($sales as $sale) {
                    $totalAmount += $sale['total_amount'];
                }
                
                // Determine due date (30 days from today)
                $dueDate = date('Y-m-d', strtotime('+30 days'));
                
                // Insert contra bon
                $cbData = [
                    'document_number' => 'CB-' . date('Y-m') . '-' . str_pad($cbCount, 4, '0', STR_PAD_LEFT),
                    'customer_id' => $customerId,
                    'created_at' => date('Y-m-d'),
                    'due_date' => $dueDate,
                    'total_amount' => $totalAmount,
                    'status' => 'UNPAID',
                    'notes' => 'Konsolidasi tagihan bulan ' . date('F Y') . ' untuk ' . $customer['name']
                ];
                
                $db->table('contra_bons')->insert($cbData);
                $cbId = $db->insertID();
                
                // Link sales to contra bon
                foreach ($sales as $sale) {
                    $db->table('sales')->where('id', $sale['id'])->update(['contra_bon_id' => $cbId]);
                }
                
                $statusEmoji = '⏳';
                echo sprintf(
                    "   %s [%d] %s | %s | Invoices: %d | Total: Rp %s\n",
                    $statusEmoji,
                    $cbCount,
                    $cbData['document_number'],
                    $customer['name'],
                    count($sales),
                    number_format($totalAmount, 0, ',', '.')
                );
            }
            
            // Create financial summary views for reporting
            echo "\n   Creating Financial Summary Views...\n";
            
            // Drop views if exist
            $db->query("DROP VIEW IF EXISTS v_monthly_sales_summary");
            $db->query("DROP VIEW IF EXISTS v_monthly_purchases_summary");
            $db->query("DROP VIEW IF EXISTS v_monthly_expenses_summary");
            $db->query("DROP VIEW IF EXISTS v_cash_flow_summary");
            $db->query("DROP VIEW IF EXISTS v_customer_aging");
            $db->query("DROP VIEW IF EXISTS v_supplier_aging");
            $db->query("DROP VIEW IF EXISTS v_financial_summary");
            
            // Create views
            $this->createMonthlySalesSummary($db);
            echo "   ✅ Created v_monthly_sales_summary\n";
            
            $this->createMonthlyPurchasesSummary($db);
            echo "   ✅ Created v_monthly_purchases_summary\n";
            
            $this->createMonthlyExpensesSummary($db);
            echo "   ✅ Created v_monthly_expenses_summary\n";
            
            $this->createCashFlowSummary($db);
            echo "   ✅ Created v_cash_flow_summary\n";
            
            $this->createCustomerAging($db);
            echo "   ✅ Created v_customer_aging\n";
            
            $this->createSupplierAging($db);
            echo "   ✅ Created v_supplier_aging\n";
            
            $this->createFinancialSummary($db);
            echo "   ✅ Created v_financial_summary\n";
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }
            
            echo "\n✅ Financial Reporting Data Seeding Complete!\n";
            echo "   📋 Contra Bons: {$cbCount}\n";
            echo "   📊 Financial Views: 7 views created\n\n";
            
            // Show summary
            $totalCBs = $db->table('contra_bons')->countAll();
            $totalCBAmount = $db->query("SELECT SUM(total_amount) as total FROM contra_bons")->getRow()->total;
            
            echo "📈 Contra Bon Summary:\n";
            echo "   📋 Total Contra Bons: {$totalCBs}\n";
            echo "   💰 Total Value: Rp " . number_format($totalCBAmount, 0, ',', '.') . "\n";
            
            echo "\n🎯 Available Views for Reporting:\n";
            echo "   1. v_monthly_sales_summary - Monthly sales performance\n";
            echo "   2. v_monthly_purchases_summary - Monthly purchase tracking\n";
            echo "   3. v_monthly_expenses_summary - Monthly expense breakdown\n";
            echo "   4. v_cash_flow_summary - Cash flow analysis\n";
            echo "   5. v_customer_aging - Customer receivable aging\n";
            echo "   6. v_supplier_aging - Supplier payable aging\n";
            echo "   7. v_financial_summary - Complete financial overview\n";
            
        } catch (\Exception $e) {
            $db->transRollback();
            echo "❌ Error: " . $e->getMessage() . "\n\n";
            throw $e;
        }
    }
    
    private function createMonthlySalesSummary($db)
    {
        $sql = "CREATE VIEW v_monthly_sales_summary AS
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') AS month,
                    COUNT(*) AS total_transactions,
                    SUM(CASE WHEN payment_type = 'CASH' THEN 1 ELSE 0 END) AS cash_transactions,
                    SUM(CASE WHEN payment_type = 'CREDIT' THEN 1 ELSE 0 END) AS credit_transactions,
                    SUM(total_amount) AS total_sales,
                    SUM(paid_amount) AS total_paid,
                    SUM(total_amount - paid_amount) AS total_outstanding,
                    SUM(CASE WHEN payment_status = 'PAID' THEN 1 ELSE 0 END) AS paid_invoices,
                    SUM(CASE WHEN payment_status = 'UNPAID' THEN 1 ELSE 0 END) AS unpaid_invoices,
                    SUM(CASE WHEN payment_status = 'PARTIAL' THEN 1 ELSE 0 END) AS partial_invoices
                FROM sales
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month DESC";
        
        $db->query($sql);
    }
    
    private function createMonthlyPurchasesSummary($db)
    {
        $sql = "CREATE VIEW v_monthly_purchases_summary AS
                SELECT 
                    DATE_FORMAT(tanggal_po, '%Y-%m') AS month,
                    COUNT(*) AS total_po,
                    SUM(CASE WHEN status = 'Dipesan' THEN 1 ELSE 0 END) AS pending_po,
                    SUM(CASE WHEN status = 'Sebagian' THEN 1 ELSE 0 END) AS partial_po,
                    SUM(CASE WHEN status = 'Diterima Semua' THEN 1 ELSE 0 END) AS received_po,
                    SUM(total_amount) AS total_purchases,
                    SUM(received_amount) AS total_received,
                    SUM(paid_amount) AS total_paid,
                    SUM(total_amount - paid_amount) AS total_outstanding
                FROM purchase_orders
                GROUP BY DATE_FORMAT(tanggal_po, '%Y-%m')
                ORDER BY month DESC";
        
        $db->query($sql);
    }
    
    private function createMonthlyExpensesSummary($db)
    {
        $sql = "CREATE VIEW v_monthly_expenses_summary AS
                SELECT 
                    DATE_FORMAT(expense_date, '%Y-%m') AS month,
                    COUNT(*) AS total_expenses,
                    category,
                    SUM(amount) AS total_amount
                FROM expenses
                GROUP BY DATE_FORMAT(expense_date, '%Y-%m'), category
                ORDER BY month DESC, total_amount DESC";
        
        $db->query($sql);
    }
    
    private function createCashFlowSummary($db)
    {
        $sql = "CREATE VIEW v_cash_flow_summary AS
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') AS month,
                    'CASH IN' AS cash_type,
                    SUM(amount) AS total_amount
                FROM payments
                WHERE type = 'RECEIVABLE' AND method = 'CASH'
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                
                UNION ALL
                
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') AS month,
                    'CASH OUT' AS cash_type,
                    SUM(amount) AS total_amount
                FROM payments
                WHERE type = 'PAYABLE' AND method = 'CASH'
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                
                UNION ALL
                
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') AS month,
                    'EXPENSES' AS cash_type,
                    SUM(amount) AS total_amount
                FROM expenses
                WHERE payment_method = 'CASH'
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                
                ORDER BY month DESC, cash_type";
        
        $db->query($sql);
    }
    
    private function createCustomerAging($db)
    {
        $sql = "CREATE VIEW v_customer_aging AS
                SELECT 
                    c.id,
                    c.code,
                    c.name,
                    c.phone,
                    c.credit_limit,
                    c.receivable_balance,
                    SUM(CASE 
                        WHEN s.payment_status IN ('UNPAID', 'PARTIAL') AND s.due_date < CURDATE() 
                        THEN s.total_amount - s.paid_amount 
                        ELSE 0 
                    END) AS overdue_30,
                    SUM(CASE 
                        WHEN s.payment_status IN ('UNPAID', 'PARTIAL') AND s.due_date >= CURDATE() AND s.due_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) 
                        THEN s.total_amount - s.paid_amount 
                        ELSE 0 
                    END) AS overdue_60,
                    SUM(CASE 
                        WHEN s.payment_status IN ('UNPAID', 'PARTIAL') AND s.due_date > DATE_ADD(CURDATE(), INTERVAL 30 DAY) 
                        THEN s.total_amount - s.paid_amount 
                        ELSE 0 
                    END) AS overdue_90,
                    SUM(CASE 
                        WHEN s.payment_status IN ('UNPAID', 'PARTIAL') 
                        THEN s.total_amount - s.paid_amount 
                        ELSE 0 
                    END) AS total_outstanding
                FROM customers c
                LEFT JOIN sales s ON c.id = s.customer_id AND s.payment_status IN ('UNPAID', 'PARTIAL')
                GROUP BY c.id, c.code, c.name, c.phone, c.credit_limit, c.receivable_balance
                HAVING total_outstanding > 0
                ORDER BY total_outstanding DESC";
        
        $db->query($sql);
    }
    
    private function createSupplierAging($db)
    {
        $sql = "CREATE VIEW v_supplier_aging AS
                SELECT 
                    sup.id,
                    sup.code,
                    sup.name,
                    sup.phone,
                    sup.debt_balance,
                    SUM(CASE 
                        WHEN po.payment_status IN ('UNPAID', 'PARTIAL') AND po.tanggal_po < DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
                        THEN po.total_amount - po.paid_amount 
                        ELSE 0 
                    END) AS overdue_30,
                    SUM(CASE 
                        WHEN po.payment_status IN ('UNPAID', 'PARTIAL') AND po.tanggal_po >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND po.tanggal_po <= CURDATE() 
                        THEN po.total_amount - po.paid_amount 
                        ELSE 0 
                    END) AS overdue_60,
                    SUM(CASE 
                        WHEN po.payment_status IN ('UNPAID', 'PARTIAL') AND po.tanggal_po > CURDATE() 
                        THEN po.total_amount - po.paid_amount 
                        ELSE 0 
                    END) AS overdue_90,
                    SUM(CASE 
                        WHEN po.payment_status IN ('UNPAID', 'PARTIAL') 
                        THEN po.total_amount - po.paid_amount 
                        ELSE 0 
                    END) AS total_outstanding
                FROM suppliers sup
                LEFT JOIN purchase_orders po ON sup.id = po.supplier_id AND po.payment_status IN ('UNPAID', 'PARTIAL')
                GROUP BY sup.id, sup.code, sup.name, sup.phone, sup.debt_balance
                HAVING total_outstanding > 0
                ORDER BY total_outstanding DESC";
        
        $db->query($sql);
    }
    
    private function createFinancialSummary($db)
    {
        $sql = "CREATE VIEW v_financial_summary AS
                SELECT 
                    'Total Revenue' AS metric,
                    COALESCE(SUM(total_amount), 0) AS value
                FROM sales
                
                UNION ALL
                
                SELECT 
                    'Total Expenses',
                    COALESCE(SUM(amount), 0)
                FROM expenses
                
                UNION ALL
                
                SELECT 
                    'Cash Inflow',
                    COALESCE(SUM(CASE WHEN type = 'RECEIVABLE' THEN amount ELSE 0 END), 0)
                FROM payments
                
                UNION ALL
                
                SELECT 
                    'Cash Outflow',
                    COALESCE(SUM(CASE WHEN type = 'PAYABLE' THEN amount ELSE 0 END), 0)
                FROM payments
                
                UNION ALL
                
                SELECT 
                    'Outstanding Receivables',
                    COALESCE(SUM(receivable_balance), 0)
                FROM customers
                
                UNION ALL
                
                SELECT 
                    'Outstanding Payables',
                    COALESCE(SUM(debt_balance), 0)
                FROM suppliers";
        
        $db->query($sql);
    }
}
