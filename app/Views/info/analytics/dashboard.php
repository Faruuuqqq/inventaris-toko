<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="analyticsManager()">
    <!-- Page Header -->
    <div class="mb-8 flex items-start justify-between">
        <div>
            <h2 class="text-3xl font-bold text-foreground flex items-center gap-3">
                <?= icon('BarChart', 'h-8 w-8 text-primary') ?>
                Analytics Dashboard
            </h2>
            <p class="mt-1 text-muted-foreground">Analisis mendalam terhadap penjualan, pendapatan, dan performa bisnis</p>
        </div>
        <div class="flex gap-3">
            <button @click="exportReport()" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
                <?= icon('Download', 'h-5 w-5') ?>
                Export
            </button>
            <button @click="refreshData()" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition">
                <?= icon('RefreshCw', 'h-5 w-5') ?>
                Refresh
            </button>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="mb-8 rounded-lg border border-border/50 bg-surface shadow-sm overflow-hidden">
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
        <div class="rounded-lg border border-border/50 bg-gradient-to-br from-success/5 to-transparent p-6 hover:border-success/30 transition-colors">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-success/10">
                    <svg class="h-6 w-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-xs font-medium" :class="stats.revenueGrowth >= 0 ? 'text-success' : 'text-danger'">
                    <template x-if="stats.revenueGrowth >= 0">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                    </template>
                    <template x-if="stats.revenueGrowth < 0">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </template>
                    <span x-text="Math.abs(stats.revenueGrowth) + '%'"></span>
                </div>
            </div>
            <p class="text-sm font-medium text-muted-foreground">Total Pendapatan</p>
            <p class="mt-2 text-3xl font-bold text-foreground" x-text="formatCurrency(stats.totalRevenue)"></p>
        </div>

        <!-- Total Profit -->
        <div class="rounded-lg border border-border/50 bg-gradient-to-br from-primary/5 to-transparent p-6 hover:border-primary/30 transition-colors">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                    <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-xs font-medium" :class="stats.profitGrowth >= 0 ? 'text-success' : 'text-danger'">
                    <template x-if="stats.profitGrowth >= 0">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                    </template>
                    <template x-if="stats.profitGrowth < 0">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </template>
                    <span x-text="Math.abs(stats.profitGrowth) + '%'"></span>
                </div>
            </div>
            <p class="text-sm font-medium text-muted-foreground">Total Profit</p>
            <p class="mt-2 text-3xl font-bold text-foreground" x-text="formatCurrency(stats.totalProfit)"></p>
        </div>

        <!-- Total Transactions -->
        <div class="rounded-lg border border-border/50 bg-gradient-to-br from-warning/5 to-transparent p-6 hover:border-warning/30 transition-colors">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning/10">
                    <svg class="h-6 w-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-xs font-medium" :class="stats.transactionGrowth >= 0 ? 'text-success' : 'text-danger'">
                    <template x-if="stats.transactionGrowth >= 0">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                    </template>
                    <template x-if="stats.transactionGrowth < 0">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </template>
                    <span x-text="Math.abs(stats.transactionGrowth) + '%'"></span>
                </div>
            </div>
            <p class="text-sm font-medium text-muted-foreground">Total Transaksi</p>
            <p class="mt-2 text-3xl font-bold text-foreground" x-text="stats.totalTransactions"></p>
        </div>

        <!-- Average Order Value -->
        <div class="rounded-lg border border-border/50 bg-gradient-to-br from-blue-500/5 to-transparent p-6 hover:border-blue-500/30 transition-colors">
            <div class="flex items-start justify-between mb-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500/10">
                    <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-xs font-medium" :class="stats.aovGrowth >= 0 ? 'text-success' : 'text-danger'">
                    <template x-if="stats.aovGrowth >= 0">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                    </template>
                    <template x-if="stats.aovGrowth < 0">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </template>
                    <span x-text="Math.abs(stats.aovGrowth) + '%'"></span>
                </div>
            </div>
            <p class="text-sm font-medium text-muted-foreground">Rata-rata Nilai Order</p>
            <p class="mt-2 text-3xl font-bold text-foreground" x-text="formatCurrency(stats.avgOrderValue)"></p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="mb-8 grid gap-6 grid-cols-1 lg:grid-cols-2">
        <!-- Revenue Trend Chart -->
        <div class="rounded-lg border border-border/50 bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('TrendingUp', 'h-5 w-5 text-primary') ?>
                    Tren Pendapatan & Profit
                </h3>
            </div>
            <div class="p-6">
                <canvas id="revenueTrendChart" style="height: 250px;"></canvas>
            </div>
        </div>

        <!-- Category Revenue Chart -->
        <div class="rounded-lg border border-border/50 bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('PieChart', 'h-5 w-5 text-primary') ?>
                    Distribusi Pendapatan per Kategori
                </h3>
            </div>
            <div class="p-6">
                <canvas id="categoryRevenueChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Revenue by Category Breakdown -->
                    <template x-for="category in revenueByCategory" :key="category.name">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-foreground" x-text="category.name"></span>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-foreground" x-text="formatCurrency(category.revenue)"></p>
                                    <p class="text-xs text-muted-foreground" x-text="category.percentage + '%'"></p>
                                </div>
                            </div>
                            <div class="w-full bg-muted/50 rounded-full h-2 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500" 
                                     :style="'width: ' + category.percentage + '%'" 
                                     :class="getCategoryColor(category.name)"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="mb-8 rounded-lg border border-border/50 bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('CreditCard', 'h-5 w-5 text-primary') ?>
                Breakdown Metode Pembayaran
            </h3>
        </div>
        <div class="p-6">
            <div class="grid gap-6 grid-cols-1 md:grid-cols-3">
                <template x-for="method in paymentMethods" :key="method.type">
                    <div class="rounded-lg border border-border/50 p-4 hover:border-primary/30 transition-colors">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div class="h-8 w-8 rounded-lg flex items-center justify-center" :class="method.bgClass">
                                    <svg class="h-4 w-4" :class="method.iconClass" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="method.iconPath" />
                                    </svg>
                                </div>
                                <span class="font-medium text-foreground" x-text="method.label"></span>
                            </div>
                            <span class="text-xs font-medium text-muted-foreground" x-text="method.count + ' transaksi'"></span>
                        </div>
                        <p class="text-2xl font-bold text-foreground mb-1" x-text="formatCurrency(method.amount)"></p>
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
    <div class="rounded-lg border border-border/50 bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('Award', 'h-5 w-5 text-primary') ?>
                Top 10 Produk Terlaris
            </h3>
            <a href="<?= base_url('info/reports/product-performance') ?>" class="text-sm text-primary hover:underline">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border/50 bg-muted/20">
                        <th class="px-6 py-3 text-left text-sm font-semibold text-foreground">Rank</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-foreground">Produk</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-foreground">Qty Terjual</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-foreground">Total Pendapatan</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-foreground">Profit</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-foreground">Share</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(product, index) in topProducts" :key="product.id">
                        <tr class="border-b border-border/50 hover:bg-muted/30 transition">
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
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium text-muted-foreground" x-text="product.share + '%'"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

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
            'aovGrowth' => 0
        ]) ?>,
        revenueByCategory: <?= json_encode($revenueByCategory ?? []) ?>,
        paymentMethods: <?= json_encode($paymentMethods ?? []) ?>,
        topProducts: <?= json_encode($topProducts ?? []) ?>,
        dateRange: {
            from: '<?= $dateFrom ?? date('Y-m-01') ?>',
            to: '<?= $dateTo ?? date('Y-m-d') ?>'
        },
        revenueTrendChart: null,
        categoryRevenueChart: null,

        init() {
            this.initCharts();
        },

        initCharts() {
            const revenueTrendData = <?= json_encode($revenueTrend ?? []) ?>;
            
            // Revenue Trend Chart (Line Chart)
            const ctx1 = document.getElementById('revenueTrendChart');
            if (ctx1 && revenueTrendData.length > 0) {
                this.revenueTrendChart = new Chart(ctx1, {
                    type: 'line',
                    data: {
                        labels: revenueTrendData.map(d => this.formatPeriodLabel(d.period_label)),
                        datasets: [
                            {
                                label: 'Pendapatan',
                                data: revenueTrendData.map(d => parseFloat(d.revenue)),
                                borderColor: '#0F7B4D',
                                backgroundColor: 'rgba(15, 123, 77, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                pointBackgroundColor: '#0F7B4D',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2
                            },
                            {
                                label: 'Profit',
                                data: revenueTrendData.map(d => parseFloat(d.profit)),
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                pointBackgroundColor: '#10B981',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        size: 13,
                                        weight: '600',
                                        family: 'Inter, sans-serif'
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                                padding: 15,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                borderColor: '#0F7B4D',
                                borderWidth: 1,
                                callbacks: {
                                    label: (context) => {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += 'Rp ' + this.formatNumber(context.parsed.y);
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    maxRotation: 45,
                                    minRotation: 0
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.06)',
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    callback: (value) => {
                                        if (value >= 1000000) {
                                            return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                        } else if (value >= 1000) {
                                            return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                        }
                                        return 'Rp ' + value;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Category Revenue Doughnut Chart
            const ctx2 = document.getElementById('categoryRevenueChart');
            if (ctx2 && this.revenueByCategory.length > 0) {
                this.categoryRevenueChart = new Chart(ctx2, {
                    type: 'doughnut',
                    data: {
                        labels: this.revenueByCategory.map(c => c.name),
                        datasets: [{
                            data: this.revenueByCategory.map(c => parseFloat(c.revenue)),
                            backgroundColor: [
                                '#0F7B4D',
                                '#10B981',
                                '#34D399',
                                '#6EE7B7',
                                '#A7F3D0',
                                '#F59E0B',
                                '#F97316',
                                '#EF4444'
                            ],
                            borderWidth: 3,
                            borderColor: '#fff',
                            hoverOffset: 15,
                            hoverBorderWidth: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'right',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        family: 'Inter, sans-serif'
                                    },
                                    generateLabels: (chart) => {
                                        const data = chart.data;
                                        if (data.labels.length && data.datasets.length) {
                                            return data.labels.map((label, i) => {
                                                const value = data.datasets[0].data[i];
                                                const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                                const percentage = ((value / total) * 100).toFixed(1);
                                                return {
                                                    text: `${label} (${percentage}%)`,
                                                    fillStyle: data.datasets[0].backgroundColor[i],
                                                    hidden: false,
                                                    index: i
                                                };
                                            });
                                        }
                                        return [];
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                                padding: 15,
                                borderColor: '#0F7B4D',
                                borderWidth: 1,
                                callbacks: {
                                    label: (context) => {
                                        const label = context.label || '';
                                        const value = context.parsed;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${label}: Rp ${this.formatNumber(value)} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        },

        formatPeriodLabel(label) {
            // Format date labels based on grouping
            if (!label) return '';
            
            if (label.includes('W')) {
                // Weekly format: 2026-W05
                const parts = label.split('-W');
                return `Week ${parts[1]}`;
            } else if (label.length === 7) {
                // Monthly format: 2026-01
                const [year, month] = label.split('-');
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                return `${monthNames[parseInt(month) - 1]} ${year}`;
            } else {
                // Daily format: 2026-01-15
                const [year, month, day] = label.split('-');
                return `${day}/${month}`;
            }
        },

        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(Math.round(num));
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
            // Build URL with current date range
            const params = new URLSearchParams({
                date_from: this.dateRange.from,
                date_to: this.dateRange.to
            });
            window.location.href = '<?= base_url('info/analytics/export-csv') ?>?' + params.toString();
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value || 0);
        },

        getCategoryColor(categoryName) {
            const colors = ['bg-primary', 'bg-success', 'bg-warning', 'bg-danger', 'bg-blue-500', 'bg-purple-500'];
            const index = categoryName.charCodeAt(0) % colors.length;
            return colors[index];
        }
    };
}
</script>

<?= $this->endSection() ?>
