<?php

namespace Tests\Feature;

use Tests\Support\DatabaseTestCase;
use App\Models\UserModel;
use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\SaleModel;

class FinancialIntegrationTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\App\Database\Seeds\UserSeeder::class);
        $this->seed(\App\Database\Seeds\CustomerSeeder::class);
        $this->seed(\App\Database\Seeds\ProductSeeder::class);
    }

    /** @test */
    public function it_creates_correct_journal_entries_for_cash_sale()
    {
        // Arrange
        $product = $this->productModel->first();
        $customer = $this->customerModel->first();
        $saleAmount = 500000;

        // Act: Create cash sale
        $saleData = [
            'customer_id' => $customer->id,
            'payment_type' => 'cash',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'price' => 100000,
                    'subtotal' => 500000
                ]
            ],
            'total_amount' => $saleAmount,
            'paid_amount' => $saleAmount
        ];

        $response = $this->post('transactions/sales/store', $saleData);

        // Assert: Sale created
        $response->assertStatus(201);
        $saleId = $this->getLastInsertId('sales');

        // Assert: Journal entries created
        $this->assertDatabaseHas('journal_entries', [
            'reference_id' => $saleId,
            'reference_type' => 'sale',
            'account_code' => '411', // Penjualan
            'debit' => 0,
            'credit' => $saleAmount
        ]);

        $this->assertDatabaseHas('journal_entries', [
            'reference_id' => $saleId,
            'reference_type' => 'sale',
            'account_code' => '111', // Kas
            'debit' => $saleAmount,
            'credit' => 0
        ]);

        // Assert: COGS entry
        $this->assertDatabaseHas('journal_entries', [
            'reference_id' => $saleId,
            'reference_type' => 'sale',
            'account_code' => '511', // HPP
            'debit' => 350000, // 5 * 70000 (assuming buy price)
            'credit' => 350000
        ]);
    }

    /** @test */
    public function it_handles_credit_sale_with_payment_terms()
    {
        // Arrange
        $product = $this->productModel->first();
        $customer = $this->customerModel->first();
        $saleAmount = 1000000;

        // Act: Create credit sale
        $saleData = [
            'customer_id' => $customer->id,
            'payment_type' => 'credit',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                    'price' => 100000,
                    'subtotal' => 1000000
                ]
            ],
            'total_amount' => $saleAmount,
            'paid_amount' => 0,
            'credit_terms' => 30 // 30 days
        ];

        $response = $this->post('transactions/sales/store', $saleData);

        // Assert
        $response->assertStatus(201);
        $saleId = $this->getLastInsertId('sales');

        // Assert: Accounts receivable entry
        $this->assertDatabaseHas('journal_entries', [
            'reference_id' => $saleId,
            'reference_type' => 'sale',
            'account_code' => '131', // Piutang Usaha
            'debit' => $saleAmount,
            'credit' => 0
        ]);

        // Assert: Customer receivable updated
        $this->assertDatabaseHas('customer_receivables', [
            'customer_id' => $customer->id,
            'sale_id' => $saleId,
            'amount' => $saleAmount,
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'status' => 'unpaid'
        ]);
    }

    /** @test */
    public function it_processes_customer_payment_correctly()
    {
        // Arrange: Create credit sale first
        $saleId = $this->createCreditSale(2000000);
        $customer = $this->customerModel->first();
        $paymentAmount = 500000;

        // Act: Process payment
        $paymentData = [
            'customer_id' => $customer->id,
            'sale_id' => $saleId,
            'payment_amount' => $paymentAmount,
            'payment_method' => 'bank_transfer',
            'payment_date' => date('Y-m-d'),
            'notes' => 'Partial payment'
        ];

        $response = $this->post('finance/payments/receivable', $paymentData);

        // Assert
        $response->assertStatus(201);

        // Assert: Payment recorded
        $this->assertDatabaseHas('customer_payments', [
            'customer_id' => $customer->id,
            'sale_id' => $saleId,
            'amount' => $paymentAmount,
            'payment_method' => 'bank_transfer'
        ]);

        // Assert: Receivable updated
        $this->assertDatabaseHas('customer_receivables', [
            'customer_id' => $customer->id,
            'sale_id' => $saleId,
            'amount_paid' => $paymentAmount,
            'status' => 'partial'
        ]);

        // Assert: Journal entry for payment
        $this->assertDatabaseHas('journal_entries', [
            'reference_type' => 'payment',
            'account_code' => '111', // Kas
            'debit' => $paymentAmount,
            'credit' => 0
        ]);

        $this->assertDatabaseHas('journal_entries', [
            'reference_type' => 'payment',
            'account_code' => '131', // Piutang Usaha
            'debit' => 0,
            'credit' => $paymentAmount
        ]);
    }

    /** @test */
    public function it_generates_accrual_financial_statements()
    {
        // Arrange: Create transactions over multiple months
        $this->createMonthlyTransactions();

        // Act: Generate financial statements
        $response = $this->get('finance/statements/income-statement?period=2024-01');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();

        $this->assertArrayHasKey('revenue', $data);
        $this->assertArrayHasKey('expenses', $data);
        $this->assertArrayHasKey('gross_profit', $data);
        $this->assertArrayHasKey('net_profit', $data);

        $this->assertGreaterThan(0, $data['revenue']['total']);
        $this->assertGreaterThan(0, $data['gross_profit']);
    }

    /** @test */
    public function it_validates_trial_balance()
    {
        // Arrange: Create balanced transactions
        $this->createBalancedJournalEntries();

        // Act: Generate trial balance
        $response = $this->get('finance/reports/trial-balance');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();

        $this->assertArrayHasKey('accounts', $data);
        $this->assertArrayHasKey('totals', $data);

        // Trial balance must balance
        $this->assertEquals(
            $data['totals']['total_debits'],
            $data['totals']['total_credits'],
            'Trial balance must have equal debits and credits'
        );
    }

    /** @test */
    public function it_handles_supplier_payments_correctly()
    {
        // Arrange: Create purchase order
        $poId = $this->createPurchaseOrder(5000000);
        $supplier = $this->supplierModel->first();
        $paymentAmount = 2000000;

        // Act: Process supplier payment
        $paymentData = [
            'supplier_id' => $supplier->id,
            'purchase_order_id' => $poId,
            'payment_amount' => $paymentAmount,
            'payment_method' => 'transfer',
            'payment_date' => date('Y-m-d'),
            'notes' => 'Partial payment for PO#' . $poId
        ];

        $response = $this->post('finance/payments/payable', $paymentData);

        // Assert
        $response->assertStatus(201);

        // Assert: Payment recorded
        $this->assertDatabaseHas('supplier_payments', [
            'supplier_id' => $supplier->id,
            'purchase_order_id' => $poId,
            'amount' => $paymentAmount
        ]);

        // Assert: Payable updated
        $this->assertDatabaseHas('supplier_payables', [
            'supplier_id' => $supplier->id,
            'purchase_order_id' => $poId,
            'amount_paid' => $paymentAmount,
            'status' => 'partial'
        ]);

        // Assert: Journal entries
        $this->assertDatabaseHas('journal_entries', [
            'reference_type' => 'supplier_payment',
            'account_code' => '211', // Utang Usaha
            'debit' => $paymentAmount,
            'credit' => 0
        ]);
    }

    /** @test */
    public function it_prevents_duplicate_journal_entries()
    {
        // Arrange: Create sale
        $saleId = $this->createCashSale(1000000);

        // Act: Try to create duplicate journal entries
        $duplicateEntry = [
            'reference_id' => $saleId,
            'reference_type' => 'sale',
            'account_code' => '411',
            'debit' => 0,
            'credit' => 1000000
        ];

        $response = $this->post('finance/journal/entries', $duplicateEntry);

        // Assert: Should prevent duplicate
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['reference_id']);

        // Assert: Only one entry exists
        $entries = $this->db->table('journal_entries')
            ->where('reference_id', $saleId)
            ->where('reference_type', 'sale')
            ->get()
            ->getResultArray();
        
        $this->assertCount(1, $entries); // From original sale
    }

    private function createCreditSale($amount)
    {
        $product = $this->productModel->first();
        $customer = $this->customerModel->first();

        return $this->saleModel->insert([
            'customer_id' => $customer->id,
            'payment_type' => 'credit',
            'total_amount' => $amount,
            'paid_amount' => 0,
            'credit_amount' => $amount,
            'status' => 'completed',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function createPurchaseOrder($amount)
    {
        $supplier = $this->supplierModel->first();
        
        return $this->db->table('purchase_orders')->insert([
            'supplier_id' => $supplier->id,
            'total_amount' => $amount,
            'status' => 'received',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function createCashSale($amount)
    {
        $product = $this->productModel->first();
        $customer = $this->customerModel->first();

        return $this->saleModel->insert([
            'customer_id' => $customer->id,
            'payment_type' => 'cash',
            'total_amount' => $amount,
            'paid_amount' => $amount,
            'status' => 'completed',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function createMonthlyTransactions()
    {
        // Create sample transactions for January
        for ($i = 1; $i <= 10; $i++) {
            $this->createCashSale(500000 + ($i * 50000));
        }
        
        // Create some expenses
        for ($i = 1; $i <= 5; $i++) {
            $this->db->table('expenses')->insert([
                'description' => 'Monthly expense ' . $i,
                'amount' => 100000 + ($i * 20000),
                'expense_date' => '2024-01-' . str_pad($i * 5, 2, '0', STR_PAD_LEFT),
                'category' => 'operational'
            ]);
        }
    }

    private function createBalancedJournalEntries()
    {
        $entries = [
            ['account_code' => '111', 'debit' => 1000000, 'credit' => 0], // Kas
            ['account_code' => '411', 'debit' => 0, 'credit' => 1000000], // Penjualan
            ['account_code' => '211', 'debit' => 500000, 'credit' => 0], // Utang
            ['account_code' => '112', 'debit' => 0, 'credit' => 500000], // Bank
        ];

        foreach ($entries as $entry) {
            $this->db->table('journal_entries')->insert(array_merge($entry, [
                'reference_type' => 'test',
                'created_at' => date('Y-m-d H:i:s')
            ]));
        }
    }

    private function getLastInsertId($table)
    {
        return $this->db->table($table)->orderBy('id', 'DESC')->limit(1)->get()->getRow()->id;
    }
}