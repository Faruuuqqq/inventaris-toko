<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="productManager()">
    <!-- Summary Cards -->
    <div class="mb-6 grid gap-4 grid-cols-1 md:grid-cols-4">
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6 pb-2">
                <p class="text-sm font-medium text-muted-foreground">Total Produk</p>
                <p class="text-2xl font-bold"><?= $totalProducts ?></p>
            </div>
        </div>
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6 pb-2">
                <p class="text-sm font-medium text-muted-foreground">Total Kategori</p>
                <p class="text-2xl font-bold"><?= $totalCategories ?></p>
            </div>
        </div>
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6 pb-2">
                <p class="text-sm font-medium text-muted-foreground">Total Stok</p>
                <p class="text-2xl font-bold"><?= number_format($totalStock, 0, ',', '.') ?></p>
            </div>
        </div>
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6 pb-2">
                <p class="text-sm font-medium text-muted-foreground">Nilai Persediaan</p>
                <p class="text-2xl font-bold text-primary"><?= format_currency($totalValue) ?></p>
            </div>
        </div>
    </div>

    <!-- Filters & Actions -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex gap-4 flex-1">
            <!-- Search -->
            <div class="relative w-full sm:w-72">
                <span class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground">
                    <?= icon('Search', 'h-4 w-4') ?>
                </span>
                <input 
                    type="text" 
                    x-model="search"
                    placeholder="Cari produk..." 
                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 pl-9"
                >
            </div>
            
            <!-- Category Filter -->
            <select 
                x-model="categoryFilter"
                class="flex h-10 w-40 items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            >
                <option value="all">Semua Kategori</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['name'] ?>"><?= $cat['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Add Product Button (Triggers Modal) -->
        <button 
            @click="openModal()"
            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
        >
            <?= icon('Plus', 'mr-2 h-4 w-4') ?>
            Tambah Produk
        </button>
    </div>

    <!-- Products Table -->
    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <div class="p-0">
            <div class="relative w-full overflow-auto">
                <table class="w-full caption-bottom text-sm">
                    <thead class="[&_tr]:border-b">
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Kode</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Nama Produk</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Kategori</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Harga Beli</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Harga Jual</th>
                            <th class="h-12 px-4 text-center align-middle font-medium text-muted-foreground">Stok</th>
                            <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:border-0">
                        <template x-for="product in filteredProducts" :key="product.id">
                            <tr class="border-b transition-colors hover:bg-muted/50">
                                <td class="p-4 align-middle font-medium text-primary" x-text="product.sku"></td>
                                <td class="p-4 align-middle">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-8 w-8 items-center justify-center rounded bg-muted">
                                            <?= icon('Package', 'h-4 w-4 text-muted-foreground') ?>
                                        </div>
                                        <span x-text="product.name"></span>
                                    </div>
                                </td>
                                <td class="p-4 align-middle">
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80" x-text="product.category_name"></span>
                                </td>
                                <td class="p-4 align-middle text-right" x-text="formatRupiah(product.price_buy)"></td>
                                <td class="p-4 align-middle text-right font-medium" x-text="formatRupiah(product.price_sell)"></td>
                                <td class="p-4 align-middle text-center">
                                    <span 
                                        class="font-medium" 
                                        :class="parseInt(product.stock) < parseInt(product.min_stock_alert) ? 'text-destructive' : ''"
                                        x-text="product.stock + ' ' + product.unit">
                                    </span>
                                </td>
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
                        <tr x-show="filteredProducts.length === 0">
                            <td colspan="7" class="p-4 text-center text-muted-foreground">Tidak ada produk ditemukan</td>
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
                <h2 class="text-lg font-semibold leading-none tracking-tight">Tambah Produk Baru</h2>
            </div>
            
            <form action="<?= base_url('master/products/store') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="name">Nama Produk</label>
                    <input type="text" name="name" id="name" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                </div>
                
                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="sku">Kode Produk (SKU)</label>
                    <input type="text" name="sku" id="sku" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="category">Kategori</label>
                        <select name="category_id" id="category" required class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="unit">Satuan</label>
                        <input type="text" name="unit" id="unit" placeholder="Pcs, Kg, dll" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="price_buy">Harga Beli</label>
                        <input type="number" name="price_buy" id="price_buy" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="price_sell">Harga Jual</label>
                        <input type="number" name="price_sell" id="price_sell" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="min_stock_alert">Peringatan Minimal Stok</label>
                    <input type="number" name="min_stock_alert" id="min_stock_alert" value="10" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" @click="isDialogOpen = false" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                        Batal
                    </button>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function productManager() {
    return {
        products: <?= json_encode($products) ?>,
        search: '',
        categoryFilter: 'all',
        isDialogOpen: false,

        get filteredProducts() {
            return this.products.filter(product => {
                const searchLower = this.search.toLowerCase();
                const matchesSearch = product.name.toLowerCase().includes(searchLower) ||
                                    product.sku.toLowerCase().includes(searchLower);
                
                const matchesCategory = this.categoryFilter === 'all' || 
                                      product.category_name === this.categoryFilter;
                                      
                return matchesSearch && matchesCategory;
            });
        },

        openModal() {
            this.isDialogOpen = true;
        },

        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        }
    }
}
</script>

<?= $this->endSection() ?>
