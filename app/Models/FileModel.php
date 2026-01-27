<?php

namespace App\Models;

use CodeIgniter\Model;

class FileModel extends Model
{
    protected $table      = 'files';
    protected $primaryKey  = 'id_file';
    
    protected $allowedFields = [
        'nama_file', 'nama_file_sistem', 'tipe_file', 'ukuran_file', 
        'kategori', 'deskripsi', 'path', 'id_user'
    ];
    
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    
    /**
     * Get files by category
     *
     * @param string $category
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getFilesByCategory($category, $limit = null, $offset = 0)
    {
        $builder = $this->where('kategori', $category)
                          ->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->findAll();
    }
    
    /**
     * Get files by user
     *
     * @param int $userId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getFilesByUser($userId, $limit = null, $offset = 0)
    {
        $builder = $this->where('id_user', $userId)
                          ->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->findAll();
    }
    
    /**
     * Search files
     *
     * @param string $keyword
     * @param string|null $category
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function searchFiles($keyword, $category = null, $limit = null, $offset = 0)
    {
        $builder = $this->groupStart()
                          ->like('nama_file', $keyword)
                          ->orLike('deskripsi', $keyword)
                          ->groupEnd()
                          ->orderBy('created_at', 'DESC');
        
        if ($category) {
            $builder->where('kategori', $category);
        }
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->findAll();
    }
    
    /**
     * Get file statistics
     *
     * @return array
     */
    public function getFileStats()
    {
        $stats = [];
        
        // Total files
        $stats['total_files'] = $this->countAllResults();
        
        // Total size
        $builder = $this->selectSum('ukuran_file');
        $result = $builder->get()->getRow();
        $stats['total_size'] = $result->ukuran_file ?? 0;
        
        // Files by category
        $stats['by_category'] = $this->select('kategori, COUNT(*) as count')
                                   ->groupBy('kategori')
                                   ->findAll();
        
        // Files by type
        $stats['by_type'] = $this->select('SUBSTRING_INDEX(tipe_file, "/", 1) as file_type, COUNT(*) as count')
                                   ->groupBy('file_type')
                                   ->findAll();
        
        // Recent files
        $stats['recent_files'] = $this->orderBy('created_at', 'DESC')
                                      ->limit(5)
                                      ->findAll();
        
        return $stats;
    }
    
    /**
     * Get file by system name
     *
     * @param string $systemName
     * @return array|null
     */
    public function getFileBySystemName($systemName)
    {
        return $this->where('nama_file_sistem', $systemName)
                      ->first();
    }
    
    /**
     * Check if file exists
     *
     * @param string $systemName
     * @return bool
     */
    public function fileExists($systemName)
    {
        return $this->where('nama_file_sistem', $systemName)
                      ->countAllResults() > 0;
    }
    
    /**
     * Get file with user info
     *
     * @param int $id
     * @return array|null
     */
    public function getFileWithUser($id)
    {
        return $this->select('files.*, users.fullname, users.username')
                      ->join('users', 'users.id_user = files.id_user', 'left')
                      ->where('files.id_file', $id)
                      ->first();
    }
    
    /**
     * Get recent files
     *
     * @param int $limit
     * @return array
     */
    public function getRecentFiles($limit = 10)
    {
        return $this->select('files.*, users.fullname')
                      ->join('users', 'users.id_user = files.id_user', 'left')
                      ->orderBy('files.created_at', 'DESC')
                      ->limit($limit)
                      ->findAll();
    }
    
    /**
     * Clean up orphaned files
     *
     * @param int $days Number of days old
     * @return int Number of files cleaned
     */
    public function cleanupOrphanedFiles($days = 30)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-$days days"));
        
        // Get files to delete
        $filesToDelete = $this->where('created_at <', $cutoffDate)
                              ->findAll();
        
        $deletedCount = 0;
        
        foreach ($filesToDelete as $file) {
            $filePath = WRITEPATH . $file['path'];
            
            // Delete physical file
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Delete database record
            $this->delete($file['id_file']);
            $deletedCount++;
        }
        
        return $deletedCount;
    }
    
    /**
     * Format file size for display
     *
     * @param int $bytes
     * @return string
     */
    public function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } else {
            $bytes = '0 bytes';
        }
        
        return $bytes;
    }
}