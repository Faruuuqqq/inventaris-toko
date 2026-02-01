<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?= view('partials/page-header', ['title' => $title, 'subtitle' => $subtitle ?? '']) ?>

    <!-- Filters Section -->
    <div class="mb-6 rounded-lg border bg-card text-card-foreground shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Filter Pembayaran Piutang</h3>
        <div class="grid gap-4 md:grid-cols-5">
            <?= view('partials/filter-select', [
                'id' => 'customerFilter',
                'label' => 'Customer',
                'placeholder' => 'Semua Customer',
                'options' => $customers,
                'valueKey' => 'id',
                'labelKey' => 'name'
            ]) ?>
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
        <?= view('partials/filter-buttons', ['filterFn' => 'loadPayments']) ?>
    </div>

    <!-- Summary Cards -->
    <div class="grid gap-4 md:grid-cols-2 mb-6">
        <?= view('partials/stat-card', [
            'label' => 'Total Pembayaran Diterima',
            'value' => 0,
            'icon' => 'TrendingUp',
            'color' => 'success',
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

    <!-- Payments Table -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <h3 class="text-xl font-semibold">Histori Pembayaran Piutang</h3>
        </div>
        <div class="p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Pembayaran</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>No. Faktur</th>
                        <th>Metode</th>
                        <th class="text-right">Jumlah</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody id="paymentsTable">
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
    document.addEventListener('DOMContentLoaded', function() {
        loadPayments();
    });

    function loadPayments() {
        const params = {
            customer_id: document.getElementById('customerFilter').value,
            start_date: document.getElementById('startDate').value,
            end_date: document.getElementById('endDate').value,
            payment_method: document.getElementById('paymentMethod').value
        };

        showTableLoading('paymentsTable', 7);

        fetch(buildUrl('/info/history/paymentsReceivableData', params))
            .then(response => response.json())
            .then(data => {
                renderPayments(data);
            })
            .catch(error => {
                console.error('Error:', error);
                showTableError('paymentsTable', 7);
            });
    }

    function renderPayments(payments) {
        if (!payments || payments.length === 0) {
            showTableEmpty('paymentsTable', 7);
            updateSummary(0, 0);
            return;
        }

        const methodLabels = { 'CASH': 'Tunai', 'TRANSFER': 'Transfer', 'CHECK': 'Cek/Giro' };
        let total = 0;

        const tbody = document.getElementById('paymentsTable');
        tbody.innerHTML = payments.map(payment => {
            total += parseFloat(payment.amount);
            return `
                <tr>
                    <td class="font-medium">${payment.payment_number}</td>
                    <td>${formatDate(payment.payment_date)}</td>
                    <td>${payment.customer_name || '-'}</td>
                    <td>${payment.invoice_number || '-'}</td>
                    <td><span class="badge badge-secondary">${methodLabels[payment.payment_method] || payment.payment_method}</span></td>
                    <td class="text-right font-medium">${formatCurrency(payment.amount)}</td>
                    <td>${payment.notes || '-'}</td>
                </tr>
            `;
        }).join('');

        updateSummary(total, payments.length);
    }

    function updateSummary(total, count) {
        // Update stat cards dynamically
        const cards = document.querySelectorAll('.text-2xl.font-bold');
        if (cards[0]) cards[0].textContent = formatCurrency(total);
        if (cards[1]) cards[1].textContent = count;
    }

    function resetFilters() {
        document.getElementById('customerFilter').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('paymentMethod').value = '';
        loadPayments();
    }
</script>
<?= $this->endSection() ?>
