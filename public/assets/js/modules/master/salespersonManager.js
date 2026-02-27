/**
 * Salesperson Manager - Simplified with CrudMixin
 */
function salespersonManager() {
    const config = window.__PAGE_CONFIG__ || {};

    return {
        ...CrudMixin,
        items: config.salespersons || [],
        search: '',
        filters: {},
        isDialogOpen: false,
        isEditDialogOpen: false,
        isSubmitting: false,
        isEditSubmitting: false,
        errors: {},
        editErrors: {},
        editingSalesperson: {},

        get salespersons() {
            return this.filteredItems;
        },

        get activeCount() {
            return this.items.filter(s => s.is_active).length;
        },

        get totalSales() {
            return 0;
        },

        openEditModal(salesperson) {
            this.editingSalesperson = JSON.parse(JSON.stringify(salesperson));
            this.editErrors = {};
            this.isEditDialogOpen = true;
        },

        async submitForm(event) {
            await CrudMixin.submitForm.call(this, event, {
                successMsg: 'Data salesperson berhasil ditambahkan'
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
                        ModalManager.success('Data salesperson berhasil diperbarui', () => window.location.reload());
                    } else {
                        alert('Data salesperson berhasil diperbarui');
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

        deleteSalesperson(id) {
            const salesperson = this.items.find(s => s.id === id);
            CrudMixin.deleteItem.call(this, id, salesperson?.name, `${config.baseUrl}/${id}`);
        }
    };
}

document.addEventListener('alpine:init', () => {
    Alpine.data('salespersonManager', salespersonManager);
});
