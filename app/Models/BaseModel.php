<?php

namespace App\Models;

use CodeIgniter\Database\Exceptions\DataException;

class BaseModel extends \CodeIgniter\Model
{
    /**
     * Insert data with validation and sanitization
     */
    public function safeInsert(array $data)
    {
        // Sanitize data before insertion
        $sanitizedData = $this->sanitizeData($data);
        
        // Validate data
        if (!$this->validate($sanitizedData)) {
            throw new DataException(implode(', ', $this->validation->getErrors()));
        }
        
        return $this->insert($sanitizedData);
    }
    
    /**
     * Update data with validation and sanitization
     */
    public function safeUpdate($id, array $data)
    {
        // Sanitize data before update
        $sanitizedData = $this->sanitizeData($data);
        
        // Validate data
        if (!$this->validate($sanitizedData)) {
            throw new DataException(implode(', ', $this->validation->getErrors()));
        }
        
        return $this->update($id, $sanitizedData);
    }
    
    /**
     * Sanitize input data
     */
    protected function sanitizeData(array $data)
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // Handle nested arrays recursively
                $sanitized[$key] = $this->sanitizeData($value);
            } elseif (is_string($value)) {
                // Sanitize string values
                $sanitized[$key] = $this->sanitizeString($value);
            } else {
                // Keep non-string values as is (numbers, bools, etc.)
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Sanitize string values
     */
    protected function sanitizeString($string)
    {
        // Remove HTML tags
        $string = strip_tags($string);
        
        // Convert HTML entities
        $string = htmlentities($string, ENT_QUOTES, 'UTF-8');
        
        // Remove potential JavaScript
        $string = preg_replace('/(java|script):/i', '', $string);
        $string = preg_replace('/on\w+\s*=/i', '', $string);
        
        // Remove SQL injection patterns
        $string = preg_replace('/(\s|^)(select|insert|update|delete|drop|union|exec|script)(\s|$)/i', '', $string);
        
        return trim($string);
    }
    
    /**
     * Execute query with parameter binding to prevent SQL injection
     */
    protected function safeQuery($sql, $params = [])
    {
        $builder = $this->db->query($sql, $params);
        return $builder;
    }
    
    /**
     * Get single record with validation
     */
    public function safeFind($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return null;
        }
        
        return $this->find($id);
    }
    
    /**
     * Get records with pagination and security
     */
    public function safeFindAll($limit = null, $offset = 0)
    {
        // Validate pagination parameters
        $limit = is_numeric($limit) && $limit > 0 ? (int)$limit : null;
        $offset = is_numeric($offset) && $offset >= 0 ? (int)$offset : 0;
        
        return $this->findAll($limit, $offset);
    }
    
    /**
     * Count records with validation
     */
    public function safeCountAllResults($reset = true, $testOnly = false)
    {
        return $this->countAllResults($reset, $testOnly);
    }
    
    /**
     * Delete with validation
     */
    public function safeDelete($id = null, $purge = false)
    {
        if (!is_numeric($id) || $id <= 0) {
            return false;
        }
        
        // Additional validation can be added here
        // For example, check if record can be deleted
        
        return $this->delete($id, $purge);
    }
    
    /**
     * Where clause with parameter binding
     */
    public function safeWhere($key, $value = null)
    {
        if (is_array($key)) {
            // Sanitize array conditions
            $sanitized = [];
            foreach ($key as $k => $v) {
                $sanitized[$k] = $this->sanitizeValue($v);
            }
            return $this->where($sanitized);
        } else {
            return $this->where($key, $this->sanitizeValue($value));
        }
    }
    
    /**
     * Sanitize values for where clauses
     */
    protected function sanitizeValue($value)
    {
        if (is_string($value)) {
            return $this->sanitizeString($value);
        } elseif (is_array($value)) {
            return array_map([$this, 'sanitizeValue'], $value);
        }
        
        return $value;
    }
}