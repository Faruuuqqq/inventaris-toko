<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<script>
function userManager() {
    return {
        users: <?= json_encode($users) ?>,
        sessionRole: '<?= session()->get("role") ?>',
        sessionUserId: <?= session()->get('user_id') ?>,
        search: '',
        roleFilter: 'all',
        isDialogOpen: false,
        editingUser: {
            id: null,
            username: '',
            email: '',
            fullname: '',
            role: '',
            password: ''
        },

        get filteredUsers() {
            return this.users.filter(user => {
                const searchLower = this.search.toLowerCase();
                const matchesSearch = user.username.toLowerCase().includes(searchLower) ||
                                    user.fullname.toLowerCase().includes(searchLower) ||
                                    (user.email && user.email.toLowerCase().includes(searchLower));

                const matchesRole = this.roleFilter === 'all' ||
                                   user.role === this.roleFilter;

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
                this.editingUser = {
                    id: null,
                    username: '',
                    email: '',
                    fullname: '',
                    role: '',
                    password: ''
                };
            }
            this.isDialogOpen = true;
        },

        editUser(userId) {
            this.openModal(userId);
        },

        deleteUser(userId) {
            const user = this.users.find(u => u.id === userId);
            const userName = user ? user.fullname : 'pengguna ini';
            ModalManager.submitDelete(
                `<?= base_url('master/users/delete') ?>/${userId}`,
                userName,
                () => {
                    this.users = this.users.filter(u => u.id !== userId);
                }
            );
        },

        submitForm(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            const action = this.editingUser.id
                ? `<?= base_url('master/users/update') ?>/${this.editingUser.id}`
                : '<?= base_url('master/users/store') ?>';

            form.action = action;
            form.submit();
        }
    }
}
</script>

<div x-data="userManager()">
    <!-- Page Header -->
    <div class="mb-8 flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground">Manajemen Pengguna</h2>
            <p class="mt-1 text-muted-foreground">Kelola akses dan peran pengguna dalam sistem</p>
        </div>
    </div>

    <!-- Summary Cards - Compact Grid -->
    <div class="mb-8 grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
        <!-- Total Users -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-success/5 to-transparent p-5 hover:border-success/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Pengguna</p>
                    <p class="mt-2 text-2xl font-bold text-foreground"><?= count($users) ?></p>
                    <p class="mt-1 text-xs text-muted-foreground">aktif</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success/10">
                    <svg class="h-5 w-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm0 0h6v-2a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-blue/5 to-transparent p-5 hover:border-blue/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Pengguna Aktif</p>
                    <p class="mt-2 text-2xl font-bold text-foreground"><?= count(array_filter($users, fn($u) => ($u['status'] ?? 'active') === 'active')) ?></p>
                    <p class="mt-1 text-xs text-muted-foreground">status</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue/10">
                    <svg class="h-5 w-5 text-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Admin Count -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-destructive/5 to-transparent p-5 hover:border-destructive/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Pengguna Admin</p>
                    <p class="mt-2 text-2xl font-bold text-foreground"><?= count(array_filter($users, fn($u) => $u['role'] === 'ADMIN')) ?></p>
                    <p class="mt-1 text-xs text-muted-foreground">role</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-destructive/10">
                    <svg class="h-5 w-5 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Owner Count -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-warning/5 to-transparent p-5 hover:border-warning/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Pemilik Akun</p>
                    <p class="mt-2 text-2xl font-bold text-foreground"><?= count(array_filter($users, fn($u) => $u['role'] === 'OWNER')) ?></p>
                    <p class="mt-1 text-xs text-muted-foreground">role</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning/10">
                    <svg class="h-5 w-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Control Bar - Professional Toolbar -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-surface rounded-xl border border-border/50 p-4">
        <!-- Left Side: Search & Filter -->
        <div class="flex gap-3 flex-1 flex-wrap">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-64">
                <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    type="text" 
                    x-model="search"
                    placeholder="Cari username, nama, atau email..." 
                    class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-success/50 pl-10 transition-all"
                >
            </div>
            
            <!-- Role Filter -->
            <select 
                x-model="roleFilter"
                class="flex h-10 items-center rounded-lg border border-border bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-success/50 transition-all"
            >
                <option value="all">Semua Role</option>
                <option value="OWNER">OWNER</option>
                <option value="ADMIN">ADMIN</option>
                <option value="GUDANG">GUDANG</option>
                <option value="SALES">SALES</option>
            </select>
        </div>

        <!-- Right Side: Action Buttons -->
        <div class="flex gap-2">
            <!-- Export Button -->
            <button 
                class="inline-flex items-center justify-center rounded-lg border border-border bg-surface text-foreground hover:bg-muted/50 transition h-10 px-3 gap-2 text-sm font-medium"
                title="Export data"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span class="hidden sm:inline">Export</span>
            </button>

            <!-- Add User Button -->
            <button 
                @click="openModal()"
                class="inline-flex items-center justify-center rounded-lg bg-success text-white hover:bg-success-light transition h-10 px-4 gap-2 text-sm font-semibold shadow-sm hover:shadow-md"
                title="Tambah pengguna baru"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">Tambah Pengguna</span>
                <span class="sm:hidden">Tambah</span>
            </button>
        </div>
    </div>

    <!-- Users Table - Professional Data Grid -->
    <div class="rounded-xl border border-border/50 bg-surface shadow-sm overflow-hidden">
        <!-- Table Header with Column Info -->
        <div class="border-b border-border/50 bg-muted/30 px-6 py-3">
            <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                <span x-text="`${filteredUsers.length} pengguna ditemukan`"></span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border/50 bg-background/50">
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Pengguna</th>
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Email</th>
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Role</th>
                        <th class="h-12 px-6 py-3 text-center font-semibold text-foreground uppercase text-xs tracking-wide">Status</th>
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Dibuat</th>
                        <th class="h-12 px-6 py-3 text-right font-semibold text-foreground uppercase text-xs tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="user in filteredUsers" :key="user.id">
                        <tr class="border-b border-border/30 hover:bg-success/3 transition-colors duration-150">
                            <!-- User Column with Avatar -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success/10 text-success font-semibold text-sm">
                                        <span x-text="user.username.charAt(0).toUpperCase()"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-foreground truncate" x-text="user.username"></p>
                                        <p class="text-xs text-muted-foreground mt-0.5" x-text="user.fullname"></p>
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="px-6 py-4 text-sm text-foreground" x-text="user.email || '-'"></td>

                            <!-- Role Badge -->
                            <td class="px-6 py-4">
                                <span 
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold border"
                                    :class="{
                                        'border-destructive/30 bg-destructive/10 text-destructive': user.role === 'OWNER',
                                        'border-red/30 bg-red/10 text-red': user.role === 'ADMIN',
                                        'border-warning/30 bg-warning/10 text-warning': user.role === 'GUDANG',
                                        'border-blue/30 bg-blue/10 text-blue': user.role === 'SALES'
                                    }"
                                    x-text="user.role">
                                </span>
                            </td>

                            <!-- Status with Indicator -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <span 
                                        class="h-2 w-2 rounded-full"
                                        :class="(user.status || 'active') === 'active' ? 'bg-success' : 'bg-muted-foreground'">
                                    </span>
                                    <span 
                                        class="text-xs font-medium"
                                        :class="(user.status || 'active') === 'active' ? 'text-success' : 'text-muted-foreground'"
                                        x-text="(user.status || 'active') === 'active' ? 'Aktif' : 'Nonaktif'">
                                    </span>
                                </div>
                            </td>

                            <!-- Created Date -->
                            <td class="px-6 py-4 text-sm text-muted-foreground" x-text="new Date(user.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' })"></td>

                            <!-- Action Buttons -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-1.5">
                                    <template x-if="sessionRole === 'OWNER' && user.id !== sessionUserId">
                                        <button 
                                            @click="editUser(user.id)"
                                            class="inline-flex items-center justify-center rounded-lg border border-border bg-surface hover:bg-muted/50 transition h-9 w-9 text-foreground"
                                            title="Edit pengguna"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                    </template>
                                    <template x-if="sessionRole === 'OWNER' && user.id !== sessionUserId">
                                        <button 
                                            @click="deleteUser(user.id)"
                                            class="inline-flex items-center justify-center rounded-lg border border-destructive/30 bg-destructive/5 hover:bg-destructive/15 transition h-9 w-9 text-destructive"
                                            title="Hapus pengguna"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <tr x-show="filteredUsers.length === 0">
                        <td colspan="6" class="py-12 px-6 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="h-12 w-12 text-muted-foreground opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm0 0h6v-2a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <p class="text-sm font-medium text-foreground">Tidak ada pengguna ditemukan</p>
                                <p class="text-xs text-muted-foreground">Coba ubah filter atau cari dengan kata kunci lain</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="border-t border-border/50 bg-muted/20 px-6 py-3 flex items-center justify-between text-xs text-muted-foreground">
            <span x-text="`Menampilkan ${filteredUsers.length} dari ${users.length} pengguna`"></span>
            <a href="<?= base_url('master/users') ?>" class="text-success hover:text-success-light font-semibold transition">
                Refresh
            </a>
        </div>
    </div>

    <!-- Modal (Dialog) - Enhanced -->
    <div 
        x-show="isDialogOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        x-transition.opacity
        style="display: none;"
    >
        <div 
            class="w-full max-w-2xl rounded-xl border border-border/50 bg-surface shadow-xl"
            @click.away="isDialogOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <!-- Modal Header -->
            <div class="border-b border-border/50 px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-foreground" x-text="editingUser ? 'Edit Pengguna' : 'Tambah Pengguna Baru'"></h2>
                <button 
                    @click="isDialogOpen = false"
                    class="text-muted-foreground hover:text-foreground transition rounded-lg hover:bg-muted p-1"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <form @submit="submitForm" class="p-6 space-y-5">
                <?= csrf_field() ?>
                
                <!-- Row 1: Username & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-foreground" for="username">Username *</label>
                        <input 
                            type="text" 
                            name="username" 
                            id="username"
                            x-model="editingUser.username"
                            required 
                            placeholder="Contoh: john.doe"
                            class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-success/50 transition-all"
                        >
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-foreground" for="email">Email *</label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            x-model="editingUser.email"
                            required 
                            placeholder="Contoh: john@company.com"
                            class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-success/50 transition-all"
                        >
                    </div>
                </div>

                <!-- Row 2: Full Name -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="fullname">Nama Lengkap *</label>
                    <input 
                        type="text" 
                        name="fullname" 
                        id="fullname"
                        x-model="editingUser.fullname"
                        required 
                        placeholder="Contoh: John Doe"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-success/50 transition-all"
                    >
                </div>

                <!-- Row 3: Role -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="role">Role *</label>
                    <select 
                        name="role" 
                        id="role"
                        x-model="editingUser.role"
                        required 
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-success/50 transition-all"
                    >
                        <option value="">Pilih Role</option>
                        <option value="ADMIN">ADMIN</option>
                        <option value="GUDANG">GUDANG</option>
                        <option value="SALES">SALES</option>
                        <template x-if="sessionRole === 'OWNER'">
                            <option value="OWNER">OWNER</option>
                        </template>
                    </select>
                    <p class="text-xs text-muted-foreground mt-1">Pilih role yang sesuai untuk pengguna</p>
                </div>

                <!-- Row 4: Password -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="password">Password <span x-text="editingUser.id ? '' : '*'"></span></label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        x-model="editingUser.password"
                        :required="!editingUser.id"
                        :placeholder="editingUser.id ? 'Kosongkan jika tidak ingin mengubah' : 'Minimal 6 karakter'"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-success/50 transition-all"
                    >
                    <p class="text-xs text-muted-foreground mt-1" x-show="editingUser.id">Kosongkan jika tidak ingin mengubah password</p>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 pt-4 border-t border-border/50">
                    <button 
                        type="button" 
                        @click="isDialogOpen = false" 
                        class="inline-flex items-center justify-center rounded-lg border border-border bg-surface text-foreground hover:bg-muted/50 transition h-10 px-6 text-sm font-semibold"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="inline-flex items-center justify-center rounded-lg bg-success text-white hover:bg-success-light transition h-10 px-6 text-sm font-semibold shadow-sm hover:shadow-md"
                    >
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span x-text="editingUser.id ? 'Update Pengguna' : 'Simpan Pengguna'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
