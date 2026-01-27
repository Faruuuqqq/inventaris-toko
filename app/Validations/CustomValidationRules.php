<?php

namespace App\Validations;

use CodeIgniter\HTTP\RequestInterface;

class CustomValidationRules
{
    protected $request;
    
    public function __construct(RequestInterface $request = null)
    {
        $this->request = $request;
    }
    
    /**
     * Validate positive number (greater than 0)
     */
    public function positive_number($value, ?string &$error = null): bool
    {
        if (!is_numeric($value) || $value <= 0) {
            $error = 'The {field} field must be a positive number.';
            return false;
        }
        return true;
    }
    
    /**
     * Validate non-negative number (greater than or equal to 0)
     */
    public function non_negative_number($value, ?string &$error = null): bool
    {
        if (!is_numeric($value) || $value < 0) {
            $error = 'The {field} field must be a non-negative number.';
            return false;
        }
        return true;
    }
    
    /**
     * Validate Indonesian phone number format
     */
    public function indonesian_phone($value, ?string &$error = null): bool
    {
        // Remove non-digit characters first
        $cleaned = preg_replace('/[^0-9]/', '', $value);
        
        // Check if starts with 0 or +62 and has valid length
        if ((preg_match('/^0[0-9]{9,12}$/', $cleaned) || preg_match('/^62[0-9]{9,12}$/', $cleaned))) {
            return true;
        }
        
        $error = 'The {field} field must be a valid Indonesian phone number.';
        return false;
    }
    
    /**
     * Validate that a date is not in the future
     */
    public function not_future_date($value, ?string &$error = null): bool
    {
        if (empty($value)) {
            return true;
        }
        
        $date = new \DateTime($value);
        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        
        if ($date > $today) {
            $error = 'The {field} field cannot be a future date.';
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate that a date is not in the past
     */
    public function not_past_date($value, ?string &$error = null): bool
    {
        if (empty($value)) {
            return true;
        }
        
        $date = new \DateTime($value);
        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        
        if ($date < $today) {
            $error = 'The {field} field cannot be a past date.';
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate product stock availability
     */
    public function stock_available($value, ?string &$error = null, $params = []): bool
    {
        $productId = $params[0] ?? null;
        $warehouseId = $params[1] ?? null;
        
        if (empty($productId) || empty($warehouseId)) {
            return true; // Can't validate without required params
        }
        
        $stockMutationModel = new \App\Models\StockMutationModel();
        $availableStock = $stockMutationModel->getProductStock($productId, $warehouseId);
        
        if ($value > $availableStock) {
            $error = 'The {field} field exceeds available stock. Available: ' . $availableStock;
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate customer credit limit
     */
    public function credit_limit_available($value, ?string &$error = null, $params = []): bool
    {
        $customerId = $params[0] ?? null;
        
        if (empty($customerId)) {
            return true; // Can't validate without customer ID
        }
        
        $customerModel = new \App\Models\CustomerModel();
        $customer = $customerModel->find($customerId);
        
        if (!$customer) {
            return true;
        }
        
        $salesModel = new \App\Models\SalesModel();
        $currentReceivable = $salesModel->getCustomerReceivable($customerId);
        $availableCredit = $customer['limit_kredit'] - $currentReceivable;
        
        if ($value > $availableCredit) {
            $error = 'The {field} field exceeds available credit limit. Available: ' . number_format($availableCredit, 0, ',', '.');
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate that end date is after start date
     */
    public function valid_date_range($value, ?string &$error = null, $params = []): bool
    {
        $startDate = $params[0] ?? null;
        
        if (empty($startDate)) {
            return true;
        }
        
        $end = new \DateTime($value);
        $start = new \DateTime($startDate);
        
        if ($end < $start) {
            $error = 'The end date must be after the start date.';
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate password strength
     */
    public function strong_password($value, ?string &$error = null): bool
    {
        if (strlen($value) < 8) {
            $error = 'The {field} field must be at least 8 characters long.';
            return false;
        }
        
        if (!preg_match('/[A-Z]/', $value)) {
            $error = 'The {field} field must contain at least one uppercase letter.';
            return false;
        }
        
        if (!preg_match('/[a-z]/', $value)) {
            $error = 'The {field} field must contain at least one lowercase letter.';
            return false;
        }
        
        if (!preg_match('/[0-9]/', $value)) {
            $error = 'The {field} field must contain at least one number.';
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate Indonesian postal code
     */
    public function indonesian_postal_code($value, ?string &$error = null): bool
    {
        if (!preg_match('/^[0-9]{5}$/', $value)) {
            $error = 'The {field} field must be a valid 5-digit Indonesian postal code.';
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate that selection exists in database
     */
    public function exists_in($value, ?string &$error = null, $params = []): bool
    {
        $table = $params[0] ?? null;
        $field = $params[1] ?? 'id';
        
        if (empty($table) || empty($field) || empty($value)) {
            return false;
        }
        
        $db = \Config\Database::connect();
        $builder = $db->table($table);
        $exists = $builder->where($field, $value)->countAllResults() > 0;
        
        if (!$exists) {
            $error = 'The selected {field} is invalid.';
            return false;
        }
        
        return true;
    }
}