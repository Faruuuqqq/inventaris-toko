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
                    <a href="<?= base_url('/info/reports/profit-loss') ?>" class="btn btn-outline-secondary">
                        <i data-lucide="refresh-cw"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <h5 class="card-title text-success">Revenue</h5>
                    <h3><?= format_currency($revenue) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger">Cost of Goods Sold</h5>
                    <h3><?= format_currency($cogs) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <h5 class="card-title text-warning">Returns</h5>
                    <h3><?= format_currency($returns) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card <?= $grossProfit >= 0 ? 'border-success' : 'border-danger' ?>">
                <div class="card-body">
                    <h5 class="card-title <?= $grossProfit >= 0 ? 'text-success' : 'text-danger' ?>">Gross Profit</h5>
                    <h3><?= format_currency($grossProfit) ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Detailed Breakdown -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Revenue & Costs</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Revenue</span>
                            <strong><?= format_currency($revenue) ?></strong>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" style="width: <?= $revenue > 0 ? 100 : 0 ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Cost of Goods Sold</span>
                            <strong><?= format_currency($cogs) ?></strong>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-danger" style="width: <?= $revenue > 0 ? ($cogs/$revenue*100) : 0 ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Returns</span>
                            <strong><?= format_currency($returns) ?></strong>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-warning" style="width: <?= $revenue > 0 ? ($returns/$revenue*100) : 0 ?>%"></div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Gross Profit</span>
                            <strong><?= format_currency($grossProfit) ?></strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar <?= $grossProfit >= 0 ? 'bg-success' : 'bg-danger' ?>" style="width: <?= $revenue > 0 ? abs($grossProfit/$revenue*100) : 0 ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Expenses & Net Profit</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Gross Profit</span>
                            <strong><?= format_currency($grossProfit) ?></strong>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" style="width: <?= $grossProfit >= 0 ? 100 : 0 ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Expenses</span>
                            <strong><?= format_currency($expenses) ?></strong>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-danger" style="width: <?= $grossProfit > 0 ? ($expenses/$grossProfit*100) : 0 ?>%"></div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Net Profit</span>
                            <strong><?= format_currency($netProfit) ?></strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar <?= $netProfit >= 0 ? 'bg-success' : 'bg-danger' ?>" style="width: <?= $grossProfit > 0 ? abs($netProfit/$grossProfit*100) : 0 ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="row text-center">
                            <div class="col-6">
                                <small class="text-muted">Profit Margin</small>
                                <h5 class="<?= $revenue > 0 && ($netProfit/$revenue*100) >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $revenue > 0 ? round($netProfit/$revenue*100, 2) : 0 ?>%
                                </h5>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Gross Margin</small>
                                <h5 class="<?= $revenue > 0 && ($grossProfit/$revenue*100) >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $revenue > 0 ? round($grossProfit/$revenue*100, 2) : 0 ?>%
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>