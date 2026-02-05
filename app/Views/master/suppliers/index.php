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

    <!-- Summary Cards -->
    <div class="mb-8 grid gap-4 grid-cols-1 md:grid-cols-3">
        <!-- Total Suppliers -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-secondary/5 to-transparent p-5 hover:border-secondary/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Supplier</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="suppliers.length"></p>
                    <p class="mt-1 text-xs text-muted-foreground">aktif</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary/10">
                    <svg class="h-5 w-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9m5.581 0a2 2 0 100-4 2 2 0 000 4zM9 7h1.5a1 1 0 001-1V5a1 1 0 00-1-1H9a1 1 0 00-1 1v1a1 1 0 001 1z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Average Rating (or status) -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-success/5 to-transparent p-5 hover:border-success/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Supplier Terpercaya</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="trustedCount"></p>
                    <p class="mt-1 text-xs text-muted-foreground">rating ≥4.0</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success/10">
                    <svg class="h-5 w-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Contracts -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-warning/5 to-transparent p-5 hover:border-warning/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Status Kontrak</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="activeCount"></p>
                    <p class="mt-1 text-xs text-muted-foreground">aktif</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning/10">
                    <svg class="h-5 w-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Control Bar -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-surface rounded-xl border border-border/50 p-4">
        <!-- Left Side: Search -->
        <div class="flex-1">
            <div class="relative max-w-64">
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

        <!-- Right Side: Add Button -->
        <div class="flex gap-2">
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

    <!-- Supplier Cards Grid -->
    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        <template x-for="supplier in filteredSuppliers" :key="supplier.id">
            <!-- Supplier Card -->
            <div class="rounded-xl border border-border/50 bg-surface shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden group">
                <!-- Card Header -->
                <div class="border-b border-border/50 bg-gradient-to-r from-secondary/3 to-transparent px-6 py-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wider" x-text="`Supplier #${supplier.code || supplier.id}`"></p>
                            <h3 class="mt-2 text-lg font-bold text-foreground truncate group-hover:text-secondary transition" x-text="supplier.name"></h3>
                        </div>
                        <div class="flex gap-1 flex-shrink-0 ml-3">
                            <!-- Edit Button -->
                            <button 
                                @click="editSupplier(supplier.id)"
                                class="inline-flex items-center justify-center rounded-lg border border-border bg-surface hover:bg-muted/50 transition h-9 w-9 text-foreground"
                                title="Edit supplier"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <!-- Delete Button -->
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
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6 space-y-4">
                    <!-- Contact Info -->
                    <div class="space-y-2.5">
                        <!-- Phone -->
                        <div class="flex items-center gap-3 text-sm">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-muted/50">
                                <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 00.948.684l1.498 4.493a1 1 0 00.502.756l2.048 1.029a2.42 2.42 0 101.622 2.3l-2.048-1.029a1 1 0 00-.756-.502l-4.493-1.498a1 1 0 00-.684-.948V5z"/>
                                </svg>
                            </div>
                            <span class="text-foreground font-medium" x-text="supplier.phone || '(tidak ada)'"></span>
                        </div>

                        <!-- Address -->
                        <div class="flex items-start gap-3 text-sm">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-muted/50 flex-shrink-0 mt-0.5">
                                <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <span class="text-foreground font-medium line-clamp-2" x-text="supplier.address || '(tidak ada)'"></span>
                        </div>
                    </div>

                    <!-- Status & Info -->
                    <div class="border-t border-border/50 pt-4 grid grid-cols-2 gap-3">
                        <!-- Status Badge -->
                        <div class="rounded-lg bg-success/5 p-3">
                            <p class="text-xs text-muted-foreground font-semibold uppercase">Status</p>
                            <p class="mt-1 font-bold text-success text-sm">Aktif</p>
                        </div>
                        <!-- Rating -->
                        <div class="rounded-lg bg-warning/5 p-3">
                            <p class="text-xs text-muted-foreground font-semibold uppercase">Rating</p>
                            <p class="mt-1 font-bold text-warning text-sm">⭐ 4.5</p>
                        </div>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="border-t border-border/50 bg-muted/20 px-6 py-3">
                    <a :href="`<?= base_url('master/suppliers/') ?>${supplier.id}`" class="text-sm font-semibold text-secondary hover:text-blue-600 transition flex items-center gap-1">
                        Lihat detail
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </div>
        </template>

        <!-- Empty State -->
        <div x-show="filteredSuppliers.length === 0" class="col-span-full">
            <div class="rounded-xl border-2 border-dashed border-border/50 bg-muted/20 p-12 text-center">
                <svg class="h-16 w-16 mx-auto text-muted-foreground opacity-30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9m5.581 0a2 2 0 100-4 2 2 0 000 4zM9 7h1.5a1 1 0 001-1V5a1 1 0 00-1-1H9a1 1 0 00-1 1v1a1 1 0 001 1z"/>
                </svg>
                <p class="text-lg font-semibold text-foreground mt-2">Tidak ada supplier ditemukan</p>
                <p class="text-sm text-muted-foreground mt-1">Coba ubah pencarian atau tambahkan supplier baru</p>
                <button 
                    @click="isDialogOpen = true"
                    class="mt-6 inline-flex items-center justify-center rounded-lg bg-secondary text-white hover:bg-blue-600 transition h-10 px-6 gap-2 text-sm font-semibold">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Supplier Pertama
                </button>
            </div>
        </div>
    </div>

    <!-- Modal (Dialog) -->
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
            <form action="<?= base_url('master/suppliers/store') ?>" method="POST" class="p-6 space-y-5">
                <?= csrf_field() ?>
                
                <!-- Row 1: Name & Phone -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-foreground" for="name">Nama Supplier *</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            required 
                            placeholder="Contoh: PT Indah Jaya Sentosa"
                            class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary/50 transition-all"
                        >
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-foreground" for="phone">No. Telepon</label>
                        <input 
                            type="text" 
                            name="phone" 
                            id="phone" 
                            placeholder="Contoh: 081234567890"
                            class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary/50 transition-all"
                        >
                    </div>
                </div>

                <!-- Row 2: Address -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="address">Alamat Lengkap</label>
                    <textarea 
                        name="address" 
                        id="address" 
                        placeholder="Contoh: Jl. Gatot Subroto No. 456, Jakarta Selatan 12950"
                        rows="3"
                        class="flex w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary/50 transition-all resize-none"
                    ></textarea>
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
                        class="inline-flex items-center justify-center rounded-lg bg-secondary text-white hover:bg-blue-600 transition h-10 px-6 text-sm font-semibold shadow-sm hover:shadow-md"
                    >
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Simpan Supplier
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

        get filteredSuppliers() {
            return this.suppliers.filter(sup => {
                const searchLower = this.search.toLowerCase();
                return (sup.name && sup.name.toLowerCase().includes(searchLower)) ||
                       (sup.code && sup.code.toLowerCase().includes(searchLower));
            });
        },

        get trustedCount() {
            return this.suppliers.filter(s => parseFloat(s.rating || 0) >= 4.0).length;
        },

        get activeCount() {
            return this.suppliers.filter(s => s.status === 'active').length;
        },

        editSupplier(supplierId) {
            window.location.href = `<?= base_url('master/suppliers/edit') ?>/${supplierId}`;
        },

        deleteSupplier(supplierId) {
            const supplier = this.suppliers.find(s => s.id === supplierId);
            const supplierName = supplier ? supplier.name : 'supplier ini';
            ModalManager.submitDelete(
                `<?= base_url('master/suppliers/delete') ?>/${supplierId}`,
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
                alert('Gagal mengekspor data. Silakan coba lagi.');
            }
        }
    }
}
</script>

<?= $this->endSection() ?>
