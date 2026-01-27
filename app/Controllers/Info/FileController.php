<?php

namespace App\Controllers\Info;

use App\Controllers\BaseController;

class FileController extends BaseController
{
    protected $fileModel;
    
    public function __construct()
    {
        $this->fileModel = new \App\Models\FileModel();
    }
    
    public function index()
    {
        // Check if user is Owner or Admin
        if (!in_array(session()->get('role'), ['Owner', 'Admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $data = [
            'title' => 'File Manager',
            'files' => $this->fileModel->paginate(20, 'default', $this->request->getGet('page') ?? 1)
        ];
        
        return view('info/files/index', $data);
    }
    
    public function upload()
    {
        // Check if user is Owner or Admin
        if (!in_array(session()->get('role'), ['Owner', 'Admin'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Access denied'
            ], 403);
        }
        
        $file = $this->request->getFile('file');
        $category = $this->request->getPost('category') ?? 'general';
        $description = $this->request->getPost('description') ?? '';
        
        if (!$file) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No file uploaded'
            ]);
        }
        
        // Validate file
        if (!$file->isValid()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $file->getErrorString()
            ]);
        }
        
        // Check file size (max 10MB)
        if ($file->getSize() > 10485760) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'File size too large (max 10MB)'
            ]);
        }
        
        // Check file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt'];
        $fileType = $file->getExtension();
        
        if (!in_array(strtolower($fileType), $allowedTypes)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'File type not allowed'
            ]);
        }
        
        // Generate unique filename
        $newName = $file->getRandomName();
        
        // Store file
        $uploadPath = WRITEPATH . 'uploads/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        if ($file->move($uploadPath, $newName)) {
            // Save file info to database
            $fileData = [
                'nama_file' => $file->getName(),
                'nama_file_sistem' => $newName,
                'tipe_file' => $file->getMimeType(),
                'ukuran_file' => $file->getSize(),
                'kategori' => $category,
                'deskripsi' => $description,
                'path' => 'uploads/' . $newName,
                'id_user' => session()->get('id_user')
            ];
            
            $fileId = $this->fileModel->insert($fileData);
            
            if ($fileId) {
                // Log file upload
                $auditModel = new \App\Models\AuditModel();
                $auditModel->logAction('files', $fileId, 'upload', null, $fileData);
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'File uploaded successfully',
                    'file_id' => $fileId,
                    'file_url' => base_url('uploads/' . $newName)
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to save file info to database'
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to upload file'
            ]);
        }
    }
    
    public function download($id)
    {
        // Check if user is Owner or Admin
        if (!in_array(session()->get('role'), ['Owner', 'Admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $file = $this->fileModel->find($id);
        
        if (!$file) {
            return redirect()->back()->with('error', 'File not found');
        }
        
        $filePath = WRITEPATH . $file['path'];
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server');
        }
        
        // Log file download
        $auditModel = new \App\Models\AuditModel();
        $auditModel->logAction('files', $id, 'download', null, null);
        
        return $this->response->download($filePath, $file['nama_file']);
    }
    
    public function delete($id)
    {
        // Check if user is Owner or Admin
        if (!in_array(session()->get('role'), ['Owner', 'Admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $file = $this->fileModel->find($id);
        
        if (!$file) {
            return redirect()->back()->with('error', 'File not found');
        }
        
        // Delete file from filesystem
        $filePath = WRITEPATH . $file['path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Delete file from database
        $deleted = $this->fileModel->delete($id);
        
        if ($deleted) {
            // Log file deletion
            $auditModel = new \App\Models\AuditModel();
            $auditModel->logAction('files', $id, 'delete', $file, null);
            
            return redirect()->to('/info/files')->with('success', 'File deleted successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to delete file');
        }
    }
    
    public function view($id)
    {
        // Check if user is Owner or Admin
        if (!in_array(session()->get('role'), ['Owner', 'Admin'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied');
        }
        
        $file = $this->fileModel->find($id);
        
        if (!$file) {
            return redirect()->back()->with('error', 'File not found');
        }
        
        $filePath = WRITEPATH . $file['path'];
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server');
        }
        
        // Log file view
        $auditModel = new \App\Models\AuditModel();
        $auditModel->logAction('files', $id, 'view', null, null);
        
        // Display file in browser
        return $this->response->setHeader('Content-Type', $file['tipe_file'])
                               ->setHeader('Content-Disposition', 'inline; filename="' . $file['nama_file'] . '"')
                               ->setBody(file_get_contents($filePath));
    }
    
    public function bulkUpload()
    {
        // Check if user is Owner or Admin
        if (!in_array(session()->get('role'), ['Owner', 'Admin'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Access denied'
            ], 403);
        }
        
        $files = $this->request->getFiles();
        $category = $this->request->getPost('category') ?? 'general';
        $description = $this->request->getPost('description') ?? '';
        
        if (empty($files)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No files uploaded'
            ]);
        }
        
        $uploadedFiles = [];
        $errors = [];
        
        foreach ($files as $file) {
            if ($file->isValid()) {
                // Check file size (max 10MB)
                if ($file->getSize() > 10485760) {
                    $errors[] = $file->getName() . ': File size too large (max 10MB)';
                    continue;
                }
                
                // Check file type
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt'];
                $fileType = $file->getExtension();
                
                if (!in_array(strtolower($fileType), $allowedTypes)) {
                    $errors[] = $file->getName() . ': File type not allowed';
                    continue;
                }
                
                // Generate unique filename
                $newName = $file->getRandomName();
                
                // Store file
                $uploadPath = WRITEPATH . 'uploads/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                if ($file->move($uploadPath, $newName)) {
                    // Save file info to database
                    $fileData = [
                        'nama_file' => $file->getName(),
                        'nama_file_sistem' => $newName,
                        'tipe_file' => $file->getMimeType(),
                        'ukuran_file' => $file->getSize(),
                        'kategori' => $category,
                        'deskripsi' => $description,
                        'path' => 'uploads/' . $newName,
                        'id_user' => session()->get('id_user')
                    ];
                    
                    $fileId = $this->fileModel->insert($fileData);
                    
                    if ($fileId) {
                        $uploadedFiles[] = [
                            'file_id' => $fileId,
                            'original_name' => $file->getName(),
                            'system_name' => $newName,
                            'size' => $file->getSize(),
                            'type' => $file->getMimeType(),
                            'url' => base_url('uploads/' . $newName)
                        ];
                    } else {
                        $errors[] = $file->getName() . ': Failed to save file info to database';
                    }
                } else {
                    $errors[] = $file->getName() . ': Failed to upload file';
                }
            } else {
                $errors[] = $file->getName() . ': ' . $file->getErrorString();
            }
        }
        
        // Log bulk upload
        if (!empty($uploadedFiles)) {
            $auditModel = new \App\Models\AuditModel();
            $auditModel->logAction('files', 0, 'bulk_upload', null, [
                'category' => $category,
                'files_count' => count($uploadedFiles)
            ]);
        }
        
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Files processed',
            'uploaded_files' => $uploadedFiles,
            'errors' => $errors
        ]);
    }
    
    public function getFiles()
    {
        // Check if user is Owner or Admin
        if (!in_array(session()->get('role'), ['Owner', 'Admin'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Access denied'
            ], 403);
        }
        
        $category = $this->request->getGet('category');
        $search = $this->request->getGet('search');
        
        $builder = $this->fileModel;
        
        if ($category) {
            $builder->where('kategori', $category);
        }
        
        if ($search) {
            $builder->groupStart()
                   ->like('nama_file', $search)
                   ->orLike('deskripsi', $search)
                   ->groupEnd();
        }
        
        $files = $builder->orderBy('created_at', 'DESC')
                           ->findAll();
        
        return $this->response->setJSON([
            'status' => 'success',
            'files' => $files
        ]);
    }
    
    public function getCategories()
    {
        // Check if user is Owner or Admin
        if (!in_array(session()->get('role'), ['Owner', 'Admin'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Access denied'
            ], 403);
        }
        
        $categories = $this->fileModel->select('kategori')
                                   ->distinct()
                                   ->findAll();
        
        $categoryList = array_map(function($item) {
            return $item['kategori'];
        }, $categories);
        
        return $this->response->setJSON([
            'status' => 'success',
            'categories' => $categoryList
        ]);
    }
}