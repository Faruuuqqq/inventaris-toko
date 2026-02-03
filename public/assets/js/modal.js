/**
 * Modal Manager
 * Centralized modal management system for consistent UX
 * 
 * Features:
 * - Confirm/Delete/Success/Error/Warning modals
 * - Loading states with spinners
 * - Auto-close for notifications (2 seconds)
 * - Keyboard shortcuts (ESC to close)
 * - CSRF token handling for forms
 */

const ModalManager = {
    /**
     * Open a modal by ID
     */
    open(modalId) {
        const modal = document.querySelector(`.${modalId}`);
        if (modal && modal.__alpine$) {
            modal.__alpine$.getUnobservedData().open = true;
        }
    },

    /**
     * Close a modal by ID
     */
    close(modalId) {
        const modal = document.querySelector(`.${modalId}`);
        if (modal && modal.__alpine$) {
            modal.__alpine$.getUnobservedData().open = false;
        }
    },

    /**
     * Show delete confirmation modal
     * @param {string} itemName - Name of item to delete
     * @param {function} callback - Function to call on confirm
     */
    delete(itemName, callback) {
        // Set item name in modal
        const itemNameEl = document.getElementById('deleteItemName');
        if (itemNameEl) {
            itemNameEl.textContent = itemName || 'item ini';
        }

        // Set callback for confirm button
        const confirmBtn = document.getElementById('deleteConfirmBtn');
        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = 'Hapus';
            confirmBtn.onclick = () => {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<span class="inline-flex items-center gap-2"><span class="animate-spin">⚙️</span>Menghapus...</span>';
                if (callback) callback();
            };
        }

        // Open modal
        this.open('delete-modal');
    },

    /**
     * Show success notification modal (auto-close after 2 seconds)
     * @param {string} message - Success message
     * @param {function} callback - Optional callback when modal closes
     */
    success(message, callback) {
        const messageEl = document.getElementById('successMessage');
        if (messageEl) {
            messageEl.textContent = message || 'Data berhasil disimpan';
        }

        this.open('success-modal');

        // Auto-close after 2 seconds
        setTimeout(() => {
            this.close('success-modal');
            if (callback) callback();
        }, 2000);
    },

    /**
     * Show error notification modal
     * @param {string} message - Error message
     * @param {function} callback - Optional callback when close clicked
     */
    error(message, callback) {
        const messageEl = document.getElementById('errorMessage');
        if (messageEl) {
            messageEl.textContent = message || 'Terjadi kesalahan';
        }

        const closeBtn = document.getElementById('errorCloseBtn');
        if (closeBtn) {
            closeBtn.onclick = () => {
                this.close('error-modal');
                if (callback) callback();
            };
        }

        this.open('error-modal');
    },

    /**
     * Show warning/danger action modal
     * @param {string} title - Modal title
     * @param {string} message - Warning message
     * @param {function} onConfirm - Function to call on proceed
     * @param {string} proceedText - Text for proceed button (default: "Lanjutkan")
     */
    warning(title, message, onConfirm, proceedText = 'Lanjutkan') {
        const titleEl = document.getElementById('warningTitle');
        const messageEl = document.getElementById('warningMessage');
        const proceedBtn = document.getElementById('warningProceedBtn');

        if (titleEl) titleEl.textContent = title;
        if (messageEl) messageEl.textContent = message;
        if (proceedBtn) {
            proceedBtn.textContent = proceedText;
            proceedBtn.disabled = false;
            proceedBtn.onclick = () => {
                proceedBtn.disabled = true;
                proceedBtn.innerHTML = '<span class="inline-flex items-center gap-2"><span class="animate-spin">⚙️</span>Memproses...</span>';
                if (onConfirm) onConfirm();
            };
        }

        this.open('warning-modal');
    },

    /**
     * Show generic confirm modal
     * @param {string} title - Modal title
     * @param {string} message - Confirm message
     * @param {function} onConfirm - Function to call on confirm
     * @param {string} confirmText - Text for confirm button
     * @param {string} cancelText - Text for cancel button
     */
    confirm(title, message, onConfirm, confirmText = 'Konfirmasi', cancelText = 'Batal') {
        const titleEl = document.getElementById('confirmTitle');
        const messageEl = document.getElementById('confirmMessage');
        const confirmBtn = document.getElementById('confirmConfirmBtn');
        const cancelBtn = document.getElementById('confirmCancelBtn');

        if (titleEl) titleEl.textContent = title;
        if (messageEl) messageEl.textContent = message;
        
        if (confirmBtn) {
            confirmBtn.textContent = confirmText;
            confirmBtn.disabled = false;
            confirmBtn.onclick = () => {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<span class="inline-flex items-center gap-2"><span class="animate-spin">⚙️</span>Memproses...</span>';
                if (onConfirm) onConfirm();
            };
        }

        if (cancelBtn) {
            cancelBtn.textContent = cancelText;
            cancelBtn.onclick = () => {
                this.close('confirm-modal');
            };
        }

        this.open('confirm-modal');
    },

    /**
     * Submit delete form with async handling
     * @param {string} deleteUrl - URL to send delete request to
     * @param {string} itemName - Name of item being deleted (for modal)
     * @param {function} onSuccess - Callback on successful delete
     */
    submitDelete(deleteUrl, itemName, onSuccess) {
        this.delete(itemName, async () => {
            try {
                const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || 
                                 document.querySelector('meta[name="csrf-token"]')?.content;
                
                const response = await fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
                    }
                });

                this.close('delete-modal');

                if (response.ok) {
                    this.success('Data berhasil dihapus', () => {
                        if (onSuccess) onSuccess();
                        else window.location.reload();
                    });
                } else {
                    const data = await response.json();
                    this.error(data.message || 'Gagal menghapus data');
                }
            } catch (error) {
                this.close('delete-modal');
                this.error('Terjadi kesalahan: ' + error.message);
            }
        });
    }
};

// Initialize modals when Alpine.js is ready
document.addEventListener('alpine:init', () => {
    // Global Alpine data for modal state
    Alpine.store('modal', {
        isLoading: false,
        activeModal: null
    });
});

// Also initialize on DOMContentLoaded as fallback
document.addEventListener('DOMContentLoaded', () => {
    // Ensure ModalManager is globally available
    window.ModalManager = ModalManager;
    
    // Add smooth scroll behavior if not already set
    if (!document.documentElement.style.scrollBehavior) {
        document.documentElement.style.scrollBehavior = 'smooth';
    }
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ModalManager;
}
