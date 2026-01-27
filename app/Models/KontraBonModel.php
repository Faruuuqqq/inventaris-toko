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
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    // Relationships
    public function customer()
    {
        return $this->belongsTo(CustomerModel::class, 'customer_id');
    }

    public function sales()
    {
        return $this->hasMany(SaleModel::class, 'kontra_bon_id');
    }
}