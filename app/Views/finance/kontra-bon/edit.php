<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('FileEdit', 'h-8 w-8 text-primary') ?>
            <?= $title ?? 'Edit Kontra Bon' ?>
        </h1>
        <p class="text-sm text-muted-foreground mt-1"><?= $subtitle ?? 'Update data kontra bon' ?></p>
    </div>
    <a href="<?= base_url('finance/kontra-bon') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ArrowLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Alert Messages -->
<?php if (session()->getFlashdata('errors')): ?>
<div class="mb-4 rounded-lg border border-destructive/50 bg-destructive/10 p-4 flex items-start gap-3">
    <?= icon('AlertCircle', 'h-5 w-5 text-destructive flex-shrink-0 mt-0.5') ?>
    <div class="flex-1">
        <p class="text-sm font-medium text-destructive mb-2">Terjadi kesalahan:</p>
        <ul class="text-sm text-destructive space-y-1 list-inside">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<!-- Kontra Bon Edit Form -->
<form action="<?= base_url('finance/kontra-bon/update/' . $kontraBon['id']) ?>" method="post" class="space-y-6">
    <?= csrf_field() ?>

    <!-- Form Card -->
    <div class="rounded-lg border bg-card shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('FileText', 'h-5 w-5 text-primary') ?>
                Detail Kontra Bon
            </h2>
        </div>

        <div class="p-6 space-y-6">
            <!-- Document Number (Read-only) -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">No. Dokumen</label>
                <input type="text" value="<?= esc($kontraBon['document_number']) ?>" disabled class="h-10 w-full rounded-lg border border-border bg-muted px-3 py-2 text-sm text-muted-foreground font-mono">
                <p class="text-xs text-muted-foreground">Nomor dokumen tidak dapat diubah</p>
            </div>

            <!-- Customer Selection -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Customer *</label>
                <select name="customer_id" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <option value="">Pilih Customer</option>
                    <?php if (isset($customers) && is_array($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= esc($customer->id ?? $customer['id'] ?? '') ?>" <?= old('customer_id', $kontraBon['customer_id']) == ($customer->id ?? $customer['id'] ?? '') ? 'selected' : '' ?>>
                                <?= esc($customer->name ?? $customer['name'] ?? '') ?> - <?= esc($customer->phone ?? $customer['phone'] ?? 'Tidak ada telepon') ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <p class="text-xs text-muted-foreground">Pilih customer yang akan dibuatkan kontra bon</p>
            </div>

            <!-- Due Date and Total Amount -->
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Tanggal Jatuh Tempo</label>
                    <input type="date" name="due_date" value="<?= old('due_date', $kontraBon['due_date'] ? date('Y-m-d', strtotime($kontraBon['due_date'])) : '') ?>" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <p class="text-xs text-muted-foreground">Opsional - tanggal pembayaran diharapkan</p>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Total Jumlah *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground">Rp</span>
                        <input type="number" name="total_amount" value="<?= old('total_amount', $kontraBon['total_amount']) ?>" placeholder="0" required min="1" step="1" class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 pl-10 text-right text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 font-medium">
                    </div>
                    <p class="text-xs text-muted-foreground">Total nominal kontra bon dalam Rupiah</p>
                </div>
            </div>

            <!-- Status -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Status *</label>
                <select name="status" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                    <option value="PENDING" <?= old('status', $kontraBon['status']) == 'PENDING' ? 'selected' : '' ?>>Pending</option>
                    <option value="PAID" <?= old('status', $kontraBon['status']) == 'PAID' ? 'selected' : '' ?>>Lunas</option>
                    <option value="CANCELLED" <?= old('status', $kontraBon['status']) == 'CANCELLED' ? 'selected' : '' ?>>Dibatalkan</option>
                </select>
                <p class="text-xs text-muted-foreground">Status pembayaran kontra bon</p>
            </div>

            <!-- Notes -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Catatan</label>
                <textarea name="notes" rows="4" placeholder="Catatan atau keterangan tambahan..." class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"><?= old('notes', $kontraBon['notes']) ?></textarea>
                <p class="text-xs text-muted-foreground">Opsional - informasi tambahan tentang kontra bon</p>
            </div>

            <!-- Last Updated Info -->
            <?php if (isset($kontraBon['updated_at']) && $kontraBon['updated_at']): ?>
            <div class="pt-4 border-t border-border/50">
                <p class="text-xs text-muted-foreground flex items-center gap-2">
                    <?= icon('Clock', 'h-4 w-4') ?>
                    Terakhir diperbarui: <?= date('d M Y H:i', strtotime($kontraBon['updated_at'])) ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-3 justify-end">
        <a href="<?= base_url('finance/kontra-bon') ?>" class="h-10 px-6 rounded-lg border border-border/50 font-medium text-foreground hover:bg-muted transition">
            Batal
        </a>
        <button type="submit" class="h-10 px-6 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition flex items-center gap-2">
            <?= icon('Save', 'h-4 w-4') ?>
            Update Kontra Bon
        </button>
    </div>
</form>

<?= $this->endSection() ?>
