/**
 * Product Manager - Simplified with CrudMixin
 */
function productManager() {
    const config = window.__PAGE_CONFIG__ || {};

    return {
        ...CrudMixin,
        items: config.products || [],
        search: '',
        filters: { category_name: 'all' },
        isDialogOpen: false,
        isSubmitting: false,
        errors: {},

        get products() {
            return this.filteredItems;
        },

        get categoryFilter() {
            return this.filters.category_name;
        },

        set categoryFilter(value) {
            this.filters.category_name = value;
        },

        openModal() {
            this.errors = {};
            const form = document.querySelector('form[action*="master/products"]');
            if (form) form.reset();
            this.isDialogOpen = true;
        },

        async submitForm(event) {
            await CrudMixin.submitForm.call(this, event, {
                successMsg: 'Data produk berhasil ditambahkan'
            });
        },

        editProduct(id) {
            window.location.href = `${config.baseUrl}/edit/${id}`;
        },

        deleteProduct(id) {
            const product = this.items.find(p => p.id === id);
            CrudMixin.deleteItem.call(this, id, product?.name, `${config.baseUrl}/${id}`);
        },

        async exportData() {
            try {
                const exportUrl = config.exportUrl || `${config.baseUrl}/export-pdf`;
                const response = await fetch(exportUrl);
                if (!response.ok) throw new Error('Gagal mengunduh file');

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'products_export.pdf';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            } catch (error) {
                if (typeof ModalManager !== 'undefined') {
                    ModalManager.error('Gagal mengekspor data');
                } else {
                    alert('Gagal mengekspor data');
                }
            }
        }
    };
}

document.addEventListener('alpine:init', () => {
    Alpine.data('productManager', productManager);
});
