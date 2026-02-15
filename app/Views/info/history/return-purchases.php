<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground"><?= $title ?? 'Histori Retur Pembelian' ?></h2>
            <p class="mt-1 text-sm text-muted-foreground"><?= $subtitle ?? 'Riwayat retur barang kepada supplier' ?></p>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="mb-6 grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4" x-data="{ stats: { total: 0, approved: 0, pending: 0, total_amount: 0 } }">
    <!-- Total Returns -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Total Retur</p>
                <p class="mt-2 text-2xl font-bold text-foreground" x-text="stats.total">0</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                <?= icon('FileText', 'h-6 w-6 text-primary') ?>
            </div>
        </div>
    </div>

    <!-- Approved Returns -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Disetujui</p>
                <p class="mt-2 text-2xl font-bold text-success" x-text="stats.approved">0</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-success/10">
                <?= icon('CheckCircle', 'h-6 w-6 text-success') ?>
            </div>
        </div>
    </div>

    <!-- Pending Returns -->
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

    <!-- Total Return Amount -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Total Nilai Retur</p>
                <p class="mt-2 text-xl font-bold text-secondary" x-text="formatRupiah(stats.total_amount)">Rp 0</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-secondary/10">
                <?= icon('DollarSign', 'h-6 w-6 text-secondary') ?>
            </div>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="mb-6 rounded-lg border bg-card shadow-sm p-6">
    <h3 class="text-lg font-semibold text-foreground mb-4">Filter Histori Retur Pembelian</h3>
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
                <option value="PENDING">Pending</option>
                <option value="APPROVED">Disetujui</option>
                <option value="REJECTED">Ditolak</option>
            </select>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="mt-4 flex gap-3">
        <button onclick="loadReturns()" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-primary px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
            <?= icon('Filter', 'h-4 w-4') ?>
            Terapkan Filter
        </button>
        <button onclick="resetFilters()" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-border bg-background px-4 py-2 text-sm font-medium text-foreground hover:bg-muted/50 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
            <?= icon('RotateCcw', 'h-4 w-4') ?>
            Reset Filter
        </button>
    </div>
</div>

<!-- Returns Table -->
<div class="rounded-lg border bg-card shadow-sm overflow-hidden">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground">Daftar Retur Pembelian</h2>
    </div>
    <div class="w-full overflow-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-border bg-muted/50">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">No. Retur</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Tanggal</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">No. PO</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Supplier</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Alasan</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Status</th>
                    <th class="px-6 py-3 text-right font-semibold text-foreground">Total</th>
                    <th class="px-6 py-3 text-center font-semibold text-foreground">Aksi</th>
                </tr>
            </thead>
            <tbody id="returnsTable" class="divide-y divide-border">
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <?= icon('FileText', 'h-12 w-12 text-muted-foreground/50') ?>
                            <p class="text-sm font-medium text-muted-foreground">Gunakan filter untuk menampilkan data</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadReturns();
    });

    function loadReturns() {
        const params = {
            supplier_id: document.getElementById('supplierFilter').value,
            start_date: document.getElementById('startDate').value,
            end_date: document.getElementById('endDate').value,
            status: document.getElementById('statusFilter').value
        };

        showTableLoading('returnsTable', 8);

        fetch(buildUrl('<?= base_url('info/history/purchase-returns-data') ?>', params))
            .then(response => response.json())
            .then(data => {
                renderReturns(data);
            })
            .catch(error => {
                console.error('Error:', error);
                showTableError('returnsTable', 8);
            });
    }

    function renderReturns(returns) {
        if (!returns || returns.length === 0) {
            showTableEmpty('returnsTable', 8);
            updateStats([]);
            return;
        }

        const tbody = document.getElementById('returnsTable');
        tbody.innerHTML = returns.map(ret => {
            return `
                <tr class="hover:bg-muted/50 transition-colors">
                    <td class="px-6 py-4 font-mono text-sm font-medium">${ret.no_retur}</td>
                    <td class="px-6 py-4">${formatDate(ret.tanggal_retur)}</td>
                    <td class="px-6 py-4 font-mono text-sm">${ret.no_po}</td>
                    <td class="px-6 py-4">${ret.supplier_name}</td>
                    <td class="px-6 py-4 text-muted-foreground">${ret.alasan}</td>
                    <td class="px-6 py-4">${getStatusBadge(ret.status)}</td>
                    <td class="px-6 py-4 text-right font-mono font-medium">${formatRupiah(ret.total_retur)}</td>
                    <td class="px-6 py-4">
                        <div class="flex justify-center gap-2">
                            <button onclick="viewDetail(${ret.id})" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-border bg-background hover:bg-muted/50 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors" title="Lihat Detail">
                                <?= icon('Eye', 'h-4 w-4') ?>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        updateStats(returns);
    }

    function updateStats(returns) {
        const stats = {
            total: returns.length,
            approved: returns.filter(r => r.status === 'APPROVED').length,
            pending: returns.filter(r => r.status === 'PENDING').length,
            total_amount: returns.reduce((sum, r) => sum + parseFloat(r.total_retur || 0), 0)
        };

        // Update Alpine.js stats
        const statsEl = document.querySelector('[x-data]');
        if (statsEl && statsEl.__x) {
            statsEl.__x.$data.stats = stats;
        }
    }

    function getStatusBadge(status) {
        const statusConfig = {
            'PENDING': { label: 'Pending', class: 'bg-warning/10 text-warning border-warning/30' },
            'APPROVED': { label: 'Disetujui', class: 'bg-success/10 text-success border-success/30' },
            'REJECTED': { label: 'Ditolak', class: 'bg-destructive/10 text-destructive border-destructive/30' }
        };

        const config = statusConfig[status] || { label: status, class: 'bg-muted text-muted-foreground border-border' };

        return `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border ${config.class}">${config.label}</span>`;
    }

    function viewDetail(id) {
        window.open(`/transactions/purchase-returns/detail/${id}`, '_blank');
    }

    function resetFilters() {
        document.getElementById('supplierFilter').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('statusFilter').value = '';
        loadReturns();
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', { 
            day: '2-digit', 
            month: 'short', 
            year: 'numeric' 
        });
    }

    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount || 0);
    }

    function buildUrl(path, params) {
        const url = new URL(path, window.location.origin);
        Object.keys(params).forEach(key => {
            if (params[key]) {
                url.searchParams.append(key, params[key]);
            }
        });
        return url.toString();
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

    function showTableEmpty(tableId, colspan) {
        const tbody = document.getElementById(tableId);
        tbody.innerHTML = `
            <tr>
                <td colspan="${colspan}" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <?= icon('FileText', 'h-12 w-12 text-muted-foreground/50') ?>
                        <p class="text-sm font-medium text-muted-foreground">Tidak ada data ditemukan</p>
                        <p class="text-xs text-muted-foreground">Coba ubah filter pencarian Anda</p>
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
                        <p class="text-xs text-muted-foreground">Silakan coba lagi</p>
                    </div>
                </td>
            </tr>
        `;
    }
</script>
<?= $this->endSection() ?>
