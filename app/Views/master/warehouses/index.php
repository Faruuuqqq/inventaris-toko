<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="warehouseManager()">
    <!-- Summary Cards -->
    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6 pb-2">
                <p class="text-sm font-medium text-muted-foreground">Total Gudang</p>
                <p class="text-2xl font-bold" x-text="warehouses.length"></p>
            </div>
        </div>
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6 pb-2">
                <p class="text-sm font-medium text-muted-foreground">Gudang Aktif</p>
                <p class="text-2xl font-bold text-success" x-text="warehouses.filter(w => parseInt(w.is_active) === 1).length"></p>
            </div>
        </div>
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6 pb-2">
                <p class="text-sm font-medium text-muted-foreground">Total Produk Tersimpan</p>
                <p class="text-2xl font-bold">0</p> <!-- Conceptual Placeholder -->
            </div>
        </div>
    </div>

    <!-- Toolbar: Search & Add Button -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:w-72">
            <span class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground">
                <?= icon('Search', 'h-4 w-4') ?>
            </span>
            <input 
                type="text" 
                x-model="search"
                placeholder="Cari gudang..." 
                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 pl-9"
            >
        </div>
        
        <button 
            @click="isDialogOpen = true"
            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
        >
            <?= icon('Plus', 'mr-2 h-4 w-4') ?>
            Tambah Gudang
        </button>
    </div>

    <!-- Warehouses Grid -->
    <div class="grid gap-4 md:grid-cols-2">
        <template x-for="gudang in filteredWarehouses" :key="gudang.id">
            <div class="rounded-xl border bg-card text-card-foreground shadow-sm transition-shadow hover:shadow-md">
                <div class="flex flex-col space-y-1.5 p-6 pb-3">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                                <?= icon('Warehouse', 'h-5 w-5 text-primary') ?>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="text-xs text-muted-foreground" x-text="gudang.code || gudang.id"></p>
                                    <span 
                                        class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent"
                                        :class="parseInt(gudang.is_active) === 1 ? 'bg-primary text-primary-foreground hover:bg-primary/80' : 'bg-secondary text-secondary-foreground hover:bg-secondary/80'"
                                        x-text="parseInt(gudang.is_active) === 1 ? 'Aktif' : 'Tidak Aktif'">
                                    </span>
                                </div>
                                <h3 class="mt-1 text-lg font-semibold leading-none tracking-tight" x-text="gudang.name"></h3>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-8 w-8">
                                <?= icon('Pencil', 'h-4 w-4') ?>
                            </button>
                            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-8 w-8 text-destructive">
                                <?= icon('Trash2', 'h-4 w-4') ?>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="p-6 pt-0 space-y-3">
                    <div class="flex items-start gap-2 text-sm text-muted-foreground">
                        <?= icon('MapPin', 'mt-0.5 h-4 w-4 shrink-0') ?>
                        <span x-text="gudang.address || 'Belum ada alamat'"></span>
                    </div>
                    <!-- Placeholder data for now as per controller -->
                    <div class="grid grid-cols-2 gap-4 border-t pt-3">
                        <div class="flex items-center gap-2">
                            <?= icon('Package', 'h-4 w-4 text-muted-foreground') ?>
                            <div>
                                <p class="text-xs text-muted-foreground">Total Produk</p>
                                <p class="font-semibold" x-text="'0'"></p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">Nilai Stok</p>
                            <p class="font-semibold text-primary">Rp 0</p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Modal (Dialog) -->
    <div 
        x-show="isDialogOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm"
        x-transition.opacity
        style="display: none;"
    >
        <div 
            class="w-full max-w-lg rounded-lg border bg-background p-6 shadow-lg sm:rounded-lg"
            @click.away="isDialogOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <div class="flex flex-col space-y-1.5 text-center sm:text-left mb-4">
                <h2 class="text-lg font-semibold leading-none tracking-tight">Tambah Gudang Baru</h2>
            </div>
            
            <form action="<?= base_url('master/warehouses/store') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="name">Nama Gudang</label>
                    <input type="text" name="name" id="name" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                </div>
                
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="code">Kode Gudang</label>
                    <input type="text" name="code" id="code" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="address">Alamat</label>
                    <input type="text" name="address" id="address" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" @click="isDialogOpen = false" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                        Batal
                    </button>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                        Simpan
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
        }
    }
}
</script>

<?= $this->endSection() ?>
