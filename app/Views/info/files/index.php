<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('FileText', 'h-8 w-8 text-primary') ?>
            <?= $title ?? 'Manajemen File' ?>
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Kelola dan simpan file dokumen penting</p>
    </div>
    <div class="flex gap-2 flex-wrap sm:flex-nowrap">
        <button type="button" onclick="openUploadModal()" class="inline-flex items-center justify-center gap-2 h-11 px-6 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition whitespace-nowrap">
            <?= icon('Plus', 'h-5 w-5') ?>
            Upload File
        </button>
        <button type="button" onclick="openBulkUploadModal()" class="inline-flex items-center justify-center gap-2 h-11 px-6 rounded-lg border border-border/50 text-foreground font-medium hover:bg-muted transition whitespace-nowrap">
            <?= icon('Package', 'h-5 w-5') ?>
            Bulk Upload
        </button>
    </div>
</div>

<!-- Filter Section -->
<div class="mb-8 rounded-lg border bg-surface shadow-sm p-6">
    <form method="get" class="space-y-4">
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <!-- Category Filter -->
            <div class="space-y-2">
                <label for="category" class="text-sm font-medium text-foreground">Kategori</label>
                <select name="category" id="category" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= esc($cat['kategori']) ?>" <?= selected($cat['kategori'], old('category', $this->request->getGet('category'))) ?>>
                            <?= ucfirst(esc($cat['kategori'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Search -->
            <div class="space-y-2">
                <label for="search" class="text-sm font-medium text-foreground">Cari File</label>
                <input type="text" name="search" id="search" placeholder="Cari nama file..." value="<?= old('search', $this->request->getGet('search')) ?>" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
            </div>

            <!-- Search Button -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">&nbsp;</label>
                <button type="submit" class="h-10 w-full rounded-lg bg-primary text-white font-medium text-sm hover:bg-primary/90 transition flex items-center justify-center gap-2">
                    <?= icon('Search', 'h-4 w-4') ?>
                    Cari
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Files Table -->
<div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground">Daftar File</h2>
    </div>
    <div class="w-full overflow-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-border bg-muted/50">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Nama File</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Kategori</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Tipe</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Ukuran</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Diupload Oleh</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Tanggal</th>
                    <th class="px-6 py-3 text-center font-semibold text-foreground">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                <?php if (empty($files)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <?= icon('FileText', 'h-12 w-12 text-muted-foreground/50') ?>
                                <p class="text-sm font-medium text-muted-foreground">Tidak ada file ditemukan</p>
                                <p class="text-xs text-muted-foreground">Coba ubah filter pencarian Anda</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($files as $file): ?>
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <?php if (in_array($file['tipe_file'], ['image/jpeg', 'image/png', 'image/gif'])): ?>
                                        <img src="<?= base_url('uploads/' . esc($file['nama_file_sistem'])) ?>" alt="<?= esc($file['nama_file']) ?>" class="h-8 w-8 rounded object-cover flex-shrink-0">
                                    <?php else: ?>
                                        <?= icon('FileText', 'h-8 w-8 text-muted-foreground flex-shrink-0') ?>
                                    <?php endif; ?>
                                    <span class="font-medium text-foreground"><?= esc($file['nama_file']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-2.5 py-1 rounded-full bg-primary/10 text-primary text-xs font-medium border border-primary/30">
                                    <?= ucfirst(esc($file['kategori'])) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-muted-foreground text-xs font-mono"><?= esc($file['tipe_file']) ?></td>
                            <td class="px-6 py-4 text-muted-foreground"><?= format_file_size($file['ukuran_file']) ?></td>
                            <td class="px-6 py-4 text-muted-foreground"><?= esc($file['fullname']) ?></td>
                            <td class="px-6 py-4 text-muted-foreground text-sm"><?= format_datetime($file['created_at']) ?></td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" onclick="viewFile(<?= $file['id_file'] ?>)" class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-secondary/10 text-secondary hover:bg-secondary/20 transition" title="View">
                                        <?= icon('Eye', 'h-4 w-4') ?>
                                    </button>
                                    <a href="<?= base_url('/info/files/download/' . $file['id_file']) ?>" class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-success/10 text-success hover:bg-success/20 transition" title="Download">
                                        <?= icon('Download', 'h-4 w-4') ?>
                                    </a>
                                    <button type="button" onclick="deleteFile(<?= $file['id_file'] ?>, '<?= esc($file['nama_file']) ?>')" class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-destructive/10 text-destructive hover:bg-destructive/20 transition" title="Delete">
                                        <?= icon('Trash2', 'h-4 w-4') ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if (isset($pager)): ?>
        <div class="p-6 border-t border-border/50">
            <div class="flex justify-center">
                <?= $pager->links() ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center" onclick="closeUploadModal(event)">
    <div class="bg-background rounded-lg shadow-lg border border-border max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6 border-b border-border/50 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-foreground">Upload File</h2>
            <button type="button" onclick="closeUploadModal()" class="text-muted-foreground hover:text-foreground">
                <?= icon('X', 'h-5 w-5') ?>
            </button>
        </div>

        <form id="uploadForm" class="p-6 space-y-4">
            <?= csrf_field() ?>
            <div class="space-y-2">
                <label for="file" class="text-sm font-medium text-foreground">Pilih File</label>
                <input type="file" id="file" name="file" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary">
                <p class="text-xs text-muted-foreground">Ukuran maksimal: 10MB. Tipe: jpg, jpeg, png, gif, pdf, doc, docx, xls, xlsx, csv, txt</p>
            </div>

            <div class="space-y-2">
                <label for="uploadCategory" class="text-sm font-medium text-foreground">Kategori</label>
                <select id="uploadCategory" name="category" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <option value="general">General</option>
                    <option value="document">Document</option>
                    <option value="image">Image</option>
                    <option value="report">Report</option>
                    <option value="import">Import</option>
                </select>
            </div>

            <div class="space-y-2">
                <label for="uploadDescription" class="text-sm font-medium text-foreground">Deskripsi</label>
                <textarea id="uploadDescription" name="description" rows="3" placeholder="Masukkan deskripsi file..." class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"></textarea>
            </div>

            <div id="uploadProgress" class="hidden space-y-2">
                <div class="w-full bg-muted rounded-full h-2 overflow-hidden">
                    <div id="uploadProgressBar" class="bg-primary h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p class="text-xs text-muted-foreground text-center">Uploading...</p>
            </div>

            <div id="uploadResult" class="hidden rounded-lg p-3 text-sm"></div>

            <div class="flex gap-3 pt-4 border-t border-border/50">
                <button type="button" onclick="closeUploadModal()" class="flex-1 h-10 rounded-lg border border-border/50 text-foreground font-medium hover:bg-muted transition">
                    Batal
                </button>
                <button type="submit" id="uploadSubmit" class="flex-1 h-10 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition flex items-center justify-center gap-2">
                    <?= icon('Plus', 'h-4 w-4') ?>
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Upload Modal -->
<div id="bulkUploadModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center" onclick="closeBulkUploadModal(event)">
    <div class="bg-background rounded-lg shadow-lg border border-border max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6 border-b border-border/50 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-foreground">Bulk Upload File</h2>
            <button type="button" onclick="closeBulkUploadModal()" class="text-muted-foreground hover:text-foreground">
                <?= icon('X', 'h-5 w-5') ?>
            </button>
        </div>

        <form id="bulkUploadForm" class="p-6 space-y-4">
            <?= csrf_field() ?>
            <div class="space-y-2">
                <label for="files" class="text-sm font-medium text-foreground">Pilih File</label>
                <input type="file" id="files" name="files[]" multiple required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary">
                <p class="text-xs text-muted-foreground">Pilih multiple files. Ukuran maksimal: 10MB per file.</p>
            </div>

            <div class="space-y-2">
                <label for="bulkCategory" class="text-sm font-medium text-foreground">Kategori</label>
                <select id="bulkCategory" name="category" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <option value="general">General</option>
                    <option value="document">Document</option>
                    <option value="image">Image</option>
                    <option value="report">Report</option>
                    <option value="import">Import</option>
                </select>
            </div>

            <div class="space-y-2">
                <label for="bulkDescription" class="text-sm font-medium text-foreground">Deskripsi</label>
                <textarea id="bulkDescription" name="description" rows="3" placeholder="Masukkan deskripsi file..." class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"></textarea>
            </div>

            <div id="bulkUploadProgress" class="hidden space-y-2">
                <div class="w-full bg-muted rounded-full h-2 overflow-hidden">
                    <div id="bulkUploadProgressBar" class="bg-primary h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p class="text-xs text-muted-foreground text-center">Uploading...</p>
            </div>

            <div id="bulkUploadResult" class="hidden rounded-lg p-3 text-sm"></div>

            <div class="flex gap-3 pt-4 border-t border-border/50">
                <button type="button" onclick="closeBulkUploadModal()" class="flex-1 h-10 rounded-lg border border-border/50 text-foreground font-medium hover:bg-muted transition">
                    Batal
                </button>
                <button type="submit" id="bulkUploadSubmit" class="flex-1 h-10 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition flex items-center justify-center gap-2">
                    <?= icon('Plus', 'h-4 w-4') ?>
                    Upload Semua
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
}

function closeUploadModal(event) {
    if (event && event.target !== document.getElementById('uploadModal')) return;
    document.getElementById('uploadModal').classList.add('hidden');
    resetUploadForm();
}

function openBulkUploadModal() {
    document.getElementById('bulkUploadModal').classList.remove('hidden');
}

function closeBulkUploadModal(event) {
    if (event && event.target !== document.getElementById('bulkUploadModal')) return;
    document.getElementById('bulkUploadModal').classList.add('hidden');
    resetBulkUploadForm();
}

function resetUploadForm() {
    document.getElementById('uploadForm').reset();
    document.getElementById('uploadProgress').classList.add('hidden');
    document.getElementById('uploadResult').classList.add('hidden');
    document.getElementById('uploadSubmit').disabled = false;
}

function resetBulkUploadForm() {
    document.getElementById('bulkUploadForm').reset();
    document.getElementById('bulkUploadProgress').classList.add('hidden');
    document.getElementById('bulkUploadResult').classList.add('hidden');
    document.getElementById('bulkUploadSubmit').disabled = false;
}

// Single file upload
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const progressDiv = document.getElementById('uploadProgress');
    const resultDiv = document.getElementById('uploadResult');
    const submitBtn = document.getElementById('uploadSubmit');
    
    progressDiv.classList.remove('hidden');
    resultDiv.classList.add('hidden');
    submitBtn.disabled = true;
    
    fetch('<?= base_url('/info/files/upload') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        progressDiv.classList.add('hidden');
        resultDiv.classList.remove('hidden');
        
        if (data.status === 'success') {
            resultDiv.className = 'rounded-lg p-3 text-sm bg-success/10 text-success border border-success/30';
            resultDiv.innerHTML = '<strong>Sukses:</strong> ' + data.message;
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            resultDiv.className = 'rounded-lg p-3 text-sm bg-destructive/10 text-destructive border border-destructive/30';
            resultDiv.innerHTML = '<strong>Error:</strong> ' + data.message;
        }
        
        submitBtn.disabled = false;
    })
    .catch(error => {
        progressDiv.classList.add('hidden');
        resultDiv.classList.remove('hidden');
        resultDiv.className = 'rounded-lg p-3 text-sm bg-destructive/10 text-destructive border border-destructive/30';
        resultDiv.innerHTML = '<strong>Error:</strong> Upload gagal: ' + error.message;
        submitBtn.disabled = false;
    });
});

// Bulk file upload
document.getElementById('bulkUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const progressDiv = document.getElementById('bulkUploadProgress');
    const resultDiv = document.getElementById('bulkUploadResult');
    const submitBtn = document.getElementById('bulkUploadSubmit');
    
    progressDiv.classList.remove('hidden');
    resultDiv.classList.add('hidden');
    submitBtn.disabled = true;
    
    fetch('<?= base_url('/info/files/bulk-upload') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        progressDiv.classList.add('hidden');
        resultDiv.classList.remove('hidden');
        
        if (data.status === 'success') {
            resultDiv.className = 'rounded-lg p-3 text-sm bg-success/10 text-success border border-success/30';
            let html = '<strong>Sukses:</strong> ' + data.message + '<br>';
            
            if (data.uploaded_files && data.uploaded_files.length > 0) {
                html += '<strong class="block mt-2">File terupload:</strong><ul class="list-disc list-inside mt-1">';
                data.uploaded_files.forEach(file => {
                    html += '<li>' + file.original_name + '</li>';
                });
                html += '</ul>';
            }
            
            if (data.errors && data.errors.length > 0) {
                html += '<strong class="block mt-2">Errors:</strong><ul class="list-disc list-inside mt-1">';
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
            resultDiv.className = 'rounded-lg p-3 text-sm bg-destructive/10 text-destructive border border-destructive/30';
            resultDiv.innerHTML = '<strong>Error:</strong> ' + data.message;
        }
        
        submitBtn.disabled = false;
    })
    .catch(error => {
        progressDiv.classList.add('hidden');
        resultDiv.classList.remove('hidden');
        resultDiv.className = 'rounded-lg p-3 text-sm bg-destructive/10 text-destructive border border-destructive/30';
        resultDiv.innerHTML = '<strong>Error:</strong> Upload gagal: ' + error.message;
        submitBtn.disabled = false;
    });
});

function viewFile(fileId) {
    window.open('<?= base_url('/info/files/view/') ?>' + fileId, '_blank');
}

function deleteFile(fileId, fileName) {
    if (confirm('Yakin ingin menghapus "' + fileName + '"?')) {
        window.location.href = '<?= base_url('/info/files/delete/') ?>' + fileId;
    }
}
</script>

<?= $this->endSection() ?>
