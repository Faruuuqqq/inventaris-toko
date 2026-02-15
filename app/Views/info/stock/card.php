<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- Filters Section -->
<div class="mb-6 rounded-lg border bg-card text-card-foreground shadow-sm p-6">
    <h3 class="text-lg font-semibold mb-4">Filter Kartu Stok</h3>
    <div class="grid gap-4 md:grid-cols-4">
         <div class="space-y-2">
             <label for="productFilter" class="text-sm font-medium">Produk</label>
             <select id="productFilter" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
                 <option value="">Semua Produk</option>
                 <?php foreach ($products as $product): ?>
                 <option value="<?= is_array($product) ? $product['id'] : $product->id ?>"><?= is_array($product) ? $product['name'] : $product->name ?></option>
                 <?php endforeach; ?>
             </select>
         </div>
         <div class="space-y-2">
             <label for="warehouseFilter" class="text-sm font-medium">Gudang</label>
             <select id="warehouseFilter" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
                 <option value="">Semua Gudang</option>
                 <?php foreach ($warehouses as $warehouse): ?>
                 <option value="<?= esc($warehouse->id ?? $warehouse['id'] ?? '') ?>"><?= esc($warehouse->name ?? $warehouse['name'] ?? '') ?></option>
                 <?php endforeach; ?>
             </select>
         </div>
         <div class="space-y-2">
             <label for="startDate" class="text-sm font-medium">Tanggal Mulai</label>
             <input type="date" id="startDate" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
         </div>
         <div class="space-y-2">
             <label for="endDate" class="text-sm font-medium">Tanggal Akhir</label>
             <input type="date" id="endDate" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all">
         </div>
     </div>
     <div class="flex gap-2 mt-4">
         <button onclick="loadMutations()" class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary/90 transition h-10 px-4 gap-2 text-sm font-semibold shadow-sm hover:shadow-md">
             <?= icon('Search', 'h-4 w-4') ?>
             Filter
         </button>
         <button onclick="resetFilters()" class="inline-flex items-center justify-center rounded-lg border border-border bg-background text-foreground hover:bg-muted transition h-10 px-4 gap-2 text-sm font-semibold">
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
         <table class="w-full border-collapse">
             <thead class="bg-muted/50 border-b border-border">
                 <tr>
                     <th class="px-4 py-3 text-left text-sm font-semibold text-foreground">Tanggal</th>
                     <th class="px-4 py-3 text-left text-sm font-semibold text-foreground">Produk</th>
                     <th class="px-4 py-3 text-left text-sm font-semibold text-foreground">Gudang</th>
                     <th class="px-4 py-3 text-left text-sm font-semibold text-foreground">Tipe</th>
                     <th class="px-4 py-3 text-left text-sm font-semibold text-foreground">Qty</th>
                     <th class="px-4 py-3 text-left text-sm font-semibold text-foreground">Saldo Akhir</th>
                     <th class="px-4 py-3 text-left text-sm font-semibold text-foreground">Referensi</th>
                     <th class="px-4 py-3 text-left text-sm font-semibold text-foreground">Keterangan</th>
                 </tr>
             </thead>
             <tbody id="mutationsTable" class="divide-y divide-border">
                 <!-- Data will be loaded via AJAX -->
                 <tr>
                     <td colspan="9" class="text-center text-muted-foreground py-8">
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

        fetch('<?= base_url('info/stock/getMutations') ?>?' + params.toString())
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
                     <td colspan="9" class="text-center text-muted-foreground py-8">
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
                 <td class="px-4 py-3 text-sm">${formatDate(mutation.created_at)}</td>
                 <td class="px-4 py-3 text-sm">${mutation.product_name}</td>
                 <td class="px-4 py-3 text-sm">${mutation.warehouse_name}</td>
                 <td class="px-4 py-3 text-sm"><span class="badge ${typeClass}">${typeName}</span></td>
                 <td class="px-4 py-3 text-sm">${mutation.quantity}</td>
                 <td class="px-4 py-3 text-sm">${mutation.current_balance}</td>
                 <td class="px-4 py-3 text-sm">${mutation.reference_number || '-'}</td>
                 <td class="px-4 py-3 text-sm">${mutation.notes || '-'}</td>
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
