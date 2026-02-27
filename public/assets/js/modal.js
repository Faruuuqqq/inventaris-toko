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
    modalQueue: [],

    createModalHTML(options) {
        const id = options.id || 'modal_' + Date.now();
        const size = options.size || 'md';
        const variant = options.variant || 'primary';

        return `
            <div x-data="{ open: true }" id="${id}" class="fixed inset-0 z-50" x-cloak>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-40 bg-foreground/80 backdrop-blur-sm"
                     @click="open = false"></div>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4"
                     @keydown.escape="open = false">
                    <div class="card max-w-${size === 'sm' ? 'sm' : size === 'lg' ? 'lg' : 'md'} w-full" @click.stop>
                        <div class="card-header ${variant === 'danger' ? 'bg-destructive/10' : 'bg-primary/10'} flex items-center justify-between">
                            <h2 class="card-title ${variant === 'danger' ? 'text-destructive' : ''}">${options.title}</h2>
                            <button @click="open = false" class="btn btn-ghost btn-sm">✕</button>
                        </div>
                        <div class="card-content">${options.content}</div>
                        <div class="card-footer flex justify-end gap-2">
                            ${options.secondaryButton ? `<button id="${id}_cancel" class="btn btn-ghost">${options.secondaryButton.text}</button>` : ''}
                            <button id="${id}_confirm" class="btn ${variant === 'danger' ? 'btn-destructive' : 'btn-primary'}">${options.primaryButton.text}</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    },

    show(options) {
        const container = document.getElementById('globalModal');
        container.innerHTML = this.createModalHTML(options);

        Alpine.start();

        const modal = container.firstElementChild;
        const cancelBtn = document.getElementById(`${modal.id}_cancel`);
        const confirmBtn = document.getElementById(`${modal.id}_confirm`);

        if (cancelBtn) {
            cancelBtn.onclick = () => {
                Alpine.store('modal').activeModal = null;
                setTimeout(() => container.innerHTML = '', 300);
            };
        }

        if (confirmBtn) {
            confirmBtn.onclick = () => {
                if (options.primaryButton.action) options.primaryButton.action();
            };
        }

        if (options.autoClose) {
            setTimeout(() => {
                Alpine.store('modal').activeModal = null;
                setTimeout(() => container.innerHTML = '', 300);
            }, options.autoClose);
        }
    },

    /**
     * Show delete confirmation modal
     */
    delete(itemName, callback) {
        this.show({
            variant: 'danger',
            title: 'Hapus Data',
            content: `
                <p>Apakah Anda yakin ingin menghapus <strong>${itemName || 'item ini'}</strong>?</p>
                <p class="text-sm text-destructive mt-2 bg-destructive/10 p-2 rounded">Tindakan ini tidak dapat dibatalkan!</p>
            `,
            primaryButton: {
                text: 'Hapus',
                action: () => {
                    if (callback) callback();
                }
            },
            secondaryButton: {
                text: 'Batal'
            }
        });
    },

    /**
     * Show success notification modal (auto-close after 2 seconds)
     */
    success(message, callback) {
        this.show({
            variant: 'success',
            title: 'Sukses',
            content: `<p>${message || 'Data berhasil disimpan'}</p>`,
            primaryButton: {
                text: 'OK',
                action: () => {
                    if (callback) callback();
                }
            }
        });
    },

    /**
     * Show error notification modal
     */
    error(message, callback) {
        this.show({
            variant: 'danger',
            title: 'Error',
            content: `<p class="text-destructive">${message || 'Terjadi kesalahan'}</p>`,
            primaryButton: {
                text: 'Tutup',
                action: () => {
                    if (callback) callback();
                }
            }
        });
    },

    /**
     * Show warning modal
     */
    warning(title, message, onConfirm, proceedText = 'Lanjutkan') {
        this.show({
            variant: 'warning',
            title: title,
            content: `<p>${message}</p>`,
            primaryButton: {
                text: proceedText,
                action: () => {
                    if (onConfirm) onConfirm();
                }
            },
            secondaryButton: {
                text: 'Batal'
            }
        });
    },

    /**
     * Show generic confirm modal
     */
    confirm(title, message, onConfirm, confirmText = 'Konfirmasi', cancelText = 'Batal') {
        this.show({
            title: title,
            content: `<p>${message}</p>`,
            primaryButton: {
                text: confirmText,
                action: () => {
                    if (onConfirm) onConfirm();
                }
            },
            secondaryButton: {
                text: cancelText
            }
        });
    },

    /**
     * Submit delete form with async handling
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
                this.error('Terjadi kesalahan: ' + error.message);
            }
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    window.ModalManager = ModalManager;

    if (!document.documentElement.style.scrollBehavior) {
        document.documentElement.style.scrollBehavior = 'smooth';
    }
});
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
