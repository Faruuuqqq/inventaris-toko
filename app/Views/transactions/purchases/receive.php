<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title"><?= $title ?></h3>
            </div>
            <div class="col-auto">
                <a href="<?= base_url('/transactions/purchases') ?>" class="btn btn-outline-secondary">
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
                    <form method="post" action="<?= base_url('/transactions/purchases/processReceive/' . $purchaseOrder['id_po']) ?>" x-data="receiveForm()">
                        <?= csrf_field() ?>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Purchase Order Information</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>PO Number:</strong></td>
                                        <td><?= $purchaseOrder['nomor_po'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date:</strong></td>
                                        <td><?= format_date($purchaseOrder['tanggal_po']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Supplier:</strong></td>
                                        <td><?= $purchaseOrder['supplier']['nama_supplier'] ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Warehouse:</strong></td>
                                        <td><?= $purchaseOrder['warehouse']['nama_warehouse'] ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_terima" class="form-label">Receive Date</label>
                                    <input type="date" class="form-control" id="tanggal_terima" name="tanggal_terima" value="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5>Products to Receive</h5>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Ordered</th>
                                        <th>Previously Received</th>
                                        <th>Quantity to Receive</th>
                                        <th>Good Items</th>
                                        <th>Good Warehouse</th>
                                        <th>Damaged Items</th>
                                        <th>Damaged Warehouse</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($purchaseOrder['details'] as $index => $detail): ?>
                                        <tr x-data="productRow()">
                                            <input type="hidden" name="produk[<?= $index ?>][id_detail]" value="<?= $detail['id_detail'] ?>">
                                            <input type="hidden" name="produk[<?= $index ?>][id_produk]" value="<?= $detail['id_produk'] ?>">
                                            <td>
                                                <strong><?= $detail['nama_produk'] ?></strong><br>
                                                <small class="text-muted"><?= $detail['kode_produk'] ?></small>
                                            </td>
                                            <td><?= $detail['jumlah'] ?></td>
                                            <td><?= $detail['jumlah_diterima'] ?></td>
                                            <td>
                                                <input type="number" class="form-control" name="produk[<?= $index ?>][jumlah_diterima]" x-model="jumlahDiterima" x-on:input="validateQuantity()" min="0" max="<?= $detail['jumlah'] - $detail['jumlah_diterima'] ?>" value="<?= max(0, $detail['jumlah'] - $detail['jumlah_diterima']) ?>" required>
                                                <small class="text-muted">Max: <?= $detail['jumlah'] - $detail['jumlah_diterima'] ?></small>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" name="produk[<?= $index ?>][jumlah_baik]" x-model="jumlahBaik" x-on:input="validateDistribution()" min="0" value="<?= max(0, $detail['jumlah'] - $detail['jumlah_diterima']) ?>">
                                            </td>
                                            <td>
                                                <select class="form-select" name="produk[<?= $index ?>][id_warehouse_baik]" x-bind:disabled="jumlahBaik == 0">
                                                    <option value="">Select Warehouse</option>
                                                    <?php foreach ($warehouses_good as $warehouse): ?>
                                                        <option value="<?= $warehouse['id_warehouse'] ?>" <?= $purchaseOrder['id_warehouse'] == $warehouse['id_warehouse'] ? 'selected' : '' ?>>
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
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('/transactions/purchases') ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">Receive Purchase Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('receiveForm', () => ({
        // Main form logic here if needed
    }));
    
    Alpine.data('productRow', () => ({
        jumlahDiterima: 0,
        jumlahBaik: 0,
        jumlahRusak: 0,
        
        init() {
            this.jumlahDiterima = parseInt(this.$el.querySelector('input[name*="jumlah_diterima"]').value) || 0;
            this.jumlahBaik = parseInt(this.$el.querySelector('input[name*="jumlah_baik"]').value) || 0;
            this.jumlahRusak = parseInt(this.$el.querySelector('input[name*="jumlah_rusak"]').value) || 0;
        },
        
        validateQuantity() {
            const maxQty = parseInt(this.$el.querySelector('input[name*="jumlah_diterima"]').max) || 0;
            if (this.jumlahDiterima > maxQty) {
                this.jumlahDiterima = maxQty;
            }
            this.validateDistribution();
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
        }
    }));
});
</script>

<?= $this->endSection() ?>