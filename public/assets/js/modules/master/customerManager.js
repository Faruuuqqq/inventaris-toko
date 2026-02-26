/**
 * Customer Manager - Simplified with CrudMixin
 */
function customerManager() {
    const config = window.__PAGE_CONFIG__ || {};

    return {
        ...CrudMixin,
        items: config.customers || [],
        search: '',
        filters: {},
        isDialogOpen: false,
        isEditDialogOpen: false,
        isSubmitting: false,
        isEditSubmitting: false,
        errors: {},
        editErrors: {},
        editingCustomer: {},

        get customers() {
            return this.filteredItems;
        },

        get customersWithPiutang() {
            return this.items.filter(c => parseFloat(c.receivable_balance || 0) > 0).length;
        },

        get totalPiutang() {
            return this.items.reduce((sum, c) => sum + (parseFloat(c.receivable_balance) || 0), 0);
        },

        openEditModal(customer) {
            this.editingCustomer = JSON.parse(JSON.stringify(customer));
            this.editErrors = {};
            this.isEditDialogOpen = true;
        },

        async submitForm(event) {
            await CrudMixin.submitForm.call(this, event, {
                successMsg: 'Pelanggan berhasil ditambahkan'
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
                        ModalManager.success('Pelanggan berhasil diperbarui', () => window.location.reload());
                    } else {
                        alert('Pelanggan berhasil diperbarui');
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

        deleteCustomer(id) {
            const customer = this.items.find(c => c.id === id);
            CrudMixin.deleteItem.call(this, id, customer?.name, `${config.baseUrl}/${id}`);
        },

        exportData() {
            window.location.href = config.exportUrl || `${config.baseUrl}/export-pdf`;
        }
    };
}

document.addEventListener('alpine:init', () => {
    Alpine.data('customerManager', customerManager);
});
