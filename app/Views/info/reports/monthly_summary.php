<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title"><?= $title ?></h3>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('/info/reports') ?>" class="btn btn-outline-secondary">
                    <i data-lucide="arrow-left"></i>
                    Back
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <label for="year" class="form-label">Year</label>
                    <select class="form-select" id="year" name="year">
                        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                            <option value="<?= $y ?>" <?= selected($y, $year) ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i data-lucide="filter"></i>
                        Apply Filter
                    </button>
                    <a href="<?= base_url('/info/reports/monthly-summary?year=' . date('Y')) ?>" class="btn btn-outline-secondary">
                        <i data-lucide="refresh-cw"></i>
                        Current Year
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Year Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h5 class="card-title text-primary">Total Revenue</h5>
                    <h3><?= format_currency(array_sum(array_column($monthlyData, 'revenue'))) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger">Total COGS</h5>
                    <h3><?= format_currency(array_sum(array_column($monthlyData, 'cogs'))) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <h5 class="card-title text-warning">Total Returns</h5>
                    <h3><?= format_currency(array_sum(array_column($monthlyData, 'returns'))) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <h5 class="card-title text-success">Total Net Profit</h5>
                    <h3><?= format_currency(array_sum(array_column($monthlyData, 'net_profit'))) ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Monthly Data Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Monthly Breakdown</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="text-right">Revenue</th>
                            <th class="text-right">COGS</th>
                            <th class="text-right">Returns</th>
                            <th class="text-right">Gross Profit</th>
                            <th class="text-right">Expenses</th>
                            <th class="text-right">Net Profit</th>
                            <th class="text-center">Sales</th>
                            <th class="text-center">Purchases</th>
                            <th class="text-right">Profit Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($monthlyData as $month): ?>
                            <tr>
                                <td><?= $month['month_name'] ?></td>
                                <td class="text-right"><?= format_currency($month['revenue']) ?></td>
                                <td class="text-right"><?= format_currency($month['cogs']) ?></td>
                                <td class="text-right"><?= format_currency($month['returns']) ?></td>
                                <td class="text-right <?= $month['gross_profit'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= format_currency($month['gross_profit']) ?>
                                </td>
                                <td class="text-right"><?= format_currency($month['expenses']) ?></td>
                                <td class="text-right <?= $month['net_profit'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= format_currency($month['net_profit']) ?>
                                </td>
                                <td class="text-center"><?= $month['sales_count'] ?></td>
                                <td class="text-center"><?= $month['purchase_count'] ?></td>
                                <td class="text-right <?= $month['revenue'] > 0 && ($month['net_profit']/$month['revenue']*100) >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $month['revenue'] > 0 ? round($month['net_profit']/$month['revenue']*100, 2) : 0 ?>%
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td>Total</td>
                            <td class="text-right"><?= format_currency(array_sum(array_column($monthlyData, 'revenue'))) ?></td>
                            <td class="text-right"><?= format_currency(array_sum(array_column($monthlyData, 'cogs'))) ?></td>
                            <td class="text-right"><?= format_currency(array_sum(array_column($monthlyData, 'returns'))) ?></td>
                            <td class="text-right"><?= format_currency(array_sum(array_column($monthlyData, 'gross_profit'))) ?></td>
                            <td class="text-right"><?= format_currency(array_sum(array_column($monthlyData, 'expenses'))) ?></td>
                            <td class="text-right"><?= format_currency(array_sum(array_column($monthlyData, 'net_profit'))) ?></td>
                            <td class="text-center"><?= array_sum(array_column($monthlyData, 'sales_count')) ?></td>
                            <td class="text-center"><?= array_sum(array_column($monthlyData, 'purchase_count')) ?></td>
                            <td class="text-right">
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
    </div>
    
    <!-- Charts -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Revenue & Profit Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Transaction Count</h5>
                </div>
                <div class="card-body">
                    <canvas id="transactionChart" height="200"></canvas>
                </div>
            </div>
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