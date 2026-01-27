<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title"><?= $title ?></h3>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('/transactions/sales-returns') ?>" class="btn btn-outline-secondary">
                    <i data-lucide="arrow-left"></i>
                    Back
                </a>
                <?php if ($salesReturn['status'] === 'Menunggu Persetujuan'): ?>
                    <a href="<?= base_url('/transactions/sales-returns/approve/' . $salesReturn['id_retur_penjualan']) ?>" class="btn btn-success">
                        <i data-lucide="check"></i>
                        Approve
                    </a>
                    <a href="<?= base_url('/transactions/sales-returns/edit/' . $salesReturn['id_retur_penjualan']) ?>" class="btn btn-warning">
                        <i data-lucide="edit"></i>
                        Edit
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Sales Return Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <td><strong>Return Number:</strong></td>
                            <td><?= $salesReturn['nomor_retur'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Date:</strong></td>
                            <td><?= format_date($salesReturn['tanggal_retur']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Customer:</strong></td>
                            <td><?= $salesReturn['customer']['nama_customer'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>From Warehouse:</strong></td>
                            <td><?= $salesReturn['warehouse']['nama_warehouse'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td><?= status_badge($salesReturn['status']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Notes:</strong></td>
                            <td><?= $salesReturn['keterangan'] ?: '-' ?></td>
                        </tr>
                        <?php if ($salesReturn['tanggal_proses']): ?>
                            <tr>
                                <td><strong>Process Date:</strong></td>
                                <td><?= format_date($salesReturn['tanggal_proses']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($salesReturn['approval_notes']): ?>
                            <tr>
                                <td><strong>Approval Notes:</strong></td>
                                <td><?= $salesReturn['approval_notes'] ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Products</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Reason</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($salesReturn['details'] as $detail): ?>
                                    <tr>
                                        <td><?= $detail['kode_produk'] ?></td>
                                        <td><?= $detail['nama_produk'] ?></td>
                                        <td><?= $detail['jumlah'] ?></td>
                                        <td><?= $detail['alasan'] ?></td>
                                        <td><?= $detail['keterangan'] ?: '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Status:</span>
                        <?= status_badge($salesReturn['status']) ?>
                    </div>
                    <?php if ($salesReturn['total_refund'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Refund Amount:</span>
                            <strong><?= format_currency($salesReturn['total_refund']) ?></strong>
                        </div>
                    <?php endif; ?>
                    <hr>
                    <div class="text-muted small">
                        Created: <?= format_datetime($salesReturn['created_at']) ?><br>
                        <?php if ($salesReturn['approved_by']): ?>
                            Approved by: User #<?= $salesReturn['approved_by'] ?><br>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if ($salesReturn['status'] === 'Menunggu Persetujuan'): ?>
                            <a href="<?= base_url('/transactions/sales-returns/approve/' . $salesReturn['id_retur_penjualan']) ?>" class="btn btn-success">
                                <i data-lucide="check"></i>
                                Approve Return
                            </a>
                            <a href="<?= base_url('/transactions/sales-returns/edit/' . $salesReturn['id_retur_penjualan']) ?>" class="btn btn-warning">
                                <i data-lucide="edit"></i>
                                Edit Return
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?= base_url('/info/stockcard?id_produk=all') ?>" class="btn btn-info">
                            <i data-lucide="file-text"></i>
                            View Stock Card
                        </a>
                        
                        <a href="<?= base_url('/transactions/sales-returns') ?>" class="btn btn-outline-secondary" target="_blank">
                            <i data-lucide="printer"></i>
                            Print
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>