<?php
namespace App\Models;

use App\Entities\KontraBon;
use CodeIgniter\Model;

class KontraBonModel extends Model
{
    protected $table = 'kontra_bons';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = KontraBon::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'document_number', 'customer_id', 'created_at', 'due_date',
        'total_amount', 'status', 'notes'
    ];
    protected $useTimestamps = false;

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
        $builder = $this->select('kontra_bons.*, customers.name as customer_name')
            ->join('customers', 'customers.id = kontra_bons.customer_id');

        if ($customerId) {
            $builder->where('kontra_bons.customer_id', $customerId);
        }

        if ($status) {
            $builder->where('kontra_bons.status', $status);
        }

        return $builder->orderBy('kontra_bons.created_at', 'DESC')->findAll();
    }
}