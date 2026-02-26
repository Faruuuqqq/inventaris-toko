/**
 * Sales Manager Module
 * Alpine.js component for sales transaction management
 * 
 * Dependencies:
 * - Alpine.js
 * - formatNumber (from utils.js)
 * - formatDate (from utils.js)
 */

function salesManager() {
    const config = window.__PAGE_CONFIG__ || {};

    return {
        sales: config.sales || [],
        search: '',
        customerFilter: 'all',
        paymentTypeFilter: 'all',
        paymentStatusFilter: 'all',

        get filteredSales() {
            return this.sales.filter(sale => {
                const searchLower = this.search.toLowerCase();
                const matchesSearch = (sale.nomor_faktur && sale.nomor_faktur.toLowerCase().includes(searchLower)) ||
                                    (sale.customer_name && sale.customer_name.toLowerCase().includes(searchLower));
                
                const matchesCustomer = this.customerFilter === 'all' || 
                                       sale.id_customer == this.customerFilter;
                
                const matchesPaymentType = this.paymentTypeFilter === 'all' || 
                                         sale.tipe_penjualan === this.paymentTypeFilter;
                
                const matchesPaymentStatus = this.paymentStatusFilter === 'all' || 
                                           sale.status_pembayaran === this.paymentStatusFilter;
                                          
                return matchesSearch && matchesCustomer && matchesPaymentType && matchesPaymentStatus;
            });
        },

        get cashCount() {
            return this.sales.filter(s => s.tipe_penjualan === 'CASH').length;
        },

        get creditCount() {
            return this.sales.filter(s => s.tipe_penjualan === 'CREDIT').length;
        },

        get totalRevenue() {
            return this.sales.reduce((sum, s) => sum + (parseFloat(s.total_penjualan) || 0), 0);
        },

        recordPayment(saleId) {
            alert('Fitur pencatatan pembayaran akan diimplementasikan segera.');
        },

        exportSales() {
            alert('Fitur export akan diimplementasikan segera.');
        },

        getPaymentStatusLabel(status) {
            const labels = {
                'PAID': 'Lunas',
                'PENDING': 'Menunggu',
                'PARTIAL': 'Sebagian',
                'CANCELLED': 'Dibatalkan'
            };
            return labels[status] || status;
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
    Alpine.data('salesManager', salesManager);
});

if (typeof module !== 'undefined' && module.exports) {
    module.exports = { salesManager };
}
