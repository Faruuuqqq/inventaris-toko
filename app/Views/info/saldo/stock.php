<!-- Filters Section -->
<div class="mb-6 rounded-lg border bg-card text-card-foreground shadow-sm p-6">
    <h3 class="text-lg font-semibold mb-4">Filter Saldo Stok</h3>
    <div class="grid gap-4 md:grid-cols-3">
        <div class="space-y-2">
            <label for="categoryFilter">Kategori</label>
            <select id="categoryFilter" class="form-input">
                <option value="">Semua Kategori</option>
                <?php foreach ($categories ?? [] as $category): ?>
                <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="space-y-2">
            <label for="warehouseFilter">Gudang</label>
            <select id="warehouseFilter" class="form-input">
                <option value="">Semua Gudang</option>
                <?php foreach ($warehouses ?? [] as $warehouse): ?>
                <option value="<?= $warehouse['id'] ?>"><?= $warehouse['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="space-y-2">
            <label for="stockStatus">Status Stok</label>
            <select id="stockStatus" class="form-input">
                <option value="">Semua</option>
                <option value="low">Stok Rendah</option>
                <option value="normal">Stok Normal</option>
                <option value="high">Stok Tinggi</option>
            </select>
        </div>
    </div>
    <div class="flex gap-2 mt-4">
        <button onclick="loadStockBalance()" class="btn btn-primary">
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

<!-- Summary Cards -->
<div class="grid gap-6 md:grid-cols-4 mb-6">
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
        <div class="flex flex-col space-y-2">
            <p class="text-sm text-muted-foreground">Total Produk</p>
            <p class="text-2xl font-bold" id="totalProducts">0</p>
        </div>
    </div>
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
        <div class="flex flex-col space-y-2">
            <p class="text-sm text-muted-foreground">Total Stok</p>
            <p class="text-2xl font-bold" id="totalStock">0</p>
        </div>
    </div>
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
        <div class="flex flex-col space-y-2">
            <p class="text-sm text-muted-foreground">Nilai Stok</p>
            <p class="text-2xl font-bold" id="stockValue">Rp 0</p>
        </div>
    </div>
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6">
        <div class="flex flex-col space-y-2">
            <p class="text-sm text-muted-foreground">Stok Rendah</p>
            <p class="text-2xl font-bold text-destructive" id="lowStock">0</p>
        </div>
    </div>
</div>

<!-- Stock Balance Table -->
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="p-6">
        <h3 class="text-xl font-semibold">Saldo Stok</h3>
    </div>
    <div class="p-0">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Gudang</th>
                    <th class="text-right">Stok</th>
                    <th class="text-right">Min. Stok</th>
                    <th class="text-right">Harga Beli</th>
                    <th class="text-right">Nilai</th>
                </tr>
            </thead>
            <tbody id="stockTable">
                <tr>
                    <td colspan="8" class="text-center text-muted-foreground">
                        Gunakan filter untuk menampilkan data
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    function loadStockBalance() {
        const categoryId = document.getElementById('categoryFilter').value;
        const warehouseId = document.getElementById('warehouseFilter').value;
        const stockStatus = document.getElementById('stockStatus').value;
        
        const params = new URLSearchParams({
            category_id: categoryId,
            warehouse_id: warehouseId,
            stock_status: stockStatus
        });
        
        fetch('/info/saldo/stockData?' + params.toString())
            .then(response => response.json())
            .then(data => {
                renderStockBalance(data);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat data');
            });
    }

    function renderStockBalance(data) {
        const tbody = document.getElementById('stockTable');
        
        if (data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-muted-foreground">
                        Tidak ada data
                    </td>
                </tr>
            `;
            
            // Reset summary
            document.getElementById('totalProducts').textContent = '0';
            document.getElementById('totalStock').textContent = '0';
            document.getElementById('stockValue').textContent = 'Rp 0';
            document.getElementById('lowStock').textContent = '0';
            return;
        }

        // Calculate summary
        const totalProducts = new Set(data.map(item => item.product_id)).size;
        const totalStock = data.reduce((sum, item) => sum + item.quantity, 0);
        const stockValue = data.reduce((sum, item) => sum + (item.quantity * item.price_buy), 0);
        const lowStock = data.filter(item => item.quantity <= item.min_stock_alert).length;

        document.getElementById('totalProducts').textContent = totalProducts;
        document.getElementById('totalStock').textContent = totalStock.toLocaleString('id-ID');
        document.getElementById('stockValue').textContent = formatCurrency(stockValue);
        document.getElementById('lowStock').textContent = lowStock;

        tbody.innerHTML = data.map(item => {
            const stockClass = item.quantity <= item.min_stock_alert ? 'text-destructive font-bold' : '';
            
            return `
                <tr>
                    <td class="font-medium">${item.product_code}</td>
                    <td>${item.product_name}</td>
                    <td>${item.category_name || '-'}</td>
                    <td>${item.warehouse_name}</td>
                    <td class="text-right ${stockClass}">${item.quantity}</td>
                    <td class="text-right">${item.min_stock_alert}</td>
                    <td class="text-right">${formatCurrency(item.price_buy)}</td>
                    <td class="text-right font-medium">${formatCurrency(item.quantity * item.price_buy)}</td>
                </tr>
            `;
        }).join('');
    }

    function resetFilters() {
        document.getElementById('categoryFilter').value = '';
        document.getElementById('warehouseFilter').value = '';
        document.getElementById('stockStatus').value = '';
        loadStockBalance();
    }

    function exportData() {
        window.print();
    }

    function formatCurrency(amount) {
        return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
    }

    // Auto-load on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadStockBalance();
    });
</script>
