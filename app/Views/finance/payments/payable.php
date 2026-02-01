<?php $this->section('content') ?>
<?php $this->extend('layout/main') ?>
<!-- Payables Payment Form -->
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-xl font-semibold">Pembayaran Utang</h3>
                <p class="text-sm text-muted-foreground">Catat pembayaran utang ke supplier</p>
            </div>
        </div>
    </div>

    <!-- Payment Form -->
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Form Pembayaran</h3>
            
            <form action="/finance/payments/storePayable" method="post" class="space-y-4">
                <div class="space-y-2">
                    <label for="supplier_id">Supplier</label>
                    <select name="supplier_id" id="supplier_id" class="form-input">
                        <option value="">Pilih supplier</option>
                        <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?= $supplier['id'] ?>"><?= $supplier['name'] ?> (Utang: <?= format_currency($supplier['debt_balance']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="amount">Jumlah Pembayaran</label>
                        <input type="number" name="amount" id="amount" class="form-input" placeholder="0" step="0.01" required>
                    </div>
                    <div class="space-y-2">
                        <label for="payment_method">Metode Pembayaran</label>
                        <select name="payment_method" id="payment_method" class="form-input">
                            <option value="CASH">Tunai</option>
                            <option value="TRANSFER">Transfer Bank</option>
                            <option value="CHECK">Cek/Giro</option>
                        </select>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label for="notes">Catatan</label>
                    <textarea name="notes" id="notes" class="form-input" placeholder="Catatan (opsional)" rows="3"></textarea>
                </div>
                
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" class="btn btn-outline">
                        Kembali
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function formatCurrency(amount) {
        return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
    }
</script>

<?php $this->endSection() ?>