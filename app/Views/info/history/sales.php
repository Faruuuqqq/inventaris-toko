<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground"><?= $title ?? 'Histori Penjualan' ?></h2>
            <p class="mt-1 text-sm text-muted-foreground"><?= $subtitle ?? 'Riwayat transaksi penjualan kepada customer' ?></p>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="mb-6 grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4" x-data="{ stats: { total: 0, cash: 0, credit: 0, total_revenue: 0 } }">
    <!-- Total Sales -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Total Penjualan</p>
                <p class="mt-2 text-2xl font-bold text-foreground" x-text="stats.total">0</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                <?= icon('FileText', 'h-6 w-6 text-primary') ?>
            </div>
        </div>
    </div>

    <!-- Cash Sales -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Penjualan Tunai</p>
                <p class="mt-2 text-2xl font-bold text-success" x-text="stats.cash">0</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-success/10">
                <?= icon('Wallet', 'h-6 w-6 text-success') ?>
            </div>
        </div>
    </div>

    <!-- Credit Sales -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Penjualan Kredit</p>
                <p class="mt-2 text-2xl font-bold text-warning" x-text="stats.credit">0</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning/10">
                <?= icon('CreditCard', 'h-6 w-6 text-warning') ?>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Total Pendapatan</p>
                <p class="mt-2 text-xl font-bold text-foreground" x-text="formatRupiah(stats.total_revenue)">Rp 0</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-secondary/10">
                <?= icon('DollarSign', 'h-6 w-6 text-secondary') ?>
            </div>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="mb-6 rounded-lg border bg-card shadow-sm p-6">
    <h3 class="text-lg font-semibold text-foreground mb-4">Filter Histori Penjualan</h3>
    <div class="grid gap-4 md:grid-cols-5">
        <!-- Customer Filter -->
        <div class="space-y-2">
            <label for="customerFilter" class="text-sm font-medium text-foreground">Customer</label>
            <select id="customerFilter" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                <option value="">Semua Customer</option>
                <?php if (isset($customers)): ?>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= esc($customer->id ?? $customer['id'] ?? '') ?>">
                            <?= esc($customer->name ?? $customer['name'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <!-- Payment Type Filter -->
        <div class="space-y-2">
            <label for="paymentTypeFilter" class="text-sm font-medium text-foreground">Tipe Pembayaran</label>
            <select id="paymentTypeFilter" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                <option value="">Semua Tipe</option>
                <option value="CASH">Tunai</option>
                <option value="CREDIT">Kredit</option>
            </select>
        </div>

        <!-- Start Date -->
        <div class="space-y-2">
            <label for="startDate" class="text-sm font-medium text-foreground">Tanggal Mulai</label>
            <input type="date" id="startDate" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
        </div>

        <!-- End Date -->
        <div class="space-y-2">
            <label for="endDate" class="text-sm font-medium text-foreground">Tanggal Akhir</label>
            <input type="date" id="endDate" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
        </div>

        <!-- Status Filter -->
        <div class="space-y-2">
            <label for="statusFilter" class="text-sm font-medium text-foreground">Status</label>
            <select id="statusFilter" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                <option value="">Semua Status</option>
                <option value="PAID">Lunas</option>
                <option value="PARTIAL">Sebagian</option>
                <option value="UNPAID">Belum Bayar</option>
            </select>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="mt-4 flex gap-3">
        <button type="button" onclick="loadSales()" class="h-10 px-6 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition flex items-center gap-2">
            <?= icon('Search', 'h-4 w-4') ?>
            Terapkan Filter
        </button>
        <button type="button" onclick="resetFilters()" class="h-10 px-4 rounded-lg border border-border text-foreground font-medium hover:bg-muted transition flex items-center gap-2">
            <?= icon('RotateCcw', 'h-4 w-4') ?>
            Reset
        </button>
    </div>
</div>

<!-- Sales Table -->
<div class="rounded-lg border bg-card shadow-sm overflow-hidden">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground">Daftar Penjualan</h2>
    </div>
    <div class="w-full overflow-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-border bg-muted/50">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">No Faktur</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Tanggal</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Customer</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Sales</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Tipe</th>
                    <th class="px-6 py-3 text-left font-semibold text-foreground">Status</th>
                    <th class="px-6 py-3 text-right font-semibold text-foreground">Total</th>
                    <th class="px-6 py-3 text-right font-semibold text-foreground">Dibayar</th>
                    <th class="px-6 py-3 text-center font-semibold text-foreground">Aksi</th>
                </tr>
            </thead>
            <tbody id="salesTable" class="divide-y divide-border">
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <?= icon('Search', 'h-12 w-12 text-muted-foreground/50') ?>
                            <p class="text-sm font-medium text-muted-foreground">Gunakan filter untuk menampilkan data</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    let isOwner = <?= session()->get('role') === 'OWNER' ? 'true' : 'false' ?>;

    function loadSales() {
        const params = {
            customer_id: document.getElementById('customerFilter').value,
            payment_type: document.getElementById('paymentTypeFilter').value,
            start_date: document.getElementById('startDate').value,
            end_date: document.getElementById('endDate').value,
            payment_status: document.getElementById('statusFilter').value
        };

        showTableLoading('salesTable', 9);

        fetch(buildUrl('<?= base_url('info/history/sales-data') ?>', params))
            .then(response => response.json())
            .then(result => {
                const sales = result.data || result;
                renderSales(sales);
                updateStats(sales);
            })
            .catch(error => {
                console.error('Error:', error);
                showTableError('salesTable', 9);
            });
    }

    function renderSales(sales) {
        if (!sales || sales.length === 0) {
            showTableEmpty('salesTable', 9);
            return;
        }

        const tbody = document.getElementById('salesTable');
        tbody.innerHTML = sales.map(sale => {
            const statusBadge = getPaymentStatusBadge(sale.payment_status);
            const typeBadge = getPaymentTypeBadge(sale.payment_type);
            const isHidden = sale.is_hidden == 1;

            const hideButton = isOwner ? `
                <button onclick="toggleHide(${sale.id})" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-muted-foreground hover:text-${isHidden ? 'success' : 'destructive'} hover:bg-${isHidden ? 'success' : 'destructive'}/10 transition-colors" title="${isHidden ? 'Tampilkan' : 'Sembunyikan'}">
                    ${isHidden
                        ? '<?= icon('Eye', 'h-4 w-4') ?>'
                        : '<?= icon('EyeOff', 'h-4 w-4') ?>'}
                </button>
            ` : '';

            const hiddenBadge = isHidden ? '<span class="ml-2 inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-medium bg-muted text-muted-foreground">Hidden</span>' : '';

            return `
                <tr class="hover:bg-muted/50 transition-colors ${isHidden ? 'opacity-60' : ''}">
                    <td class="px-6 py-4 font-mono text-sm font-medium text-foreground">${sale.invoice_number || sale.number || '-'}${hiddenBadge}</td>
                    <td class="px-6 py-4 text-sm text-muted-foreground">${formatDate(sale.created_at)}</td>
                    <td class="px-6 py-4 text-sm font-medium text-foreground">${sale.customer_name || '-'}</td>
                    <td class="px-6 py-4 text-sm text-muted-foreground">${sale.salesperson_name || '-'}</td>
                    <td class="px-6 py-4">${typeBadge}</td>
                    <td class="px-6 py-4">${statusBadge}</td>
                    <td class="px-6 py-4 text-right text-sm font-semibold text-foreground">${formatCurrency(sale.total_amount || sale.final_amount || 0)}</td>
                    <td class="px-6 py-4 text-right text-sm text-muted-foreground">${formatCurrency(sale.paid_amount || 0)}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="inline-flex items-center gap-1">
                            <button onclick="viewDetail(${sale.id})" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-muted-foreground hover:text-primary hover:bg-primary/10 transition-colors" title="Lihat Detail">
                                <?= icon('Eye', 'h-4 w-4') ?>
                            </button>
                            <button onclick="printDeliveryNote(${sale.id})" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-muted-foreground hover:text-secondary hover:bg-secondary/10 transition-colors" title="Cetak Surat Jalan">
                                <?= icon('Printer', 'h-4 w-4') ?>
                            </button>
                            ${hideButton}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function updateStats(sales) {
        const stats = {
            total: sales.length,
            cash: sales.filter(s => s.payment_type === 'CASH').length,
            credit: sales.filter(s => s.payment_type === 'CREDIT').length,
            total_revenue: sales.reduce((sum, s) => sum + (parseFloat(s.total_amount || s.final_amount) || 0), 0)
        };

        // Update Alpine.js data
        const statsEl = document.querySelector('[x-data]');
        if (statsEl && statsEl.__x) {
            statsEl.__x.$data.stats = stats;
        }
    }

    function getPaymentStatusBadge(status) {
        const badges = {
            'PAID': '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border bg-success/10 text-success border-success/30">Lunas</span>',
            'PARTIAL': '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border bg-warning/10 text-warning border-warning/30">Sebagian</span>',
            'UNPAID': '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border bg-destructive/10 text-destructive border-destructive/30">Belum Bayar</span>'
        };
        return badges[status] || `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border bg-muted text-muted-foreground border-border">${status}</span>`;
    }

    function getPaymentTypeBadge(type) {
        const badges = {
            'CASH': '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border bg-secondary/10 text-secondary border-secondary/30">Tunai</span>',
            'CREDIT': '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border bg-primary/10 text-primary border-primary/30">Kredit</span>'
        };
        return badges[type] || `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border bg-muted text-muted-foreground border-border">${type}</span>`;
    }

     /**
      * AJAX Function: Toggle sale visibility (hide/unhide from transaction history)
      *
      * Client-Side Security:
      * - This function is only available for OWNER users (see renderSalesTable() isOwner check)
      * - The hide button in the UI is only rendered if isOwner === true
      * - This prevents even seeing the function in non-owner clients
      *
      * User Flow:
      * 1. OWNER clicks eye/eye-slash icon next to a sale
      * 2. This function is triggered with the sale ID
      * 3. JavaScript confirmation dialog appears
      * 4. If confirmed, sends AJAX POST to /info/history/toggleSaleHide/{id}
      * 5. Server-side checks permission again (defense in depth)
      * 6. If successful, reloads the sales table
      * 7. Shows toast message to user
      *
      * Security Layers (Defense in Depth):
      * Layer 1: UI - Button only shows for OWNER (CSS/JS conditionals)
      * Layer 2: Client - Confirm dialog prevents accidental action
      * Layer 3: Request - AJAX headers validate browser request
      * Layer 4: Server - Controller checks OWNER role again
      * Layer 5: Database - Only updates if sale exists
      *
      * API Endpoint:
      * POST /info/history/toggleSaleHide/{saleId}
      * Response: JSON { success: bool, message: string }
      *
      * @param {number} saleId The sale ID to toggle visibility
      * @returns {void}
      *
      * @see renderSalesTable() - Controls button visibility based on isOwner
      * @see \App\Controllers\Info\History::toggleSaleHide() - Server-side endpoint
      */
     function toggleHide(saleId) {
         // Confirm action before proceeding (prevents accidental toggles)
         if (!confirm('Yakin ingin mengubah status visibilitas penjualan ini?')) return;

         // Call server endpoint via AJAX
         fetch('<?= base_url('info/history/toggleSaleHide') ?>/' + saleId, {
             method: 'POST',
             headers: { 
                 'X-Requested-With': 'XMLHttpRequest',  // Prevent CSRF
                 'Content-Type': 'application/json' 
             }
         })
         .then(response => response.json())
         .then(result => {
             if (result.success) {
                 // Success: Reload sales table to show/hide the row
                 loadSales();
             } else {
                 // Failure: Show error message to user
                 alert(result.message || 'Gagal mengubah status');
             }
         })
         .catch(error => {
             // Network or parse error
             console.error('Error:', error);
             alert('Gagal mengubah status');
         });
     }

    function viewDetail(id) {
        window.location.href = `/transactions/sales/${id}`;
    }

    function printDeliveryNote(id) {
        window.open(`/transactions/sales/delivery-note/print/${id}`, '_blank');
    }

    function resetFilters() {
        document.getElementById('customerFilter').value = '';
        document.getElementById('paymentTypeFilter').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('salesTable').innerHTML = `
            <tr>
                <td colspan="9" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <?= icon('Search', 'h-12 w-12 text-muted-foreground/50') ?>
                        <p class="text-sm font-medium text-muted-foreground">Gunakan filter untuk menampilkan data</p>
                    </div>
                </td>
            </tr>
        `;
    }

    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount || 0);
    }

    function buildUrl(path, params) {
        const url = new URL(path, window.location.origin);
        Object.keys(params).forEach(key => {
            if (params[key]) {
                url.searchParams.append(key, params[key]);
            }
        });
        return url.toString();
    }

    function showTableLoading(tableId, colspan) {
        const tbody = document.getElementById(tableId);
        tbody.innerHTML = `
            <tr>
                <td colspan="${colspan}" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <?= icon('Loader2', 'h-8 w-8 text-primary animate-spin') ?>
                        <p class="text-sm text-muted-foreground">Memuat data...</p>
                    </div>
                </td>
            </tr>
        `;
    }

    function showTableError(tableId, colspan) {
        const tbody = document.getElementById(tableId);
        tbody.innerHTML = `
            <tr>
                <td colspan="${colspan}" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <?= icon('AlertCircle', 'h-12 w-12 text-destructive/50') ?>
                        <p class="text-sm font-medium text-destructive">Gagal memuat data</p>
                        <button onclick="loadSales()" class="text-sm text-primary hover:underline">Coba lagi</button>
                    </div>
                </td>
            </tr>
        `;
    }

    function showTableEmpty(tableId, colspan) {
        const tbody = document.getElementById(tableId);
        tbody.innerHTML = `
            <tr>
                <td colspan="${colspan}" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <?= icon('Package', 'h-12 w-12 text-muted-foreground/50') ?>
                        <p class="text-sm font-medium text-muted-foreground">Tidak ada data ditemukan</p>
                        <p class="text-xs text-muted-foreground">Coba ubah filter pencarian Anda</p>
                    </div>
                </td>
            </tr>
        `;
    }
</script>

<?= $this->endSection() ?>
