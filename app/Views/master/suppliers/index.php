<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="supplierManager()">
    <!-- Page Header -->
    <div class="mb-8 flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground">Manajemen Supplier</h2>
            <p class="mt-1 text-muted-foreground">Kelola daftar pemasok dan hubungan dagang Anda</p>
        </div>
    </div>

    <!-- Summary Cards - Compact Grid (Product Style) -->
    <div class="mb-8 grid gap-4 grid-cols-1 md:grid-cols-3">
        <!-- Total Suppliers -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-secondary/5 to-transparent p-5 hover:border-secondary/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Supplier</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="suppliers.length"></p>
                    <p class="mt-1 text-xs text-muted-foreground">aktif</p>
                </div>
                 <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary/10 flex-shrink-0">
                     <svg class="h-5 w-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9m5.581 0a2 2 0 100-4 2 2 0 000 4zM9 7h1.5a1 1 0 001-1V5a1 1 0 00-1-1H9a1 1 0 00-1 1v1a1 1 0 001 1z"/>
                     </svg>
                 </div>
            </div>
        </div>

        <!-- Active Suppliers -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-success/5 to-transparent p-5 hover:border-success/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Status Aktif</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="activeCount"></p>
                    <p class="mt-1 text-xs text-muted-foreground">tersedia</p>
                </div>
                 <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success/10 flex-shrink-0">
                     <svg class="h-5 w-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                     </svg>
                 </div>
            </div>
        </div>

        <!-- Total Debt -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-warning/5 to-transparent p-5 hover:border-warning/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Utang</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="formatRupiah(totalDebt)"></p>
                    <p class="mt-1 text-xs text-muted-foreground">hutang dagang</p>
                </div>
                 <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning/10 flex-shrink-0">
                     <svg class="h-5 w-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                     </svg>
                 </div>
            </div>
        </div>
    </div>

    <!-- Control Bar - Professional Toolbar -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-surface rounded-xl border border-border/50 p-4">
        <!-- Left Side: Search -->
        <div class="flex-1 min-w-0">
            <div class="relative max-w-md">
                <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    type="text" 
                    x-model="search"
                    placeholder="Cari nama atau kode supplier..." 
                    class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary/50 pl-10 transition-all"
                >
            </div>
        </div>

        <!-- Right Side: Action Buttons -->
        <div class="flex gap-2 flex-shrink-0">
            <!-- Export Button -->
            <button 
                @click="exportData()"
                class="inline-flex items-center justify-center rounded-lg border border-border bg-surface text-foreground hover:bg-muted/50 transition h-10 px-3 gap-2 text-sm font-medium"
                title="Export data ke PDF"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span class="hidden sm:inline">Export</span>
            </button>

            <!-- Add Button -->
            <button 
                @click="isDialogOpen = true"
                class="inline-flex items-center justify-center rounded-lg bg-secondary text-white hover:bg-blue-600 transition h-10 px-4 gap-2 text-sm font-semibold shadow-sm hover:shadow-md whitespace-nowrap"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">Tambah Supplier</span>
                <span class="sm:hidden">Tambah</span>
            </button>
        </div>
    </div>

    <!-- Suppliers Table -->
    <div class="rounded-xl border border-border/50 bg-surface shadow-sm overflow-hidden">
        <!-- Table Header Info -->
        <div class="border-b border-border/50 bg-muted/30 px-6 py-3">
            <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                <span x-text="`${filteredSuppliers.length} supplier ditemukan`"></span>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border/50 bg-background/50">
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Nama</th>
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Kode</th>
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Telepon</th>
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Alamat</th>
                        <th class="h-12 px-6 py-3 text-right font-semibold text-foreground uppercase text-xs tracking-wide">Total Transaksi</th>
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Status</th>
                        <th class="h-12 px-6 py-3 text-right font-semibold text-foreground uppercase text-xs tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="supplier in filteredSuppliers" :key="supplier.id">
                        <tr class="border-b border-border/30 hover:bg-secondary/3 transition-colors duration-150">
                            <!-- Nama -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-muted">
                                        <svg class="h-5 w-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9m5.581 0a2 2 0 100-4 2 2 0 000 4zM9 7h1.5a1 1 0 001-1V5a1 1 0 00-1-1H9a1 1 0 00-1 1v1a1 1 0 001 1z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-foreground truncate" x-text="supplier.name"></p>
                                        <p class="text-xs text-muted-foreground mt-0.5" x-text="`${supplier.phone || '(tidak ada)'}`"></p>
                                    </div>
                                </div>
                            </td>

                            <!-- Kode -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full border border-border/30 bg-muted/30 px-2.5 py-1 text-xs font-semibold text-foreground font-mono" x-text="supplier.code || '-'"></span>
                            </td>

                            <!-- Phone -->
                            <td class="px-6 py-4 text-foreground text-sm" x-text="supplier.phone || '-'"></td>

                            <!-- Alamat -->
                            <td class="px-6 py-4">
                                <div class="flex items-start">
                                    <p class="text-sm text-foreground line-clamp-2 max-w-xs" x-text="supplier.address || '-'"></p>
                                </div>
                            </td>

                            <!-- Total Transaksi -->
                            <td class="px-6 py-4 text-right">
                                <p class="font-semibold text-foreground" x-text="formatRupiah(supplier.total_transaction || 0)"></p>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full border border-success/30 bg-success/10 px-2.5 py-1 text-xs font-semibold text-success">
                                    ✓ Aktif
                                </span>
                            </td>

                            <!-- Action -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-1.5">
                                    <button 
                                        @click="openEditModal(supplier)"
                                        class="inline-flex items-center justify-center rounded-lg border border-border bg-surface hover:bg-muted/50 transition h-9 w-9 text-foreground"
                                        title="Edit supplier"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button 
                                        @click="deleteSupplier(supplier.id)"
                                        class="inline-flex items-center justify-center rounded-lg border border-destructive/30 bg-destructive/5 hover:bg-destructive/15 transition h-9 w-9 text-destructive"
                                        title="Hapus supplier"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <tr x-show="filteredSuppliers.length === 0">
                        <td colspan="7" class="py-12 px-6 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="h-12 w-12 text-muted-foreground opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9m5.581 0a2 2 0 100-4 2 2 0 000 4zM9 7h1.5a1 1 0 001-1V5a1 1 0 00-1-1H9a1 1 0 00-1 1v1a1 1 0 001 1z"/>
                                </svg>
                                <p class="text-sm font-medium text-foreground">Tidak ada supplier ditemukan</p>
                                <p class="text-xs text-muted-foreground">Coba ubah pencarian atau tambahkan supplier baru</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Supplier Modal -->
    <div 
        x-show="isDialogOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        x-transition.opacity
        style="display: none;"
    >
        <div 
            class="w-full max-w-md rounded-xl border border-border/50 bg-surface shadow-xl"
            @click.away="isDialogOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <!-- Modal Header -->
            <div class="border-b border-border/50 px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-foreground">Tambah Supplier Baru</h2>
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
            <form @submit.prevent="submitForm" action="<?= base_url('master/suppliers/store') ?>" method="POST" class="p-6 space-y-4">
                <?= csrf_field() ?>
                
                <!-- Nama Supplier -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="name">Nama Supplier *</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        required 
                        placeholder="Contoh: PT Indah Jaya Sentosa"
                        :class="{'border-destructive': errors.name}"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary/50 transition-all"
                    >
                    <span x-show="errors.name" class="text-destructive text-xs mt-1" x-text="errors.name"></span>
                </div>

                <!-- Telepon -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="phone">No. Telepon</label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="phone" 
                        placeholder="Contoh: 081234567890"
                        :class="{'border-destructive': errors.phone}"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary/50 transition-all"
                    >
                    <span x-show="errors.phone" class="text-destructive text-xs mt-1" x-text="errors.phone"></span>
                </div>

                <!-- Alamat -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="address">Alamat Lengkap</label>
                    <textarea 
                        name="address" 
                        id="address" 
                        placeholder="Contoh: Jl. Gatot Subroto No. 456, Jakarta Selatan"
                        rows="2"
                        :class="{'border-destructive': errors.address}"
                        class="flex w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary/50 transition-all resize-none"
                    ></textarea>
                    <span x-show="errors.address" class="text-destructive text-xs mt-1" x-text="errors.address"></span>
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
                        :disabled="isSubmitting"
                        class="inline-flex items-center justify-center rounded-lg bg-secondary text-white hover:bg-blue-600 transition h-10 px-6 text-sm font-semibold shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg x-show="!isSubmitting" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span x-show="isSubmitting" class="inline-flex items-center gap-2 mr-2">
                            <span class="animate-spin">⚙️</span>
                        </span>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Supplier'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Supplier Modal -->
    <div 
        x-show="isEditDialogOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        x-transition.opacity
        style="display: none;"
    >
        <div 
            class="w-full max-w-md rounded-xl border border-border/50 bg-surface shadow-xl"
            @click.away="isEditDialogOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <!-- Modal Header -->
            <div class="border-b border-border/50 px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-foreground">Edit Supplier</h2>
                <button 
                    @click="isEditDialogOpen = false"
                    class="text-muted-foreground hover:text-foreground transition rounded-lg hover:bg-muted p-1"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body -->
            <form @submit.prevent="submitEditForm" :action="`<?= base_url('master/suppliers') ?>/${editingSupplier.id}`" method="POST" class="p-6 space-y-4">
                <?= csrf_field() ?>
                
                <!-- Nama Supplier -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="edit_name">Nama Supplier *</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="edit_name" 
                        required 
                        x-model="editingSupplier.name"
                        :class="{'border-destructive': editErrors.name}"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary/50 transition-all"
                    >
                    <span x-show="editErrors.name" class="text-destructive text-xs mt-1" x-text="editErrors.name"></span>
                </div>

                <!-- Telepon -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="edit_phone">No. Telepon</label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="edit_phone" 
                        x-model="editingSupplier.phone"
                        :class="{'border-destructive': editErrors.phone}"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary/50 transition-all"
                    >
                    <span x-show="editErrors.phone" class="text-destructive text-xs mt-1" x-text="editErrors.phone"></span>
                </div>

                <!-- Alamat -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="edit_address">Alamat Lengkap</label>
                    <textarea 
                        name="address" 
                        id="edit_address" 
                        rows="2"
                        x-model="editingSupplier.address"
                        :class="{'border-destructive': editErrors.address}"
                        class="flex w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary/50 transition-all resize-none"
                    ></textarea>
                    <span x-show="editErrors.address" class="text-destructive text-xs mt-1" x-text="editErrors.address"></span>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end gap-3 pt-4 border-t border-border/50">
                    <button 
                        type="button" 
                        @click="isEditDialogOpen = false" 
                        class="inline-flex items-center justify-center rounded-lg border border-border bg-surface text-foreground hover:bg-muted/50 transition h-10 px-6 text-sm font-semibold"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        :disabled="isEditSubmitting"
                        class="inline-flex items-center justify-center rounded-lg bg-secondary text-white hover:bg-blue-600 transition h-10 px-6 text-sm font-semibold shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg x-show="!isEditSubmitting" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span x-show="isEditSubmitting" class="inline-flex items-center gap-2 mr-2">
                            <span class="animate-spin">⚙️</span>
                        </span>
                        <span x-text="isEditSubmitting ? 'Menyimpan...' : 'Update Supplier'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function supplierManager() {
    return {
        suppliers: <?= json_encode($suppliers ?? []) ?>,
        search: '',
        isDialogOpen: false,
        isEditDialogOpen: false,
        isSubmitting: false,
        isEditSubmitting: false,
        errors: {},
        editErrors: {},
        editingSupplier: {},

        get filteredSuppliers() {
            return this.suppliers.filter(sup => {
                const searchLower = this.search.toLowerCase();
                return (sup.name && sup.name.toLowerCase().includes(searchLower)) ||
                       (sup.code && sup.code.toLowerCase().includes(searchLower));
            });
        },

        get activeCount() {
            return this.suppliers.filter(s => s.status === 'active' || s.is_active === 1).length;
        },

        get totalDebt() {
            return this.suppliers.reduce((sum, s) => sum + (parseFloat(s.debt_balance) || 0), 0);
        },

        openEditModal(supplier) {
            this.editingSupplier = JSON.parse(JSON.stringify(supplier));
            this.editErrors = {};
            this.isEditDialogOpen = true;
        },

        async submitForm(event) {
            event.preventDefault();
            const form = event.target;
            
            this.errors = {};
            this.isSubmitting = true;

            try {
                const formData = new FormData(form);
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok || response.status === 201) {
                    ModalManager.success('Supplier berhasil ditambahkan', () => {
                        this.isDialogOpen = false;
                        form.reset();
                        this.errors = {};
                        window.location.reload();
                    });
                } else if (response.status === 422) {
                    const data = await response.json();
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                    ModalManager.error(data.message || 'Terjadi kesalahan validasi.');
                } else {
                    const data = await response.json();
                    ModalManager.error(data.message || 'Gagal menyimpan data.');
                }
            } catch (error) {
                console.error('Form submission error:', error);
                ModalManager.error('Terjadi kesalahan: ' + error.message);
            } finally {
                this.isSubmitting = false;
            }
        },

        async submitEditForm(event) {
            event.preventDefault();
            const form = event.target;
            
            this.editErrors = {};
            this.isEditSubmitting = true;

            try {
                const formData = new FormData(form);
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok || response.status === 200) {
                    ModalManager.success('Supplier berhasil diperbarui', () => {
                        this.isEditDialogOpen = false;
                        this.editErrors = {};
                        this.editingSupplier = {};
                        window.location.reload();
                    });
                } else if (response.status === 422) {
                    const data = await response.json();
                    if (data.errors) {
                        this.editErrors = data.errors;
                    }
                    ModalManager.error(data.message || 'Terjadi kesalahan validasi.');
                } else {
                    const data = await response.json();
                    ModalManager.error(data.message || 'Gagal memperbarui data.');
                }
            } catch (error) {
                console.error('Form submission error:', error);
                ModalManager.error('Terjadi kesalahan: ' + error.message);
            } finally {
                this.isEditSubmitting = false;
            }
        },

        deleteSupplier(supplierId) {
            const supplier = this.suppliers.find(s => s.id === supplierId);
            const supplierName = supplier ? supplier.name : 'supplier ini';
            ModalManager.submitDelete(
                `<?= base_url('master/suppliers') ?>/${supplierId}`,
                supplierName,
                () => {
                    this.suppliers = this.suppliers.filter(s => s.id !== supplierId);
                }
            );
        },

        exportData() {
            try {
                window.location.href = `<?= base_url('master/suppliers/export-pdf') ?>`;
            } catch (error) {
                console.error('Export failed:', error);
                alert('Gagal mengekspor data.');
            }
        },

        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number || 0);
        }
    }
}
</script>

<?= $this->endSection() ?>
