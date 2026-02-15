<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground"><?= $title ?? 'Histori Pembelian' ?></h2>
            <p class="mt-1 text-sm text-muted-foreground"><?= $subtitle ?? 'Riwayat transaksi pembelian dari supplier' ?></p>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="mb-6 grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4" x-data="{ stats: { total: 0, pending: 0, completed: 0, total_amount: 0 } }">
    <!-- Total Purchase Orders -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Total PO</p>
                <p class="mt-2 text-2xl font-bold text-foreground" x-text="stats.total">0</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                <?= icon('FileText', 'h-6 w-6 text-primary') ?>
            </div>
        </div>
    </div>

    <!-- Pending Orders -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Pending</p>
                <p class="mt-2 text-2xl font-bold text-warning" x-text="stats.pending">0</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning/10">
                <?= icon('Clock', 'h-6 w-6 text-warning') ?>
            </div>
        </div>
    </div>

    <!-- Completed Orders -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Completed</p>
                <p class="mt-2 text-2xl font-bold text-success" x-text="stats.completed">0</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-success/10">
                <?= icon('CheckCircle', 'h-6 w-6 text-success') ?>
            </div>
        </div>
    </div>

    <!-- Total Amount -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Total Pembelian</p>
                <p class="mt-2 text-xl font-bold text-foreground" x-text="formatRupiah(stats.total_amount)">Rp 0</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-secondary/10">
                <?= icon('DollarSign', 'h-6 w-6 text-secondary') ?>
            </div>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="mb-6 rounded-lg border bg-card shadow-sm p-6">
    <h3 class="text-lg font-semibold text-foreground mb-4">Filter Histori Pembelian</h3>
    <div class="grid gap-4 md:grid-cols-4">
        <!-- Supplier Filter -->
        <div class="space-y-2">
            <label for="supplierFilter" class="text-sm font-medium text-foreground">Supplier</label>
            <select id="supplierFilter" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                <option value="">Semua Supplier</option>
                <?php if (isset($suppliers) && is_array($suppliers)): ?>
                    <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?= esc($supplier->id ?? $supplier['id'] ?? '') ?>"><?= esc($supplier->name ?? $supplier['name'] ?? '') ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <!-- Start Date -->
        <div class="space-y-2">
            <label for="startDate" class="text-sm font-medium text-foreground">Tanggal Mulai</label>
            <input type="date" id="startDate" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
        </div>

        <!-- End Date -->
        <div class="space-y-2">
            <label for="endDate" class="text-sm font-medium text-foreground">Tanggal Akhir</label>
            <input type="date" id="endDate" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
        </div>

        <!-- Status Filter -->
        <div class="space-y-2">
            <label for="statusFilter" class="text-sm font-medium text-foreground">Status</label>
            <select id="statusFilter" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                <option value="">Semua Status</option>
                <option value="Pending">Pending</option>
                <option value="Diterima Sebagian">Diterima Sebagian</option>
                <option value="Diterima Semua">Diterima Semua</option>
            </select>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="mt-4 flex gap-3">
        <button type="button" onclick="loadPurchases()" class="h-10 px-6 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition flex items-center gap-2">
            <?= icon('Search', 'h-4 w-4') ?>
            Terapkan Filter
        </button>
        <button type="button" onclick="resetFilters()" class="h-10 px-4 rounded-lg border border-border text-foreground font-medium hover:bg-muted transition flex items-center gap-2">
            <?= icon('RotateCcw', 'h-4 w-4') ?>
            Reset
        </button>
    </div>
</div>

<!-- Purchases Table -->
<div class="rounded-lg border bg-card shadow-sm overflow-hidden">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground">Daftar Pembelian</h2>
    </div>
    <div class="w-full overflow-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-border bg-muted/50">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">No PO</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Tanggal</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Supplier</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Status</th>
                    <th class="px-6 py-3 text-right font-semibold text-foreground">Total</th>
                    <th class="px-6 py-3 text-right font-semibold text-foreground">Diterima</th>
                    <th class="px-6 py-3 text-center font-semibold text-foreground">Aksi</th>
                </tr>
            </thead>
            <tbody id="purchasesTable" class="divide-y divide-border">
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <?= icon('Search', 'h-12 w-12 text-muted-foreground/50') ?>
                            <p class="text-sm font-medium text-muted-foreground">Gunakan filter untuk menampilkan data</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    function loadPurchases() {
        const params = {
            supplier_id: document.getElementById('supplierFilter').value,
            start_date: document.getElementById('startDate').value,
            end_date: document.getElementById('endDate').value,
            status: document.getElementById('statusFilter').value
        };

        showTableLoading('purchasesTable', 7);

        fetch(buildUrl('<?= base_url('info/history/purchases-data') ?>', params))
            .then(response => response.json())
            .then(data => {
                renderPurchases(data);
                updateStats(data);
            })
            .catch(error => {
                console.error('Error:', error);
                showTableError('purchasesTable', 7);
            });
    }

    function renderPurchases(purchases) {
        if (!purchases || purchases.length === 0) {
            showTableEmpty('purchasesTable', 7);
            return;
        }

        const tbody = document.getElementById('purchasesTable');
        tbody.innerHTML = purchases.map(purchase => {
            const statusBadge = getStatusBadge(purchase.status);

            return `
                <tr class="hover:bg-muted/50 transition-colors">
                    <td class="px-6 py-4 font-mono text-sm font-medium text-foreground">${purchase.nomor_po || '-'}</td>
                    <td class="px-6 py-4 text-sm text-muted-foreground">${formatDate(purchase.tanggal_po)}</td>
                    <td class="px-6 py-4 text-sm font-medium text-foreground">${purchase.supplier_name || '-'}</td>
                    <td class="px-6 py-4">${statusBadge}</td>
                    <td class="px-6 py-4 text-right text-sm font-semibold text-foreground">${formatCurrency(purchase.total_amount || 0)}</td>
                    <td class="px-6 py-4 text-right text-sm text-muted-foreground">${formatCurrency(purchase.received_amount || 0)}</td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="viewDetail(${purchase.id_po})" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-muted-foreground hover:text-primary hover:bg-primary/10 transition-colors" title="Lihat Detail">
                            <?= icon('Eye', 'h-4 w-4') ?>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function updateStats(purchases) {
        const stats = {
            total: purchases.length,
            pending: purchases.filter(p => p.status === 'Pending').length,
            completed: purchases.filter(p => p.status === 'Diterima Semua').length,
            total_amount: purchases.reduce((sum, p) => sum + (parseFloat(p.total_amount) || 0), 0)
        };

        // Update Alpine.js data
        const statsEl = document.querySelector('[x-data]');
        if (statsEl && statsEl.__x) {
            statsEl.__x.$data.stats = stats;
        }
    }

    function getStatusBadge(status) {
        const badges = {
            'Pending': '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border bg-warning/10 text-warning border-warning/30">Pending</span>',
            'Diterima Sebagian': '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border bg-secondary/10 text-secondary border-secondary/30">Sebagian</span>',
            'Diterima Semua': '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border bg-success/10 text-success border-success/30">Completed</span>'
        };
        return badges[status] || `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border bg-muted text-muted-foreground border-border">${status}</span>`;
    }

    function viewDetail(id) {
        window.location.href = `/transactions/purchases/${id}`;
    }

    function resetFilters() {
        document.getElementById('supplierFilter').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('purchasesTable').innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <?= icon('Search', 'h-12 w-12 text-muted-foreground/50') ?>
                        <p class="text-sm font-medium text-muted-foreground">Gunakan filter untuk menampilkan data</p>
                    </div>
                </td>
            </tr>
        `;
    }

    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount || 0);
    }

    function showTableLoading(tableId, colspan) {
        const tbody = document.getElementById(tableId);
        tbody.innerHTML = `
            <tr>
                <td colspan="${colspan}" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <?= icon('Loader2', 'h-8 w-8 text-primary animate-spin') ?>
                        <p class="text-sm text-muted-foreground">Memuat data...</p>
                    </div>
                </td>
            </tr>
        `;
    }

    function showTableError(tableId, colspan) {
        const tbody = document.getElementById(tableId);
        tbody.innerHTML = `
            <tr>
                <td colspan="${colspan}" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <?= icon('AlertCircle', 'h-12 w-12 text-destructive/50') ?>
                        <p class="text-sm font-medium text-destructive">Gagal memuat data</p>
                        <button onclick="loadPurchases()" class="text-sm text-primary hover:underline">Coba lagi</button>
                    </div>
                </td>
            </tr>
        `;
    }

    function showTableEmpty(tableId, colspan) {
        const tbody = document.getElementById(tableId);
        tbody.innerHTML = `
            <tr>
                <td colspan="${colspan}" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <?= icon('Package', 'h-12 w-12 text-muted-foreground/50') ?>
                        <p class="text-sm font-medium text-muted-foreground">Tidak ada data ditemukan</p>
                        <p class="text-xs text-muted-foreground">Coba ubah filter pencarian Anda</p>
                    </div>
                </td>
            </tr>
        `;
    }
</script>

<?= $this->endSection() ?>
