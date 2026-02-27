/**
 * Purchase Manager Module
 * Alpine.js component for purchase order management
 * 
 * Dependencies:
 * - Alpine.js
 * - ModalManager (from modal.js)
 * - formatNumber (from utils.js)
 * - formatDate (from utils.js)
 */

function purchaseManager() {
    const config = window.__PAGE_CONFIG__ || {};

    return {
        purchaseOrders: config.purchaseOrders || [],
        search: '',
        supplierFilter: 'all',

        get filteredPurchaseOrders() {
            return this.purchaseOrders.filter(po => {
                const searchLower = this.search.toLowerCase();
                const matchesSearch = (po.nomor_po && po.nomor_po.toLowerCase().includes(searchLower)) ||
                                    (po.name && po.name.toLowerCase().includes(searchLower));
                
                const matchesSupplier = this.supplierFilter === 'all' || 
                                       po.id_supplier == this.supplierFilter;
                                      
                return matchesSearch && matchesSupplier;
            });
        },

        get pendingReceived() {
            return this.purchaseOrders.filter(po => po.status === 'Dipesan' || po.status === 'Sebagian Diterima').length;
        },

        get fullyReceived() {
            return this.purchaseOrders.filter(po => po.status === 'Diterima Semua').length;
        },

        deletePO(poId) {
            const po = this.purchaseOrders.find(p => p.id_po === poId);
            const poNumber = po ? po.nomor_po : 'PO ini';
            const deleteUrl = `${config.baseUrl || '/transactions/purchases/delete'}/${poId}`;

            if (typeof ModalManager !== 'undefined') {
                ModalManager.submitDelete(deleteUrl, poNumber, () => {
                    this.purchaseOrders = this.purchaseOrders.filter(p => p.id_po !== poId);
                });
            } else {
                if (confirm(`Hapus ${poNumber}?`)) {
                    fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': getCsrfToken()
                        }
                    }).then(response => {
                        if (response.ok) {
                            this.purchaseOrders = this.purchaseOrders.filter(p => p.id_po !== poId);
                        }
                    });
                }
            }
        },

        formatNumber(value) {
            if (typeof formatNumber === 'function') {
                return formatNumber(value);
            }
            return parseFloat(value || 0).toLocaleString('id-ID');
        },

        formatDate(dateStr) {
            if (typeof formatDate === 'function') {
                return formatDate(dateStr);
            }
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
        }
    };
}

document.addEventListener('alpine:init', () => {
    Alpine.data('purchaseManager', purchaseManager);
});

if (typeof module !== 'undefined' && module.exports) {
    module.exports = { purchaseManager };
}
