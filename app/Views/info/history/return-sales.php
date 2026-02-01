<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?= view('partials/page-header', ['title' => $title, 'subtitle' => $subtitle ?? '']) ?>

    <!-- Filters Section -->
    <div class="mb-6 rounded-lg border bg-card text-card-foreground shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Filter Histori Retur Penjualan</h3>
        <div class="grid gap-4 md:grid-cols-4">
            <?= view('partials/filter-select', [
                'id' => 'customerFilter',
                'label' => 'Customer',
                'placeholder' => 'Semua Customer',
                'options' => $customers,
                'valueKey' => 'id',
                'labelKey' => 'name'
            ]) ?>
            <?= view('partials/filter-date-range') ?>
            <?= view('partials/filter-status', ['type' => 'return']) ?>
        </div>
        <?= view('partials/filter-buttons', ['filterFn' => 'loadReturns']) ?>
    </div>

    <!-- Returns Table -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <h3 class="text-xl font-semibold">Histori Retur Penjualan</h3>
        </div>
        <div class="p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th>No Retur</th>
                        <th>Tanggal</th>
                        <th>No Faktur</th>
                        <th>Customer</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th class="text-right">Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="returnsTable">
                    <tr>
                        <td colspan="8" class="text-center text-muted-foreground">
                            Gunakan filter untuk menampilkan data
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function loadReturns() {
        const params = {
            customer_id: document.getElementById('customerFilter').value,
            start_date: document.getElementById('startDate').value,
            end_date: document.getElementById('endDate').value,
            status: document.getElementById('statusFilter').value
        };

        showTableLoading('returnsTable', 8);

        fetch(buildUrl('/info/history/salesReturnsData', params))
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
            return;
        }

        const tbody = document.getElementById('returnsTable');
        tbody.innerHTML = returns.map(ret => {
            const statusClass = getStatusBadgeClass(ret.status);

            return `
                <tr>
                    <td class="font-medium">${ret.no_retur}</td>
                    <td>${formatDate(ret.tanggal_retur)}</td>
                    <td>${ret.no_faktur}</td>
                    <td>${ret.customer_name}</td>
                    <td>${ret.alasan}</td>
                    <td><span class="badge ${statusClass}">${ret.status}</span></td>
                    <td class="text-right font-medium">${formatCurrency(ret.total_retur)}</td>
                    <td>
                        <button onclick="viewDetail(${ret.id})" class="btn btn-ghost btn-icon btn-sm" title="Detail">
                            <?= icon('Eye', 'h-4 w-4') ?>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function viewDetail(id) {
        window.open(`/transactions/sales-returns/detail/${id}`, '_blank');
    }

    function resetFilters() {
        document.getElementById('customerFilter').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('statusFilter').value = '';
        loadReturns();
    }
</script>
<?= $this->endSection() ?>
