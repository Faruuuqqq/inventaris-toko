<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title"><?= $title ?></h3>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <a href="<?= base_url('/info/reports/profit-loss') ?>" class="btn btn-outline-primary">
                        <i data-lucide="trending-up"></i>
                        Profit & Loss
                    </a>
                    <a href="<?= base_url('/info/reports/cash-flow') ?>" class="btn btn-outline-success">
                        <i data-lucide="dollar-sign"></i>
                        Cash Flow
                    </a>
                    <a href="<?= base_url('/info/reports/monthly-summary') ?>" class="btn btn-outline-info">
                        <i data-lucide="calendar"></i>
                        Monthly Summary
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?= $salesThisMonth['count'] ?? 0 ?></h4>
                            <span>Sales This Month</span>
                        </div>
                        <div class="align-self-center">
                            <i data-lucide="shopping-cart" class="icon-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small>Revenue: <?= format_currency($salesThisMonth['total'] ?? 0) ?></small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?= $purchasesThisMonth['count'] ?? 0 ?></h4>
                            <span>Purchases This Month</span>
                        </div>
                        <div class="align-self-center">
                            <i data-lucide="package" class="icon-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small>Total: <?= format_currency($purchasesThisMonth['total'] ?? 0) ?></small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?= format_currency($totalSales) ?></h4>
                            <span>Total Sales</span>
                        </div>
                        <div class="align-self-center">
                            <i data-lucide="trending-up" class="icon-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small>All time revenue</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4><?= format_currency($totalPurchases) ?></h4>
                            <span>Total Purchases</span>
                        </div>
                        <div class="align-self-center">
                            <i data-lucide="trending-down" class="icon-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small>All time purchases</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts and Tables -->
    <div class="row">
        <!-- Top Products -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Top Products</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Qty Sold</th>
                                    <th class="text-right">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($topProducts)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No data available</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($topProducts as $product): ?>
                                        <tr>
                                            <td>
                                                <small class="text-muted"><?= $product['kode_produk'] ?></small><br>
                                                <?= $product['nama_produk'] ?>
                                            </td>
                                            <td class="text-center"><?= $product['total_sold'] ?></td>
                                            <td class="text-right"><?= format_currency($product['revenue']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top Customers -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Top Customers</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th class="text-center">Transactions</th>
                                    <th class="text-right">Total Spent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($topCustomers)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No data available</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($topCustomers as $customer): ?>
                                        <tr>
                                            <td><?= $customer['nama_customer'] ?></td>
                                            <td class="text-center"><?= $customer['transaction_count'] ?></td>
                                            <td class="text-right"><?= format_currency($customer['total_spent']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Low Stock Alert -->
    <?php if (!empty($lowStockProducts)): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title">
                            <i data-lucide="alert-triangle"></i>
                            Low Stock Products
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Current Stock</th>
                                        <th class="text-center">Min Stock</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lowStockProducts as $product): ?>
                                        <tr>
                                            <td>
                                                <small class="text-muted"><?= $product['kode_produk'] ?></small><br>
                                                <?= $product['nama_produk'] ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?= $product['stok'] > 0 ? 'bg-warning' : 'bg-danger' ?>">
                                                    <?= $product['stok'] ?>
                                                </span>
                                            </td>
                                            <td class="text-center"><?= $product['minimal_stok'] ?></td>
                                            <td>
                                                <?php if ($product['stok'] == 0): ?>
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Low Stock</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>