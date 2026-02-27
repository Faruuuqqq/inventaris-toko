/**
 * User Manager - Simplified
 */
function userManager() {
    const config = window.__PAGE_CONFIG__ || {};

    return {
        users: config.users || [],
        sessionRole: config.sessionRole || '',
        sessionUserId: config.sessionUserId || 0,
        search: '',
        filters: { role: 'all' },

        get filteredUsers() {
            return this.users.filter(user => {
                const searchLower = this.search.toLowerCase();
                const matchesSearch = user.username.toLowerCase().includes(searchLower) ||
                                    user.fullname.toLowerCase().includes(searchLower) ||
                                    (user.email && user.email.toLowerCase().includes(searchLower));

                const matchesRole = this.filters.role === 'all' || user.role === this.filters.role;

                return matchesSearch && matchesRole;
            });
        },

        openModal(userId = null) {
            if (userId) {
                const user = this.users.find(u => u.id === userId);
                if (user) {
                    this.editingUser = {
                        id: user.id,
                        username: user.username,
                        email: user.email || '',
                        fullname: user.fullname,
                        role: user.role,
                        password: ''
                    };
                }
            } else {
                this.editingUser = { id: null, username: '', email: '', fullname: '', role: '', password: '' };
            }
            this.isDialogOpen = true;
        },

        deleteUser(userId) {
            const user = this.users.find(u => u.id === userId);
            const deleteUrl = `${config.baseUrl}/delete/${userId}`;
            CrudMixin.deleteItem.call(this, userId, user?.fullname, deleteUrl);
        },

        submitForm(e) {
            e.preventDefault();
            const form = e.target;
            const action = this.editingUser.id
                ? `${config.baseUrl}/update/${this.editingUser.id}`
                : `${config.baseUrl}/store`;
            form.action = action;
            form.submit();
        }
    };
}

document.addEventListener('alpine:init', () => {
    Alpine.data('userManager', userManager);
});
