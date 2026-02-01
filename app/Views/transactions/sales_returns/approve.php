<?= $this->extend('layout/main') ?>

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
                                    <th>Good Items</th>
                                    <th>Good Warehouse</th>
                                    <th>Damaged Items</th>
                                    <th>Damaged Warehouse</th>
                                    <th>Refund Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($salesReturn['details'] as $index => $detail): ?>
                                    <tr x-data="productRow()">
                                        <input type="hidden" name="produk[<?= $index ?>][id_detail]" value="<?= $detail['id_detail'] ?>">
                                        <input type="hidden" name="produk[<?= $index ?>][id_produk]" value="<?= $detail['id_produk'] ?>">
                                        <td>
                                            <strong><?= $detail['nama_produk'] ?></strong><br>
                                            <small class="text-muted"><?= $detail['kode_produk'] ?></small><br>
                                            <small class="text-info">Reason: <?= $detail['alasan'] ?></small>
                                        </td>
                                        <td><?= $detail['jumlah'] ?></td>
                                        <td>
                                            <input type="number" class="form-control" name="produk[<?= $index ?>][jumlah_diterima]" x-model="jumlahDiterima" x-on:input="validateQuantity()" min="0" max="<?= $detail['jumlah'] ?>" value="<?= $detail['jumlah'] ?>" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="produk[<?= $index ?>][jumlah_baik]" x-model="jumlahBaik" x-on:input="validateDistribution()" min="0" value="<?= $detail['jumlah'] ?>">
                                        </td>
                                        <td>
                                            <select class="form-select" name="produk[<?= $index ?>][id_warehouse_baik]" x-bind:disabled="jumlahBaik == 0">
                                                <option value="">Select Warehouse</option>
                                                <?php foreach ($warehouses_good as $warehouse): ?>
                                                    <option value="<?= $warehouse['id_warehouse'] ?>">
                                                        <?= $warehouse['nama_warehouse'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="produk[<?= $index ?>][jumlah_rusak]" x-model="jumlahRusak" x-on:input="validateDistribution()" min="0" value="0">
                                        </td>
                                        <td>
                                            <select class="form-select" name="produk[<?= $index ?>][id_warehouse_rusak]" x-bind:disabled="jumlahRusak == 0">
                                                <option value="">Select Warehouse</option>
                                                <?php foreach ($warehouses_damaged as $warehouse): ?>
                                                    <option value="<?= $warehouse['id_warehouse'] ?>">
                                                        <?= $warehouse['nama_warehouse'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="produk[<?= $index ?>][jumlah_refund]" x-model="jumlahRefund" x-on:input="calculateRefund()" min="0" value="<?= $detail['jumlah'] * $detail['harga_jual'] ?>" step="0.01">
                                            <small class="text-muted">Max: <?= $detail['jumlah'] * $detail['harga_jual'] ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="7" class="text-end">Total Refund:</th>
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
                            <a href="<?= base_url('/transactions/sales-returns') ?>" class="btn btn-secondary">Cancel</a>
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

<form id="approvalForm" method="post" action="<?= base_url('/transactions/sales-returns/processApproval/' . $salesReturn['id_retur_penjualan']) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="action" id="actionValue">
</form>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('productRow', () => ({
        jumlahDiterima: <?= $detail['jumlah'] ?>,
        jumlahBaik: <?= $detail['jumlah'] ?>,
        jumlahRusak: 0,
        jumlahRefund: <?= $detail['jumlah'] * $detail['harga_jual'] ?>,
        hargaJual: <?= $detail['harga_jual'] ?>,
        
        init() {
            this.calculateTotalRefund();
        },
        
        validateQuantity() {
            const maxQty = parseInt(this.$el.querySelector('input[name*="jumlah_diterima"]').max) || 0;
            if (this.jumlahDiterima > maxQty) {
                this.jumlahDiterima = maxQty;
            }
            this.validateDistribution();
            this.calculateMaxRefund();
        },
        
        validateDistribution() {
            const total = this.jumlahBaik + this.jumlahRusak;
            if (total > this.jumlahDiterima) {
                const excess = total - this.jumlahDiterima;
                if (this.jumlahRusak >= excess) {
                    this.jumlahRusak -= excess;
                } else {
                    this.jumlahBaik -= (excess - this.jumlahRusak);
                    this.jumlahRusak = 0;
                }
            }
            this.calculateMaxRefund();
        },
        
        calculateMaxRefund() {
            this.jumlahRefund = Math.min(this.jumlahRefund, this.jumlahDiterima * this.hargaJual);
            this.calculateTotalRefund();
        },
        
        calculateTotalRefund() {
            // This will be called by parent component
            document.dispatchEvent(new CustomEvent('updateTotalRefund'));
        }
    }));
    
    // Global event listener for total refund calculation
    document.addEventListener('updateTotalRefund', () => {
        let total = 0;
        document.querySelectorAll('input[name*="jumlah_refund"]').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('totalRefundDisplay').textContent = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(total);
    });
});

function processApprove() {
    document.getElementById('actionValue').value = 'approve';
    document.getElementById('approvalForm').submit();
}

function processReject() {
    document.getElementById('actionValue').value = 'reject';
    document.getElementById('approvalForm').submit();
}

// Initialize total refund display
document.addEventListener('DOMContentLoaded', () => {
    document.dispatchEvent(new CustomEvent('updateTotalRefund'));
});
</script>

<?= $this->endSection() ?>