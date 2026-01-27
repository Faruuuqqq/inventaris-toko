<?php

namespace App\Controllers\Info;

use App\Controllers\BaseController;

class SystemController extends BaseController
{
    protected $configModel;
    
    public function __construct()
    {
        $this->configModel = new \App\Models\ConfigModel();
    }
    
    public function index()
    {
        // Check if user is Owner
        if (session()->get('role') !== 'Owner') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $data = [
            'title' => 'System Configuration',
            'config' => $this->configModel->getConfig(),
            'systemInfo' => $this->getSystemInfo()
        ];
        
        return view('info/system/index', $data);
    }
    
    public function update()
    {
        // Check if user is Owner
        if (session()->get('role') !== 'Owner') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $config = $this->request->getPost();
        
        // Validate and update config
        $rules = [
            'company_name' => 'required|min_length[3]|max_length[100]',
            'company_address' => 'max_length[255]',
            'company_phone' => 'max_length[20]',
            'company_email' => 'valid_email|max_length[100]',
            'tax_rate' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'currency' => 'required|in_list[IDR,USD]',
            'date_format' => 'required|in_list[d-m-Y,Y-m-d]',
            'time_format' => 'required|in_list[H:i,A]',
            'backup_frequency' => 'required|in_list[daily,weekly,monthly]',
            'max_login_attempts' => 'required|integer|greater_than[0]',
            'session_timeout' => 'required|integer|greater_than[0]',
            'enable_email_notifications' => 'required|in_list[0,1]',
            'smtp_host' => 'max_length[100]',
            'smtp_port' => 'integer|greater_than[0]',
            'smtp_username' => 'max_length[100]',
            'smtp_password' => 'max_length[100]',
            'smtp_encryption' => 'in_list[tls,ssl,none]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            foreach ($config as $key => $value) {
                if ($key === 'csrf_test_name') continue; // Skip CSRF token
                
                // Convert checkbox values
                if ($key === 'enable_email_notifications') {
                    $value = $value ?? '0';
                }
                
                // Skip password if empty
                if ($key === 'smtp_password' && empty($value)) {
                    continue;
                }
                
                $this->configModel->updateConfig($key, $value);
            }
            
            // Log configuration update
            $auditModel = new \App\Models\AuditModel();
            $auditModel->logAction('system_config', 0, 'update', null, $config);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }
            
            return redirect()->to('/info/system')->with('success', 'System configuration updated successfully');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to update system configuration: ' . $e->getMessage());
        }
    }
    
    public function backup()
    {
        // Check if user is Owner
        if (session()->get('role') !== 'Owner') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $data = [
            'title' => 'Backup & Restore',
            'backups' => $this->getBackupList(),
            'config' => $this->configModel->getConfig()
        ];
        
        return view('info/system/backup', $data);
    }
    
    public function createBackup()
    {
        // Check if user is Owner
        if (session()->get('role') !== 'Owner') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Access denied'
            ], 403);
        }
        
        $backupType = $this->request->getPost('type') ?? 'full';
        $includeFiles = $this->request->getPost('include_files') ?? '0';
        
        try {
            $backupFile = $this->performBackup($backupType, $includeFiles === '1');
            
            // Log backup creation
            $auditModel = new \App\Models\AuditModel();
            $auditModel->logAction('backups', 0, 'create', null, [
                'type' => $backupType,
                'include_files' => $includeFiles,
                'file' => $backupFile
            ]);
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Backup created successfully',
                'file' => $backupFile
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to create backup: ' . $e->getMessage()
            ]);
        }
    }
    
    public function downloadBackup($file)
    {
        // Check if user is Owner
        if (session()->get('role') !== 'Owner') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $backupPath = WRITEPATH . 'backups/' . $file;
        
        if (!file_exists($backupPath)) {
            return redirect()->back()->with('error', 'Backup file not found');
        }
        
        // Log backup download
        $auditModel = new \App\Models\AuditModel();
        $auditModel->logAction('backups', 0, 'download', null, ['file' => $file]);
        
        return $this->response->download($backupPath, $file);
    }
    
    public function deleteBackup($file)
    {
        // Check if user is Owner
        if (session()->get('role') !== 'Owner') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $backupPath = WRITEPATH . 'backups/' . $file;
        
        if (!file_exists($backupPath)) {
            return redirect()->back()->with('error', 'Backup file not found');
        }
        
        if (unlink($backupPath)) {
            // Log backup deletion
            $auditModel = new \App\Models\AuditModel();
            $auditModel->logAction('backups', 0, 'delete', null, ['file' => $file]);
            
            return redirect()->back()->with('success', 'Backup file deleted successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to delete backup file');
        }
    }
    
    public function restore()
    {
        // Check if user is Owner
        if (session()->get('role') !== 'Owner') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Access denied'
            ], 403);
        }
        
        $file = $this->request->getFile('backup_file');
        
        if (!$file) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No file uploaded'
            ]);
        }
        
        if (!$file->isValid()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $file->getErrorString()
            ]);
        }
        
        // Check file type
        if ($file->getExtension() !== 'zip') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid file type. Only ZIP files are allowed'
            ]);
        }
        
        try {
            $tempPath = WRITEPATH . 'temp/' . $file->getRandomName();
            $file->move(WRITEPATH . 'temp/', $tempPath);
            
            $this->performRestore($tempPath);
            
            // Log restore
            $auditModel = new \App\Models\AuditModel();
            $auditModel->logAction('backups', 0, 'restore', null, ['file' => $file->getName()]);
            
            // Clean up temp file
            unlink($tempPath);
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Backup restored successfully'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to restore backup: ' . $e->getMessage()
            ]);
        }
    }
    
    public function maintenance()
    {
        // Check if user is Owner
        if (session()->get('role') !== 'Owner') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $data = [
            'title' => 'System Maintenance',
            'maintenanceInfo' => $this->getMaintenanceInfo()
        ];
        
        return view('info/system/maintenance', $data);
    }
    
    public function performMaintenance()
    {
        // Check if user is Owner
        if (session()->get('role') !== 'Owner') {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $task = $this->request->getPost('task');
        
        try {
            switch ($task) {
                case 'clear_cache':
                    $this->clearCache();
                    $message = 'Cache cleared successfully';
                    break;
                    
                case 'optimize_tables':
                    $this->optimizeTables();
                    $message = 'Database tables optimized successfully';
                    break;
                    
                case 'clean_uploads':
                    $this->cleanUploads();
                    $message = 'Upload directory cleaned successfully';
                    break;
                    
                case 'rebuild_indexes':
                    $this->rebuildIndexes();
                    $message = 'Database indexes rebuilt successfully';
                    break;
                    
                default:
                    throw new \Exception('Invalid maintenance task');
            }
            
            // Log maintenance
            $auditModel = new \App\Models\AuditModel();
            $auditModel->logAction('maintenance', 0, 'perform', null, ['task' => $task]);
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to perform maintenance: ' . $e->getMessage());
        }
    }
    
    private function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'codeigniter_version' => \CodeIgniter\CodeIgniter::CI_VERSION,
            'database' => \Config\Database::connect()->getPlatform(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'disk_free_space' => disk_free_space(WRITEPATH),
            'disk_total_space' => disk_total_space(WRITEPATH)
        ];
    }
    
    private function getBackupList()
    {
        $backupDir = WRITEPATH . 'backups/';
        $backups = [];
        
        if (is_dir($backupDir)) {
            $files = scandir($backupDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                    $filePath = $backupDir . $file;
                    $backups[] = [
                        'name' => $file,
                        'size' => filesize($filePath),
                        'date' => date('Y-m-d H:i:s', filemtime($filePath))
                    ];
                }
            }
        }
        
        // Sort by date descending
        usort($backups, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return $backups;
    }
    
    private function performBackup($type, $includeFiles)
    {
        $backupDir = WRITEPATH . 'backups/';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.zip';
        $backupPath = $backupDir . $filename;
        
        $zip = new \ZipArchive();
        
        if ($zip->open($backupPath, \ZipArchive::CREATE) === TRUE) {
            // Database backup
            $db = \Config\Database::connect();
            $this->backupDatabase($db, $zip);
            
            // Files backup
            if ($includeFiles) {
                $this->backupFiles($zip);
            }
            
            $zip->close();
        }
        
        return $filename;
    }
    
    private function backupDatabase($db, $zip)
    {
        $filename = 'database_backup_' . date('Y-m-d_H-i-s') . '.sql';
        $sqlFile = WRITEPATH . 'temp/' . $filename;
        
        // Get all tables
        $tables = $db->listTables();
        
        $sql = '';
        foreach ($tables as $table) {
            $sql .= $this->getTableStructure($db, $table);
            $sql .= $this->getTableData($db, $table);
        }
        
        file_put_contents($sqlFile, $sql);
        $zip->addFile($sqlFile, $filename);
        unlink($sqlFile);
    }
    
    private function backupFiles($zip)
    {
        $directories = [
            'uploads' => 'uploads/',
            'writable' => 'writable/'
        ];
        
        foreach ($directories as $name => $path) {
            $fullPath = WRITEPATH . $path;
            if (is_dir($fullPath)) {
                $zip->addEmptyDir($name);
                $this->addFolderToZip($fullPath, $name, $zip);
            }
        }
    }
    
    private function addFolderToZip($folder, $relativePath, $zip)
    {
        $files = scandir($folder);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $fullPath = $folder . '/' . $file;
                $zipRelativePath = $relativePath . '/' . $file;
                
                if (is_dir($fullPath)) {
                    $zip->addEmptyDir($zipRelativePath);
                    $this->addFolderToZip($fullPath, $zipRelativePath, $zip);
                } else {
                    $zip->addFile($fullPath, $zipRelativePath);
                }
            }
        }
    }
    
    private function performRestore($backupPath)
    {
        $tempDir = WRITEPATH . 'temp/restore_' . time() . '/';
        mkdir($tempDir, 0755, true);
        
        $zip = new \ZipArchive();
        
        if ($zip->open($backupPath) === TRUE) {
            $zip->extractTo($tempDir);
            $zip->close();
            
            // Restore database
            if (file_exists($tempDir . 'database_backup.sql')) {
                $this->restoreDatabase($tempDir . 'database_backup.sql');
            }
            
            // Restore files if exists
            if (is_dir($tempDir . 'uploads/')) {
                $this->restoreFiles($tempDir . 'uploads/', WRITEPATH . 'uploads/');
            }
            
            // Clean up temp directory
            $this->deleteDirectory($tempDir);
        }
    }
    
    private function restoreDatabase($sqlFile)
    {
        $sql = file_get_contents($sqlFile);
        $db = \Config\Database::connect();
        
        // Split SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $db->query($statement);
            }
        }
    }
    
    private function restoreFiles($source, $destination)
    {
        // Backup current files
        $backupDir = $destination . '_backup_' . time() . '/';
        if (is_dir($destination)) {
            $this->copyDirectory($destination, $backupDir);
        }
        
        // Restore new files
        $this->copyDirectory($source, $destination);
    }
    
    private function copyDirectory($source, $destination)
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        $files = scandir($source);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $sourcePath = $source . '/' . $file;
                $destPath = $destination . '/' . $file;
                
                if (is_dir($sourcePath)) {
                    $this->copyDirectory($sourcePath, $destPath);
                } else {
                    copy($sourcePath, $destPath);
                }
            }
        }
    }
    
    private function deleteDirectory($dir)
    {
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $dir . '/' . $file;
                    
                    if (is_dir($filePath)) {
                        $this->deleteDirectory($filePath);
                    } else {
                        unlink($filePath);
                    }
                }
            }
            rmdir($dir);
        }
    }
    
    private function clearCache()
    {
        $cachePath = WRITEPATH . 'cache/';
        if (is_dir($cachePath)) {
            $this->deleteDirectory($cachePath);
            mkdir($cachePath, 0755, true);
        }
    }
    
    private function optimizeTables()
    {
        $db = \Config\Database::connect();
        $tables = $db->listTables();
        
        foreach ($tables as $table) {
            $db->query("OPTIMIZE TABLE `$table`");
        }
    }
    
    private function cleanUploads()
    {
        $tempDir = WRITEPATH . 'temp/';
        if (is_dir($tempDir)) {
            $this->deleteDirectory($tempDir);
            mkdir($tempDir, 0755, true);
        }
    }
    
    private function rebuildIndexes()
    {
        $db = \Config\Database::connect();
        $tables = $db->listTables();
        
        foreach ($tables as $table) {
            $db->query("ANALYZE TABLE `$table`");
        }
    }
    
    private function getMaintenanceInfo()
    {
        return [
            'last_backup' => $this->getLastBackupDate(),
            'cache_size' => $this->getCacheSize(),
            'temp_files_size' => $this->getTempFilesSize(),
            'disk_usage' => $this->getDiskUsage()
        ];
    }
    
    private function getLastBackupDate()
    {
        $backups = $this->getBackupList();
        return !empty($backups) ? $backups[0]['date'] : null;
    }
    
    private function getCacheSize()
    {
        $cachePath = WRITEPATH . 'cache/';
        return $this->getDirectorySize($cachePath);
    }
    
    private function getTempFilesSize()
    {
        $tempPath = WRITEPATH . 'temp/';
        return $this->getDirectorySize($tempPath);
    }
    
    private function getDiskUsage()
    {
        $totalSpace = disk_total_space(WRITEPATH);
        $freeSpace = disk_free_space(WRITEPATH);
        $usedSpace = $totalSpace - $freeSpace;
        
        return [
            'total' => $totalSpace,
            'used' => $usedSpace,
            'free' => $freeSpace,
            'percentage' => ($usedSpace / $totalSpace) * 100
        ];
    }
    
    private function getDirectorySize($dir)
    {
        if (!is_dir($dir)) {
            return 0;
        }
        
        $size = 0;
        $files = scandir($dir);
        
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $dir . '/' . $file;
                
                if (is_dir($filePath)) {
                    $size += $this->getDirectorySize($filePath);
                } else {
                    $size += filesize($filePath);
                }
            }
        }
        
        return $size;
    }
}