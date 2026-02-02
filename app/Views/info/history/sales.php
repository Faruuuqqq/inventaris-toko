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
                <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
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
                <svg class="h-6 w-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
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
                <svg class="h-6 w-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
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
                <svg class="h-6 w-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
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
                <?php if (isset($customers) && is_array($customers)): ?>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?= esc($customer['id']) ?>"><?= esc($customer['name']) ?></option>
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
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Terapkan Filter
        </button>
        <button type="button" onclick="resetFilters()" class="h-10 px-4 rounded-lg border border-border text-foreground font-medium hover:bg-muted transition flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
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
                            <svg class="h-12 w-12 text-muted-foreground/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <p class="text-sm font-medium text-muted-foreground">Gunakan filter untuk menampilkan data</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    let isOwner = <?= session()->get('role') === 'owner' ? 'true' : 'false' ?>;

    function loadSales() {
        const params = {
            customer_id: document.getElementById('customerFilter').value,
            payment_type: document.getElementById('paymentTypeFilter').value,
            start_date: document.getElementById('startDate').value,
            end_date: document.getElementById('endDate').value,
            payment_status: document.getElementById('statusFilter').value
        };

        showTableLoading('salesTable', 9);

        fetch(buildUrl('/info/history/sales-data', params))
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
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${isHidden
                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>'
                            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>'}
                    </svg>
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
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <button onclick="printDeliveryNote(${sale.id})" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-muted-foreground hover:text-secondary hover:bg-secondary/10 transition-colors" title="Cetak Surat Jalan">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
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

    function toggleHide(saleId) {
        if (!confirm('Yakin ingin mengubah status visibilitas penjualan ini?')) return;

        fetch('/info/history/toggleSaleHide/' + saleId, {
            method: 'POST',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest', 
                'Content-Type': 'application/json' 
            }
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
                        <svg class="h-12 w-12 text-muted-foreground/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
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
                        <svg class="animate-spin h-8 w-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
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
                        <svg class="h-12 w-12 text-destructive/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
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
                        <svg class="h-12 w-12 text-muted-foreground/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-sm font-medium text-muted-foreground">Tidak ada data ditemukan</p>
                        <p class="text-xs text-muted-foreground">Coba ubah filter pencarian Anda</p>
                    </div>
                </td>
            </tr>
        `;
    }
</script>

<?= $this->endSection() ?>
