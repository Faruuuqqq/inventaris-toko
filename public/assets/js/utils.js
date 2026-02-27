/**
 * Shared Utility Functions
 * Inventaris Toko Application
 * 
 * Only includes functions that are actively used across the application
 */

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID');
}

function formatCurrency(amount) {
    if (amount === null || amount === undefined) return 'Rp 0';
    return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
}

function formatNumber(number) {
    if (number === null || number === undefined) return '0';
    return parseFloat(number).toLocaleString('id-ID');
}

function exportData() {
    window.print();
}

function showTableLoading(tbodyId, colspan) {
    const tbody = document.getElementById(tbodyId);
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="${colspan}" class="text-center text-muted-foreground py-8">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memuat data...
                    </div>
                </td>
            </tr>
        `;
    }
}

function showTableEmpty(tbodyId, colspan, message = 'Tidak ada data') {
    const tbody = document.getElementById(tbodyId);
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="${colspan}" class="text-center text-muted-foreground py-8">
                    ${message}
                </td>
            </tr>
        `;
    }
}

function showTableError(tbodyId, colspan, message = 'Gagal memuat data') {
    const tbody = document.getElementById(tbodyId);
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="${colspan}" class="text-center text-destructive py-8">
                    ${message}
                </td>
            </tr>
        `;
    }
}

function buildUrl(baseUrl, params) {
    const searchParams = new URLSearchParams();
    for (const [key, value] of Object.entries(params)) {
        if (value !== null && value !== undefined && value !== '') {
            searchParams.append(key, value);
        }
    }
    const queryString = searchParams.toString();
    return queryString ? `${baseUrl}?${queryString}` : baseUrl;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getCsrfToken() {
    return document.querySelector('input[name="csrf_token"]')?.value ||
           document.querySelector('meta[name="csrf-token"]')?.content ||
           '';
}
