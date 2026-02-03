<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <svg class="h-8 w-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m0 0v10l8 4"/>
            </svg>
            <?= $title ?? 'Saldo Stok' ?>
        </h1>
        <p class="text-sm text-muted-foreground mt-1"><?= $subtitle ?? 'Analisis dan monitoring saldo stok produk' ?></p>
    </div>
    <a href="<?= base_url('/info') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<!-- Filters Section -->
<div class="mb-8 rounded-lg border bg-surface shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Filter Saldo Stok
        </h3>
    </div>
    <div class="grid gap-4 md:grid-cols-3">
        <div class="space-y-2">
            <label for="categoryFilter" class="text-sm font-medium text-foreground">Kategori</label>
            <select id="categoryFilter" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                <option value="">Semua Kategori</option>
                <?php foreach ($categories ?? [] as $category): ?>
                <option value="<?= esc($category->id) ?>"><?= esc($category->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="space-y-2">
            <label for="warehouseFilter" class="text-sm font-medium text-foreground">Gudang</label>
            <select id="warehouseFilter" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                <option value="">Semua Gudang</option>
                <?php foreach ($warehouses ?? [] as $warehouse): ?>
                <option value="<?= esc($warehouse->id) ?>"><?= esc($warehouse->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="space-y-2">
            <label for="stockStatus" class="text-sm font-medium text-foreground">Status Stok</label>
            <select id="stockStatus" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                <option value="">Semua</option>
                <option value="low">Stok Rendah</option>
                <option value="normal">Stok Normal</option>
                <option value="high">Stok Tinggi</option>
            </select>
        </div>
    </div>
    <div class="flex gap-2 mt-4">
        <button onclick="loadStockBalance()" class="inline-flex items-center justify-center gap-2 h-10 px-6 rounded-lg bg-primary text-white font-medium text-sm hover:bg-primary/90 transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Terapkan Filter
        </button>
        <button onclick="resetFilters()" class="inline-flex items-center justify-center gap-2 h-10 px-6 rounded-lg border border-border/50 bg-background text-foreground font-medium text-sm hover:bg-muted transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Reset
        </button>
        <button onclick="exportData()" class="inline-flex items-center justify-center gap-2 h-10 px-6 rounded-lg border border-border/50 bg-background text-foreground font-medium text-sm hover:bg-muted transition ml-auto">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2m0 0v-8m0 8H7m5 0h5"/>
            </svg>
            Export
        </button>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Total Products -->
    <div class="rounded-lg border bg-surface shadow-sm p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Total Produk</p>
                <p class="text-2xl font-bold text-foreground mt-2" id="totalProducts">0</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m0 0v10l8 4"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total Stock -->
    <div class="rounded-lg border bg-surface shadow-sm p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Total Stok</p>
                <p class="text-2xl font-bold text-foreground mt-2" id="totalStock">0</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success/10">
                <svg class="h-6 w-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Stock Value -->
    <div class="rounded-lg border bg-surface shadow-sm p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Nilai Stok</p>
                <p class="text-2xl font-bold text-foreground mt-2" id="stockValue">Rp 0</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary/10">
                <svg class="h-6 w-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="rounded-lg border bg-surface shadow-sm p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Stok Rendah</p>
                <p class="text-2xl font-bold text-destructive mt-2" id="lowStock">0</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-destructive/10">
                <svg class="h-6 w-6 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Stock Balance Table -->
<div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground">Saldo Stok Detail</h2>
    </div>
    <div class="w-full overflow-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-border bg-muted/50">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Kode</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Nama Produk</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Kategori</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Gudang</th>
                    <th class="px-6 py-3 text-right font-semibold text-foreground">Stok</th>
                    <th class="px-6 py-3 text-right font-semibold text-foreground">Min. Stok</th>
                    <th class="px-6 py-3 text-right font-semibold text-foreground">Harga Beli</th>
                    <th class="px-6 py-3 text-right font-semibold text-foreground">Nilai</th>
                </tr>
            </thead>
            <tbody id="stockTable" class="divide-y divide-border">
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="h-12 w-12 text-muted-foreground/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            <p class="text-sm font-medium text-muted-foreground">Gunakan filter untuk menampilkan data</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    function loadStockBalance() {
        const categoryId = document.getElementById('categoryFilter').value;
        const warehouseId = document.getElementById('warehouseFilter').value;
        const stockStatus = document.getElementById('stockStatus').value;

        const params = new URLSearchParams({
            category_id: categoryId,
            warehouse_id: warehouseId,
            stock_status: stockStatus
        });

        // Show loading state
        const tbody = document.getElementById('stockTable');
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <svg class="animate-spin h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-sm text-muted-foreground">Memuat data...</p>
                    </div>
                </td>
            </tr>
        `;

        fetch('<?= base_url('/info/saldo/stock-data') ?>?' + params.toString())
            .then(response => response.json())
            .then(data => {
                renderStockBalance(data);
            })
            .catch(error => {
                console.error('Error:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="h-12 w-12 text-destructive/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm font-medium text-destructive">Gagal memuat data</p>
                                <p class="text-xs text-muted-foreground">Silakan coba lagi</p>
                            </div>
                        </td>
                    </tr>
                `;
            });
    }

    function renderStockBalance(data) {
        const tbody = document.getElementById('stockTable');

        if (data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="h-12 w-12 text-muted-foreground/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm font-medium text-muted-foreground">Tidak ada data</p>
                            <p class="text-xs text-muted-foreground">Coba ubah filter pencarian Anda</p>
                        </div>
                    </td>
                </tr>
            `;

            // Reset summary
            document.getElementById('totalProducts').textContent = '0';
            document.getElementById('totalStock').textContent = '0';
            document.getElementById('stockValue').textContent = 'Rp 0';
            document.getElementById('lowStock').textContent = '0';
            return;
        }

        // Calculate summary
        const totalProducts = new Set(data.map(item => item.product_id)).size;
        const totalStock = data.reduce((sum, item) => sum + parseInt(item.quantity), 0);
        const stockValue = data.reduce((sum, item) => sum + (parseInt(item.quantity) * parseFloat(item.price_buy)), 0);
        const lowStock = data.filter(item => parseInt(item.quantity) <= parseInt(item.min_stock_alert)).length;

        document.getElementById('totalProducts').textContent = totalProducts;
        document.getElementById('totalStock').textContent = totalStock.toLocaleString('id-ID');
        document.getElementById('stockValue').textContent = formatCurrency(stockValue);
        document.getElementById('lowStock').textContent = lowStock;

        tbody.innerHTML = data.map(item => {
            const stockClass = parseInt(item.quantity) <= parseInt(item.min_stock_alert) 
                ? 'text-destructive font-bold' 
                : 'text-foreground';

            return `
                <tr class="hover:bg-muted/50 transition-colors">
                    <td class="px-6 py-4 font-mono text-xs font-medium text-muted-foreground">${esc(item.product_code)}</td>
                    <td class="px-6 py-4 font-medium text-foreground">${esc(item.product_name)}</td>
                    <td class="px-6 py-4 text-muted-foreground">${esc(item.category_name || '-')}</td>
                    <td class="px-6 py-4 text-muted-foreground">${esc(item.warehouse_name)}</td>
                    <td class="px-6 py-4 text-right ${stockClass}">${item.quantity}</td>
                    <td class="px-6 py-4 text-right text-muted-foreground">${item.min_stock_alert}</td>
                    <td class="px-6 py-4 text-right text-muted-foreground">${formatCurrency(item.price_buy)}</td>
                    <td class="px-6 py-4 text-right font-medium text-foreground">${formatCurrency(item.quantity * item.price_buy)}</td>
                </tr>
            `;
        }).join('');
    }

    function resetFilters() {
        document.getElementById('categoryFilter').value = '';
        document.getElementById('warehouseFilter').value = '';
        document.getElementById('stockStatus').value = '';
        loadStockBalance();
    }

    function exportData() {
        window.print();
    }

    function formatCurrency(amount) {
        return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
    }

    function esc(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Auto-load on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadStockBalance();
    });
</script>

<?= $this->endSection() ?>
