/**
 * Shared CRUD Mixin for Alpine.js Components
 * Reduces code duplication across master data modules
 */

const CrudMixin = {
    init() {
        this.config = window.__PAGE_CONFIG__ || {};
        this.items = this.config.items || [];
    },

    get filteredItems() {
        return this.items.filter(item => {
            const searchLower = this.search.toLowerCase();
            const searchFields = this.config.searchFields || ['name'];
            const matchesSearch = searchFields.some(field => 
                (item[field] || '').toLowerCase().includes(searchLower)
            );

            if (!matchesSearch) return false;

            for (const [key, value] of Object.entries(this.filters || {})) {
                if (value && value !== 'all' && item[key] != value) {
                    return false;
                }
            }
            return true;
        });
    },

    async submitForm(event, options = {}) {
        event.preventDefault();
        const form = event.target;

        this.errors = {};
        this.isSubmitting = true;

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = response.status !== 204 ? await response.json() : {};

            if (response.ok || response.status === 201) {
                const successMsg = options.successMsg || 'Data berhasil disimpan';
                if (typeof ModalManager !== 'undefined') {
                    ModalManager.success(successMsg, () => window.location.reload());
                } else {
                    alert(successMsg);
                    window.location.reload();
                }
            } else if (response.status === 422) {
                this.errors = data.errors || {};
                if (typeof ModalManager !== 'undefined') {
                    ModalManager.error(data.message || 'Validasi gagal');
                }
            } else {
                throw new Error(data.message || 'Gagal menyimpan data');
            }
        } catch (error) {
            console.error('Form error:', error);
            if (typeof ModalManager !== 'undefined') {
                ModalManager.error('Terjadi kesalahan: ' + error.message);
            } else {
                alert('Terjadi kesalahan: ' + error.message);
            }
        } finally {
            this.isSubmitting = false;
        }
    },

    deleteItem(id, name, deleteUrl) {
        const itemName = name || 'item ini';
        if (typeof ModalManager !== 'undefined') {
            ModalManager.submitDelete(deleteUrl, itemName, () => {
                this.items = this.items.filter(item => item.id !== id);
            });
        } else if (confirm(`Hapus ${itemName}?`)) {
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            }).then(response => {
                if (response.ok) {
                    this.items = this.items.filter(item => item.id !== id);
                }
            });
        }
    },

    formatRupiah(number) {
        if (typeof formatCurrency === 'function') {
            return formatCurrency(number);
        }
        return 'Rp ' + parseFloat(number || 0).toLocaleString('id-ID');
    },

    formatTanggal(dateStr) {
        if (typeof formatDate === 'function') {
            return formatDate(dateStr);
        }
        if (!dateStr) return '-';
        return new Date(dateStr).toLocaleDateString('id-ID');
    }
};
