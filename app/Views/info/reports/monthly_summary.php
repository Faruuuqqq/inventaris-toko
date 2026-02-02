<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('TrendingUp', 'h-8 w-8 text-primary') ?>
            Ringkasan Bulanan
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Analisis tren penjualan, pembelian, dan profit per bulan</p>
    </div>
    <a href="<?= base_url('/info/reports') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ArrowLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Filter Form Card -->
<div class="rounded-lg border bg-surface shadow-sm overflow-hidden mb-8">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <?= icon('Filter', 'h-5 w-5 text-primary') ?>
            Filter Data
        </h2>
    </div>
    <div class="p-6">
        <form method="get" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="space-y-2 flex-1">
                <label for="year" class="text-sm font-medium text-foreground">Pilih Tahun</label>
                <select id="year" name="year" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?= $y ?>" <?= selected($y, $year) ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center justify-center gap-2 h-10 px-4 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition">
                    <?= icon('Filter', 'h-5 w-5') ?>
                    Tampilkan
                </button>
                <a href="<?= base_url('/info/reports/monthly-summary?year=' . date('Y')) ?>" class="inline-flex items-center justify-center gap-2 h-10 px-4 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
                    <?= icon('RefreshCw', 'h-5 w-5') ?>
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Year Summary Cards -->
<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
    <!-- Total Revenue -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Revenue</p>
                    <p class="text-2xl font-bold text-foreground mt-2">
                        <?= format_currency(array_sum(array_column($monthlyData, 'revenue'))) ?>
                    </p>
                </div>
                <div class="p-3 rounded-lg bg-primary/10">
                    <?= icon('TrendingUp', 'h-6 w-6 text-primary') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Total COGS -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total COGS</p>
                    <p class="text-2xl font-bold text-foreground mt-2">
                        <?= format_currency(array_sum(array_column($monthlyData, 'cogs'))) ?>
                    </p>
                </div>
                <div class="p-3 rounded-lg bg-destructive/10">
                    <?= icon('TrendingDown', 'h-6 w-6 text-destructive') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Returns -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Returns</p>
                    <p class="text-2xl font-bold text-foreground mt-2">
                        <?= format_currency(array_sum(array_column($monthlyData, 'returns'))) ?>
                    </p>
                </div>
                <div class="p-3 rounded-lg bg-warning/10">
                    <?= icon('RotateCcw', 'h-6 w-6 text-warning') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Net Profit -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Total Net Profit</p>
                    <p class="text-2xl font-bold text-foreground mt-2">
                        <?= format_currency(array_sum(array_column($monthlyData, 'net_profit'))) ?>
                    </p>
                </div>
                <div class="p-3 rounded-lg bg-success/10">
                    <?= icon('CheckCircle', 'h-6 w-6 text-success') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Data Table -->
<div class="rounded-lg border bg-surface shadow-sm overflow-hidden mb-8">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <?= icon('FileText', 'h-5 w-5 text-primary') ?>
            Breakdown Bulanan
        </h2>
    </div>
    <div class="p-6 overflow-auto">
        <table class="w-full text-sm">
            <thead class="bg-muted/50 border-b border-border/50">
                <tr>
                    <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Bulan</th>
                    <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Revenue</th>
                    <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">COGS</th>
                    <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Retur</th>
                    <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Gross Profit</th>
                    <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Biaya</th>
                    <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Net Profit</th>
                    <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Penjualan</th>
                    <th class="h-10 px-4 text-center align-middle font-medium text-muted-foreground">Pembelian</th>
                    <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Margin</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/50">
                <?php foreach ($monthlyData as $month): ?>
                    <tr class="hover:bg-muted/50 transition">
                        <td class="px-4 py-3 font-medium text-foreground"><?= $month['month_name'] ?></td>
                        <td class="px-4 py-3 text-right text-foreground"><?= format_currency($month['revenue']) ?></td>
                        <td class="px-4 py-3 text-right text-foreground"><?= format_currency($month['cogs']) ?></td>
                        <td class="px-4 py-3 text-right text-foreground"><?= format_currency($month['returns']) ?></td>
                        <td class="px-4 py-3 text-right font-medium <?= $month['gross_profit'] >= 0 ? 'text-success' : 'text-destructive' ?>">
                            <?= format_currency($month['gross_profit']) ?>
                        </td>
                        <td class="px-4 py-3 text-right text-foreground"><?= format_currency($month['expenses']) ?></td>
                        <td class="px-4 py-3 text-right font-semibold <?= $month['net_profit'] >= 0 ? 'text-success' : 'text-destructive' ?>">
                            <?= format_currency($month['net_profit']) ?>
                        </td>
                        <td class="px-4 py-3 text-center font-medium text-foreground"><?= $month['sales_count'] ?></td>
                        <td class="px-4 py-3 text-center font-medium text-foreground"><?= $month['purchase_count'] ?></td>
                        <td class="px-4 py-3 text-right font-medium <?= $month['revenue'] > 0 && ($month['net_profit']/$month['revenue']*100) >= 0 ? 'text-success' : 'text-destructive' ?>">
                            <?= $month['revenue'] > 0 ? round($month['net_profit']/$month['revenue']*100, 2) : 0 ?>%
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-muted/30 border-t border-border/50 font-semibold">
                <tr>
                    <td class="px-4 py-3 text-foreground">Total</td>
                    <td class="px-4 py-3 text-right text-foreground">
                        <?= format_currency(array_sum(array_column($monthlyData, 'revenue'))) ?>
                    </td>
                    <td class="px-4 py-3 text-right text-foreground">
                        <?= format_currency(array_sum(array_column($monthlyData, 'cogs'))) ?>
                    </td>
                    <td class="px-4 py-3 text-right text-foreground">
                        <?= format_currency(array_sum(array_column($monthlyData, 'returns'))) ?>
                    </td>
                    <td class="px-4 py-3 text-right text-foreground">
                        <?= format_currency(array_sum(array_column($monthlyData, 'gross_profit'))) ?>
                    </td>
                    <td class="px-4 py-3 text-right text-foreground">
                        <?= format_currency(array_sum(array_column($monthlyData, 'expenses'))) ?>
                    </td>
                    <td class="px-4 py-3 text-right text-foreground">
                        <?= format_currency(array_sum(array_column($monthlyData, 'net_profit'))) ?>
                    </td>
                    <td class="px-4 py-3 text-center text-foreground">
                        <?= array_sum(array_column($monthlyData, 'sales_count')) ?>
                    </td>
                    <td class="px-4 py-3 text-center text-foreground">
                        <?= array_sum(array_column($monthlyData, 'purchase_count')) ?>
                    </td>
                    <td class="px-4 py-3 text-right text-foreground">
                        <?php
                        $totalRevenue = array_sum(array_column($monthlyData, 'revenue'));
                        $totalNetProfit = array_sum(array_column($monthlyData, 'net_profit'));
                        echo $totalRevenue > 0 ? round($totalNetProfit/$totalRevenue*100, 2) : 0;
                        ?>%
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Charts -->
<div class="grid gap-6 lg:grid-cols-2">
    <!-- Revenue & Profit Trend Chart -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('LineChart', 'h-5 w-5 text-primary') ?>
                Tren Revenue & Profit
            </h2>
        </div>
        <div class="p-6">
            <canvas id="revenueChart" height="80"></canvas>
        </div>
    </div>

    <!-- Transaction Count Chart -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('BarChart3', 'h-5 w-5 text-primary') ?>
                Jumlah Transaksi
            </h2>
        </div>
        <div class="p-6">
            <canvas id="transactionChart" height="80"></canvas>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue & Profit Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: [<?= "'" . implode("','", array_column($monthlyData, 'month_name')) . "'" ?>],
        datasets: [{
            label: 'Revenue',
            data: [<?= implode(',', array_column($monthlyData, 'revenue')) ?>],
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            tension: 0.1
        }, {
            label: 'Gross Profit',
            data: [<?= implode(',', array_column($monthlyData, 'gross_profit')) ?>],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.1
        }, {
            label: 'Net Profit',
            data: [<?= implode(',', array_column($monthlyData, 'net_profit')) ?>],
            borderColor: 'rgb(75, 192, 75)',
            backgroundColor: 'rgba(75, 192, 75, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                    }
                }
            }
        }
    }
});

// Transaction Count Chart
const transactionCtx = document.getElementById('transactionChart').getContext('2d');
new Chart(transactionCtx, {
    type: 'bar',
    data: {
        labels: [<?= "'" . implode("','", array_column($monthlyData, 'month_name')) . "'" ?>],
        datasets: [{
            label: 'Sales',
            data: [<?= implode(',', array_column($monthlyData, 'sales_count')) ?>],
            backgroundColor: 'rgba(54, 162, 235, 0.5)'
        }, {
            label: 'Purchases',
            data: [<?= implode(',', array_column($monthlyData, 'purchase_count')) ?>],
            backgroundColor: 'rgba(255, 99, 132, 0.5)'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>

<?= $this->endSection() ?>