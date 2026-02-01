<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h3 class="page-title"><?= $title ?></h3>
            <p class="text-muted"><?= $subtitle ?? '' ?></p>
        </div>
        <div class="col-auto">
            <a href="<?= base_url('/finance/expenses/create') ?>" class="btn btn-primary">
                <i data-lucide="plus"></i> Tambah Biaya
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Filter</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="categoryFilter" class="form-label">Kategori</label>
                    <select id="categoryFilter" class="form-select">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $key => $label): ?>
                            <option value="<?= $key ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="startDate" class="form-label">Tanggal Mulai</label>
                    <input type="date" id="startDate" class="form-control" value="<?= date('Y-m-01') ?>">
                </div>
                <div class="col-md-2">
                    <label for="endDate" class="form-label">Tanggal Akhir</label>
                    <input type="date" id="endDate" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-2">
                    <label for="paymentMethod" class="form-label">Metode Bayar</label>
                    <select id="paymentMethod" class="form-select">
                        <option value="">Semua</option>
                        <option value="CASH">Tunai</option>
                        <option value="TRANSFER">Transfer</option>
                        <option value="CHECK">Cek/Giro</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button onclick="loadExpenses()" class="btn btn-primary">Filter</button>
                    <button onclick="resetFilters()" class="btn btn-outline-secondary">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Biaya</h5>
                    <h3 id="totalAmount">Rp 0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Jumlah Transaksi</h5>
                    <h3 id="totalCount">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Aksi</h5>
                    <a href="<?= base_url('/finance/expenses/summary') ?>" class="btn btn-outline-primary btn-sm">
                        Lihat Ringkasan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Daftar Biaya</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No. Biaya</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Metode</th>
                            <th class="text-end">Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="expensesTable">
                        <tr>
                            <td colspan="7" class="text-center text-muted">Klik Filter untuk menampilkan data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus biaya ini?</p>
                <p class="text-muted" id="deleteExpenseInfo"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
            </div>
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

        fetch('<?= base_url('/finance/expenses/getData') ?>?' + params.toString())
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
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Tidak ada data</td></tr>';
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
                <tr>
                    <td><code>${expense.expense_number}</code></td>
                    <td>${formatDate(expense.expense_date)}</td>
                    <td><span class="badge bg-secondary">${categories[expense.category] || expense.category}</span></td>
                    <td>${expense.description}</td>
                    <td>${methodLabel[expense.payment_method] || expense.payment_method}</td>
                    <td class="text-end fw-bold">${formatCurrency(expense.amount)}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="<?= base_url('/finance/expenses/edit') ?>/${expense.id}" class="btn btn-outline-primary" title="Edit">
                                <i data-lucide="edit" style="width:14px;height:14px;"></i>
                            </a>
                            <button onclick="confirmDelete(${expense.id}, '${expense.expense_number}')" class="btn btn-outline-danger" title="Hapus">
                                <i data-lucide="trash" style="width:14px;height:14px;"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        // Re-initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
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
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (!deleteExpenseId) return;

        fetch('<?= base_url('/finance/expenses/delete') ?>/' + deleteExpenseId, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(result => {
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
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
    });

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('id-ID');
    }

    function formatCurrency(amount) {
        return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
    }
</script>

<?= $this->endSection() ?>
