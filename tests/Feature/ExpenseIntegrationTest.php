<?php

namespace Tests\Feature;

use Tests\Support\DatabaseTestCase;
use App\Models\UserModel;
use App\Models\ExpenseModel;
use App\Models\JournalEntryModel;

class ExpenseIntegrationTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\App\Database\Seeds\UserSeeder::class);
        $this->seed(\App\Database\Seeds\ExpenseSeeder::class);
    }

    /** @test */
    public function it_creates_expense_with_proper_journal_entry()
    {
        // Arrange
        $user = $this->userModel->first();
        $expenseData = [
            'description' => 'Pembelian ATK Kantor',
            'amount' => 1500000,
            'expense_date' => date('Y-m-d'),
            'category' => 'operational',
            'payment_method' => 'cash',
            'approved_by' => $user->id,
            'notes' => 'Pembelian kertas, pulpen, dan stapler'
        ];

        // Act
        $response = $this->post('finance/expenses/store', $expenseData);

        // Assert: Expense created
        $response->assertStatus(201);
        $expenseId = $this->getLastInsertId('expenses');

        $this->assertDatabaseHas('expenses', [
            'description' => 'Pembelian ATK Kantor',
            'amount' => 1500000,
            'category' => 'operational'
        ]);

        // Assert: Journal entry created
        $this->assertDatabaseHas('journal_entries', [
            'reference_id' => $expenseId,
            'reference_type' => 'expense',
            'account_code' => '511', // Beban Operasional
            'debit' => 1500000,
            'credit' => 0
        ]);

        $this->assertDatabaseHas('journal_entries', [
            'reference_id' => $expenseId,
            'reference_type' => 'expense',
            'account_code' => '111', // Kas
            'debit' => 0,
            'credit' => 1500000
        ]);
    }

    /** @test */
    public function it_validates_expense_limits_and_budgets()
    {
        // Arrange: Create expense that exceeds monthly budget
        $expenseData = [
            'description' => 'Bonus Karyawan',
            'amount' => 50000000, // High amount
            'expense_date' => date('Y-m-d'),
            'category' => 'salary',
            'payment_method' => 'transfer'
        ];

        // Act
        $response = $this->post('finance/expenses/store', $expenseData);

        // Assert: Should require approval for high amounts
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['amount']);

        // Assert: No expense created
        $this->assertDatabaseMissing('expenses', [
            'description' => 'Bonus Karyawan'
        ]);
    }

    /** @test */
    public function it_processes_expense_approval_workflow()
    {
        // Arrange: Create expense requiring approval
        $expenseId = $this->createPendingExpense(25000000);
        $approver = $this->userModel->first();

        // Act: Approve expense
        $response = $this->put("finance/expenses/{$expenseId}/approve", [
            'approved' => true,
            'approval_notes' => 'Disetujui karena kebutuhan mendesak',
            'approved_by' => $approver->id,
            'approval_date' => date('Y-m-d H:i:s')
        ]);

        // Assert
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('expenses', [
            'id' => $expenseId,
            'status' => 'approved',
            'approved_by' => $approver->id
        ]);

        // Assert: Approval history recorded
        $this->assertDatabaseHas('expense_approvals', [
            'expense_id' => $expenseId,
            'approved_by' => $approver->id,
            'status' => 'approved'
        ]);
    }

    /** @test */
    public function it_generates_expense_reports_by_category()
    {
        // Arrange: Create expenses in different categories
        $this->createExpensesInCategories();

        // Act: Get expense report
        $response = $this->get('finance/expenses/report?period=monthly&group_by=category');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();

        $this->assertArrayHasKey('categories', $data);
        $this->assertArrayHasKey('totals', $data);
        $this->assertArrayHasKey('trend', $data);

        // Verify category grouping
        $categories = $data['categories'];
        $this->assertArrayHasKey('operational', $categories);
        $this->assertArrayHasKey('marketing', $categories);
        $this->assertArrayHasKey('salary', $categories);
    }

    /** @test */
    public function it_tracks_expense_attachments()
    {
        // Arrange: Create expense with attachment
        $expenseId = $this->createPendingExpense(1000000);

        // Act: Upload attachment
        $_FILES = [
            'attachment' => [
                'name' => 'receipt.jpg',
                'type' => 'image/jpeg',
                'size' => 1024000,
                'tmp_name' => '/tmp/test_receipt.jpg'
            ]
        ];

        $response = $this->post("finance/expenses/{$expenseId}/attachment", [
            'expense_id' => $expenseId,
            'attachment_type' => 'receipt'
        ]);

        // Assert
        $response->assertStatus(201);
        
        $this->assertDatabaseHas('expense_attachments', [
            'expense_id' => $expenseId,
            'attachment_type' => 'receipt',
            'file_name' => 'receipt.jpg'
        ]);
    }

    /** @test */
    public function it_prevents_duplicate_expense_entries()
    {
        // Arrange: Create expense
        $expenseData = [
            'description' => 'Test Expense',
            'amount' => 500000,
            'expense_date' => '2024-01-15',
            'receipt_number' => 'RCP-001'
        ];

        $response = $this->post('finance/expenses/store', $expenseData);
        $this->assertEquals(201, $response->getStatusCode());

        // Act: Try to create duplicate
        $duplicateResponse = $this->post('finance/expenses/store', $expenseData);

        // Assert: Should prevent duplicate
        $duplicateResponse->assertStatus(422);
        $duplicateResponse->assertJsonValidationErrors(['receipt_number']);

        // Assert: Only one expense exists
        $expenses = $this->db->table('expenses')
            ->where('receipt_number', 'RCP-001')
            ->get()
            ->getResultArray();
        
        $this->assertCount(1, $expenses);
    }

    /** @test */
    public function it_calculates_monthly_expense_trends()
    {
        // Arrange: Create expenses over 6 months
        $this->createMonthlyExpenses();

        // Act: Get trend analysis
        $response = $this->get('finance/expenses/trends?period=6months');

        // Assert
        $response->assertStatus(200);
        $data = $response->getJSON();

        $this->assertArrayHasKey('monthly_data', $data);
        $this->assertArrayHasKey('average', $data);
        $this->assertArrayHasKey('growth_rate', $data);

        // Should have 6 months of data
        $this->assertCount(6, $data['monthly_data']);

        // Verify trend calculation
        $monthlyData = $data['monthly_data'];
        $totalExpenses = array_sum(array_column($monthlyData, 'total_amount'));
        $this->assertGreaterThan(0, $totalExpenses);
    }

    private function createPendingExpense($amount)
    {
        return $this->db->table('expenses')->insertGetId([
            'description' => 'Test Expense',
            'amount' => $amount,
            'expense_date' => date('Y-m-d'),
            'category' => 'operational',
            'payment_method' => 'cash',
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function createExpensesInCategories()
    {
        $categories = ['operational', 'marketing', 'salary', 'maintenance'];
        
        foreach ($categories as $category) {
            for ($i = 1; $i <= 3; $i++) {
                $this->db->table('expenses')->insert([
                    'description' => ucfirst($category) . ' Expense ' . $i,
                    'amount' => 1000000 + ($i * 500000),
                    'expense_date' => date('Y-m-d', strtotime("-{$i} days")),
                    'category' => $category,
                    'payment_method' => 'cash',
                    'status' => 'approved',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    private function createMonthlyExpenses()
    {
        for ($month = 6; $month >= 1; $month--) {
            $baseAmount = 10000000;
            $variation = rand(-20, 20) / 100; // ±20% variation
            
            $monthlyTotal = $baseAmount + ($baseAmount * $variation);
            
            $this->db->table('expenses')->insert([
                'description' => 'Monthly Expense - Month ' . $month,
                'amount' => abs($monthlyTotal),
                'expense_date' => date('Y-m-d', strtotime("-{$month} months")),
                'category' => 'operational',
                'payment_method' => 'cash',
                'status' => 'approved',
                'created_at' => date('Y-m-d H:i:s', strtotime("-{$month} months"))
            ]);
        }
    }

    private function getLastInsertId($table)
    {
        return $this->db->table($table)->orderBy('id', 'DESC')->limit(1)->get()->getRow()->id;
    }
}