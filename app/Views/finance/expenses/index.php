<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('Wallet', 'h-8 w-8 text-warning') ?>
            <?= $title ?? 'Biaya Operasional' ?>
        </h1>
        <p class="text-sm text-muted-foreground mt-1"><?= $subtitle ?? 'Kelola biaya dan pengeluaran operasional' ?></p>
    </div>
    <a href="<?= base_url('finance/expenses/create') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition whitespace-nowrap">
        <?= icon('Plus', 'h-5 w-5') ?>
        Tambah Biaya
    </a>
</div>

<!-- Alert Messages -->
<?php if (session()->getFlashdata('success')): ?>
<div class="mb-4 rounded-lg border border-success/50 bg-success/10 p-4 flex items-start gap-3">
    <?= icon('CheckCircle', 'h-5 w-5 text-success flex-shrink-0 mt-0.5') ?>
    <p class="text-sm text-success font-medium"><?= session()->getFlashdata('success') ?></p>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<div class="mb-4 rounded-lg border border-destructive/50 bg-destructive/10 p-4 flex items-start gap-3">
    <?= icon('AlertCircle', 'h-5 w-5 text-destructive flex-shrink-0 mt-0.5') ?>
    <p class="text-sm text-destructive font-medium"><?= session()->getFlashdata('error') ?></p>
</div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="grid gap-4 md:grid-cols-3 mb-6">
    <div class="rounded-lg border bg-surface p-4 flex items-center gap-4">
        <div class="h-12 w-12 rounded-lg bg-warning/15 flex items-center justify-center">
            <?= icon('TrendingDown', 'h-6 w-6 text-warning') ?>
        </div>
        <div>
            <p class="text-xs text-muted-foreground">Total Biaya</p>
            <p class="text-2xl font-bold text-warning" id="totalAmount">Rp 0</p>
        </div>
    </div>
    
    <div class="rounded-lg border bg-surface p-4 flex items-center gap-4">
        <div class="h-12 w-12 rounded-lg bg-primary/15 flex items-center justify-center">
            <?= icon('Receipt', 'h-6 w-6 text-primary') ?>
        </div>
        <div>
            <p class="text-xs text-muted-foreground">Jumlah Transaksi</p>
            <p class="text-2xl font-bold text-primary" id="totalCount">0</p>
        </div>
    </div>

    <div class="rounded-lg border bg-surface p-4">
        <p class="text-xs text-muted-foreground mb-2">Aksi</p>
        <a href="<?= base_url('finance/expenses/summary') ?>" class="inline-flex items-center gap-2 text-sm font-medium text-primary hover:text-primary/80 transition">
            <?= icon('BarChart3', 'h-4 w-4') ?>
            Lihat Ringkasan
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="rounded-lg border bg-surface p-6 mb-6">
    <div class="space-y-4">
        <h3 class="text-lg font-semibold text-foreground">Filter & Cari</h3>
        <div class="grid gap-4 md:grid-cols-5">
            <!-- Category Filter -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Kategori</label>
                <select id="categoryFilter" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $key => $label): ?>
                        <option value="<?= $key ?>"><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Start Date -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Tanggal Mulai</label>
                <input type="date" id="startDate" value="<?= date('Y-m-01') ?>" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
            </div>

            <!-- End Date -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Tanggal Akhir</label>
                <input type="date" id="endDate" value="<?= date('Y-m-d') ?>" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
            </div>

            <!-- Payment Method -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Metode Bayar</label>
                <select id="paymentMethod" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <option value="">Semua Metode</option>
                    <option value="CASH">Tunai</option>
                    <option value="TRANSFER">Transfer</option>
                    <option value="CHECK">Cek/Giro</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end gap-2">
                <button onclick="loadExpenses()" class="flex-1 h-10 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition">
                    <?= icon('Filter', 'h-4 w-4 inline mr-2') ?>
                    Filter
                </button>
                <button onclick="resetFilters()" class="h-10 px-4 border border-border/50 bg-background text-foreground font-medium rounded-lg hover:bg-muted transition">
                    <?= icon('RotateCcw', 'h-4 w-4') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Expenses Table -->
<div class="rounded-lg border bg-surface overflow-hidden">
    <div class="relative w-full overflow-auto">
        <table class="w-full text-sm">
            <thead class="bg-muted/50 border-b border-border/50">
                <tr>
                    <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">No. Biaya</th>
                    <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">Tanggal</th>
                    <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">Kategori</th>
                    <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">Deskripsi</th>
                    <th class="h-12 px-6 text-center align-middle font-medium text-muted-foreground">Metode</th>
                    <th class="h-12 px-6 text-right align-middle font-medium text-muted-foreground">Jumlah</th>
                    <th class="h-12 px-6 text-center align-middle font-medium text-muted-foreground">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/50" id="expensesTable">
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-muted-foreground">
                        <div class="flex flex-col items-center gap-2">
                            <?= icon('Package', 'h-8 w-8 opacity-50') ?>
                            <p>Klik Filter untuk menampilkan data</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 transition-opacity duration-200">
    <div class="bg-surface rounded-lg border border-border p-6 max-w-sm w-full mx-4 shadow-lg animate-in">
        <h3 class="text-lg font-bold text-foreground mb-2">Hapus Biaya?</h3>
        <p class="text-sm text-muted-foreground mb-1">Apakah Anda yakin ingin menghapus biaya ini?</p>
        <p class="text-sm text-muted-foreground font-medium mb-6" id="deleteExpenseInfo"></p>
        <div class="flex gap-3 justify-end">
            <button onclick="closeDeleteModal()" class="h-10 px-4 rounded-lg border border-border/50 font-medium text-foreground hover:bg-muted transition">
                Batal
            </button>
            <button type="button" id="confirmDelete" onclick="performDelete()" class="h-10 px-4 rounded-lg bg-destructive text-white font-medium hover:bg-destructive/90 transition">
                Hapus
            </button>
        </div>
    </div>
</div>

<script>
    let deleteExpenseId = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadExpenses();
    });

    function loadExpenses() {
        const category = document.getElementById('categoryFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const paymentMethod = document.getElementById('paymentMethod').value;

        const params = new URLSearchParams({
            category: category,
            start_date: startDate,
            end_date: endDate,
            payment_method: paymentMethod
        });

        fetch('<?= base_url('finance/expenses/getData') ?>?' + params.toString())
            .then(response => response.json())
            .then(result => {
                renderExpenses(result.data);
                document.getElementById('totalAmount').textContent = formatCurrency(result.total);
                document.getElementById('totalCount').textContent = result.count;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat data');
            });
    }

    function renderExpenses(expenses) {
        const tbody = document.getElementById('expensesTable');

        if (!expenses || expenses.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-muted-foreground">
                        <div class="flex flex-col items-center gap-2">
                            <?= icon('Package', 'h-8 w-8 opacity-50') ?>
                            <p>Tidak ada data</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        const categories = <?= json_encode($categories) ?>;

        tbody.innerHTML = expenses.map(expense => {
            const methodLabel = {
                'CASH': 'Tunai',
                'TRANSFER': 'Transfer',
                'CHECK': 'Cek/Giro'
            };

            return `
                <tr class="hover:bg-muted/50 transition">
                    <td class="px-6 py-4 font-mono text-sm text-primary">${expense.expense_number}</td>
                    <td class="px-6 py-4 text-sm text-muted-foreground">${formatDate(expense.expense_date)}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-warning/15 text-warning">
                            ${categories[expense.category] || expense.category}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-foreground">${escapeHtml(expense.description)}</td>
                    <td class="px-6 py-4 text-center text-sm text-muted-foreground">${methodLabel[expense.payment_method] || expense.payment_method}</td>
                    <td class="px-6 py-4 text-right font-bold text-warning">${formatCurrency(expense.amount)}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="<?= base_url('finance/expenses/edit/') ?>${expense.id}" 
                               class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-border/50 text-muted-foreground hover:text-primary hover:border-primary/50 transition"
                               title="Edit">
                                <?= icon('Edit', 'h-4 w-4') ?>
                            </a>
                            <button onclick="confirmDelete(${expense.id}, '${escapeHtml(expense.expense_number)}')"
                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-destructive/50 text-destructive hover:bg-destructive/10 transition"
                                    title="Hapus">
                                <?= icon('Trash2', 'h-4 w-4') ?>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function resetFilters() {
        document.getElementById('categoryFilter').value = '';
        document.getElementById('startDate').value = '<?= date('Y-m-01') ?>';
        document.getElementById('endDate').value = '<?= date('Y-m-d') ?>';
        document.getElementById('paymentMethod').value = '';
        loadExpenses();
    }

    function confirmDelete(id, expenseNumber) {
        deleteExpenseId = id;
        document.getElementById('deleteExpenseInfo').textContent = 'No. Biaya: ' + expenseNumber;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deleteExpenseId = null;
    }

    function performDelete() {
        if (!deleteExpenseId) return;

        fetch('<?= base_url('finance/expenses/delete') ?>/' + deleteExpenseId, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(result => {
            closeDeleteModal();
            if (result.success) {
                loadExpenses();
            } else {
                alert(result.message || 'Gagal menghapus data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal menghapus data');
        });
    }

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    function formatCurrency(amount) {
        return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>

<?= $this->endSection() ?>
