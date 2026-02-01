<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- Filters Section -->
<div class="mb-6 rounded-lg border bg-card text-card-foreground shadow-sm p-6">
    <h3 class="text-lg font-semibold mb-4">Filter Kartu Stok</h3>
    <div class="grid gap-4 md:grid-cols-4">
        <div class="space-y-2">
            <label for="productFilter">Produk</label>
            <select id="productFilter" class="form-input">
                <option value="">Semua Produk</option>
                <?php foreach ($products as $product): ?>
                <option value="<?= is_array($product) ? $product['id'] : $product->id ?>"><?= is_array($product) ? $product['name'] : $product->name ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="space-y-2">
            <label for="warehouseFilter">Gudang</label>
            <select id="warehouseFilter" class="form-input">
                <option value="">Semua Gudang</option>
                <?php foreach ($warehouses as $warehouse): ?>
                <option value="<?= is_array($warehouse) ? $warehouse['id'] : $warehouse->id ?>"><?= is_array($warehouse) ? $warehouse['name'] : $warehouse->name ?></option>
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
    </div>
    <div class="flex gap-2 mt-4">
        <button onclick="loadMutations()" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35"/></svg>
            Filter
        </button>
        <button onclick="resetFilters()" class="btn btn-outline">
            Reset
        </button>
    </div>
</div>

<!-- Stock Mutations Table -->
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="p-6">
        <h3 class="text-xl font-semibold">Histori Mutasi Stok</h3>
    </div>
    <div class="p-0">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>Gudang</th>
                    <th>Tipe</th>
                    <th>Qty</th>
                    <th>Saldo Akhir</th>
                    <th>Referensi</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody id="mutationsTable">
                <!-- Data will be loaded via AJAX -->
                <tr>
                    <td colspan="9" class="text-center text-muted-foreground">
                        Pilih produk dan filter untuk menampilkan data
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    function loadMutations() {
        const productId = document.getElementById('productFilter').value;
        const warehouseId = document.getElementById('warehouseFilter').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        const params = new URLSearchParams({
            product_id: productId,
            warehouse_id: warehouseId,
            start_date: startDate,
            end_date: endDate
        });

        fetch('/info/stock/getMutations?' + params.toString())
            .then(response => response.json())
            .then(data => {
                renderMutations(data);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat data');
            });
    }

    function renderMutations(mutations) {
        const tbody = document.getElementById('mutationsTable');
        tbody.innerHTML = '';

        if (mutations.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center text-muted-foreground">
                        Tidak ada data mutasi yang ditemukan
                    </td>
                </tr>
            `;
            return;
        }

        mutations.forEach(mutation => {
            const row = document.createElement('tr');

            const typeClass = {
                'IN': 'badge-success',
                'OUT': 'badge-destructive',
                'ADJUSTMENT_IN': 'badge-success',
                'ADJUSTMENT_OUT': 'badge-destructive',
                'TRANSFER': 'badge-warning'
            }[mutation.type] || 'badge-secondary';

            const typeName = {
                'IN': 'Masuk',
                'OUT': 'Keluar',
                'ADJUSTMENT_IN': 'Adjus Masuk',
                'ADJUSTMENT_OUT': 'Adjus Keluar',
                'TRANSFER': 'Pindah'
            }[mutation.type] || mutation.type;

            row.innerHTML = `
                <td>${formatDate(mutation.created_at)}</td>
                <td>${mutation.product_name}</td>
                <td>${mutation.warehouse_name}</td>
                <td><span class="badge ${typeClass}">${typeName}</span></td>
                <td>${mutation.quantity}</td>
                <td>${mutation.current_balance}</td>
                <td>${mutation.reference_number || '-'}</td>
                <td>${mutation.notes || '-'}</td>
            `;
            tbody.appendChild(row);
        });
    }

    function resetFilters() {
        document.getElementById('productFilter').value = '';
        document.getElementById('warehouseFilter').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';

        document.getElementById('mutationsTable').innerHTML = `
            <tr>
                <td colspan="9" class="text-center text-muted-foreground">
                    Pilih produk dan filter untuk menampilkan data
                </td>
            </tr>
        `;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });
    }

    // Load initial data on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Set default date range (last 30 days)
        const endDate = new Date();
        const startDate = new Date();
        startDate.setDate(endDate.getDate() - 30);

        document.getElementById('endDate').value = endDate.toISOString().split('T')[0];
        document.getElementById('startDate').value = startDate.toISOString().split('T')[0];

        loadMutations();
    });
</script>
<?= $this->endSection() ?>
