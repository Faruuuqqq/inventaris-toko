<!-- Filters Section -->
<div class="mb-6 rounded-lg border bg-card text-card-foreground shadow-sm p-6">
    <h3 class="text-lg font-semibold mb-4">Filter Histori Pembelian</h3>
    <div class="grid gap-4 md:grid-cols-4">
        <div class="space-y-2">
            <label for="supplierFilter">Supplier</label>
            <select id="supplierFilter" class="form-input">
                <option value="">Semua Supplier</option>
                <?php foreach ($suppliers as $supplier): ?>
                <option value="<?= $supplier['id'] ?>"><?= $supplier['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="space-y-2">
            <label for="startDate">Tanggal Mulai</label>
            <input type="date" id="startDate" class="form-input">
        </div>
        <div class="space-y-2">
            <label for="endDate">Tanggal Akhir</label>
            <input type="date" id="endDate" class="form-input">
        </div>
        <div class="space-y-2">
            <label for="statusFilter">Status</label>
            <select id="statusFilter" class="form-input">
                <option value="">Semua</option>
                <option value="Dipesan">Dipesan</option>
                <option value="Sebagian">Sebagian Diterima</option>
                <option value="Diterima Semua">Diterima Semua</option>
                <option value="Dibatalkan">Dibatalkan</option>
            </select>
        </div>
    </div>
    <div class="flex gap-2 mt-4">
        <button onclick="loadPurchases()" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35"/></svg>
            Filter
        </button>
        <button onclick="resetFilters()" class="btn btn-outline">
            Reset
        </button>
        <button onclick="exportData()" class="btn btn-outline ml-auto">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4 M7 10l5 5 5-5 M12 15V3"/></svg>
            Export
        </button>
    </div>
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

<script>
    function loadPurchases() {
        const supplierId = document.getElementById('supplierFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const status = document.getElementById('statusFilter').value;
        
        const params = new URLSearchParams({
            supplier_id: supplierId,
            start_date: startDate,
            end_date: endDate,
            status: status
        });
        
        fetch('/info/history/purchasesData?' + params.toString())
            .then(response => response.json())
            .then(data => {
                renderPurchases(data);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat data');
            });
    }

    function renderPurchases(purchases) {
        const tbody = document.getElementById('purchasesTable');
        
        if (purchases.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted-foreground">
                        Tidak ada data
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = purchases.map(purchase => {
            const statusClass = purchase.status === 'Diterima Semua' ? 'bg-success' : 
                             purchase.status === 'Dibatalkan' ? 'bg-destructive' : 'bg-warning';

            return `
                <tr>
                    <td class="font-medium">${purchase.nomor_po}</td>
                    <td>${formatDate(purchase.tanggal_po)}</td>
                    <td>${purchase.supplier_name}</td>
                    <td><span class="badge ${statusClass}">${purchase.status}</span></td>
                    <td class="text-right font-medium">${formatCurrency(purchase.total_amount)}</td>
                    <td class="text-right">${formatCurrency(purchase.received_amount || 0)}</td>
                    <td>
                        <div class="flex gap-1">
                            <button onclick="viewDetail(${purchase.id_po})" class="btn btn-ghost" style="height: 32px; width: 32px; padding: 0;" title="Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
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

    function exportData() {
        window.print();
    }

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('id-ID');
    }

    function formatCurrency(amount) {
        return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
    }
</script>
