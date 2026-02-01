<?php

// Create missing API controllers - Customers and Suppliers

$controllers = [
    [
        'file' => 'app/Controllers/Api/CustomersController.php',
        'content' => '<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\CustomerModel;

class CustomersController extends ResourceController
{
    use ResponseTrait;
    
    protected $customerModel;
    
    public function __construct()
    {
        $this->customerModel = new CustomerModel();
    }
    
    public function index()
    {
        $customers = $this->customerModel->findAll();
        return $this->respond($customers);
    }
    
    public function show($id = null)
    {
        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return $this->failNotFound('Customer not found');
        }
        return $this->respond($customer);
    }
    
    public function create()
    {
        $data = $this->request->getPost();
        $id = $this->customerModel->insert($data);
        return $this->respondCreated($id);
    }
    
    public function update($id = null)
    {
        $data = $this->request->getPost();
        $this->customerModel->update($id, $data);
        return $this->respond($data);
    }
    
    public function delete($id = null)
    {
        $this->customerModel->delete($id);
        return $this->respondDeleted();
    }
    
    public function receivable($id)
    {
        $customer = $this->customerModel->find($id);
        return $this->respond($customer);
    }
    
    public function creditLimit()
    {
        $customers = $this->customerModel->findAll();
        return $this->respond($customers);
    }
}
',
    ],
    [
        'file' => 'app/Controllers/Api/SuppliersController.php',
        'content' => '<?php

namespace App\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\SupplierModel;

class SuppliersController extends ResourceController
{
    use ResponseTrait;
    
    protected $supplierModel;
    
    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
    }
    
    public function index()
    {
        $suppliers = $this->supplierModel->findAll();
        return $this->respond($suppliers);
    }
    
    public function show($id = null)
    {
        $supplier = $this->supplierModel->find($id);
        if (!$supplier) {
            return $this->failNotFound('Supplier not found');
        }
        return $this->respond($supplier);
    }
    
    public function create()
    {
        $data = $this->request->getPost();
        $id = $this->supplierModel->insert($data);
        return $this->respondCreated($id);
    }
    
    public function update($id = null)
    {
        $data = $this->request->getPost();
        $this->supplierModel->update($id, $data);
        return $this->respond($data);
    }
    
    public function delete($id = null)
    {
        $this->supplierModel->delete($id);
        return $this->respondDeleted();
    }
}
',
    ],
];

foreach ($controllers as $controller) {
    if (!file_exists($controller['file'])) {
        echo "Creating: {$controller['file']}\n";
        file_put_contents($controller['file'], $controller['content']);
        echo "  ✓ Created!\n";
    } else {
        echo "  - Already exists, skipping\n";
    }
}

echo "\nDone!\n";
