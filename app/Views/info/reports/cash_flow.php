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
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $startDate ?>">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $endDate ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i data-lucide="filter"></i>
                        Apply Filter
                    </button>
                    <a href="<?= base_url('/info/reports/cash-flow') ?>" class="btn btn-outline-secondary">
                        <i data-lucide="refresh-cw"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body">
                    <h5 class="card-title text-success">Total Cash Inflows</h5>
                    <h3><?= format_currency(array_sum($cashInflows)) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger">Total Cash Outflows</h5>
                    <h3><?= format_currency(array_sum($cashOutflows)) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card <?= $netCashFlow >= 0 ? 'border-success' : 'border-danger' ?>">
                <div class="card-body">
                    <h5 class="card-title <?= $netCashFlow >= 0 ? 'text-success' : 'text-danger' ?>">Net Cash Flow</h5>
                    <h3><?= format_currency($netCashFlow) ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cash Flow Details -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title">Cash Inflows</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($cashInflows as $type => $amount): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span><?= $type ?></span>
                                <strong><?= format_currency($amount) ?></strong>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: <?= array_sum($cashInflows) > 0 ? ($amount/array_sum($cashInflows)*100) : 0 ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title">Cash Outflows</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($cashOutflows as $type => $amount): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span><?= $type ?></span>
                                <strong><?= format_currency($amount) ?></strong>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-danger" style="width: <?= array_sum($cashOutflows) > 0 ? ($amount/array_sum($cashOutflows)*100) : 0 ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Net Cash Flow Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Cash Flow Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <h6 class="text-success">Total Inflows</h6>
                                <h4><?= format_currency(array_sum($cashInflows)) ?></h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <h6 class="text-danger">Total Outflows</h6>
                                <h4><?= format_currency(array_sum($cashOutflows)) ?></h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <h6 class="<?= $netCashFlow >= 0 ? 'text-success' : 'text-danger' ?>">Net Cash Flow</h6>
                                <h4><?= format_currency($netCashFlow) ?></h4>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Period: <?= format_date($startDate) ?> - <?= format_date($endDate) ?></span>
                            <span class="<?= $netCashFlow >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= $netCashFlow >= 0 ? 'Positive' : 'Negative' ?> Cash Flow
                            </span>
                        </div>
                        <div class="progress">
                            <?php
                            $total = array_sum($cashInflows) + array_sum($cashOutflows);
                            $inflowPercentage = $total > 0 ? (array_sum($cashInflows)/$total*100) : 50;
                            ?>
                            <div class="progress-bar bg-success" style="width: <?= $inflowPercentage ?>%"></div>
                            <div class="progress-bar bg-danger" style="width: <?= 100 - $inflowPercentage ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>