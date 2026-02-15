<?php
namespace App\Models;

use CodeIgniter\Model;

class KontraBonModel extends Model
{
    protected $table = 'kontra_bons';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'document_number', 'customer_id', 'created_at', 'due_date',
        'total_amount', 'status', 'notes'
    ];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Validation
    protected $validationRules = [
        'customer_id' => 'required|numeric',
        'total_amount' => 'required|decimal',
        'status' => 'required|in_list[PENDING,PAID,CANCELLED]',
    ];
    
    protected $validationMessages = [
        'customer_id' => [
            'required' => 'Customer harus dipilih',
            'numeric' => 'Customer tidak valid',
        ],
        'total_amount' => [
            'required' => 'Total amount harus diisi',
            'decimal' => 'Total amount harus berupa angka',
        ],
        'status' => [
            'required' => 'Status harus dipilih',
            'in_list' => 'Status tidak valid',
        ],
    ];

    /**
     * Get unpaid kontra bons
     */
    public function getUnpaid($customerId = null)
    {
        $builder = $this->where('status !=', 'PAID');
        if ($customerId) {
            $builder->where('customer_id', $customerId);
        }
        return $builder->orderBy('created_at', 'ASC')->findAll();
    }

    /**
     * Update status
     */
    public function updateStatus($kontraBonId, $status)
    {
        $kontraBon = $this->find($kontraBonId);
        if (!$kontraBon) {
            throw new \Exception('Kontra Bon not found');
        }

        return $this->update($kontraBonId, ['status' => $status]);
    }

    /**
     * Generate document number
     */
    public function generateDocumentNumber(): string
    {
        $date = date('Ymd');
        $count = $this->where('DATE(created_at)', date('Y-m-d'))->countAllResults();
        return 'KB-' . $date . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get kontra bons with customer details
     */
    public function getWithCustomer($customerId = null, $status = null)
    {
        $builder = $this->select('kontra_bons.*, customers.name as customer_name, customers.phone as customer_phone, customers.address as customer_address')
            ->join('customers', 'customers.id = kontra_bons.customer_id');

        if ($customerId) {
            $builder->where('kontra_bons.customer_id', $customerId);
        }

        if ($status) {
            $builder->where('kontra_bons.status', $status);
        }

        return $builder->orderBy('kontra_bons.created_at', 'DESC')->findAll();
    }
    
    /**
     * Get all kontra bons with customer information (alias for consistency)
     */
    public function getAllWithCustomer($status = null)
    {
        return $this->getWithCustomer(null, $status);
    }
    
    /**
     * Get single kontra bon with full customer details
     */
     public function getById($id)
     {
         return $this->select('kontra_bons.*, customers.name as customer_name, customers.phone as customer_phone, customers.address as customer_address')
             ->join('customers', 'customers.id = kontra_bons.customer_id', 'left')
             ->where('kontra_bons.id', $id)
             ->first();
     }
    
    /**
     * Get statistics
     */
    public function getStatistics()
    {
        $total = $this->countAll();
        $pending = $this->where('status', 'PENDING')->countAllResults(false);
        $paid = $this->where('status', 'PAID')->countAllResults(false);
        $cancelled = $this->where('status', 'CANCELLED')->countAllResults();

        $totalAmount = $this->selectSum('total_amount')->first();

        return [
            'total' => $total,
            'pending' => $pending,
            'paid' => $paid,
            'cancelled' => $cancelled,
            'total_amount' => $totalAmount['total_amount'] ?? 0,
        ];
    }
}