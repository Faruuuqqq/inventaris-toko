/**
 * File Manager Module
 * Vanilla JS module for file management
 * 
 * Dependencies:
 * - None (self-contained)
 */

(function() {
    const config = window.__PAGE_CONFIG__ || {};

    const fileManager = {
        openUploadModal: function() {
            document.getElementById('uploadModal').classList.remove('hidden');
        },

        closeUploadModal: function(event) {
            if (event && event.target !== document.getElementById('uploadModal')) return;
            document.getElementById('uploadModal').classList.add('hidden');
            fileManager.resetUploadForm();
        },

        openBulkUploadModal: function() {
            document.getElementById('bulkUploadModal').classList.remove('hidden');
        },

        closeBulkUploadModal: function(event) {
            if (event && event.target !== document.getElementById('bulkUploadModal')) return;
            document.getElementById('bulkUploadModal').classList.add('hidden');
            fileManager.resetBulkUploadForm();
        },

        resetUploadForm: function() {
            document.getElementById('uploadForm').reset();
            document.getElementById('uploadProgress').classList.add('hidden');
            document.getElementById('uploadResult').classList.add('hidden');
            document.getElementById('uploadSubmit').disabled = false;
        },

        resetBulkUploadForm: function() {
            document.getElementById('bulkUploadForm').reset();
            document.getElementById('bulkUploadProgress').classList.add('hidden');
            document.getElementById('bulkUploadResult').classList.add('hidden');
            document.getElementById('bulkUploadSubmit').disabled = false;
        },

        handleSingleUpload: function(e) {
            e.preventDefault();

            const formData = new FormData(e.target);
            const progressDiv = document.getElementById('uploadProgress');
            const resultDiv = document.getElementById('uploadResult');
            const submitBtn = document.getElementById('uploadSubmit');

            progressDiv.classList.remove('hidden');
            resultDiv.classList.add('hidden');
            submitBtn.disabled = true;

            fetch(config.uploadUrl || '/info/files/upload', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                progressDiv.classList.add('hidden');
                resultDiv.classList.remove('hidden');

                if (data.status === 'success') {
                    resultDiv.className = 'rounded-lg p-3 text-sm bg-success/10 text-success border border-success/50';
                    resultDiv.innerHTML = '<strong>Sukses:</strong> ' + data.message;
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    resultDiv.className = 'rounded-lg p-3 text-sm bg-destructive/10 text-destructive border border-destructive/50';
                    resultDiv.innerHTML = '<strong>Error:</strong> ' + data.message;
                }

                submitBtn.disabled = false;
            })
            .catch(error => {
                progressDiv.classList.add('hidden');
                resultDiv.classList.remove('hidden');
                resultDiv.className = 'rounded-lg p-3 text-sm bg-destructive/10 text-destructive border border-destructive/50';
                resultDiv.innerHTML = '<strong>Error:</strong> Upload gagal: ' + error.message;
                submitBtn.disabled = false;
            });
        },

        handleBulkUpload: function(e) {
            e.preventDefault();

            const formData = new FormData(e.target);
            const progressDiv = document.getElementById('bulkUploadProgress');
            const resultDiv = document.getElementById('bulkUploadResult');
            const submitBtn = document.getElementById('bulkUploadSubmit');

            progressDiv.classList.remove('hidden');
            resultDiv.classList.add('hidden');
            submitBtn.disabled = true;

            fetch(config.bulkUploadUrl || '/info/files/bulk-upload', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                progressDiv.classList.add('hidden');
                resultDiv.classList.remove('hidden');

                if (data.status === 'success') {
                    resultDiv.className = 'rounded-lg p-3 text-sm bg-success/10 text-success border border-success/50';
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
                    resultDiv.className = 'rounded-lg p-3 text-sm bg-destructive/10 text-destructive border border-destructive/50';
                    resultDiv.innerHTML = '<strong>Error:</strong> ' + data.message;
                }

                submitBtn.disabled = false;
            })
            .catch(error => {
                progressDiv.classList.add('hidden');
                resultDiv.classList.remove('hidden');
                resultDiv.className = 'rounded-lg p-3 text-sm bg-destructive/10 text-destructive border border-destructive/50';
                resultDiv.innerHTML = '<strong>Error:</strong> Upload gagal: ' + error.message;
                submitBtn.disabled = false;
            });
        },

        viewFile: function(fileId) {
            window.open((config.viewUrl || '/info/files/view/') + fileId, '_blank');
        },

        deleteFile: function(fileId, fileName) {
            if (confirm('Yakin ingin menghapus "' + fileName + '"?')) {
                window.location.href = (config.deleteUrl || '/info/files/delete/') + fileId;
            }
        },

        init: function() {
            document.addEventListener('DOMContentLoaded', function() {
                const uploadForm = document.getElementById('uploadForm');
                if (uploadForm) {
                    uploadForm.addEventListener('submit', fileManager.handleSingleUpload);
                }

                const bulkUploadForm = document.getElementById('bulkUploadForm');
                if (bulkUploadForm) {
                    bulkUploadForm.addEventListener('submit', fileManager.handleBulkUpload);
                }
            });
        }
    };

    window.openUploadModal = fileManager.openUploadModal;
    window.closeUploadModal = fileManager.closeUploadModal;
    window.openBulkUploadModal = fileManager.openBulkUploadModal;
    window.closeBulkUploadModal = fileManager.closeBulkUploadModal;
    window.viewFile = fileManager.viewFile;
    window.deleteFile = fileManager.deleteFile;

    fileManager.init();
})();
