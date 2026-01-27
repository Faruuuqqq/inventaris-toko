<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfigModel extends Model
{
    protected $table      = 'system_config';
    protected $primaryKey  = 'id_config';
    
    protected $allowedFields = [
        'config_key', 'config_value'
    ];
    
    protected $useTimestamps = false;
    
    /**
     * Get configuration value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getConfigValue($key, $default = null)
    {
        $config = $this->where('config_key', $key)->first();
        return $config ? $config['config_value'] : $default;
    }
    
    /**
     * Set configuration value
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function setConfigValue($key, $value)
    {
        $config = $this->where('config_key', $key)->first();
        
        if ($config) {
            return $this->update($config['id_config'], ['config_value' => $value]);
        } else {
            return $this->insert(['config_key' => $key, 'config_value' => $value]);
        }
    }
    
    /**
     * Get all configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $config = $this->findAll();
        $result = [];
        
        foreach ($config as $item) {
            $result[$item['config_key']] = $item['config_value'];
        }
        
        return $result;
    }
    
    /**
     * Update configuration
     *
     * @param array $config
     * @return bool
     */
    public function updateConfig($key, $value)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Check if key exists
            $existing = $this->where('config_key', $key)->first();
            
            if ($existing) {
                $this->update($existing['id_config'], ['config_value' => $value]);
            } else {
                $this->insert(['config_key' => $key, 'config_value' => $value]);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return true;
            
        } catch (\Exception $e) {
            $db->transRollback();
            return false;
        }
    }
    
    /**
     * Update multiple configuration values
     *
     * @param array $configArray
     * @return bool
     */
    public function updateMultipleConfig($configArray)
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            foreach ($configArray as $key => $value) {
                // Skip CSRF token
                if ($key === 'csrf_test_name') continue;
                
                $this->updateConfig($key, $value);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return true;
            
        } catch (\Exception $e) {
            $db->transRollback();
            return false;
        }
    }
    
    /**
     * Get company configuration
     *
     * @return array
     */
    public function getCompanyConfig()
    {
        $keys = [
            'company_name',
            'company_address',
            'company_phone',
            'company_email',
            'tax_number'
        ];
        
        $config = [];
        foreach ($keys as $key) {
            $config[$key] = $this->getConfigValue($key);
        }
        
        return $config;
    }
    
    /**
     * Get system configuration
     *
     * @return array
     */
    public function getSystemConfig()
    {
        $keys = [
            'currency',
            'date_format',
            'time_format',
            'timezone',
            'language',
            'decimal_places'
        ];
        
        $config = [];
        foreach ($keys as $key) {
            $config[$key] = $this->getConfigValue($key);
        }
        
        return $config;
    }
    
    /**
     * Get security configuration
     *
     * @return array
     */
    public function getSecurityConfig()
    {
        $keys = [
            'max_login_attempts',
            'session_timeout',
            'password_min_length',
            'password_require_uppercase',
            'password_require_lowercase',
            'password_require_number',
            'password_require_special',
            'enable_2fa',
            'allowed_ips'
        ];
        
        $config = [];
        foreach ($keys as $key) {
            $config[$key] = $this->getConfigValue($key);
        }
        
        return $config;
    }
    
    /**
     * Get notification configuration
     *
     * @return array
     */
    public function getNotificationConfig()
    {
        $keys = [
            'enable_email_notifications',
            'smtp_host',
            'smtp_port',
            'smtp_username',
            'smtp_password',
            'smtp_encryption',
            'from_email',
            'from_name',
            'low_stock_alert',
            'backup_frequency'
        ];
        
        $config = [];
        foreach ($keys as $key) {
            $config[$key] = $this->getConfigValue($key);
        }
        
        return $config;
    }
    
    /**
     * Initialize default configuration
     *
     * @return bool
     */
    public function initializeDefaults()
    {
        $defaults = [
            'company_name' => 'Toko Distributor',
            'company_address' => '',
            'company_phone' => '',
            'company_email' => '',
            'tax_number' => '',
            'currency' => 'IDR',
            'date_format' => 'd-m-Y',
            'time_format' => 'H:i',
            'timezone' => 'Asia/Jakarta',
            'language' => 'id',
            'decimal_places' => '0',
            'max_login_attempts' => '5',
            'session_timeout' => '3600',
            'password_min_length' => '8',
            'password_require_uppercase' => '1',
            'password_require_lowercase' => '1',
            'password_require_number' => '1',
            'password_require_special' => '0',
            'enable_2fa' => '0',
            'allowed_ips' => '',
            'enable_email_notifications' => '0',
            'smtp_host' => '',
            'smtp_port' => '587',
            'smtp_username' => '',
            'smtp_password' => '',
            'smtp_encryption' => 'tls',
            'from_email' => '',
            'from_name' => '',
            'low_stock_alert' => '1',
            'backup_frequency' => 'daily'
        ];
        
        foreach ($defaults as $key => $value) {
            $this->updateConfig($key, $value);
        }
        
        return true;
    }
    
    /**
     * Validate configuration key
     *
     * @param string $key
     * @return bool
     */
    public function isValidConfigKey($key)
    {
        $validKeys = [
            'company_name',
            'company_address',
            'company_phone',
            'company_email',
            'tax_number',
            'currency',
            'date_format',
            'time_format',
            'timezone',
            'language',
            'decimal_places',
            'max_login_attempts',
            'session_timeout',
            'password_min_length',
            'password_require_uppercase',
            'password_require_lowercase',
            'password_require_number',
            'password_require_special',
            'enable_2fa',
            'allowed_ips',
            'enable_email_notifications',
            'smtp_host',
            'smtp_port',
            'smtp_username',
            'smtp_password',
            'smtp_encryption',
            'from_email',
            'from_name',
            'low_stock_alert',
            'backup_frequency'
        ];
        
        return in_array($key, $validKeys);
    }
}