<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="salesManager()">
    <!-- Page Header -->
    <div class="mb-8 flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground">Manajemen Penjualan</h2>
            <p class="mt-1 text-muted-foreground">Kelola semua transaksi penjualan tunai dan kredit</p>
        </div>
    </div>

    <!-- Summary Cards - Compact Grid -->
    <div class="mb-8 grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
        <!-- Total Transactions -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-primary/5 to-transparent p-5 hover:border-primary/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Transaksi</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="sales.length"></p>
                    <p class="mt-1 text-xs text-muted-foreground">penjualan</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <?= icon('Info', 'h-5 w-5 text-primary') ?>
                </div>
            </div>
        </div>

        <!-- Cash Sales -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-success/5 to-transparent p-5 hover:border-success/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Penjualan Tunai</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="cashCount"></p>
                    <p class="mt-1 text-xs text-muted-foreground">transaksi</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success/10">
                    <?= icon('DollarSign', 'h-5 w-5 text-success') ?>
                </div>
            </div>
        </div>

        <!-- Credit Sales -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-warning/5 to-transparent p-5 hover:border-warning/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Penjualan Kredit</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="creditCount"></p>
                    <p class="mt-1 text-xs text-muted-foreground">transaksi</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning/10">
                    <?= icon('CheckCircle', 'h-5 w-5 text-warning') ?>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="rounded-xl border border-border/50 bg-gradient-to-br from-blue/5 to-transparent p-5 hover:border-blue/30 transition-colors">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Pendapatan</p>
                    <p class="mt-2 text-2xl font-bold text-foreground" x-text="'Rp ' + formatNumber(totalRevenue)"></p>
                    <p class="mt-1 text-xs text-muted-foreground">semua penjualan</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue/10">
                    <?= icon('DollarSign', 'h-5 w-5 text-blue') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Control Bar - Professional Toolbar -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-surface rounded-xl border border-border/50 p-4">
        <!-- Left Side: Search & Filter -->
        <div class="flex gap-3 flex-1 flex-wrap">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-64">
                <?= icon('Search', 'absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground') ?>
                <input 
                    type="text" 
                    x-model="search"
                    placeholder="Cari No. Faktur atau customer..." 
                    class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 pl-10 transition-all"
                >
            </div>
            
            <!-- Customer Filter -->
            <select 
                x-model="customerFilter"
                class="flex h-10 items-center rounded-lg border border-border bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all"
            >
                <option value="all">Semua Customer</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?= $customer['id'] ?>"><?= $customer['name'] ?></option>
                <?php endforeach; ?>
            </select>

            <!-- Payment Type Filter -->
            <select 
                x-model="paymentTypeFilter"
                class="flex h-10 items-center rounded-lg border border-border bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all"
            >
                <option value="all">Semua Tipe</option>
                <option value="CASH">Tunai</option>
                <option value="CREDIT">Kredit</option>
            </select>

            <!-- Payment Status Filter -->
            <select 
                x-model="paymentStatusFilter"
                class="flex h-10 items-center rounded-lg border border-border bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all"
            >
                <option value="all">Semua Status</option>
                <option value="PAID">Lunas</option>
                <option value="PENDING">Menunggu</option>
                <option value="PARTIAL">Sebagian</option>
            </select>
        </div>

        <!-- Right Side: Action Buttons -->
        <div class="flex gap-2">
            <!-- Export Button -->
            <button 
                @click="exportSales()"
                class="inline-flex items-center justify-center rounded-lg border border-border bg-surface text-foreground hover:bg-muted/50 transition h-10 px-3 gap-2 text-sm font-medium"
                title="Export data"
            >
                <?= icon('ArrowDown', 'h-4 w-4') ?>
                <span class="hidden sm:inline">Export</span>
            </button>

            <!-- Create Sale Button -->
            <div class="relative group">
                <button 
                    class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary-light transition h-10 px-4 gap-2 text-sm font-semibold shadow-sm hover:shadow-md"
                    title="Tambah penjualan baru"
                >
                    <?= icon('Plus', 'h-5 w-5') ?>
                    <span class="hidden sm:inline">Penjualan</span>
                    <span class="sm:hidden">Buat</span>
                </button>
                <!-- Dropdown Menu -->
                <div class="absolute right-0 mt-2 w-48 bg-surface border border-border/50 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                    <a href="<?= base_url('transactions/sales/cash') ?>" class="block px-4 py-2 text-sm text-foreground hover:bg-muted/50 first:rounded-t-lg">
                        <div class="font-medium">Penjualan Tunai</div>
                        <div class="text-xs text-muted-foreground">Pembayaran langsung</div>
                    </a>
                    <a href="<?= base_url('transactions/sales/credit') ?>" class="block px-4 py-2 text-sm text-foreground hover:bg-muted/50 border-t border-border/30 last:rounded-b-lg">
                        <div class="font-medium">Penjualan Kredit</div>
                        <div class="text-xs text-muted-foreground">Cicilan atau tempo</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Table - Professional Data Grid -->
    <div class="rounded-xl border border-border/50 bg-surface shadow-sm overflow-hidden">
        <!-- Table Header with Column Info -->
        <div class="border-b border-border/50 bg-muted/30 px-6 py-3">
            <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                <span x-text="`${filteredSales.length} penjualan ditemukan`"></span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border/50 bg-background/50">
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">No. Faktur</th>
                        <th class="h-12 px-6 py-3 text-left font-semibold text-foreground uppercase text-xs tracking-wide">Customer</th>
                        <th class="h-12 px-6 py-3 text-center font-semibold text-foreground uppercase text-xs tracking-wide">Tipe</th>
                        <th class="h-12 px-6 py-3 text-center font-semibold text-foreground uppercase text-xs tracking-wide">Status Pembayaran</th>
                        <th class="h-12 px-6 py-3 text-right font-semibold text-foreground uppercase text-xs tracking-wide">Total</th>
                        <th class="h-12 px-6 py-3 text-right font-semibold text-foreground uppercase text-xs tracking-wide">Tanggal</th>
                        <th class="h-12 px-6 py-3 text-right font-semibold text-foreground uppercase text-xs tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="sale in filteredSales" :key="sale.id_sale">
                        <tr class="border-b border-border/30 hover:bg-primary/3 transition-colors duration-150">
                            <!-- Invoice Number -->
                            <td class="px-6 py-4">
                                <a :href="`<?= base_url('transactions/sales/') ?>${sale.id_sale}`" class="font-semibold text-primary hover:text-primary-light transition" x-text="sale.nomor_faktur"></a>
                            </td>

                            <!-- Customer -->
                            <td class="px-6 py-4 font-medium text-foreground" x-text="sale.customer_name || '-'"></td>

                            <!-- Sale Type Badge -->
                            <td class="px-6 py-4 text-center">
                                <span 
                                    class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold"
                                    :class="{
                                        'border-success/30 bg-success/10 text-success': sale.tipe_penjualan === 'CASH',
                                        'border-warning/30 bg-warning/10 text-warning': sale.tipe_penjualan === 'CREDIT'
                                    }"
                                    x-text="sale.tipe_penjualan === 'CASH' ? 'Tunai' : 'Kredit'">
                                </span>
                            </td>

                            <!-- Payment Status Badge -->
                            <td class="px-6 py-4 text-center">
                                <span 
                                    class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold"
                                    :class="{
                                        'border-success/30 bg-success/10 text-success': sale.status_pembayaran === 'PAID',
                                        'border-warning/30 bg-warning/10 text-warning': sale.status_pembayaran === 'PENDING',
                                        'border-blue/30 bg-blue/10 text-blue': sale.status_pembayaran === 'PARTIAL',
                                        'border-destructive/30 bg-destructive/10 text-destructive': sale.status_pembayaran === 'CANCELLED'
                                    }"
                                    x-text="getPaymentStatusLabel(sale.status_pembayaran)">
                                </span>
                            </td>

                            <!-- Total Value -->
                            <td class="px-6 py-4 text-right font-bold text-foreground" x-text="'Rp ' + formatNumber(sale.total_penjualan)"></td>

                            <!-- Date -->
                            <td class="px-6 py-4 text-right text-muted-foreground" x-text="formatDate(sale.tanggal_penjualan)"></td>

                            <!-- Action Buttons -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-1.5">
                                    <a 
                                        :href="`<?= base_url('transactions/sales/') ?>${sale.id_sale}`"
                                        class="inline-flex items-center justify-center rounded-lg border border-border bg-surface hover:bg-muted/50 transition h-9 w-9 text-foreground"
                                        title="Lihat detail"
                                    >
                                        <?= icon('Eye', 'h-4 w-4') ?>
                                    </a>
                                    <template x-if="sale.status_pembayaran !== 'PAID' && sale.status_pembayaran !== 'CANCELLED'">
                                        <button 
                                            @click="recordPayment(sale.id_sale)"
                                            class="inline-flex items-center justify-center rounded-lg border border-primary/30 bg-primary/5 hover:bg-primary/15 transition h-9 w-9 text-primary"
                                            title="Catat pembayaran"
                                        >
                                            <?= icon('DollarSign', 'h-4 w-4') ?>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <tr x-show="filteredSales.length === 0">
                        <td colspan="7" class="py-12 px-6 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <?= icon('Info', 'h-12 w-12 text-muted-foreground opacity-30') ?>
                                <p class="text-sm font-medium text-foreground">Tidak ada penjualan ditemukan</p>
                                <p class="text-xs text-muted-foreground">Coba ubah filter atau cari dengan kata kunci lain</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="border-t border-border/50 bg-muted/20 px-6 py-3 flex items-center justify-between text-xs text-muted-foreground">
            <span x-text="`Menampilkan ${filteredSales.length} dari ${sales.length} penjualan`"></span>
            <a href="<?= base_url('transactions/sales') ?>" class="text-primary hover:text-primary-light font-semibold transition">
                Refresh
            </a>
        </div>
    </div>
</div>

<script>
function salesManager() {
    return {
        sales: <?= json_encode($sales ?? []) ?>,
        search: '',
        customerFilter: 'all',
        paymentTypeFilter: 'all',
        paymentStatusFilter: 'all',

        get filteredSales() {
            return this.sales.filter(sale => {
                const searchLower = this.search.toLowerCase();
                const matchesSearch = (sale.nomor_faktur && sale.nomor_faktur.toLowerCase().includes(searchLower)) ||
                                    (sale.customer_name && sale.customer_name.toLowerCase().includes(searchLower));
                
                const matchesCustomer = this.customerFilter === 'all' || 
                                       sale.id_customer == this.customerFilter;
                
                const matchesPaymentType = this.paymentTypeFilter === 'all' || 
                                         sale.tipe_penjualan === this.paymentTypeFilter;
                
                const matchesPaymentStatus = this.paymentStatusFilter === 'all' || 
                                           sale.status_pembayaran === this.paymentStatusFilter;
                                          
                return matchesSearch && matchesCustomer && matchesPaymentType && matchesPaymentStatus;
            });
        },

        get cashCount() {
            return this.sales.filter(s => s.tipe_penjualan === 'CASH').length;
        },

        get creditCount() {
            return this.sales.filter(s => s.tipe_penjualan === 'CREDIT').length;
        },

        get totalRevenue() {
            return this.sales.reduce((sum, s) => sum + (parseFloat(s.total_penjualan) || 0), 0);
        },

        recordPayment(saleId) {
            alert('Fitur pencatatan pembayaran akan diimplementasikan segera.');
            // window.location.href = `<?= base_url('finance/payments/receivable') ?>?sale_id=${saleId}`;
        },

        exportSales() {
            alert('Fitur export akan diimplementasikan segera.');
        },

        getPaymentStatusLabel(status) {
            const labels = {
                'PAID': 'Lunas',
                'PENDING': 'Menunggu',
                'PARTIAL': 'Sebagian',
                'CANCELLED': 'Dibatalkan'
            };
            return labels[status] || status;
        },

        formatNumber(value) {
            return parseFloat(value || 0).toLocaleString('id-ID');
        },

        formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
        }
    }
}
</script>

<?= $this->endSection() ?>
