/**
 * Warehouse Manager - Simplified with CrudMixin
 */
function warehouseManager() {
    const config = window.__PAGE_CONFIG__ || {};

    return {
        ...CrudMixin,
        items: config.warehouses || [],
        search: '',
        filters: {},
        isDialogOpen: false,
        isEditDialogOpen: false,
        isSubmitting: false,
        isEditSubmitting: false,
        errors: {},
        editErrors: {},
        editingWarehouse: {},

        get warehouses() {
            return this.filteredItems;
        },

        get activeCount() {
            return this.items.filter(w => parseInt(w.is_active) === 1).length;
        },

        openEditModal(warehouse) {
            this.editingWarehouse = JSON.parse(JSON.stringify(warehouse));
            this.editErrors = {};
            this.isEditDialogOpen = true;
        },

        async submitForm(event) {
            await CrudMixin.submitForm.call(this, event, {
                successMsg: 'Data gudang berhasil ditambahkan'
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

                const data = await response.json();
                if (response.ok) {
                    if (typeof ModalManager !== 'undefined') {
                        ModalManager.success('Data gudang berhasil diperbarui', () => window.location.reload());
                    } else {
                        alert('Data gudang berhasil diperbarui');
                        window.location.reload();
                    }
                } else if (response.status === 422) {
                    this.editErrors = data.errors || {};
                } else {
                    throw new Error(data.message || 'Gagal memperbarui data');
                }
            } catch (error) {
                if (typeof ModalManager !== 'undefined') {
                    ModalManager.error('Terjadi kesalahan: ' + error.message);
                } else {
                    alert('Terjadi kesalahan: ' + error.message);
                }
            } finally {
                this.isEditSubmitting = false;
            }
        },

        deleteWarehouse(id) {
            const warehouse = this.items.find(w => w.id === id);
            CrudMixin.deleteItem.call(this, id, warehouse?.name, `${config.baseUrl}/${id}`);
        }
    };
}

document.addEventListener('alpine:init', () => {
    Alpine.data('warehouseManager', warehouseManager);
});
