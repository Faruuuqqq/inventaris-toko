<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?= view('partials/page-header', ['title' => $title, 'subtitle' => $subtitle ?? '']) ?>

    <!-- Filters Section -->
    <div class="mb-6 rounded-lg border bg-card text-card-foreground shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Filter Histori Penjualan</h3>
        <div class="grid gap-4 md:grid-cols-5">
            <?= view('partials/filter-select', [
                'id' => 'customerFilter',
                'label' => 'Customer',
                'placeholder' => 'Semua Customer',
                'options' => $customers,
                'valueKey' => 'id',
                'labelKey' => 'name'
            ]) ?>
            <div class="space-y-2">
                <label for="paymentTypeFilter">Tipe Pembayaran</label>
                <select id="paymentTypeFilter" class="form-input">
                    <option value="">Semua</option>
                    <option value="CASH">Tunai</option>
                    <option value="CREDIT">Kredit</option>
                </select>
            </div>
            <?= view('partials/filter-date-range') ?>
            <?= view('partials/filter-status', ['type' => 'payment']) ?>
        </div>
        <?= view('partials/filter-buttons', ['filterFn' => 'loadSales']) ?>
    </div>

    <!-- Sales Table -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <h3 class="text-xl font-semibold">Histori Penjualan</h3>
        </div>
        <div class="p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th>No Faktur</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Sales</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Dibayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="salesTable">
                    <tr>
                        <td colspan="9" class="text-center text-muted-foreground">
                            Gunakan filter untuk menampilkan data
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function loadSales() {
        const params = {
            customer_id: document.getElementById('customerFilter').value,
            payment_type: document.getElementById('paymentTypeFilter').value,
            start_date: document.getElementById('startDate').value,
            end_date: document.getElementById('endDate').value,
            payment_status: document.getElementById('statusFilter').value
        };

        showTableLoading('salesTable', 9);

        fetch(buildUrl('/info/history/salesData', params))
            .then(response => response.json())
            .then(result => {
                renderSales(result.data, result.isOwner);
            })
            .catch(error => {
                console.error('Error:', error);
                showTableError('salesTable', 9);
            });
    }

    function renderSales(sales, isOwner) {
        if (!sales || sales.length === 0) {
            showTableEmpty('salesTable', 9);
            return;
        }

        const tbody = document.getElementById('salesTable');
        tbody.innerHTML = sales.map(sale => {
            const statusClass = getStatusBadgeClass(sale.payment_status);
            const statusText = getPaymentStatusText(sale.payment_status);
            const paymentTypeText = getPaymentTypeText(sale.payment_type);
            const isHidden = sale.is_hidden == 1;
            const hiddenBadge = isHidden ? '<span class="badge bg-dark ml-1">Hidden</span>' : '';
            const rowClass = isHidden ? 'table-secondary opacity-75' : '';

            const hideButton = isOwner ? `
                <button onclick="toggleHide(${sale.id})" class="btn btn-ghost btn-icon btn-sm" title="${isHidden ? 'Tampilkan' : 'Sembunyikan'}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="${isHidden ? '#22c55e' : '#ef4444'}">
                        ${isHidden
                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>'
                            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>'}
                    </svg>
                </button>
            ` : '';

            return `
                <tr class="${rowClass}">
                    <td class="font-medium">${sale.invoice_number || sale.number}${hiddenBadge}</td>
                    <td>${formatDate(sale.created_at)}</td>
                    <td>${sale.customer_name}</td>
                    <td>${sale.salesperson_name || '-'}</td>
                    <td><span class="badge badge-secondary">${paymentTypeText}</span></td>
                    <td><span class="badge ${statusClass}">${statusText}</span></td>
                    <td class="text-right font-medium">${formatCurrency(sale.total_amount || sale.final_amount)}</td>
                    <td class="text-right">${formatCurrency(sale.paid_amount)}</td>
                    <td>
                        <div class="flex gap-1">
                            <button onclick="viewDetail(${sale.id})" class="btn btn-ghost btn-icon btn-sm" title="Detail">
                                <?= icon('Eye', 'h-4 w-4') ?>
                            </button>
                            <button onclick="printDeliveryNote(${sale.id})" class="btn btn-ghost btn-icon btn-sm" title="Cetak Surat Jalan">
                                <?= icon('Printer', 'h-4 w-4') ?>
                            </button>
                            ${hideButton}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function toggleHide(saleId) {
        if (!confirmAction('Yakin ingin mengubah status visibilitas penjualan ini?')) return;

        fetch('/info/history/toggleSaleHide/' + saleId, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                loadSales();
            } else {
                alert(result.message || 'Gagal mengubah status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengubah status');
        });
    }

    function viewDetail(id) {
        window.open(`/transactions/sales/detail/${id}`, '_blank');
    }

    function printDeliveryNote(id) {
        window.open(`/transactions/delivery-note/print/${id}`, '_blank');
    }

    function resetFilters() {
        document.getElementById('customerFilter').value = '';
        document.getElementById('paymentTypeFilter').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('statusFilter').value = '';
        loadSales();
    }
</script>
<?= $this->endSection() ?>
