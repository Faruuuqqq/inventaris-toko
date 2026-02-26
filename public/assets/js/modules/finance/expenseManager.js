/**
 * Expense Manager Module
 * Vanilla JS for expense management
 * 
 * Dependencies:
 * - ModalManager (from modal.js)
 * - formatCurrency, formatDate (from utils.js)
 */

let deleteExpenseId = null;

function expenseManager() {
    const config = window.__PAGE_CONFIG__ || {};

    return {
        init() {
            this.loadExpenses();
        },

        async loadExpenses() {
            const category = document.getElementById('categoryFilter')?.value || '';
            const startDate = document.getElementById('startDate')?.value || '';
            const endDate = document.getElementById('endDate')?.value || '';
            const paymentMethod = document.getElementById('paymentMethod')?.value || '';

            const params = new URLSearchParams({
                category: category,
                start_date: startDate,
                end_date: endDate,
                payment_method: paymentMethod
            });

            try {
                const response = await fetch(`${config.dataUrl}?${params.toString()}`);
                const result = await response.json();
                
                this.renderExpenses(result.data);
                document.getElementById('totalAmount').textContent = this.formatCurrency(result.total);
                document.getElementById('totalCount').textContent = result.count;
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal memuat data');
            }
        },

        renderExpenses(expenses) {
            const tbody = document.getElementById('expensesTable');
            const categories = config.categories || {};

            if (!expenses || expenses.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-muted-foreground">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="h-8 w-8 opacity-50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <p>Tidak ada data</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            const methodLabel = {
                'CASH': 'Tunai',
                'TRANSFER': 'Transfer',
                'CHECK': 'Cek/Giro'
            };

            tbody.innerHTML = expenses.map(expense => `
                <tr class="hover:bg-muted/50 transition">
                    <td class="px-6 py-4 font-mono text-sm text-primary">${this.escapeHtml(expense.expense_number)}</td>
                    <td class="px-6 py-4 text-sm text-muted-foreground">${this.formatDate(expense.expense_date)}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-warning/15 text-warning">
                            ${categories[expense.category] || expense.category}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-foreground">${this.escapeHtml(expense.description)}</td>
                    <td class="px-6 py-4 text-center text-sm text-muted-foreground">${methodLabel[expense.payment_method] || expense.payment_method}</td>
                    <td class="px-6 py-4 text-right font-bold text-warning">${this.formatCurrency(expense.amount)}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="${config.baseUrl}/edit/${expense.id}" 
                               class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-border text-muted-foreground hover:text-primary hover:border-primary/50 transition"
                               title="Edit">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <button x-on:click="confirmDelete(${expense.id}, '${this.escapeHtml(expense.expense_number)}')"
                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-destructive/50 text-destructive hover:bg-destructive/10 transition"
                                    title="Hapus">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        },

        resetFilters() {
            document.getElementById('categoryFilter').value = '';
            document.getElementById('startDate').value = config.defaultStartDate;
            document.getElementById('endDate').value = config.defaultEndDate;
            document.getElementById('paymentMethod').value = '';
            this.loadExpenses();
        },

        confirmDelete(id, expenseNumber) {
            deleteExpenseId = id;
            document.getElementById('deleteExpenseInfo').textContent = 'No. Biaya: ' + expenseNumber;
            document.getElementById('deleteModal').classList.remove('hidden');
        },

        closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            deleteExpenseId = null;
        },

        async performDelete() {
            if (!deleteExpenseId) return;

            try {
                const response = await fetch(`${config.baseUrl}/delete/${deleteExpenseId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': typeof getCsrfToken === 'function' ? getCsrfToken() : ''
                    }
                });

                const result = await response.json();
                this.closeDeleteModal();
                
                if (result.success) {
                    this.loadExpenses();
                } else {
                    alert(result.message || 'Gagal menghapus data');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal menghapus data');
            }
        },

        formatCurrency(amount) {
            if (typeof formatCurrency === 'function') {
                return formatCurrency(amount);
            }
            return 'Rp ' + parseFloat(amount || 0).toLocaleString('id-ID');
        },

        formatDate(dateStr) {
            if (typeof formatDate === 'function') {
                return formatDate(dateStr);
            }
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },

        escapeHtml(text) {
            if (typeof escapeHtml === 'function') {
                return escapeHtml(text);
            }
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    const manager = expenseManager();
    manager.init();

    // Make functions globally available for onclick handlers
    window.loadExpenses = () => manager.loadExpenses();
    window.resetFilters = () => manager.resetFilters();
    window.confirmDelete = (id, num) => manager.confirmDelete(id, num);
    window.closeDeleteModal = () => manager.closeDeleteModal();
    window.performDelete = () => manager.performDelete();
});

if (typeof module !== 'undefined' && module.exports) {
    module.exports = { expenseManager };
}
