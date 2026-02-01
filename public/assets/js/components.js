// UI Component Utilities

/**
 * Dropdown Component
 */
class Dropdown {
    constructor(element) {
        this.element = element;
        this.button = element.querySelector('[data-dropdown-toggle]');
        this.menu = element.querySelector('.dropdown-menu');

        if (this.button && this.menu) {
            this.init();
        }
    }

    init() {
        this.button.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggle();
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!this.element.contains(e.target)) {
                this.close();
            }
        });

        // Close on escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.close();
            }
        });
    }

    toggle() {
        this.menu.classList.toggle('show');
    }

    close() {
        this.menu.classList.remove('show');
    }

    open() {
        this.menu.classList.add('show');
    }
}

/**
 * Modal Component
 */
class Modal {
    constructor(element) {
        this.element = element;
        this.backdrop = null;
        this.init();
    }

    init() {
        // Close on backdrop click
        this.element.addEventListener('click', (e) => {
            if (e.target === this.element) {
                this.close();
            }
        });

        // Close on escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen()) {
                this.close();
            }
        });

        // Close buttons
        const closeButtons = this.element.querySelectorAll('[data-modal-close]');
        closeButtons.forEach(button => {
            button.addEventListener('click', () => this.close());
        });
    }

    open() {
        // Create backdrop
        this.backdrop = document.createElement('div');
        this.backdrop.className = 'modal-backdrop';
        document.body.appendChild(this.backdrop);

        // Show modal
        this.element.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        // Trigger animation
        setTimeout(() => {
            this.backdrop.style.opacity = '1';
        }, 10);
    }

    close() {
        // Hide backdrop
        if (this.backdrop) {
            this.backdrop.style.opacity = '0';
            setTimeout(() => {
                this.backdrop.remove();
                this.backdrop = null;
            }, 200);
        }

        // Hide modal
        this.element.style.display = 'none';
        document.body.style.overflow = '';
    }

    isOpen() {
        return this.element.style.display === 'flex';
    }
}

/**
 * Loading State Manager
 */
const LoadingState = {
    /**
     * Show loading state on button
     * @param {HTMLButtonElement} button
     */
    showButton(button) {
        button.disabled = true;
        button.classList.add('btn-loading');
        button.dataset.originalText = button.innerHTML;
    },

    /**
     * Hide loading state on button
     * @param {HTMLButtonElement} button
     */
    hideButton(button) {
        button.disabled = false;
        button.classList.remove('btn-loading');
        if (button.dataset.originalText) {
            button.innerHTML = button.dataset.originalText;
            delete button.dataset.originalText;
        }
    },

    /**
     * Show loading overlay
     * @param {string} message
     */
    showOverlay(message = 'Loading...') {
        let overlay = document.getElementById('loading-overlay');

        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'loading-overlay';
            overlay.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/50';
            overlay.innerHTML = `
                <div class="bg-card rounded-lg p-6 shadow-xl">
                    <div class="flex items-center gap-3">
                        <div class="animate-spin h-5 w-5 border-2 border-primary border-t-transparent rounded-full"></div>
                        <span class="text-foreground">${message}</span>
                    </div>
                </div>
            `;
            document.body.appendChild(overlay);
        }

        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    },

    /**
     * Hide loading overlay
     */
    hideOverlay() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
};

/**
 * Confirm Dialog
 */
const ConfirmDialog = {
    /**
     * Show confirmation dialog
     * @param {Object} options
     * @returns {Promise<boolean>}
     */
    show(options = {}) {
        const defaults = {
            title: 'Konfirmasi',
            message: 'Apakah Anda yakin?',
            confirmText: 'Ya',
            cancelText: 'Batal',
            type: 'warning' // success, error, warning, info
        };

        const config = { ...defaults, ...options };

        return new Promise((resolve) => {
            // Create dialog
            const dialog = document.createElement('div');
            dialog.className = 'fixed inset-0 z-50 flex items-center justify-center';
            dialog.innerHTML = `
                <div class="modal-backdrop" style="opacity: 0;"></div>
                <div class="bg-card rounded-lg p-6 shadow-xl max-w-md w-full mx-4 relative z-10" style="opacity: 0; transform: scale(0.95);">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            ${this.getIcon(config.type)}
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-foreground mb-2">${config.title}</h3>
                            <p class="text-sm text-muted-foreground">${config.message}</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button class="btn btn-secondary" data-action="cancel">${config.cancelText}</button>
                        <button class="btn btn-${config.type === 'error' ? 'destructive' : 'primary'}" data-action="confirm">${config.confirmText}</button>
                    </div>
                </div>
            `;

            document.body.appendChild(dialog);
            document.body.style.overflow = 'hidden';

            // Animate in
            setTimeout(() => {
                const backdrop = dialog.querySelector('.modal-backdrop');
                const content = dialog.querySelector('.bg-card');
                backdrop.style.transition = 'opacity 0.2s';
                content.style.transition = 'opacity 0.2s, transform 0.2s';
                backdrop.style.opacity = '1';
                content.style.opacity = '1';
                content.style.transform = 'scale(1)';
            }, 10);

            // Handle buttons
            const confirmBtn = dialog.querySelector('[data-action="confirm"]');
            const cancelBtn = dialog.querySelector('[data-action="cancel"]');

            const close = (result) => {
                const backdrop = dialog.querySelector('.modal-backdrop');
                const content = dialog.querySelector('.bg-card');
                backdrop.style.opacity = '0';
                content.style.opacity = '0';
                content.style.transform = 'scale(0.95)';

                setTimeout(() => {
                    dialog.remove();
                    document.body.style.overflow = '';
                    resolve(result);
                }, 200);
            };

            confirmBtn.addEventListener('click', () => close(true));
            cancelBtn.addEventListener('click', () => close(false));

            // Close on escape
            const escapeHandler = (e) => {
                if (e.key === 'Escape') {
                    close(false);
                    document.removeEventListener('keydown', escapeHandler);
                }
            };
            document.addEventListener('keydown', escapeHandler);
        });
    },

    getIcon(type) {
        const icons = {
            success: '<svg class="h-6 w-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
            error: '<svg class="h-6 w-6 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
            warning: '<svg class="h-6 w-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
            info: '<svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        };
        return icons[type] || icons.info;
    }
};

/**
 * Copy to Clipboard
 */
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            if (typeof Toast !== 'undefined') {
                Toast.success('Berhasil disalin ke clipboard');
            }
        }).catch(() => {
            if (typeof Toast !== 'undefined') {
                Toast.error('Gagal menyalin ke clipboard');
            }
        });
    } else {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();

        try {
            document.execCommand('copy');
            if (typeof Toast !== 'undefined') {
                Toast.success('Berhasil disalin ke clipboard');
            }
        } catch (err) {
            if (typeof Toast !== 'undefined') {
                Toast.error('Gagal menyalin ke clipboard');
            }
        }

        document.body.removeChild(textarea);
    }
}

/**
 * Initialize all components
 */
document.addEventListener('DOMContentLoaded', function () {
    // Initialize dropdowns
    document.querySelectorAll('.dropdown').forEach(el => new Dropdown(el));

    // Initialize modals
    document.querySelectorAll('.modal').forEach(el => new Modal(el));

    // Modal triggers
    document.querySelectorAll('[data-modal-target]').forEach(trigger => {
        trigger.addEventListener('click', function () {
            const targetId = this.dataset.modalTarget;
            const modal = document.getElementById(targetId);
            if (modal && modal._modal) {
                modal._modal.open();
            }
        });
    });

    // Copy buttons
    document.querySelectorAll('[data-copy]').forEach(button => {
        button.addEventListener('click', function () {
            const text = this.dataset.copy;
            copyToClipboard(text);
        });
    });

    // Confirm delete buttons
    document.querySelectorAll('[data-confirm-delete]').forEach(button => {
        button.addEventListener('click', async function (e) {
            e.preventDefault();

            const confirmed = await ConfirmDialog.show({
                title: 'Hapus Data',
                message: 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
                confirmText: 'Hapus',
                cancelText: 'Batal',
                type: 'error'
            });

            if (confirmed) {
                // If it's a form, submit it
                if (this.form) {
                    this.form.submit();
                }
                // If it has href, navigate
                else if (this.href) {
                    window.location.href = this.href;
                }
            }
        });
    });
});

// Export for use in other scripts
window.UI = {
    Dropdown,
    Modal,
    LoadingState,
    ConfirmDialog,
    copyToClipboard
};
