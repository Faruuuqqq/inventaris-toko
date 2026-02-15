/**
 * Notification System JavaScript
 * Handles real-time notifications fetching and updates
 */

class NotificationSystem {
    constructor() {
        this.endpoint = {
            unreadCount: base_url + 'notifications/getUnreadCount',
            recent: base_url + 'notifications/getRecent',
            markRead: base_url + 'notifications/markAsRead',
            checkSystem: base_url + 'notifications/checkSystemNotifications',
            settings: base_url + 'notifications/getSettings',
            updateSettings: base_url + 'notifications/updateSettings'
        };
        
        this.init();
    }
    
    init() {
        // Fetch notifications immediately
        this.fetchNotifications();
        
        // Set up periodic refresh (every 30 seconds)
        setInterval(() => this.fetchNotifications(), 30000);
        
        // Set up event listeners
        this.setupEventListeners();
        
        // Check system notifications on load
        this.checkSystemNotifications();
    }
    
    setupEventListeners() {
        // Mark as read when clicking notification
        document.addEventListener('click', (e) => {
            const notificationItem = e.target.closest('[data-notification-id]');
            if (notificationItem && notificationItem.dataset.link) {
                this.markAsRead(notificationItem.dataset.notificationId);
            }
        });
        
        // Mark all as read
        const markAllReadBtn = document.querySelector('[data-action="mark-all-read"]');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.markAllAsRead();
            });
        }
        
        // Notification settings toggles
        document.querySelectorAll('[data-notification-setting]').forEach(toggle => {
            toggle.addEventListener('change', (e) => {
                this.updateNotificationSetting(
                    e.target.dataset.notificationSetting,
                    e.target.checked
                );
            });
        });
    }
    
    async fetchNotifications() {
        try {
            const [countResponse, recentResponse] = await Promise.all([
                fetch(this.endpoint.unreadCount, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }),
                fetch(this.endpoint.recent, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
            ]);
            
            const countData = await countResponse.json();
            const recentData = await recentResponse.json();
            
            this.updateNotificationBadge(countData.count || 0);
            this.updateNotificationDropdown(recentData.notifications || []);
            
        } catch (error) {
            console.error('Error fetching notifications:', error);
        }
    }
    
    updateNotificationBadge(count) {
        const badge = document.querySelector('[data-notification-badge]');
        if (badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('hidden');
                badge.classList.add('animate-pulse');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('animate-pulse');
            }
        }
    }
    
    updateNotificationDropdown(notifications) {
        const container = document.querySelector('[data-notification-container]');
        if (!container) return;
        
        if (notifications.length === 0) {
            container.innerHTML = `
                <div class="px-4 py-3 hover:bg-primary-lighter/50 cursor-pointer transition-colors border-b border-border last:border-0">
                    <p class="text-sm font-medium text-foreground">Belum ada notifikasi</p>
                    <p class="text-xs text-muted-foreground mt-1">Anda akan menerima pemberitahuan di sini</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        notifications.forEach(notification => {
            html += `
                <a href="${notification.link || '#'}" 
                   class="block px-4 py-3 hover:bg-primary-lighter/50 transition-colors border-b border-border last:border-0 ${notification.is_read ? 'opacity-75' : ''}"
                   data-notification-id="${notification.id}"
                   data-link="${notification.link || ''}">
                    <div class="flex items-start gap-3">
                        <div class="mt-1">
                            ${this.getNotificationIcon(notification.type)}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-foreground ${!notification.is_read ? 'font-semibold' : ''}">${notification.title}</p>
                            <p class="text-sm text-muted-foreground mt-0.5 line-clamp-2">${notification.message}</p>
                            <p class="text-xs text-muted-foreground mt-1">${this.formatTime(notification.created_at)}</p>
                        </div>
                        ${!notification.is_read ? '<div class="w-2 h-2 bg-primary rounded-full mt-2"></div>' : ''}
                    </div>
                </a>
            `;
        });
        
        // Add "Mark all as read" button
        html += `
            <div class="px-4 py-2 border-t border-border">
                <button data-action="mark-all-read" class="text-xs text-primary hover:text-primary/80 transition-colors">
                    Tandai semua sudah dibaca
                </button>
            </div>
        `;
        
        container.innerHTML = html;
    }
    
    getNotificationIcon(type) {
        const icons = {
            'low_stock': '<svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
            'overdue_receivable': '<svg class="w-5 h-5 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'overdue_payable': '<svg class="w-5 h-5 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'pending_po': '<svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h11v-5L4 9v10z"></path></svg>',
            'info': '<svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        };
        
        return icons[type] || icons['info'];
    }
    
    formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMins / 60);
        const diffDays = Math.floor(diffHours / 24);
        
        if (diffMins < 1) return 'Baru saja';
        if (diffMins < 60) return `${diffMins} menit lalu`;
        if (diffHours < 24) return `${diffHours} jam lalu`;
        if (diffDays < 7) return `${diffDays} hari lalu`;
        
        return date.toLocaleDateString('id-ID');
    }
    
    async markAsRead(notificationId) {
        try {
            const response = await fetch(`${this.endpoint.markRead}/${notificationId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content
                }
            });
            
            if (response.ok) {
                this.fetchNotifications(); // Refresh
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }
    
    async markAllAsRead() {
        try {
            const response = await fetch(this.endpoint.markRead, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content
                }
            });
            
            if (response.ok) {
                this.fetchNotifications(); // Refresh
            }
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    }
    
    async checkSystemNotifications() {
        try {
            await fetch(this.endpoint.checkSystem, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content
                }
            });
            
            // After checking, fetch updated notifications
            setTimeout(() => this.fetchNotifications(), 1000);
        } catch (error) {
            console.error('Error checking system notifications:', error);
        }
    }
    
    async updateNotificationSetting(setting, value) {
        try {
            // First get current settings
            const settingsResponse = await fetch(this.endpoint.settings, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const settings = await settingsResponse.json();
            
            // Update the specific setting
            settings.settings[setting] = value;
            
            // Save updated settings
            const response = await fetch(this.endpoint.updateSettings, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify(settings.settings)
            });
            
            if (response.ok) {
                // Show success message
                this.showToast('Pengaturan notifikasi berhasil disimpan', 'success');
            }
        } catch (error) {
            console.error('Error updating notification setting:', error);
            this.showToast('Gagal menyimpan pengaturan', 'error');
        }
    }
    
    showToast(message, type = 'info') {
        // Simple toast implementation
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded-lg text-white text-sm z-50 transition-all transform translate-y-0 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Initialize notification system when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.notificationSystem = new NotificationSystem();
});