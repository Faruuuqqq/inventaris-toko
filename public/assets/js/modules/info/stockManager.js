/**
 * Stock Manager Module
 * Vanilla JS module for stock balance management
 * 
 * Dependencies:
 * - formatCurrency (from utils.js)
 */

(function() {
    const config = window.__PAGE_CONFIG__ || {};

    const stockManager = {
        loadStockBalance: function() {
            const categoryId = document.getElementById('categoryFilter').value;
            const warehouseId = document.getElementById('warehouseFilter').value;
            const stockStatus = document.getElementById('stockStatus').value;

            const params = new URLSearchParams({
                category_id: categoryId,
                warehouse_id: warehouseId,
                stock_status: stockStatus
            });

            const tbody = document.getElementById('stockTable');
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-sm text-muted-foreground">Memuat data...</p>
                        </div>
                    </td>
                </tr>
            `;

            fetch((config.dataUrl || '/info/saldo/stock-data') + '?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    stockManager.renderStockBalance(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="h-12 w-12 text-destructive/50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p class="text-sm font-medium text-destructive">Gagal memuat data</p>
                                    <p class="text-xs text-muted-foreground">Silakan coba lagi</p>
                                </div>
                            </td>
                        </tr>
                    `;
                });
        },

        renderStockBalance: function(data) {
            const tbody = document.getElementById('stockTable');

            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="h-12 w-12 text-muted-foreground/50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm font-medium text-muted-foreground">Tidak ada data</p>
                                <p class="text-xs text-muted-foreground">Coba ubah filter pencarian Anda</p>
                            </div>
                        </td>
                    </tr>
                `;

                document.getElementById('totalProducts').textContent = '0';
                document.getElementById('totalStock').textContent = '0';
                document.getElementById('stockValue').textContent = 'Rp 0';
                document.getElementById('lowStock').textContent = '0';
                return;
            }

            const totalProducts = new Set(data.map(item => item.product_id)).size;
            const totalStock = data.reduce((sum, item) => sum + parseInt(item.quantity), 0);
            const stockValue = data.reduce((sum, item) => sum + (parseInt(item.quantity) * parseFloat(item.price_buy)), 0);
            const lowStock = data.filter(item => parseInt(item.quantity) <= parseInt(item.min_stock_alert)).length;

            document.getElementById('totalProducts').textContent = totalProducts;
            document.getElementById('totalStock').textContent = totalStock.toLocaleString('id-ID');
            document.getElementById('stockValue').textContent = stockManager.formatCurrency(stockValue);
            document.getElementById('lowStock').textContent = lowStock;

            tbody.innerHTML = data.map(item => {
                const stockClass = parseInt(item.quantity) <= parseInt(item.min_stock_alert) 
                    ? 'text-destructive font-bold' 
                    : 'text-foreground';

                return `
                    <tr class="hover:bg-muted/50 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs font-medium text-muted-foreground">${stockManager.esc(item.product_code)}</td>
                        <td class="px-6 py-4 font-medium text-foreground">${stockManager.esc(item.product_name)}</td>
                        <td class="px-6 py-4 text-muted-foreground">${stockManager.esc(item.category_name || '-')}</td>
                        <td class="px-6 py-4 text-muted-foreground">${stockManager.esc(item.warehouse_name)}</td>
                        <td class="px-6 py-4 text-right ${stockClass}">${item.quantity}</td>
                        <td class="px-6 py-4 text-right text-muted-foreground">${item.min_stock_alert}</td>
                        <td class="px-6 py-4 text-right text-muted-foreground">${stockManager.formatCurrency(item.price_buy)}</td>
                        <td class="px-6 py-4 text-right font-medium text-foreground">${stockManager.formatCurrency(item.quantity * item.price_buy)}</td>
                    </tr>
                `;
            }).join('');
        },

        resetFilters: function() {
            document.getElementById('categoryFilter').value = '';
            document.getElementById('warehouseFilter').value = '';
            document.getElementById('stockStatus').value = '';
            stockManager.loadStockBalance();
        },

        exportData: function() {
            window.print();
        },

        formatCurrency: function(amount) {
            if (typeof formatCurrency === 'function') {
                return formatCurrency(amount);
            }
            return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
        },

        esc: function(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        },

        init: function() {
            document.addEventListener('DOMContentLoaded', function() {
                stockManager.loadStockBalance();
            });
        }
    };

    window.stockManager = stockManager;
    stockManager.init();
})();
