/**
 * Supplier Manager - Simplified with CrudMixin
 */
function supplierManager() {
    const config = window.__PAGE_CONFIG__ || {};

    return {
        ...CrudMixin,
        items: config.suppliers || [],
        search: '',
        filters: {},
        isDialogOpen: false,
        isEditDialogOpen: false,
        isSubmitting: false,
        isEditSubmitting: false,
        errors: {},
        editErrors: {},
        editingSupplier: {},

        get suppliers() {
            return this.filteredItems;
        },

        get activeCount() {
            return this.items.filter(s => s.status === 'active' || s.is_active === 1).length;
        },

        get totalDebt() {
            return this.items.reduce((sum, s) => sum + (parseFloat(s.debt_balance) || 0), 0);
        },

        openEditModal(supplier) {
            this.editingSupplier = JSON.parse(JSON.stringify(supplier));
            this.editErrors = {};
            this.isEditDialogOpen = true;
        },

        async submitForm(event) {
            await CrudMixin.submitForm.call(this, event, {
                successMsg: 'Supplier berhasil ditambahkan'
            });
        },

        async submitEditForm(event) {
            event.preventDefault();
            this.editErrors = {};
            this.isEditSubmitting = true;

            try {
                const form = event.target;
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (response.ok) {
                    if (typeof ModalManager !== 'undefined') {
                        ModalManager.success('Supplier berhasil diperbarui', () => window.location.reload());
                    } else {
                        alert('Supplier berhasil diperbarui');
                        window.location.reload();
                    }
                } else if (response.status === 422) {
                    this.editErrors = (await response.json()).errors || {};
                }
            } catch (error) {
                if (typeof ModalManager !== 'undefined') {
                    ModalManager.error('Terjadi kesalahan: ' + error.message);
                }
            } finally {
                this.isEditSubmitting = false;
            }
        },

        deleteSupplier(id) {
            const supplier = this.items.find(s => s.id === id);
            CrudMixin.deleteItem.call(this, id, supplier?.name, `${config.baseUrl}/${id}`);
        },

        exportData() {
            window.location.href = config.exportUrl || `${config.baseUrl}/export-pdf`;
        }
    };
}

document.addEventListener('alpine:init', () => {
    Alpine.data('supplierManager', supplierManager);
});
