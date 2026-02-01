<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="inventoryManager()">
    <!-- Page Header -->
    <div class="mb-8 flex items-start justify-between">
        <div>
            <h2 class="text-3xl font-bold text-foreground flex items-center gap-3">
                <?= icon('Package', 'h-8 w-8 text-primary') ?>
                Manajemen Inventaris
            </h2>
            <p class="mt-1 text-muted-foreground">Pantau stok, atur reorder, dan kelola tingkat stok produk</p>
        </div>
        <div class="flex gap-3">
            <button @click="exportCSV()" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
                <?= icon('Download', 'h-5 w-5') ?>
                Export
            </button>
            <a href="<?= base_url('master/products') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition">
                <?= icon('Plus', 'h-5 w-5') ?>
                Produk Baru
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="mb-8 grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
        <!-- Total Products -->
        <div class="rounded-lg border border-border/50 bg-gradient-to-br from-primary/5 to-transparent p-5 hover:border-primary/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Produk</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="totalProducts"></p>
                    <p class="mt-1 text-xs text-muted-foreground">item stok</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="rounded-lg border border-warning/30 bg-gradient-to-br from-warning/5 to-transparent p-5 hover:border-warning/50 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Stok Rendah</p>
                    <p class="mt-2 text-2xl font-bold text-warning" x-text="lowStockCount"></p>
                    <p class="mt-1 text-xs text-muted-foreground">perlu perhatian</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning/10">
                    <svg class="h-5 w-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 5v.01M7.08 6.24l1.41 1.41m3.54-3.54l1.41-1.41m3.54 3.54l1.41 1.41m3.54-3.54l1.41-1.41M5 12a7 7 0 1114 0 7 7 0 01-14 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Out of Stock -->
        <div class="rounded-lg border border-danger/30 bg-gradient-to-br from-danger/5 to-transparent p-5 hover:border-danger/50 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Stok Kosong</p>
                    <p class="mt-2 text-2xl font-bold text-danger" x-text="outOfStockCount"></p>
                    <p class="mt-1 text-xs text-muted-foreground">tidak tersedia</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-danger/10">
                    <svg class="h-5 w-5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Inventory Value -->
        <div class="rounded-lg border border-success/30 bg-gradient-to-br from-success/5 to-transparent p-5 hover:border-success/50 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Nilai Inventaris</p>
                    <p class="mt-2 text-2xl font-bold text-success" x-text="formatCurrency(totalInventoryValue)"></p>
                    <p class="mt-1 text-xs text-muted-foreground">total aset</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success/10">
                    <svg class="h-5 w-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="mb-8 rounded-lg border border-border/50 bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h3 class="text-lg font-semibold text-foreground">Filter & Pencarian</h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
                <!-- Search -->
                <div>
                    <label class="text-sm font-medium text-foreground block mb-2">Cari Produk</label>
                    <input 
                        type="text" 
                        x-model="search"
                        placeholder="Nama produk, SKU, barcode..."
                        class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
                    >
                </div>

                <!-- Stock Status Filter -->
                <div>
                    <label class="text-sm font-medium text-foreground block mb-2">Status Stok</label>
                    <select x-model="stockStatusFilter" class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="all">Semua Status</option>
                        <option value="normal">Stok Normal</option>
                        <option value="low">Stok Rendah</option>
                        <option value="out">Stok Kosong</option>
                        <option value="overstock">Overstock</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="text-sm font-medium text-foreground block mb-2">Kategori</label>
                    <select x-model="categoryFilter" class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Semua Kategori</option>
                        <?php if (isset($categories) && is_array($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <label class="text-sm font-medium text-foreground block mb-2">Sortir</label>
                    <select x-model="sortBy" class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="name">Nama Produk</option>
                        <option value="stock-low">Stok Terendah</option>
                        <option value="stock-high">Stok Tertinggi</option>
                        <option value="value-high">Nilai Tertinggi</option>
                        <option value="value-low">Nilai Terendah</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="rounded-lg border border-border/50 bg-surface shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border/50 bg-muted/30">
                        <th class="px-6 py-3 text-left text-sm font-semibold text-foreground">Produk</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-foreground">SKU</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-foreground">Stok Saat Ini</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-foreground">Min/Maks</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-foreground">Harga/Unit</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-foreground">Total Nilai</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-foreground">Status</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-foreground">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="product in filteredProducts" :key="product.id">
                        <tr class="border-b border-border/50 hover:bg-muted/30 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded bg-primary/10 flex items-center justify-center">
                                        <span class="text-xs font-bold text-primary" x-text="product.name.charAt(0).toUpperCase()"></span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-foreground" x-text="product.name"></p>
                                        <p class="text-xs text-muted-foreground" x-text="product.category_name || 'Uncategorized'"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <code class="text-sm bg-muted/50 px-2 py-1 rounded text-foreground font-mono" x-text="product.sku"></code>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-lg" x-text="product.current_stock" :class="product.current_stock === 0 ? 'text-danger' : product.current_stock <= product.min_stock ? 'text-warning' : 'text-success'"></span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm">
                                <span class="text-muted-foreground" x-text="product.min_stock + ' / ' + product.max_stock"></span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium" x-text="formatCurrency(product.price)"></td>
                            <td class="px-6 py-4 text-right font-bold" x-text="formatCurrency(product.current_stock * product.price)"></td>
                            <td class="px-6 py-4 text-center">
                                <template x-if="product.current_stock === 0">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger/10 text-danger">
                                        <?= icon('AlertCircle', 'h-3 w-3') ?>
                                        Kosong
                                    </span>
                                </template>
                                <template x-if="product.current_stock > 0 && product.current_stock <= product.min_stock">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning/10 text-warning">
                                        <?= icon('AlertTriangle', 'h-3 w-3') ?>
                                        Rendah
                                    </span>
                                </template>
                                <template x-if="product.current_stock > product.min_stock && product.current_stock <= product.max_stock">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-success/10 text-success">
                                        <?= icon('CheckCircle', 'h-3 w-3') ?>
                                        Normal
                                    </span>
                                </template>
                                <template x-if="product.current_stock > product.max_stock">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                        <?= icon('Info', 'h-3 w-3') ?>
                                        Overstock
                                    </span>
                                </template>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a :href="'<?= base_url('master/products/') ?>' + product.id" class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-border/50 text-foreground hover:bg-muted transition" title="Detail">
                                        <?= icon('Eye', 'h-4 w-4') ?>
                                    </a>
                                    <button @click="editReorder(product.id)" class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-border/50 text-foreground hover:bg-muted transition" title="Edit Min/Maks">
                                        <?= icon('Edit', 'h-4 w-4') ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <template x-if="filteredProducts.length === 0">
            <div class="p-12 text-center">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-lg bg-muted/50 mb-3">
                    <?= icon('Package', 'h-6 w-6 text-muted-foreground') ?>
                </div>
                <p class="text-foreground font-medium">Tidak ada produk yang cocok</p>
                <p class="text-sm text-muted-foreground mt-1">Coba ubah filter atau pencarian Anda</p>
            </div>
        </template>
    </div>

    <!-- Reorder Modal -->
    <template x-if="showReorderModal">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="closeReorderModal()">
            <div class="bg-surface rounded-lg shadow-lg w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-foreground">Atur Min/Maks Stok</h3>
                    <button @click="closeReorderModal()" class="text-muted-foreground hover:text-foreground transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-foreground block mb-2">Stok Minimum</label>
                        <input 
                            type="number" 
                            x-model.number="reorderForm.min_stock"
                            class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
                            placeholder="0"
                        >
                    </div>
                    <div>
                        <label class="text-sm font-medium text-foreground block mb-2">Stok Maksimum</label>
                        <input 
                            type="number" 
                            x-model.number="reorderForm.max_stock"
                            class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
                            placeholder="0"
                        >
                    </div>
                </div>

                <div class="mt-6 flex gap-3 justify-end">
                    <button @click="closeReorderModal()" class="h-10 px-4 rounded-lg border border-border/50 text-foreground font-medium hover:bg-muted transition">
                        Batal
                    </button>
                    <button @click="saveReorder()" class="h-10 px-4 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function inventoryManager() {
    return {
        products: <?= json_encode($products ?? []) ?>,
        search: '',
        stockStatusFilter: 'all',
        categoryFilter: '',
        sortBy: 'name',
        showReorderModal: false,
        reorderForm: {
            product_id: null,
            min_stock: 0,
            max_stock: 0
        },

        get filteredProducts() {
            let filtered = this.products.filter(product => {
                const searchLower = this.search.toLowerCase();
                const matchesSearch = !this.search || 
                    product.name.toLowerCase().includes(searchLower) ||
                    (product.sku && product.sku.toLowerCase().includes(searchLower));
                
                const matchesCategory = !this.categoryFilter || product.category_id == this.categoryFilter;
                
                let matchesStatus = true;
                if (this.stockStatusFilter === 'low') {
                    matchesStatus = product.current_stock > 0 && product.current_stock <= product.min_stock;
                } else if (this.stockStatusFilter === 'out') {
                    matchesStatus = product.current_stock === 0;
                } else if (this.stockStatusFilter === 'normal') {
                    matchesStatus = product.current_stock > product.min_stock && product.current_stock <= product.max_stock;
                } else if (this.stockStatusFilter === 'overstock') {
                    matchesStatus = product.current_stock > product.max_stock;
                }

                return matchesSearch && matchesCategory && matchesStatus;
            });

            // Sort
            if (this.sortBy === 'stock-low') {
                filtered.sort((a, b) => a.current_stock - b.current_stock);
            } else if (this.sortBy === 'stock-high') {
                filtered.sort((a, b) => b.current_stock - a.current_stock);
            } else if (this.sortBy === 'value-high') {
                filtered.sort((a, b) => (b.current_stock * b.price) - (a.current_stock * a.price));
            } else if (this.sortBy === 'value-low') {
                filtered.sort((a, b) => (a.current_stock * a.price) - (b.current_stock * b.price));
            } else {
                filtered.sort((a, b) => a.name.localeCompare(b.name));
            }

            return filtered;
        },

        get totalProducts() {
            return this.products.length;
        },

        get lowStockCount() {
            return this.products.filter(p => p.current_stock > 0 && p.current_stock <= p.min_stock).length;
        },

        get outOfStockCount() {
            return this.products.filter(p => p.current_stock === 0).length;
        },

        get totalInventoryValue() {
            return this.products.reduce((sum, p) => sum + (p.current_stock * p.price), 0);
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value || 0);
        },

        editReorder(productId) {
            const product = this.products.find(p => p.id === productId);
            if (product) {
                this.reorderForm.product_id = productId;
                this.reorderForm.min_stock = product.min_stock;
                this.reorderForm.max_stock = product.max_stock;
                this.showReorderModal = true;
            }
        },

        closeReorderModal() {
            this.showReorderModal = false;
            this.reorderForm = { product_id: null, min_stock: 0, max_stock: 0 };
        },

        saveReorder() {
            if (!this.reorderForm.product_id) return;
            
            // In production, this would be an AJAX call
            alert('Fitur penyimpanan akan diimplementasikan segera.');
            this.closeReorderModal();
        },

        exportCSV() {
            window.location.href = '<?= base_url('info/inventory/export-csv') ?>';
        }
    };
}
</script>

<?= $this->endSection() ?>
