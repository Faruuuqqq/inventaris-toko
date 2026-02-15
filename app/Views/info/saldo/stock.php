<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('Package', 'h-8 w-8 text-primary') ?>
            <?= $title ?? 'Saldo Stok' ?>
        </h1>
        <p class="text-sm text-muted-foreground mt-1"><?= $subtitle ?? 'Analisis dan monitoring saldo stok produk' ?></p>
    </div>
    <a href="<?= base_url('/info') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ChevronLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Filters Section -->
<div class="mb-8 rounded-lg border bg-surface shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <?= icon('Filter', 'h-5 w-5 text-primary') ?>
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
            <?= icon('Filter', 'h-4 w-4') ?>
            Terapkan Filter
        </button>
        <button onclick="resetFilters()" class="inline-flex items-center justify-center gap-2 h-10 px-6 rounded-lg border border-border/50 bg-background text-foreground font-medium text-sm hover:bg-muted transition">
            <?= icon('RotateCcw', 'h-4 w-4') ?>
            Reset
        </button>
        <button onclick="exportData()" class="inline-flex items-center justify-center gap-2 h-10 px-6 rounded-lg border border-border/50 bg-background text-foreground font-medium text-sm hover:bg-muted transition ml-auto">
            <?= icon('Download', 'h-4 w-4') ?>
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
                <?= icon('Package', 'h-6 w-6 text-primary') ?>
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
                <?= icon('CheckCircle', 'h-6 w-6 text-success') ?>
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
                <?= icon('DollarSign', 'h-6 w-6 text-secondary') ?>
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
                <?= icon('AlertCircle', 'h-6 w-6 text-destructive') ?>
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
                            <?= icon('Filter', 'h-12 w-12 text-muted-foreground/50') ?>
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
                        <?= icon('Loader2', 'h-8 w-8 text-primary animate-spin') ?>
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
                                <?= icon('AlertCircle', 'h-12 w-12 text-destructive/50') ?>
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
                            <?= icon('FileText', 'h-12 w-12 text-muted-foreground/50') ?>
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
