<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="supplierManager()">
    <!-- Toolbar: Search & Add Button -->
    <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:w-72">
            <span class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground">
                <?= icon('Search', 'h-4 w-4') ?>
            </span>
            <input 
                type="text" 
                x-model="search"
                placeholder="Cari supplier..." 
                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 pl-9"
            >
        </div>

        <button 
            @click="isDialogOpen = true"
            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
        >
            <?= icon('Plus', 'mr-2 h-4 w-4') ?>
            Tambah Supplier
        </button>
    </div>

    <!-- Table -->
    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <div class="p-0">
            <div class="relative w-full overflow-auto">
                <table class="w-full caption-bottom text-sm">
                    <thead class="[&_tr]:border-b">
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Kode</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Nama Supplier</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Telepon</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Alamat</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:border-0">
                        <template x-for="supplier in filteredSuppliers" :key="supplier.id">
                            <tr class="border-b transition-colors hover:bg-muted/50">
                                <td class="p-4 align-middle font-medium text-primary" x-text="supplier.code || supplier.id"></td>
                                <td class="p-4 align-middle font-medium" x-text="supplier.name"></td>
                                <td class="p-4 align-middle" x-text="supplier.phone || '-'"></td>
                                <td class="p-4 align-middle max-w-[200px] truncate" x-text="supplier.address || '-'"></td>
                                <td class="p-4 align-middle text-right">
                                    <div class="flex justify-end gap-1">
                                        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-8 w-8">
                                            <?= icon('Pencil', 'h-4 w-4') ?>
                                        </button>
                                        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-8 w-8 text-destructive">
                                            <?= icon('Trash2', 'h-4 w-4') ?>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredSuppliers.length === 0">
                            <td colspan="5" class="p-4 text-center text-muted-foreground">Tidak ada supplier ditemukan</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
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
                <h2 class="text-lg font-semibold leading-none tracking-tight">Tambah Supplier Baru</h2>
            </div>
            
            <form action="<?= base_url('master/suppliers/store') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="name">Nama Supplier</label>
                    <input type="text" name="name" id="name" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                </div>
                
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none" for="phone">No. Telepon</label>
                    <input type="text" name="phone" id="phone" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                </div>
                
                <!-- Note: Address might not be in controller validation rules yet, but good to have in form -->
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
        }
    }
}
</script>

<?= $this->endSection() ?>
