<?php

namespace App\Controllers\Info;

use App\Controllers\BaseController;

class AuditController extends BaseController
{
    protected $auditModel;
    protected $userModel;
    
    public function __construct()
    {
        $this->auditModel = new \App\Models\AuditModel();
        $this->userModel = new \App\Models\UserModel();
    }
    
    public function index()
    {
        // Check if user is Owner or Admin
        if (!in_array(session()->get('role'), ['Owner', 'Admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $data = [
            'title' => 'Audit Trail',
            'auditStats' => $this->auditModel->getAuditStats(),
            'suspiciousActivities' => $this->auditModel->getSuspiciousActivities()
        ];
        
        return view('info/audit/index', $data);
    }
    
    public function userAudit($userId)
    {
        // Check if user is Owner or Admin
        if (!in_array(session()->get('role'), ['Owner', 'Admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to('/info/audit')->with('error', 'User not found');
        }
        
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        $action = $this->request->getGet('action');
        $page = $this->request->getGet('page') ?? 1;
        
        $auditLogs = $this->auditModel->getUserAudit($userId, $dateFrom, $dateTo, $action, $page);
        
        $data = [
            'title' => 'User Audit Trail - ' . $user['fullname'],
            'user' => $user,
            'auditLogs' => $auditLogs,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'action' => $action
            ]
        ];
        
        return view('info/audit/user', $data);
    }
    
    public function tableAudit($table)
    {
        // Check if user is Owner or Admin
        if (!in_array(session()->get('role'), ['Owner', 'Admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        $action = $this->request->getGet('action');
        
        $auditLogs = $this->auditModel->getTableAudit($table, $dateFrom, $dateTo, $action, 100);
        
        $data = [
            'title' => 'Table Audit Trail - ' . ucfirst($table),
            'table' => $table,
            'auditLogs' => $auditLogs,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'action' => $action
            ]
        ];
        
        return view('info/audit/table', $data);
    }
    
    public function recordAudit($table, $recordId)
    {
        // Check if user is Owner or Admin
        if (!in_array(session()->get('role'), ['Owner', 'Admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $auditLogs = $this->auditModel->getRecordAudit($table, $recordId);
        
        $data = [
            'title' => 'Record Audit Trail',
            'table' => $table,
            'recordId' => $recordId,
            'auditLogs' => $auditLogs
        ];
        
        return view('info/audit/record', $data);
    }
    
    public function failedLogins()
    {
        // Check if user is Owner or Admin
        if (!in_array(session()->get('role'), ['Owner', 'Admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        $ipAddress = $this->request->getGet('ip_address');
        
        $failedLogins = $this->auditModel->getFailedLogins($dateFrom, $dateTo, $ipAddress);
        
        $data = [
            'title' => 'Failed Login Attempts',
            'failedLogins' => $failedLogins,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'ip_address' => $ipAddress
            ]
        ];
        
        return view('info/audit/failed_logins', $data);
    }
    
    public function suspiciousActivities()
    {
        // Check if user is Owner or Admin
        if (!in_array(session()->get('role'), ['Owner', 'Admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $limit = $this->request->getGet('limit') ?? 50;
        $activities = $this->auditModel->getSuspiciousActivities($limit);
        
        $data = [
            'title' => 'Suspicious Activities',
            'activities' => $activities
        ];
        
        return view('info/audit/suspicious', $data);
    }
    
    public function export()
    {
        // Check if user is Owner or Admin
        if (!in_array(session()->get('role'), ['Owner', 'Admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        $action = $this->request->getGet('action');
        $table = $this->request->getGet('table');
        $format = $this->request->getGet('format') ?? 'csv';
        
        if ($table) {
            $auditLogs = $this->auditModel->getTableAudit($table, $dateFrom, $dateTo, $action);
        } else {
            $auditLogs = $this->auditModel->getTableAudit('audit_trail', $dateFrom, $dateTo, $action);
        }
        
        if ($format === 'csv') {
            $this->exportToCSV($auditLogs, 'audit_logs_' . date('Y-m-d') . '.csv');
        } else if ($format === 'json') {
            $this->exportToJSON($auditLogs, 'audit_logs_' . date('Y-m-d') . '.json');
        } else {
            return redirect()->back()->with('error', 'Invalid export format');
        }
    }
    
    public function clean()
    {
        // Check if user is Owner
        if (session()->get('role') !== 'Owner') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $days = $this->request->getPost('days') ?? 90;
        
        if ($this->request->getMethod() === 'POST') {
            $cleaned = $this->auditModel->cleanOldLogs($days);
            
            if ($cleaned) {
                return redirect()->to('/info/audit')->with('success', 'Audit logs cleaned successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to clean audit logs');
            }
        }
        
        $data = [
            'title' => 'Clean Audit Logs',
            'days' => $days
        ];
        
        return view('info/audit/clean', $data);
    }
    
    private function exportToCSV($data, $filename)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add CSV header
        fputcsv($output, ['ID', 'User', 'Table', 'Record ID', 'Action', 'Old Values', 'New Values', 'IP Address', 'User Agent', 'Created At']);
        
        // Add data rows
        foreach ($data as $row) {
            fputcsv($output, [
                $row['id_audit'],
                $row['fullname'] ?? 'System',
                $row['table_name'],
                $row['record_id'],
                $row['action'],
                $row['old_values'],
                $row['new_values'],
                $row['ip_address'],
                $row['user_agent'],
                $row['created_at']
            ]);
        }
        
        fclose($output);
    }
    
    private function exportToJSON($data, $filename)
    {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        echo json_encode($data, JSON_PRETTY_PRINT);
    }
}