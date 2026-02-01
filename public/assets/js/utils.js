/**
 * Shared Utility Functions
 * Inventaris Toko Application
 */

/**
 * Format date to Indonesian locale
 * @param {string} dateStr - Date string to format
 * @returns {string} Formatted date
 */
function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID');
}

/**
 * Format date with time to Indonesian locale
 * @param {string} dateStr - DateTime string to format
 * @returns {string} Formatted datetime
 */
function formatDateTime(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID') + ' ' + date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

/**
 * Format number as Indonesian currency (Rupiah)
 * @param {number|string} amount - Amount to format
 * @returns {string} Formatted currency
 */
function formatCurrency(amount) {
    if (amount === null || amount === undefined) return 'Rp 0';
    return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
}

/**
 * Format number with thousand separators
 * @param {number|string} number - Number to format
 * @returns {string} Formatted number
 */
function formatNumber(number) {
    if (number === null || number === undefined) return '0';
    return parseFloat(number).toLocaleString('id-ID');
}

/**
 * Export current page by printing
 */
function exportData() {
    window.print();
}

/**
 * Show loading spinner in a table tbody
 * @param {string} tbodyId - ID of tbody element
 * @param {number} colspan - Number of columns
 */
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

/**
 * Show empty state in a table tbody
 * @param {string} tbodyId - ID of tbody element
 * @param {number} colspan - Number of columns
 * @param {string} message - Message to display
 */
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

/**
 * Show error state in a table tbody
 * @param {string} tbodyId - ID of tbody element
 * @param {number} colspan - Number of columns
 * @param {string} message - Error message
 */
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

/**
 * Fetch data from API with error handling
 * @param {string} url - API URL
 * @param {Object} options - Fetch options
 * @returns {Promise} JSON response
 */
async function fetchApi(url, options = {}) {
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                ...options.headers
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

/**
 * Build URL with query parameters
 * @param {string} baseUrl - Base URL
 * @param {Object} params - Query parameters object
 * @returns {string} URL with query string
 */
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

/**
 * Get status badge class based on status value
 * @param {string} status - Status value
 * @param {Object} mapping - Custom status to class mapping
 * @returns {string} CSS class
 */
function getStatusBadgeClass(status, mapping = null) {
    const defaultMapping = {
        'PAID': 'bg-success',
        'Lunas': 'bg-success',
        'Disetujui': 'bg-success',
        'Diterima Semua': 'bg-success',
        'UNPAID': 'bg-destructive',
        'Belum Lunas': 'bg-destructive',
        'Ditolak': 'bg-destructive',
        'Dibatalkan': 'bg-destructive',
        'PARTIAL': 'bg-warning',
        'Sebagian': 'bg-warning',
        'Pending': 'bg-warning',
        'Dipesan': 'bg-warning'
    };

    const statusMap = mapping || defaultMapping;
    return statusMap[status] || 'bg-secondary';
}

/**
 * Get payment type text in Indonesian
 * @param {string} type - Payment type (CASH/CREDIT)
 * @returns {string} Indonesian text
 */
function getPaymentTypeText(type) {
    const types = {
        'CASH': 'Tunai',
        'CREDIT': 'Kredit'
    };
    return types[type] || type;
}

/**
 * Get payment status text in Indonesian
 * @param {string} status - Payment status
 * @returns {string} Indonesian text
 */
function getPaymentStatusText(status) {
    const statuses = {
        'PAID': 'Lunas',
        'UNPAID': 'Belum Lunas',
        'PARTIAL': 'Sebagian'
    };
    return statuses[status] || status;
}

/**
 * Confirm action with dialog
 * @param {string} message - Confirmation message
 * @returns {boolean} User's choice
 */
function confirmAction(message = 'Apakah Anda yakin?') {
    return confirm(message);
}

/**
 * Show toast notification (basic implementation)
 * @param {string} message - Message to show
 * @param {string} type - Type: success, error, warning, info
 */
function showToast(message, type = 'info') {
    // Basic alert for now, can be replaced with better toast library
    alert(message);
}

/**
 * Debounce function for search inputs
 * @param {Function} func - Function to debounce
 * @param {number} wait - Wait time in ms
 * @returns {Function} Debounced function
 */
function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
