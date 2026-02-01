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
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Return Information</h6>
                            <table class="table table-sm table-borderless">
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
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_proses" class="form-label">Process Date</label>
                                <input type="date" class="form-control" id="tanggal_proses" name="tanggal_proses" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="approval_notes" class="form-label">Approval Notes</label>
                                <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h5>Products to Process</h5>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Requested Quantity</th>
                                    <th>Approved Quantity</th>
                                    <th>Refund Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($purchaseReturn['details'] as $index => $detail): ?>
                                    <tr>
                                        <input type="hidden" name="produk[<?= $index ?>][id_detail]" value="<?= $detail['id_detail'] ?>">
                                        <input type="hidden" name="produk[<?= $index ?>][id_produk]" value="<?= $detail['id_produk'] ?>">
                                        <td>
                                            <strong><?= $detail['nama_produk'] ?></strong><br>
                                            <small class="text-muted"><?= $detail['kode_produk'] ?></small><br>
                                            <small class="text-info">Reason: <?= $detail['alasan'] ?></small>
                                        </td>
                                        <td><?= $detail['jumlah'] ?></td>
                                        <td>
                                            <input type="number" class="form-control" name="produk[<?= $index ?>][jumlah_dikembalikan]" min="0" max="<?= $detail['jumlah'] ?>" value="<?= $detail['jumlah'] ?>" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="produk[<?= $index ?>][jumlah_refund]" min="0" value="<?= $detail['jumlah'] * $detail['harga_beli_terakhir'] ?>" step="0.01">
                                            <small class="text-muted">Max: <?= $detail['jumlah'] * $detail['harga_beli_terakhir'] ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total Refund:</th>
                                    <th class="fw-bold" id="totalRefundDisplay">0</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <button type="button" class="btn btn-danger" onclick="processReject()">
                                <i data-lucide="x"></i>
                                Reject
                            </button>
                        </div>
                        <div>
                            <a href="<?= base_url('/transactions/purchase-returns') ?>" class="btn btn-secondary">Cancel</a>
                            <button type="button" class="btn btn-success" onclick="processApprove()">
                                <i data-lucide="check"></i>
                                Approve Return
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="approvalForm" method="post" action="<?= base_url('/transactions/purchase-returns/processApproval/' . $purchaseReturn['id_retur_pembelian']) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="action" id="actionValue">
    <input type="hidden" name="tanggal_proses" id="hiddenTanggalProses">
    <input type="hidden" name="approval_notes" id="hiddenApprovalNotes">
    
    <?php foreach ($purchaseReturn['details'] as $index => $detail): ?>
        <input type="hidden" name="produk[<?= $index ?>][id_detail]" value="<?= $detail['id_detail'] ?>">
        <input type="hidden" name="produk[<?= $index ?>][id_produk]" value="<?= $detail['id_produk'] ?>">
        <input type="hidden" name="produk[<?= $index ?>][jumlah_dikembalikan]" value="<?= $detail['jumlah'] ?>">
        <input type="hidden" name="produk[<?= $index ?>][jumlah_refund]" value="<?= $detail['jumlah'] * $detail['harga_beli_terakhir'] ?>">
    <?php endforeach; ?>
</form>

<script>
function processApprove() {
    document.getElementById('actionValue').value = 'approve';
    document.getElementById('hiddenTanggalProses').value = document.getElementById('tanggal_proses').value;
    document.getElementById('hiddenApprovalNotes').value = document.getElementById('approval_notes').value;
    document.getElementById('approvalForm').submit();
}

function processReject() {
    document.getElementById('actionValue').value = 'reject';
    document.getElementById('hiddenTanggalProses').value = document.getElementById('tanggal_proses').value;
    document.getElementById('hiddenApprovalNotes').value = document.getElementById('approval_notes').value;
    document.getElementById('approvalForm').submit();
}

function calculateTotalRefund() {
    let total = 0;
    const inputs = document.querySelectorAll('input[name*="jumlah_refund"]');
    inputs.forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('totalRefundDisplay').textContent = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(total);
}

// Initialize total refund display
document.addEventListener('DOMContentLoaded', () => {
    calculateTotalRefund();
    
    // Add event listeners to refund amount inputs
    document.querySelectorAll('input[name*="jumlah_refund"]').forEach(input => {
        input.addEventListener('input', calculateTotalRefund);
    });
});
</script>

<?= $this->endSection() ?>