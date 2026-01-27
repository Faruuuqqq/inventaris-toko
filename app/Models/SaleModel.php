<?php
namespace App\Models;

use App\Entities\Sale;
use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';
    protected $returnType = Sale::class;
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'invoice_number', 'customer_id', 'user_id', 'salesperson_id',
        'warehouse_id', 'payment_type', 'due_date', 'total_amount',
        'paid_amount', 'payment_status', 'is_hidden', 'kontra_bon_id'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    // GLOBAL SCOPE: Hide hidden sales from non-Owner users
    public function findAll(?int $limit = null, ?int $offset = 0)
    {
        $userRole = session()->get('role');

        if ($userRole !== 'OWNER') {
            $this->where('is_hidden', 0);
        }

        return parent::findAll($limit, $offset);
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(CustomerModel::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function salesperson()
    {
        return $this->belongsTo(SalespersonModel::class, 'salesperson_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(WarehouseModel::class, 'warehouse_id');
    }

    public function items()
    {
        return $this->hasMany(SaleItemModel::class, 'sale_id');
    }

    public function kontraBon()
    {
        return $this->belongsTo(KontraBonModel::class, 'kontra_bon_id');
    }
}