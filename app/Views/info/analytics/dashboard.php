<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="analyticsManager()">
    <!-- Page Header -->
    <div class="mb-8 flex items-start justify-between">
        <div>
            <h2 class="text-3xl font-bold text-foreground flex items-center gap-3">
                <?= icon('BarChart', 'h-8 w-8 text-primary') ?>
                Ringkasan Bisnis
            </h2>
            <p class="mt-1 text-muted-foreground">Ringkasan pendapatan, profit, dan transaksi</p>
        </div>
        <div class="flex gap-3">
            <button @click="exportReport()" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border text-foreground font-medium rounded-lg hover:bg-muted transition">
                <?= icon('Download', 'h-5 w-5') ?>
                Export
            </button>
            <button @click="refreshData()" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition">
                <?= icon('RotateCcw', 'h-5 w-5') ?>
                Refresh
            </button>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="mb-8 rounded-lg border border-border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="grid gap-4 grid-cols-1 md:grid-cols-4">
                <div>
                    <label class="text-sm font-medium text-foreground block mb-2">Tanggal Mulai</label>
                    <input
                        type="date"
                        x-model="dateFrom"
                        class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
                    >
                </div>
                <div>
                    <label class="text-sm font-medium text-foreground block mb-2">Tanggal Akhir</label>
                    <input
                        type="date"
                        x-model="dateTo"
                        class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
                    >
                </div>
                <div>
                    <label class="text-sm font-medium text-foreground block mb-2">Periode Cepat</label>
                    <select @change="setQuickPeriod($event.target.value)" class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Periode</option>
                        <option value="today">Hari Ini</option>
                        <option value="week">7 Hari Terakhir</option>
                        <option value="month">30 Hari Terakhir</option>
                        <option value="quarter">90 Hari Terakhir</option>
                        <option value="year">Tahun Ini</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button @click="applyFilter()" class="w-full h-10 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="mb-8 grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
        <!-- Total Revenue -->
        <div class="rounded-lg border border-border bg-gradient-to-br from-success/5 to-transparent p-6 hover:border-success/50 transition-colors">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-success/10">
                    <?= icon('DollarSign', 'h-6 w-6 text-success') ?>
                </div>
                <div class="flex items-center gap-1 text-xs font-medium" :class="stats.revenueGrowth >= 0 ? 'text-success' : 'text-destructive'">
                    <template x-if="stats.revenueGrowth >= 0">
                        <?= icon('ArrowUp', 'h-3 w-3') ?>
                    </template>
                    <template x-if="stats.revenueGrowth < 0">
                        <?= icon('ArrowDown', 'h-3 w-3') ?>
                    </template>
                    <span x-text="Math.abs(stats.revenueGrowth) + '%'"></span>
                </div>
            </div>
            <p class="text-sm font-medium text-muted-foreground">Total Pendapatan</p>
            <p class="mt-2 text-3xl font-bold text-foreground" x-text="formatCurrency(stats.totalRevenue)"></p>
        </div>

        <!-- Total Profit -->
        <div class="rounded-lg border border-border bg-gradient-to-br from-primary/5 to-transparent p-6 hover:border-primary/50 transition-colors">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                    <?= icon('TrendingUp', 'h-6 w-6 text-primary') ?>
                </div>
                <div class="flex items-center gap-1 text-xs font-medium" :class="stats.profitGrowth >= 0 ? 'text-success' : 'text-destructive'">
                    <template x-if="stats.profitGrowth >= 0">
                        <?= icon('ArrowUp', 'h-3 w-3') ?>
                    </template>
                    <template x-if="stats.profitGrowth < 0">
                        <?= icon('ArrowDown', 'h-3 w-3') ?>
                    </template>
                    <span x-text="Math.abs(stats.profitGrowth) + '%'"></span>
                </div>
            </div>
            <p class="text-sm font-medium text-muted-foreground">Total Profit</p>
            <p class="mt-2 text-3xl font-bold text-foreground" x-text="formatCurrency(stats.totalProfit)"></p>
        </div>

        <!-- Total Transactions -->
        <div class="rounded-lg border border-border bg-gradient-to-br from-blue-500/5 to-transparent p-6 hover:border-blue-500/50 transition-colors">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500/10">
                    <?= icon('ShoppingCart', 'h-6 w-6 text-blue-500') ?>
                </div>
                <div class="flex items-center gap-1 text-xs font-medium" :class="stats.transactionGrowth >= 0 ? 'text-success' : 'text-destructive'">
                    <template x-if="stats.transactionGrowth >= 0">
                        <?= icon('ArrowUp', 'h-3 w-3') ?>
                    </template>
                    <template x-if="stats.transactionGrowth < 0">
                        <?= icon('ArrowDown', 'h-3 w-3') ?>
                    </template>
                    <span x-text="Math.abs(stats.transactionGrowth) + '%'"></span>
                </div>
            </div>
            <p class="text-sm font-medium text-muted-foreground">Total Transaksi</p>
            <p class="mt-2 text-3xl font-bold text-foreground" x-text="stats.totalTransactions.toLocaleString('id-ID')"></p>
        </div>

        <!-- Average Order Value -->
        <div class="rounded-lg border border-border bg-gradient-to-br from-purple-500/5 to-transparent p-6 hover:border-purple-500/50 transition-colors">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500/10">
                    <?= icon('TrendingUp', 'h-6 w-6 text-purple-500') ?>
                </div>
                <div class="flex items-center gap-1 text-xs font-medium" :class="stats.avgGrowth >= 0 ? 'text-success' : 'text-destructive'">
                    <template x-if="stats.avgGrowth >= 0">
                        <?= icon('ArrowUp', 'h-3 w-3') ?>
                    </template>
                    <template x-if="stats.avgGrowth < 0">
                        <?= icon('ArrowDown', 'h-3 w-3') ?>
                    </template>
                    <span x-text="Math.abs(stats.avgGrowth) + '%'"></span>
                </div>
            </div>
            <p class="text-sm font-medium text-muted-foreground">Rata-rata Pesanan</p>
            <p class="mt-2 text-3xl font-bold text-foreground" x-text="formatCurrency(stats.avgOrderValue)"></p>
        </div>
    </div>

    <!-- Revenue by Category Table -->
    <div class="mb-8 rounded-lg border border-border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border bg-muted/30">
            <h3 class="text-lg font-semibold text-foreground">Pendapatan per Kategori</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border bg-muted/20">
                        <th class="px-6 py-3 text-left text-sm font-semibold text-foreground">Kategori</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-foreground">Pendapatan</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-foreground">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="category in revenueByCategory" :key="category.name">
                        <tr class="border-b border-border hover:bg-muted/30 transition">
                            <td class="px-6 py-4">
                                <p class="font-medium text-foreground" x-text="category.name"></p>
                                <p class="text-xs text-muted-foreground" x-text="category.count + ' transaksi'"></p>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-foreground" x-text="formatCurrency(category.revenue)"></td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-medium text-muted-foreground" x-text="category.percentage + '%'"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="rounded-lg border border-border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border bg-muted/30">
            <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('CreditCard', 'h-5 w-5 text-primary') ?>
                Metode Pembayaran
            </h3>
        </div>
        <div class="p-6">
            <div class="grid gap-4 grid-cols-1 md:grid-cols-2">
                <template x-for="method in paymentMethods" :key="method.type">
                    <div class="rounded-lg border border-border p-4 hover:border-primary/50 transition-colors">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div class="h-8 w-8 rounded-lg flex items-center justify-center" :class="method.bgClass">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :class="method.iconClass">
                                        <path x-text="method.iconPath"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-foreground" x-text="method.label"></span>
                            </div>
                            <span class="text-xs font-medium text-muted-foreground" x-text="method.count + ' transaksi'"></span>
                        </div>
                        <p class="text-2xl font-bold text-foreground mb-2" x-text="formatCurrency(method.amount)"></p>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-muted/50 rounded-full h-1.5 overflow-hidden">
                                <div class="h-full rounded-full" :class="method.barClass" :style="'width: ' + method.percentage + '%'"></div>
                            </div>
                            <span class="text-xs font-medium text-muted-foreground" x-text="method.percentage + '%'"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Top Products Table -->
    <div class="rounded-lg border border-border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border bg-muted/30 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('Award', 'h-5 w-5 text-primary') ?>
                Top 10 Produk Terlaris
            </h3>
            <a href="<?= base_url('info/reports/product-performance') ?>" class="text-sm text-primary hover:underline">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border bg-muted/20">
                        <th class="px-6 py-3 text-left text-sm font-semibold text-foreground">Rank</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-foreground">Produk</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-foreground">Qty Terjual</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-foreground">Total Pendapatan</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-foreground">Profit</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(product, index) in topProducts" :key="product.id">
                        <tr class="border-b border-border hover:bg-muted/30 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full font-bold text-sm"
                                     :class="index === 0 ? 'bg-yellow-100 text-yellow-700' : index === 1 ? 'bg-gray-100 text-gray-700' : index === 2 ? 'bg-orange-100 text-orange-700' : 'bg-muted/50 text-muted-foreground'">
                                    <span x-text="index + 1"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-foreground" x-text="product.name"></p>
                                <p class="text-xs text-muted-foreground" x-text="product.sku"></p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-lg text-foreground" x-text="product.qty_sold"></span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium" x-text="formatCurrency(product.revenue)"></td>
                            <td class="px-6 py-4 text-right font-bold text-success" x-text="formatCurrency(product.profit)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function analyticsManager() {
    return {
        dateFrom: '<?= $dateFrom ?? date('Y-m-01') ?>',
        dateTo: '<?= $dateTo ?? date('Y-m-d') ?>',
        stats: <?= json_encode($stats ?? [
            'totalRevenue' => 0,
            'totalProfit' => 0,
            'totalTransactions' => 0,
            'avgOrderValue' => 0,
            'revenueGrowth' => 0,
            'profitGrowth' => 0,
            'transactionGrowth' => 0,
            'avgGrowth' => 0,
        ]) ?>,
        revenueByCategory: <?= json_encode($revenueByCategory ?? []) ?>,
        paymentMethods: <?= json_encode($paymentMethods ?? []) ?>,
        topProducts: <?= json_encode($topProducts ?? []) ?>,
        dateRange: {
            from: '<?= $dateFrom ?? date('Y-m-01') ?>',
            to: '<?= $dateTo ?? date('Y-m-d') ?>'
        },

        setQuickPeriod(period) {
            const today = new Date();
            const endDate = today.toISOString().split('T')[0];
            let startDate;

            switch(period) {
                case 'today':
                    startDate = endDate;
                    break;
                case 'week':
                    const weekAgo = new Date(today.setDate(today.getDate() - 7));
                    startDate = weekAgo.toISOString().split('T')[0];
                    break;
                case 'month':
                    const monthAgo = new Date(today.setDate(today.getDate() - 30));
                    startDate = monthAgo.toISOString().split('T')[0];
                    break;
                case 'quarter':
                    const quarterAgo = new Date(today.setDate(today.getDate() - 90));
                    startDate = quarterAgo.toISOString().split('T')[0];
                    break;
                case 'year':
                    startDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
                    break;
                default:
                    return;
            }

            this.dateFrom = startDate;
            this.dateTo = endDate;
        },

        applyFilter() {
            window.location.href = '<?= base_url('info/analytics/dashboard') ?>?date_from=' + this.dateFrom + '&date_to=' + this.dateTo;
        },

        refreshData() {
            window.location.reload();
        },

        exportReport() {
            const params = new URLSearchParams({
                date_from: this.dateRange.from,
                date_to: this.dateRange.to
            });
            window.location.href = '<?= base_url('info/reports/export-csv') ?>?' + params.toString();
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value || 0);
        }
    };
}
</script>

<?= $this->endSection() ?>
