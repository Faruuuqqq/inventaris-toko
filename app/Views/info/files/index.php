<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title"><?= $title ?></h3>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i data-lucide="upload"></i>
                    Upload Files
                </button>
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
                    <i data-lucide="package"></i>
                    Bulk Upload
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title">Files Manager</h5>
                        </div>
                        <div class="col-auto">
                            <form method="get" class="d-flex gap-2">
                                <select name="category" class="form-select form-select-sm" style="width: 150px;">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['kategori'] ?>" <?= selected($cat['kategori'], old('category', $this->request->getGet('category'))) ?>>
                                            <?= ucfirst($cat['kategori']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <input type="text" class="form-control" name="search" placeholder="Search files..." value="<?= old('search', $this->request->getGet('search')) ?>">
                                    <button type="submit" class="btn btn-outline-secondary">
                                        <i data-lucide="search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Uploaded By</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($files)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No files found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($files as $file): ?>
                                        <tr>
                                            <td>
                                                <?php if (in_array($file['tipe_file'], ['image/jpeg', 'image/png', 'image/gif'])): ?>
                                                    <img src="<?= base_url('uploads/' . $file['nama_file_sistem']) ?>" alt="<?= $file['nama_file'] ?>" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                                                <?php endif; ?>
                                                <span><?= $file['nama_file'] ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary"><?= ucfirst($file['kategori']) ?></span>
                                            </td>
                                            <td><?= $file['tipe_file'] ?></td>
                                            <td><?= format_file_size($file['ukuran_file']) ?></td>
                                            <td><?= $file['fullname'] ?></td>
                                            <td><?= format_datetime($file['created_at']) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-info" onclick="viewFile(<?= $file['id_file'] ?>)">
                                                        <i data-lucide="eye"></i>
                                                    </button>
                                                    <a href="<?= base_url('/info/files/download/' . $file['id_file']) ?>" class="btn btn-sm btn-success">
                                                        <i data-lucide="download"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteFile(<?= $file['id_file'] ?>, '<?= $file['nama_file'] ?>')">
                                                        <i data-lucide="trash-2"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (isset($pager)): ?>
                        <div class="d-flex justify-content-center mt-3">
                            <?= $pager->links() ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadForm" method="post" action="<?= base_url('/info/files/upload') ?>" enctype="multipart/form-data">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="file" class="form-label">Select File</label>
                        <input type="file" class="form-control" id="file" name="file" required>
                        <div class="form-text">Max file size: 10MB. Allowed types: jpg, jpeg, png, gif, pdf, doc, docx, xls, xlsx, csv, txt</div>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="general">General</option>
                            <option value="document">Document</option>
                            <option value="image">Image</option>
                            <option value="report">Report</option>
                            <option value="import">Import</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="progress" id="uploadProgress" style="display: none;">
                        <div class="progress-bar" role="progressbar" style="width: 0%;"></div>
                    </div>
                    <div class="alert" id="uploadResult" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="upload"></i>
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Upload Modal -->
<div class="modal fade" id="bulkUploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Upload Files</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulkUploadForm" method="post" action="<?= base_url('/info/files/bulk-upload') ?>" enctype="multipart/form-data">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="files" class="form-label">Select Files</label>
                        <input type="file" class="form-control" id="files" name="files[]" multiple required>
                        <div class="form-text">Select multiple files. Max file size: 10MB per file.</div>
                    </div>
                    <div class="mb-3">
                        <label for="bulkCategory" class="form-label">Category</label>
                        <select class="form-select" id="bulkCategory" name="category">
                            <option value="general">General</option>
                            <option value="document">Document</option>
                            <option value="image">Image</option>
                            <option value="report">Report</option>
                            <option value="import">Import</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="bulkDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="bulkDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="progress" id="bulkUploadProgress" style="display: none;">
                        <div class="progress-bar" role="progressbar" style="width: 0%;"></div>
                    </div>
                    <div class="alert" id="bulkUploadResult" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="package"></i>
                        Upload All
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Single file upload
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const progressBar = document.getElementById('uploadProgress');
    const resultDiv = document.getElementById('uploadResult');
    const submitBtn = this.querySelector('button[type="submit"]');
    
    // Show progress
    progressBar.style.display = 'block';
    resultDiv.style.display = 'none';
    submitBtn.disabled = true;
    
    fetch('<?= base_url('/info/files/upload') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        progressBar.style.display = 'none';
        resultDiv.style.display = 'block';
        
        if (data.status === 'success') {
            resultDiv.className = 'alert alert-success';
            resultDiv.innerHTML = '<i data-lucide="check-circle"></i> ' + data.message;
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            resultDiv.className = 'alert alert-danger';
            resultDiv.innerHTML = '<i data-lucide="x-circle"></i> ' + data.message;
        }
        
        submitBtn.disabled = false;
    })
    .catch(error => {
        progressBar.style.display = 'none';
        resultDiv.style.display = 'block';
        resultDiv.className = 'alert alert-danger';
        resultDiv.innerHTML = '<i data-lucide="x-circle"></i> Upload failed: ' + error.message;
        submitBtn.disabled = false;
    });
});

// Bulk file upload
document.getElementById('bulkUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const progressBar = document.getElementById('bulkUploadProgress');
    const resultDiv = document.getElementById('bulkUploadResult');
    const submitBtn = this.querySelector('button[type="submit"]');
    
    // Show progress
    progressBar.style.display = 'block';
    resultDiv.style.display = 'none';
    submitBtn.disabled = true;
    
    fetch('<?= base_url('/info/files/bulk-upload') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        progressBar.style.display = 'none';
        resultDiv.style.display = 'block';
        
        if (data.status === 'success') {
            resultDiv.className = 'alert alert-success';
            let html = '<i data-lucide="check-circle"></i> ' + data.message;
            
            if (data.uploaded_files && data.uploaded_files.length > 0) {
                html += '<br><strong>Uploaded files:</strong><ul>';
                data.uploaded_files.forEach(file => {
                    html += '<li>' + file.original_name + '</li>';
                });
                html += '</ul>';
            }
            
            if (data.errors && data.errors.length > 0) {
                html += '<br><strong>Errors:</strong><ul>';
                data.errors.forEach(error => {
                    html += '<li>' + error + '</li>';
                });
                html += '</ul>';
            }
            
            resultDiv.innerHTML = html;
            setTimeout(() => {
                location.reload();
            }, 3000);
        } else {
            resultDiv.className = 'alert alert-danger';
            resultDiv.innerHTML = '<i data-lucide="x-circle"></i> ' + data.message;
        }
        
        submitBtn.disabled = false;
    })
    .catch(error => {
        progressBar.style.display = 'none';
        resultDiv.style.display = 'block';
        resultDiv.className = 'alert alert-danger';
        resultDiv.innerHTML = '<i data-lucide="x-circle"></i> Upload failed: ' + error.message;
        submitBtn.disabled = false;
    });
});

function viewFile(fileId) {
    window.open('<?= base_url('/info/files/view/') ?>' + fileId, '_blank');
}

function deleteFile(fileId, fileName) {
    if (confirm('Are you sure you want to delete "' + fileName + '"?')) {
        window.location.href = '<?= base_url('/info/files/delete/') ?>' + fileId;
    }
}
</script>

<?= $this->endSection() ?>