<!-- Filters Section -->
<div class="mb-6 rounded-lg border bg-card text-card-foreground shadow-sm p-6">
    <h3 class="text-lg font-semibold mb-4">Filter Histori Penjualan</h3>
    <div class="grid gap-4 md:grid-cols-5">
        <div class="space-y-2">
            <label for="customerFilter">Customer</label>
            <select id="customerFilter" class="form-input">
                <option value="">Semua Customer</option>
                <?php foreach ($customers as $customer): ?>
                <option value="<?= $customer['id'] ?>"><?= $customer['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="space-y-2">
            <label for="paymentTypeFilter">Tipe Pembayaran</label>
            <select id="paymentTypeFilter" class="form-input">
                <option value="">Semua</option>
                <option value="CASH">Tunai</option>
                <option value="CREDIT">Kredit</option>
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
            <label for="statusFilter">Status Pembayaran</label>
            <select id="statusFilter" class="form-input">
                <option value="">Semua</option>
                <option value="PAID">Lunas</option>
                <option value="UNPAID">Belum Lunas</option>
                <option value="PARTIAL">Sebagian</option>
            </select>
        </div>
    </div>
    <div class="flex gap-2 mt-4">
        <button onclick="loadSales()" class="btn btn-primary">
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

<script>
    function loadSales() {
        const customerId = document.getElementById('customerFilter').value;
        const paymentType = document.getElementById('paymentTypeFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const status = document.getElementById('statusFilter').value;
        
        const params = new URLSearchParams({
            customer_id: customerId,
            payment_type: paymentType,
            start_date: startDate,
            end_date: endDate,
            payment_status: status
        });
        
        fetch('/info/history/salesData?' + params.toString())
            .then(response => response.json())
            .then(data => {
                renderSales(data);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat data');
            });
    }

    function renderSales(sales) {
        const tbody = document.getElementById('salesTable');
        
        if (sales.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center text-muted-foreground">
                        Tidak ada data
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = sales.map(sale => {
            const statusClass = sale.payment_status === 'PAID' ? 'bg-success' : 
                            sale.payment_status === 'UNPAID' ? 'bg-destructive' : 'bg-warning';
            const statusText = sale.payment_status === 'PAID' ? 'Lunas' : 
                             sale.payment_status === 'UNPAID' ? 'Belum Lunas' : 'Sebagian';
            const paymentTypeText = sale.payment_type === 'CASH' ? 'Tunai' : 'Kredit';

            return `
                <tr>
                    <td class="font-medium">${sale.invoice_number}</td>
                    <td>${formatDate(sale.created_at)}</td>
                    <td>${sale.customer_name}</td>
                    <td>${sale.salesperson_name || '-'}</td>
                    <td><span class="badge badge-secondary">${paymentTypeText}</span></td>
                    <td><span class="badge ${statusClass}">${statusText}</span></td>
                    <td class="text-right font-medium">${formatCurrency(sale.total_amount)}</td>
                    <td class="text-right">${formatCurrency(sale.paid_amount)}</td>
                    <td>
                        <div class="flex gap-1">
                            <button onclick="viewDetail(${sale.id})" class="btn btn-ghost" style="height: 32px; width: 32px; padding: 0;" title="Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            <button onclick="printDeliveryNote(${sale.id})" class="btn btn-ghost" style="height: 32px; width: 32px; padding: 0;" title="Cetak Surat Jalan">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="6 9 6 2 18 2 18 9" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8" stroke-width="2"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
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
