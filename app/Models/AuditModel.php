<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditModel extends Model
{
    protected $table      = 'audit_trail';
    protected $primaryKey  = 'id_audit';
    
    protected $allowedFields = [
        'id_user', 'table_name', 'record_id', 'action', 'old_values', 
        'new_values', 'ip_address', 'user_agent', 'created_at'
    ];
    
    protected $useTimestamps = false;
    
    /**
     * Log an action
     *
     * @param string $table The database table name
     * @param int $recordId The ID of the record
     * @param string $action The action performed (insert, update, delete)
     * @param array|null $oldValues The old values (for updates)
     * @param array|null $newValues The new values
     * @param int|null $userId The user ID performing the action
     * @return bool
     */
    public function logAction($table, $recordId, $action, $oldValues = null, $newValues = null, $userId = null)
    {
        $request = \Config\Services::request();
        
        $data = [
            'id_user'       => $userId ?? session()->get('id_user'),
            'table_name'     => $table,
            'record_id'      => $recordId,
            'action'         => $action,
            'old_values'     => $oldValues ? json_encode($oldValues) : null,
            'new_values'     => $newValues ? json_encode($newValues) : null,
            'ip_address'     => $request->getIPAddress(),
            'user_agent'     => $request->getUserAgent(),
            'created_at'     => date('Y-m-d H:i:s')
        ];
        
        return $this->insert($data);
    }
    
    /**
     * Log login action
     *
     * @param int $userId The user ID
     * @param string $status The login status (success, failed)
     * @param string|null $reason The reason for failure
     * @return bool
     */
    public function logLogin($userId, $status, $reason = null)
    {
        $request = \Config\Services::request();
        
        $data = [
            'id_user'       => $userId,
            'table_name'     => 'users',
            'record_id'      => $userId,
            'action'         => 'login',
            'old_values'     => null,
            'new_values'     => json_encode([
                'status' => $status,
                'reason' => $reason
            ]),
            'ip_address'     => $request->getIPAddress(),
            'user_agent'     => $request->getUserAgent(),
            'created_at'     => date('Y-m-d H:i:s')
        ];
        
        return $this->insert($data);
    }
    
    /**
     * Get audit logs for a specific record
     *
     * @param string $table The database table name
     * @param int $recordId The ID of the record
     * @param int|null $limit Limit the number of results
     * @return array
     */
    public function getRecordAudit($table, $recordId, $limit = null)
    {
        $builder = $this->db->table($this->table)
            ->select('audit_trail.*, users.fullname, users.username')
            ->join('users', 'users.id_user = audit_trail.id_user', 'left')
            ->where('table_name', $table)
            ->where('record_id', $recordId)
            ->orderBy('created_at', 'DESC');
            
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get audit logs for a specific user
     *
     * @param int $userId The user ID
     * @param string|null $dateFrom Start date
     * @param string|null $dateTo End date
     * @param string|null $action Specific action
     * @param int|null $page Page number
     * @param int|null $perPage Items per page
     * @return array
     */
    public function getUserAudit($userId, $dateFrom = null, $dateTo = null, $action = null, $page = null, $perPage = 20)
    {
        $builder = $this->db->table($this->table)
            ->where('id_user', $userId);
            
        if ($dateFrom) {
            $builder->where('created_at >=', $dateFrom);
        }
        
        if ($dateTo) {
            $builder->where('created_at <=', $dateTo);
        }
        
        if ($action) {
            $builder->where('action', $action);
        }
        
        $builder->orderBy('created_at', 'DESC');
        
        if ($page && $perPage) {
            $offset = ($page - 1) * $perPage;
            $builder->limit($perPage, $offset);
        }
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get audit logs by table
     *
     * @param string $table The database table name
     * @param string|null $dateFrom Start date
     * @param string|null $dateTo End date
     * @param string|null $action Specific action
     * @param int|null $limit Limit the number of results
     * @return array
     */
    public function getTableAudit($table, $dateFrom = null, $dateTo = null, $action = null, $limit = null)
    {
        $builder = $this->db->table($this->table)
            ->where('table_name', $table);
            
        if ($dateFrom) {
            $builder->where('created_at >=', $dateFrom);
        }
        
        if ($dateTo) {
            $builder->where('created_at <=', $dateTo);
        }
        
        if ($action) {
            $builder->where('action', $action);
        }
        
        $builder->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get audit statistics
     *
     * @param string|null $dateFrom Start date
     * @param string|null $dateTo End date
     * @return array
     */
    public function getAuditStats($dateFrom = null, $dateTo = null)
    {
        $builder = $this->db->table($this->table);
        
        if ($dateFrom) {
            $builder->where('created_at >=', $dateFrom);
        }
        
        if ($dateTo) {
            $builder->where('created_at <=', $dateTo);
        }
        
        $stats = [];
        
        // Total actions
        $stats['total_actions'] = $builder->countAllResults();
        
        // Actions by type
        $actionsQuery = $this->db->table($this->table)
            ->select('action, COUNT(*) as count');
            
        if ($dateFrom) {
            $actionsQuery->where('created_at >=', $dateFrom);
        }
        
        if ($dateTo) {
            $actionsQuery->where('created_at <=', $dateTo);
        }
        
        $stats['actions_by_type'] = $actionsQuery->groupBy('action')
            ->get()
            ->getResultArray();
        
        // Actions by table
        $tablesQuery = $this->db->table($this->table)
            ->select('table_name, COUNT(*) as count');
            
        if ($dateFrom) {
            $tablesQuery->where('created_at >=', $dateFrom);
        }
        
        if ($dateTo) {
            $tablesQuery->where('created_at <=', $dateTo);
        }
        
        $stats['actions_by_table'] = $tablesQuery->groupBy('table_name')
            ->get()
            ->getResultArray();
        
        // Top users
        $usersQuery = $this->db->table($this->table)
            ->select('users.fullname, users.username, COUNT(*) as count')
            ->join('users', 'users.id_user = audit_trail.id_user', 'left')
            ->where('audit_trail.id_user !=', null);
            
        if ($dateFrom) {
            $usersQuery->where('created_at >=', $dateFrom);
        }
        
        if ($dateTo) {
            $usersQuery->where('created_at <=', $dateTo);
        }
        
        $stats['top_users'] = $usersQuery->groupBy('users.id_user, users.fullname, users.username')
            ->orderBy('count', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
        
        return $stats;
    }
    
    /**
     * Get failed login attempts
     *
     * @param string|null $dateFrom Start date
     * @param string|null $dateTo End date
     * @param string|null $ipAddress Specific IP address
     * @return array
     */
    public function getFailedLogins($dateFrom = null, $dateTo = null, $ipAddress = null)
    {
        $builder = $this->db->table($this->table)
            ->select('audit_trail.*, users.username')
            ->join('users', 'users.id_user = audit_trail.id_user', 'left')
            ->where('table_name', 'users')
            ->where('action', 'login')
            ->where('JSON_EXTRACT(new_values, "$.status")', 'failed');
            
        if ($dateFrom) {
            $builder->where('created_at >=', $dateFrom);
        }
        
        if ($dateTo) {
            $builder->where('created_at <=', $dateTo);
        }
        
        if ($ipAddress) {
            $builder->where('ip_address', $ipAddress);
        }
        
        return $builder->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Get suspicious activities
     *
     * @param int $limit Limit the number of results
     * @return array
     */
    public function getSuspiciousActivities($limit = 50)
    {
        // Define suspicious patterns
        $suspiciousActions = [
            'delete' => 'Deletion of records',
            'login' => 'Failed login attempts',
            'update' => 'Frequent updates'
        ];
        
        $activities = [];
        
        foreach ($suspiciousActions as $action => $description) {
            $builder = $this->db->table($this->table)
                ->select('audit_trail.*, users.fullname, users.username')
                ->join('users', 'users.id_user = audit_trail.id_user', 'left')
                ->where('action', $action)
                ->orderBy('created_at', 'DESC')
                ->limit($limit / count($suspiciousActions));
            
            if ($action === 'login') {
                $builder->where('JSON_EXTRACT(new_values, "$.status")', 'failed');
            }
            
            $results = $builder->get()->getResultArray();
            
            if (!empty($results)) {
                $activities[] = [
                    'action_type' => $action,
                    'description' => $description,
                    'activities' => $results
                ];
            }
        }
        
        return $activities;
    }
    
    /**
     * Clean old audit logs
     *
     * @param int $days Number of days to keep
     * @return bool
     */
    public function cleanOldLogs($days = 90)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-$days days"));
        
        return $this->db->table($this->table)
            ->where('created_at <', $cutoffDate)
            ->delete();
    }
}