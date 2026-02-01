// Toast Notification System
const Toast = {
    container: null,
    
    init() {
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        }
    },
    
    show(message, type = 'info', title = null, duration = 5000) {
        this.init();
        
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        const icons = {
            success: '<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
            error: '<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
            warning: '<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
            info: '<svg class="toast-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        };
        
        toast.innerHTML = `
            ${icons[type] || icons.info}
            <div class="toast-content">
                ${title ? `<div class="toast-title">${title}</div>` : ''}
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="Toast.remove(this.parentElement)">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        
        this.container.appendChild(toast);
        
        if (duration > 0) {
            setTimeout(() => this.remove(toast), duration);
        }
        
        return toast;
    },
    
    remove(toast) {
        toast.classList.add('removing');
        setTimeout(() => {
            toast.remove();
            if (this.container.children.length === 0) {
                this.container.remove();
                this.container = null;
            }
        }, 300);
    },
    
    success(message, title = 'Berhasil') {
        return this.show(message, 'success', title);
    },
    
    error(message, title = 'Error') {
        return this.show(message, 'error', title);
    },
    
    warning(message, title = 'Peringatan') {
        return this.show(message, 'warning', title);
    },
    
    info(message, title = null) {
        return this.show(message, 'info', title);
    }
};

// HTMX Integration
document.addEventListener('htmx:afterSwap', function(event) {
    const response = event.detail.xhr.response;
    try {
        const data = JSON.parse(response);
        if (data.toast) {
            Toast[data.toast.type](data.toast.message, data.toast.title);
        }
    } catch (e) {
        // Not JSON, ignore
    }
});

// Flash message support
document.addEventListener('DOMContentLoaded', function() {
    // Check for flash messages from PHP session
    const flashSuccess = document.querySelector('[data-flash-success]');
    const flashError = document.querySelector('[data-flash-error]');
    const flashWarning = document.querySelector('[data-flash-warning]');
    const flashInfo = document.querySelector('[data-flash-info]');
    
    if (flashSuccess) {
        Toast.success(flashSuccess.dataset.flashSuccess);
        flashSuccess.remove();
    }
    if (flashError) {
        Toast.error(flashError.dataset.flashError);
        flashError.remove();
    }
    if (flashWarning) {
        Toast.warning(flashWarning.dataset.flashWarning);
        flashWarning.remove();
    }
    if (flashInfo) {
        Toast.info(flashInfo.dataset.flashInfo);
        flashInfo.remove();
    }
});
