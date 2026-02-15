<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground"><?= $title ?? 'Histori Biaya/Jasa' ?></h2>
            <p class="mt-1 text-sm text-muted-foreground"><?= $subtitle ?? 'Riwayat pengeluaran biaya operasional dan jasa' ?></p>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="mb-6 grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4" x-data="{ stats: { total: 0, total_amount: 0, this_month: 0, avg_amount: 0 } }">
    <!-- Total Expenses -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Total Transaksi</p>
                <p class="mt-2 text-2xl font-bold text-foreground" x-text="stats.total">0</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                <?= icon('FileText', 'h-6 w-6 text-primary') ?>
            </div>
        </div>
    </div>

    <!-- Total Amount -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Total Pengeluaran</p>
                <p class="mt-2 text-xl font-bold text-destructive" x-text="formatRupiah(stats.total_amount)">Rp 0</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-destructive/10">
                <?= icon('TrendingDown', 'h-6 w-6 text-destructive') ?>
            </div>
        </div>
    </div>

    <!-- This Month -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Bulan Ini</p>
                <p class="mt-2 text-2xl font-bold text-warning" x-text="stats.this_month">0</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning/10">
                <?= icon('Calendar', 'h-6 w-6 text-warning') ?>
            </div>
        </div>
    </div>

    <!-- Average Amount -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Rata-rata</p>
                <p class="mt-2 text-xl font-bold text-secondary" x-text="formatRupiah(stats.avg_amount)">Rp 0</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-secondary/10">
                <?= icon('Calculator', 'h-6 w-6 text-secondary') ?>
            </div>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="mb-6 rounded-lg border bg-card shadow-sm p-6">
    <h3 class="text-lg font-semibold text-foreground mb-4">Filter Histori Biaya/Jasa</h3>
    <div class="grid gap-4 md:grid-cols-4">
        <!-- Category Filter -->
        <div class="space-y-2">
            <label for="categoryFilter" class="text-sm font-medium text-foreground">Kategori</label>
            <select id="categoryFilter" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                <option value="">Semua Kategori</option>
                <?php foreach ($categories as $key => $label): ?>
                    <option value="<?= $key ?>"><?= $label ?></option>
                <?php endforeach; ?>
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

        <!-- Payment Method Filter -->
        <div class="space-y-2">
            <label for="paymentMethod" class="text-sm font-medium text-foreground">Metode Pembayaran</label>
            <select id="paymentMethod" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                <option value="">Semua Metode</option>
                <option value="CASH">Tunai</option>
                <option value="TRANSFER">Transfer</option>
                <option value="CHECK">Cek/Giro</option>
            </select>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="mt-4 flex gap-3">
        <button onclick="loadExpenses()" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-primary px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
            <?= icon('Filter', 'h-4 w-4') ?>
            Terapkan Filter
        </button>
        <button onclick="resetFilters()" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-border bg-background px-4 py-2 text-sm font-medium text-foreground hover:bg-muted/50 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
            <?= icon('RotateCcw', 'h-4 w-4') ?>
            Reset Filter
        </button>
    </div>
</div>

<!-- Expenses Table -->
<div class="rounded-lg border bg-card shadow-sm overflow-hidden">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground">Daftar Biaya/Jasa</h2>
    </div>
    <div class="w-full overflow-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-border bg-muted/50">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">No. Biaya</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Tanggal</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Kategori</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Deskripsi</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Metode</th>
                    <th class="px-6 py-3 text-right font-semibold text-foreground">Jumlah</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Catatan</th>
                </tr>
            </thead>
            <tbody id="expensesTable" class="divide-y divide-border">
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-muted-foreground">
                        <div class="flex flex-col items-center gap-2">
                            <?= icon('FileText', 'h-8 w-8 opacity-50') ?>
                            <p>Klik Filter untuk menampilkan data</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    const categories = <?= json_encode($categories) ?>;

    document.addEventListener('DOMContentLoaded', function() {
        loadExpenses();
    });

    function loadExpenses() {
        const params = {
            category: document.getElementById('categoryFilter').value,
            start_date: document.getElementById('startDate').value,
            end_date: document.getElementById('endDate').value,
            payment_method: document.getElementById('paymentMethod').value
        };

        showTableLoading('expensesTable', 7);

        fetch(buildUrl('<?= base_url('info/history/expenses-data') ?>', params))
            .then(response => response.json())
            .then(data => {
                renderExpenses(data);
            })
            .catch(error => {
                console.error('Error:', error);
                showTableError('expensesTable', 7);
            });
    }

    function renderExpenses(expenses) {
        if (!expenses || expenses.length === 0) {
            showTableEmpty('expensesTable', 7);
            updateStats([]);
            return;
        }

        const tbody = document.getElementById('expensesTable');
        tbody.innerHTML = expenses.map(expense => {
            return `
                <tr class="hover:bg-muted/50 transition-colors">
                    <td class="px-6 py-4 font-mono text-sm font-medium">${expense.expense_number}</td>
                    <td class="px-6 py-4">${formatDate(expense.expense_date)}</td>
                    <td class="px-6 py-4">${getCategoryBadge(expense.category)}</td>
                    <td class="px-6 py-4">${expense.description}</td>
                    <td class="px-6 py-4">${getPaymentMethodBadge(expense.payment_method)}</td>
                    <td class="px-6 py-4 text-right font-mono font-medium">${formatRupiah(expense.amount)}</td>
                    <td class="px-6 py-4 text-muted-foreground">${expense.notes || '-'}</td>
                </tr>
            `;
        }).join('');

        updateStats(expenses);
    }

    function updateStats(expenses) {
        const now = new Date();
        const currentMonth = now.getMonth();
        const currentYear = now.getFullYear();

        const stats = {
            total: expenses.length,
            total_amount: expenses.reduce((sum, e) => sum + parseFloat(e.amount || 0), 0),
            this_month: expenses.filter(e => {
                const date = new Date(e.expense_date);
                return date.getMonth() === currentMonth && date.getFullYear() === currentYear;
            }).length,
            avg_amount: 0
        };

        stats.avg_amount = stats.total > 0 ? stats.total_amount / stats.total : 0;

        // Update Alpine.js stats
        const statsEl = document.querySelector('[x-data]');
        if (statsEl && statsEl.__x) {
            statsEl.__x.$data.stats = stats;
        }
    }

    function getCategoryBadge(category) {
        const categoryColors = {
            'RENT': 'bg-purple-100 text-purple-700 border-purple-300',
            'UTILITIES': 'bg-blue-100 text-blue-700 border-blue-300',
            'SALARY': 'bg-green-100 text-green-700 border-green-300',
            'MAINTENANCE': 'bg-yellow-100 text-yellow-700 border-yellow-300',
            'OTHER': 'bg-gray-100 text-gray-700 border-gray-300'
        };

        const color = categoryColors[category] || 'bg-secondary/10 text-secondary border-secondary/30';
        const label = categories[category] || category;

        return `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border ${color}">${label}</span>`;
    }

    function getPaymentMethodBadge(method) {
        const methodConfig = {
            'CASH': { label: 'Tunai', class: 'bg-success/10 text-success border-success/30' },
            'TRANSFER': { label: 'Transfer', class: 'bg-secondary/10 text-secondary border-secondary/30' },
            'CHECK': { label: 'Cek/Giro', class: 'bg-warning/10 text-warning border-warning/30' }
        };

        const config = methodConfig[method] || { label: method, class: 'bg-muted text-muted-foreground border-border' };

        return `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border ${config.class}">${config.label}</span>`;
    }

    function resetFilters() {
        document.getElementById('categoryFilter').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('paymentMethod').value = '';
        loadExpenses();
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
                    <td colspan="7" class="px-6 py-8 text-center text-muted-foreground">
                        <div class="flex flex-col items-center gap-2">
                            <?= icon('FileText', 'h-8 w-8 opacity-50') ?>
                            <p>Tidak ada data</p>
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
