<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="warehouseManager()">
    <!-- Page Header -->
    <div class="mb-8 flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground">Manajemen Gudang</h2>
            <p class="mt-1 text-muted-foreground">Kelola lokasi penyimpanan dan inventaris gudang Anda</p>
        </div>
    </div>

    <!-- Summary Cards - Gradient Theme -->
    <div class="mb-8 grid gap-4 grid-cols-1 md:grid-cols-3">
        <!-- Total Warehouses -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-warning/5 to-transparent p-5 hover:border-warning/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Gudang</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="warehouses.length"></p>
                    <p class="mt-1 text-xs text-muted-foreground">lokasi</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning/10">
                    <svg class="h-5 w-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/>
                        <line x1="7" y1="7" x2="7.01" y2="7"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Warehouses -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-success/5 to-transparent p-5 hover:border-success/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Gudang Aktif</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="warehouses.filter(w => parseInt(w.is_active) === 1).length"></p>
                    <p class="mt-1 text-xs text-muted-foreground">status</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success/10">
                    <svg class="h-5 w-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Storage Value -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-blue/5 to-transparent p-5 hover:border-blue/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Nilai Stok</p>
                    <p class="mt-2 text-2xl font-bold text-foreground">Rp 0</p>
                    <p class="mt-1 text-xs text-muted-foreground">semua gudang</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue/10">
                    <svg class="h-5 w-5 text-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Control Bar -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-surface rounded-xl border border-border/50 p-4">
        <!-- Left Side: Search -->
        <div class="flex gap-3 flex-wrap items-center flex-1">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-64">
                <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    type="text" 
                    x-model="search"
                    placeholder="Cari nama atau kode gudang..." 
                    class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-warning/50 pl-10 transition-all"
                >
            </div>
        </div>

        <!-- Right Side: Add Button -->
        <button 
            @click="isDialogOpen = true"
            class="inline-flex items-center justify-center rounded-lg bg-warning text-white hover:bg-warning-light transition h-10 px-4 gap-2 text-sm font-semibold shadow-sm hover:shadow-md whitespace-nowrap"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="hidden sm:inline">Tambah Gudang</span>
            <span class="sm:hidden">Tambah</span>
        </button>
    </div>

    <!-- Warehouse Cards Grid - Enhanced -->
    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        <template x-for="warehouse in filteredWarehouses" :key="warehouse.id">
            <!-- Warehouse Card -->
            <div class="rounded-xl border border-border/50 bg-surface shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden group">
                <!-- Card Header with Badge -->
                <div class="border-b border-border/50 bg-gradient-to-r from-warning/3 to-transparent px-6 py-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wider" x-text="`Gudang #${warehouse.code || warehouse.id}`"></p>
                            <h3 class="mt-2 text-lg font-bold text-foreground truncate group-hover:text-warning transition" x-text="warehouse.name"></h3>
                        </div>
                        <div class="flex gap-1 flex-shrink-0 ml-3">
                            <!-- Edit Button -->
                            <button 
                                @click="editWarehouse(warehouse.id)"
                                class="inline-flex items-center justify-center rounded-lg border border-border bg-surface hover:bg-muted/50 transition h-9 w-9 text-foreground"
                                title="Edit gudang"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <!-- Delete Button -->
                            <button 
                                @click="deleteWarehouse(warehouse.id)"
                                class="inline-flex items-center justify-center rounded-lg border border-destructive/30 bg-destructive/5 hover:bg-destructive/15 transition h-9 w-9 text-destructive"
                                title="Hapus gudang"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="mt-3">
                        <span 
                            class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold"
                            :class="parseInt(warehouse.is_active) === 1 
                                ? 'border-success/30 bg-success/15 text-success' 
                                : 'border-muted-foreground/30 bg-muted/15 text-muted-foreground'">
                            <span class="inline-block h-2 w-2 rounded-full" :class="parseInt(warehouse.is_active) === 1 ? 'bg-success' : 'bg-muted-foreground'"></span>
                            <span x-text="parseInt(warehouse.is_active) === 1 ? 'Aktif' : 'Nonaktif'"></span>
                        </span>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6 space-y-4">
                    <!-- Location Info -->
                    <div class="space-y-2.5">
                        <!-- Address -->
                        <div class="flex items-start gap-3 text-sm">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-muted/50 flex-shrink-0 mt-0.5">
                                <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <span class="text-foreground font-medium line-clamp-2" x-text="warehouse.address || '(tidak ada)'"></span>
                        </div>
                    </div>

                    <!-- Storage Info -->
                    <div class="border-t border-border/50 pt-4 grid grid-cols-2 gap-3">
                        <!-- Item Count -->
                        <div class="rounded-lg bg-warning/5 p-3">
                            <p class="text-xs text-muted-foreground font-semibold uppercase">Total Produk</p>
                            <p class="mt-1 font-bold text-warning text-sm">0</p>
                        </div>
                        <!-- Stock Value -->
                        <div class="rounded-lg bg-blue/5 p-3">
                            <p class="text-xs text-muted-foreground font-semibold uppercase">Nilai Stok</p>
                            <p class="mt-1 font-bold text-blue text-sm">Rp 0</p>
                        </div>
                    </div>
                </div>

                <!-- Card Footer with Link -->
                <div class="border-t border-border/50 bg-muted/20 px-6 py-3">
                    <a href="#" class="text-sm font-semibold text-warning hover:text-warning-light transition flex items-center gap-1">
                        Lihat detail
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </div>
        </template>

        <!-- Empty State -->
        <div x-show="filteredWarehouses.length === 0" class="col-span-full">
            <div class="rounded-xl border-2 border-dashed border-border/50 bg-muted/20 p-12 text-center">
                <svg class="h-16 w-16 mx-auto text-muted-foreground opacity-30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/>
                </svg>
                <p class="text-lg font-semibold text-foreground mt-2">Tidak ada gudang ditemukan</p>
                <p class="text-sm text-muted-foreground mt-1">Coba ubah filter atau cari dengan kata kunci lain</p>
                <button 
                    @click="isDialogOpen = true"
                    class="mt-6 inline-flex items-center justify-center rounded-lg bg-warning text-white hover:bg-warning-light transition h-10 px-6 gap-2 text-sm font-semibold">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Gudang Pertama
                </button>
            </div>
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
                <h2 class="text-xl font-bold text-foreground">Tambah Gudang Baru</h2>
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
            <form action="<?= base_url('master/warehouses/store') ?>" method="POST" class="p-6 space-y-5">
                <?= csrf_field() ?>
                
                <!-- Row 1: Name & Code -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-foreground" for="name">Nama Gudang *</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            required 
                            placeholder="Contoh: Gudang Pusat Jakarta"
                            class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-warning/50 transition-all"
                        >
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-foreground" for="code">Kode Gudang *</label>
                        <input 
                            type="text" 
                            name="code" 
                            id="code" 
                            required 
                            placeholder="Contoh: GDG-001"
                            class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-warning/50 transition-all"
                        >
                    </div>
                </div>

                <!-- Row 2: Address -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="address">Alamat Lengkap</label>
                    <textarea 
                        name="address" 
                        id="address" 
                        placeholder="Contoh: Jl. Merpati No. 456, Jakarta Utara 14450"
                        rows="3"
                        class="flex w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-warning/50 transition-all resize-none"
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
                        class="inline-flex items-center justify-center rounded-lg bg-warning text-white hover:bg-warning-light transition h-10 px-6 text-sm font-semibold shadow-sm hover:shadow-md"
                    >
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Simpan Gudang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function warehouseManager() {
    return {
        warehouses: <?= json_encode($warehouses ?? []) ?>,
        search: '',
        isDialogOpen: false,

        get filteredWarehouses() {
            return this.warehouses.filter(w => {
                const searchLower = this.search.toLowerCase();
                return (w.name && w.name.toLowerCase().includes(searchLower)) ||
                       (w.code && w.code.toLowerCase().includes(searchLower));
            });
        },

        editWarehouse(warehouseId) {
            window.location.href = `<?= base_url('master/warehouses/edit') ?>/${warehouseId}`;
        },

        deleteWarehouse(warehouseId) {
            const warehouse = this.warehouses.find(w => w.id === warehouseId);
            const warehouseName = warehouse ? warehouse.name : 'gudang ini';
            ModalManager.submitDelete(
                `<?= base_url('master/warehouses/delete') ?>/${warehouseId}`,
                warehouseName,
                () => {
                    this.warehouses = this.warehouses.filter(w => w.id !== warehouseId);
                }
            );
        }
    }
}
</script>

<?= $this->endSection() ?>
