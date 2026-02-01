<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?= view('partials/page-header', ['title' => $title, 'subtitle' => $subtitle ?? '']) ?>

    <!-- Filters Section -->
    <div class="mb-6 rounded-lg border bg-card text-card-foreground shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Filter Histori Biaya/Jasa</h3>
        <div class="grid gap-4 md:grid-cols-5">
            <div class="space-y-2">
                <label for="categoryFilter">Kategori</label>
                <select id="categoryFilter" class="form-input">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $key => $label): ?>
                    <option value="<?= $key ?>"><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?= view('partials/filter-date-range') ?>
            <div class="space-y-2">
                <label for="paymentMethod">Metode Pembayaran</label>
                <select id="paymentMethod" class="form-input">
                    <option value="">Semua</option>
                    <option value="CASH">Tunai</option>
                    <option value="TRANSFER">Transfer</option>
                    <option value="CHECK">Cek/Giro</option>
                </select>
            </div>
        </div>
        <?= view('partials/filter-buttons', ['filterFn' => 'loadExpenses']) ?>
    </div>

    <!-- Summary Cards -->
    <div class="grid gap-4 md:grid-cols-2 mb-6">
        <?= view('partials/stat-card', [
            'label' => 'Total Pengeluaran',
            'value' => 0,
            'icon' => 'TrendingDown',
            'color' => 'destructive',
            'format' => 'none'
        ]) ?>
        <?= view('partials/stat-card', [
            'label' => 'Jumlah Transaksi',
            'value' => 0,
            'icon' => 'FileText',
            'color' => 'primary',
            'format' => 'none'
        ]) ?>
    </div>

    <!-- Expenses Table -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <h3 class="text-xl font-semibold">Histori Biaya/Jasa</h3>
        </div>
        <div class="p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Biaya</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th>Metode</th>
                        <th class="text-right">Jumlah</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody id="expensesTable">
                    <tr>
                        <td colspan="7" class="text-center text-muted-foreground">
                            Gunakan filter untuk menampilkan data
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
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

        fetch(buildUrl('/info/history/expensesData', params))
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
            updateSummary(0, 0);
            return;
        }

        const methodLabels = { 'CASH': 'Tunai', 'TRANSFER': 'Transfer', 'CHECK': 'Cek/Giro' };
        let total = 0;

        const tbody = document.getElementById('expensesTable');
        tbody.innerHTML = expenses.map(expense => {
            total += parseFloat(expense.amount);
            return `
                <tr>
                    <td class="font-medium">${expense.expense_number}</td>
                    <td>${formatDate(expense.expense_date)}</td>
                    <td><span class="badge badge-secondary">${categories[expense.category] || expense.category}</span></td>
                    <td>${expense.description}</td>
                    <td><span class="badge badge-secondary">${methodLabels[expense.payment_method] || expense.payment_method}</span></td>
                    <td class="text-right font-medium">${formatCurrency(expense.amount)}</td>
                    <td>${expense.notes || '-'}</td>
                </tr>
            `;
        }).join('');

        updateSummary(total, expenses.length);
    }

    function updateSummary(total, count) {
        const cards = document.querySelectorAll('.text-2xl.font-bold');
        if (cards[0]) cards[0].textContent = formatCurrency(total);
        if (cards[1]) cards[1].textContent = count;
    }

    function resetFilters() {
        document.getElementById('categoryFilter').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('paymentMethod').value = '';
        loadExpenses();
    }
</script>
<?= $this->endSection() ?>
