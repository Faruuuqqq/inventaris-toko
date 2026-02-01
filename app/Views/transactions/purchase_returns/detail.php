<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title"><?= $title ?></h3>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('/transactions/purchase-returns') ?>" class="btn btn-outline-secondary">
                    <i data-lucide="arrow-left"></i>
                    Back
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Purchase Return Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <td><strong>Return Number:</strong></td>
                            <td><?= $purchaseReturn['nomor_retur'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Date:</strong></td>
                            <td><?= format_date($purchaseReturn['tanggal_retur']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Supplier:</strong></td>
                            <td><?= $purchaseReturn['supplier']['name'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>From Warehouse:</strong></td>
                            <td><?= $purchaseReturn['warehouse']['nama_warehouse'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td><?= status_badge($purchaseReturn['status']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Notes:</strong></td>
                            <td><?= $purchaseReturn['keterangan'] ?: '-' ?></td>
                        </tr>
                        <?php if ($purchaseReturn['tanggal_proses']): ?>
                            <tr>
                                <td><strong>Process Date:</strong></td>
                                <td><?= format_date($purchaseReturn['tanggal_proses']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($purchaseReturn['approval_notes']): ?>
                            <tr>
                                <td><strong>Approval Notes:</strong></td>
                                <td><?= $purchaseReturn['approval_notes'] ?></td>
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
                                <?php foreach ($purchaseReturn['details'] as $detail): ?>
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
                        <?= status_badge($purchaseReturn['status']) ?>
                    </div>
                    <?php if ($purchaseReturn['total_refund'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Refund Amount:</span>
                            <strong><?= format_currency($purchaseReturn['total_refund']) ?></strong>
                        </div>
                    <?php endif; ?>
                    <hr>
                    <div class="text-muted small">
                        Created: <?= format_datetime($purchaseReturn['created_at']) ?><br>
                        <?php if ($purchaseReturn['approved_by']): ?>
                            Approved by: User #<?= $purchaseReturn['approved_by'] ?><br>
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
                        <?php if ($purchaseReturn['status'] === 'Menunggu Persetujuan'): ?>
                            <a href="<?= base_url('/transactions/purchase-returns/approve/' . $purchaseReturn['id_retur_pembelian']) ?>" class="btn btn-success">
                                <i data-lucide="check"></i>
                                Approve Return
                            </a>
                            <a href="<?= base_url('/transactions/purchase-returns/edit/' . $purchaseReturn['id_retur_pembelian']) ?>" class="btn btn-warning">
                                <i data-lucide="edit"></i>
                                Edit Return
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?= base_url('/info/stockcard?id_produk=all') ?>" class="btn btn-info">
                            <i data-lucide="file-text"></i>
                            View Stock Card
                        </a>
                        
                        <a href="<?= base_url('/transactions/purchase-returns') ?>" class="btn btn-outline-secondary" target="_blank">
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