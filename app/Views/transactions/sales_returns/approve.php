<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('CheckCircle', 'h-8 w-8 text-primary') ?>
            Persetujuan Retur Penjualan
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Tinjau dan setujui permintaan retur dari customer</p>
    </div>
    <a href="<?= base_url('transactions/sales-returns') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ArrowLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Return Information -->
<div class="rounded-lg border bg-surface shadow-sm overflow-hidden mb-6">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <?= icon('FileText', 'h-5 w-5 text-primary') ?>
            Informasi Retur
        </h2>
    </div>

    <div class="p-6 grid gap-6 md:grid-cols-2">
        <!-- Left Column: Return Details -->
        <div class="space-y-4">
            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">No. Retur</p>
                <p class="text-lg font-bold text-foreground mt-1"><?= $salesReturn['nomor_retur'] ?></p>
            </div>
            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Tanggal Retur</p>
                <p class="text-sm font-medium text-foreground mt-1"><?= format_date($salesReturn['tanggal_retur']) ?></p>
            </div>
            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Customer</p>
                <p class="text-sm font-medium text-foreground mt-1"><?= $salesReturn['customer']['name'] ?></p>
            </div>
            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Gudang Asal</p>
                <p class="text-sm font-medium text-foreground mt-1"><?= $salesReturn['warehouse']['nama_warehouse'] ?></p>
            </div>
            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Status</p>
                <div class="mt-1"><?= status_badge($salesReturn['status']) ?></div>
            </div>
        </div>

        <!-- Right Column: Approval Form -->
        <div class="space-y-4">
            <div class="space-y-2">
                <label for="tanggal_proses" class="text-sm font-medium text-foreground">Tanggal Proses *</label>
                <input type="date" id="tanggal_proses" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="space-y-2">
                <label for="approval_notes" class="text-sm font-medium text-foreground">Catatan Persetujuan</label>
                <textarea id="approval_notes" rows="3" placeholder="Masukkan catatan persetujuan..." class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Products to Process -->
<div class="rounded-lg border bg-surface shadow-sm overflow-hidden mb-6">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <?= icon('Package', 'h-5 w-5 text-primary') ?>
            Produk Retur
        </h2>
    </div>

    <div class="p-6 overflow-auto">
        <table class="w-full text-sm">
            <thead class="bg-muted/50 border-b border-border/50">
                <tr>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Produk</th>
                    <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-24">Qty Diminta</th>
                    <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-24">Qty Setuju</th>
                    <th class="h-12 px-4 text-center align-middle font-medium text-muted-foreground w-20">Baik</th>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-28">Gudang Baik</th>
                    <th class="h-12 px-4 text-center align-middle font-medium text-muted-foreground w-20">Rusak</th>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground w-28">Gudang Rusak</th>
                    <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground w-32">Refund</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border/50">
                <?php foreach ($salesReturn['details'] as $index => $detail): ?>
                    <tr class="hover:bg-muted/50 transition">
                        <input type="hidden" name="produk[<?= $index ?>][id_detail]" value="<?= $detail['id_detail'] ?>">
                        <input type="hidden" name="produk[<?= $index ?>][id_produk]" value="<?= $detail['id_produk'] ?>">
                        <td class="px-4 py-3">
                            <div>
                                <p class="font-semibold text-foreground"><?= $detail['nama_produk'] ?></p>
                                <p class="text-xs text-muted-foreground"><?= $detail['kode_produk'] ?></p>
                                <p class="text-xs text-primary/70 mt-1">Alasan: <?= $detail['alasan'] ?></p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-foreground"><?= $detail['jumlah'] ?></td>
                        <td class="px-4 py-3">
                            <input type="number" class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-primary/50 qty-approved-input" name="produk[<?= $index ?>][jumlah_diterima]" min="0" max="<?= $detail['jumlah'] ?>" value="<?= $detail['jumlah'] ?>" required>
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm text-center focus:outline-none focus:ring-2 focus:ring-primary/50 qty-good-input" name="produk[<?= $index ?>][jumlah_baik]" min="0" value="<?= $detail['jumlah'] ?>" data-index="<?= $index ?>">
                        </td>
                        <td class="px-4 py-3">
                            <select class="h-9 w-full rounded-lg border border-border bg-background px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 warehouse-good-select" name="produk[<?= $index ?>][id_warehouse_baik]" data-index="<?= $index ?>">
                                <option value="">-</option>
                                <?php foreach ($warehouses_good as $warehouse): ?>
                                    <option value="<?= $warehouse['id_warehouse'] ?>">
                                        <?= $warehouse['nama_warehouse'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm text-center focus:outline-none focus:ring-2 focus:ring-primary/50 qty-damaged-input" name="produk[<?= $index ?>][jumlah_rusak]" min="0" value="0" data-index="<?= $index ?>">
                        </td>
                        <td class="px-4 py-3">
                            <select class="h-9 w-full rounded-lg border border-border bg-background px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 warehouse-damaged-select" name="produk[<?= $index ?>][id_warehouse_rusak]" data-index="<?= $index ?>">
                                <option value="">-</option>
                                <?php foreach ($warehouses_damaged as $warehouse): ?>
                                    <option value="<?= $warehouse['id_warehouse'] ?>">
                                        <?= $warehouse['nama_warehouse'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <div class="space-y-1">
                                <input type="number" class="w-full h-9 rounded-lg border border-border bg-background px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-primary/50 refund-input" name="produk[<?= $index ?>][jumlah_refund]" min="0" value="<?= $detail['jumlah'] * $detail['harga_jual'] ?>" step="1" data-index="<?= $index ?>">
                                <p class="text-xs text-muted-foreground">Max: Rp <?= number_format($detail['jumlah'] * $detail['harga_jual'], 0, ',', '.') ?></p>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-muted/30 border-t border-border/50 font-semibold">
                <tr>
                    <th colspan="7" class="px-4 py-3 text-right">Total Refund:</th>
                    <th class="px-4 py-3 text-right text-foreground" id="totalRefundDisplay">Rp 0</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex items-center justify-between gap-3">
    <button type="button" onclick="processReject()" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-destructive text-white font-medium rounded-lg hover:bg-destructive/90 transition">
        <?= icon('X', 'h-5 w-5') ?>
        Tolak
    </button>
    <div class="flex gap-3">
        <a href="<?= base_url('transactions/sales-returns') ?>" class="inline-flex items-center justify-center h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
            Batal
        </a>
        <button type="button" onclick="processApprove()" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-success text-white font-medium rounded-lg hover:bg-success/90 transition">
            <?= icon('Check', 'h-5 w-5') ?>
            Setujui Retur
        </button>
    </div>
</div>

<!-- Hidden Form for Submission -->
<form id="approvalForm" method="post" action="<?= base_url('transactions/sales-returns/processApproval/' . $salesReturn['id_retur_penjualan']) ?>" class="hidden">
    <?= csrf_field() ?>
    <input type="hidden" name="action" id="actionValue">
    <input type="hidden" name="tanggal_proses" id="hiddenTanggalProses">
    <input type="hidden" name="approval_notes" id="hiddenApprovalNotes">
    
    <?php foreach ($salesReturn['details'] as $index => $detail): ?>
        <input type="hidden" name="produk[<?= $index ?>][id_detail]" value="<?= $detail['id_detail'] ?>">
        <input type="hidden" name="produk[<?= $index ?>][id_produk]" value="<?= $detail['id_produk'] ?>">
        <input type="hidden" name="produk[<?= $index ?>][jumlah_diterima]">
        <input type="hidden" name="produk[<?= $index ?>][jumlah_baik]">
        <input type="hidden" name="produk[<?= $index ?>][id_warehouse_baik]">
        <input type="hidden" name="produk[<?= $index ?>][jumlah_rusak]">
        <input type="hidden" name="produk[<?= $index ?>][id_warehouse_rusak]">
        <input type="hidden" name="produk[<?= $index ?>][jumlah_refund]">
    <?php endforeach; ?>
</form>

<script>
function updateHiddenFields() {
    // Update all hidden fields from visible inputs before submit
    document.querySelectorAll('input[name*="jumlah_diterima"]').forEach((input, idx) => {
        document.querySelector(`input[name="produk[${idx}][jumlah_diterima]"]`).value = input.value;
    });
    document.querySelectorAll('.qty-good-input').forEach((input, idx) => {
        document.querySelector(`input[name="produk[${idx}][jumlah_baik]"]`).value = input.value;
    });
    document.querySelectorAll('.warehouse-good-select').forEach((select, idx) => {
        document.querySelector(`input[name="produk[${idx}][id_warehouse_baik]"]`).value = select.value;
    });
    document.querySelectorAll('.qty-damaged-input').forEach((input, idx) => {
        document.querySelector(`input[name="produk[${idx}][jumlah_rusak]"]`).value = input.value;
    });
    document.querySelectorAll('.warehouse-damaged-select').forEach((select, idx) => {
        document.querySelector(`input[name="produk[${idx}][id_warehouse_rusak]"]`).value = select.value;
    });
    document.querySelectorAll('.refund-input').forEach((input, idx) => {
        document.querySelector(`input[name="produk[${idx}][jumlah_refund]"]`).value = input.value;
    });
}

function validateDistribution(index) {
    const approvedQty = parseFloat(document.querySelector(`input[name="produk[${index}][jumlah_diterima]"]`).value) || 0;
    const goodQty = parseFloat(document.querySelector(`.qty-good-input[data-index="${index}"]`).value) || 0;
    const damagedQty = parseFloat(document.querySelector(`.qty-damaged-input[data-index="${index}"]`).value) || 0;
    const maxRefund = document.querySelector(`.refund-input[data-index="${index}"]`).dataset.maxRefund || 0;
    
    // Validate total doesn't exceed approved
    const total = goodQty + damagedQty;
    if (total > approvedQty) {
        // Adjust damaged if needed
        if (damagedQty > approvedQty - goodQty) {
            document.querySelector(`.qty-damaged-input[data-index="${index}"]`).value = Math.max(0, approvedQty - goodQty);
        } else {
            document.querySelector(`.qty-good-input[data-index="${index}"]`).value = Math.max(0, approvedQty - damagedQty);
        }
    }
    
    // Disable warehouse selects if qty is 0
    document.querySelector(`.warehouse-good-select[data-index="${index}"]`).disabled = goodQty === 0;
    document.querySelector(`.warehouse-damaged-select[data-index="${index}"]`).disabled = damagedQty === 0;
}

function calculateTotalRefund() {
    let total = 0;
    const inputs = document.querySelectorAll('.refund-input');
    inputs.forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('totalRefundDisplay').textContent = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(total);
}

function processApprove() {
    updateHiddenFields();
    document.getElementById('actionValue').value = 'approve';
    document.getElementById('hiddenTanggalProses').value = document.getElementById('tanggal_proses').value;
    document.getElementById('hiddenApprovalNotes').value = document.getElementById('approval_notes').value;
    document.getElementById('approvalForm').submit();
}

function processReject() {
    const returNumber = document.querySelector('[x-text="retur.nomor_retur"]')?.textContent || 'retur ini';
    ModalManager.warning(
        'Tolak Retur Penjualan',
        `Apakah Anda yakin ingin menolak retur ${returNumber}? Tindakan ini akan menandai retur sebagai ditolak.`,
        () => {
            updateHiddenFields();
            document.getElementById('actionValue').value = 'reject';
            document.getElementById('hiddenTanggalProses').value = document.getElementById('tanggal_proses').value;
            document.getElementById('hiddenApprovalNotes').value = document.getElementById('approval_notes').value;
            document.getElementById('approvalForm').submit();
        },
        'Tolak Retur'
    );
}

document.addEventListener('DOMContentLoaded', () => {
    // Add event listeners for quantity and warehouse changes
    document.querySelectorAll('input[name*="jumlah_diterima"]').forEach(input => {
        input.addEventListener('input', (e) => {
            const idx = e.target.name.match(/\d+/)[0];
            validateDistribution(idx);
            calculateTotalRefund();
        });
    });
    
    document.querySelectorAll('.qty-good-input').forEach(input => {
        input.addEventListener('input', (e) => {
            const idx = e.target.dataset.index;
            validateDistribution(idx);
            calculateTotalRefund();
        });
    });
    
    document.querySelectorAll('.qty-damaged-input').forEach(input => {
        input.addEventListener('input', (e) => {
            const idx = e.target.dataset.index;
            validateDistribution(idx);
            calculateTotalRefund();
        });
    });
    
    document.querySelectorAll('.warehouse-good-select, .warehouse-damaged-select').forEach(select => {
        select.addEventListener('change', calculateTotalRefund);
    });
    
    document.querySelectorAll('.refund-input').forEach(input => {
        input.addEventListener('input', calculateTotalRefund);
    });
    
    calculateTotalRefund();
});
</script>

<?= $this->endSection() ?>