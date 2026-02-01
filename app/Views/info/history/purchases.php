<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?= view('partials/page-header', ['title' => $title, 'subtitle' => $subtitle ?? '']) ?>

    <!-- Filters Section -->
    <div class="mb-6 rounded-lg border bg-card text-card-foreground shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Filter Histori Pembelian</h3>
        <div class="grid gap-4 md:grid-cols-4">
            <?= view('partials/filter-select', [
                'id' => 'supplierFilter',
                'label' => 'Supplier',
                'placeholder' => 'Semua Supplier',
                'options' => $suppliers,
                'valueKey' => 'id',
                'labelKey' => 'name'
            ]) ?>
            <?= view('partials/filter-date-range') ?>
            <?= view('partials/filter-status', ['type' => 'order']) ?>
        </div>
        <?= view('partials/filter-buttons', ['filterFn' => 'loadPurchases']) ?>
    </div>

    <!-- Purchases Table -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <h3 class="text-xl font-semibold">Histori Pembelian</h3>
        </div>
        <div class="p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th>No PO</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Status</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Diterima</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="purchasesTable">
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
    function loadPurchases() {
        const params = {
            supplier_id: document.getElementById('supplierFilter').value,
            start_date: document.getElementById('startDate').value,
            end_date: document.getElementById('endDate').value,
            status: document.getElementById('statusFilter').value
        };

        showTableLoading('purchasesTable', 7);

        fetch(buildUrl('/info/history/purchasesData', params))
            .then(response => response.json())
            .then(data => {
                renderPurchases(data);
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
            const statusClass = getStatusBadgeClass(purchase.status);

            return `
                <tr>
                    <td class="font-medium">${purchase.nomor_po}</td>
                    <td>${formatDate(purchase.tanggal_po)}</td>
                    <td>${purchase.supplier_name}</td>
                    <td><span class="badge ${statusClass}">${purchase.status}</span></td>
                    <td class="text-right font-medium">${formatCurrency(purchase.total_amount)}</td>
                    <td class="text-right">${formatCurrency(purchase.received_amount || 0)}</td>
                    <td>
                        <button onclick="viewDetail(${purchase.id_po})" class="btn btn-ghost btn-icon btn-sm" title="Detail">
                            <?= icon('Eye', 'h-4 w-4') ?>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function viewDetail(id) {
        window.open(`/transactions/purchases/detail/${id}`, '_blank');
    }

    function resetFilters() {
        document.getElementById('supplierFilter').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('statusFilter').value = '';
        loadPurchases();
    }
</script>
<?= $this->endSection() ?>
