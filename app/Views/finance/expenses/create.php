<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('ReceiptText', 'h-8 w-8 text-warning') ?>
            <?= $title ?? 'Tambah Biaya Operasional' ?>
        </h1>
        <p class="text-sm text-muted-foreground mt-1"><?= $subtitle ?? 'Catat biaya pengeluaran operasional' ?></p>
    </div>
    <a href="<?= base_url('finance/expenses') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
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

<!-- Expense Form -->
<form action="<?= base_url('finance/expenses/store') ?>" method="post" class="space-y-6">
    <?= csrf_field() ?>

    <!-- Form Card -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('FileText', 'h-5 w-5 text-primary') ?>
                Detail Biaya
            </h2>
        </div>

        <div class="p-6 space-y-6">
            <!-- Expense Number and Date -->
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">No. Biaya</label>
                    <input type="text" value="<?= $expense_number ?>" disabled class="h-10 w-full rounded-lg border border-border bg-muted px-3 py-2 text-sm text-muted-foreground font-mono">
                    <p class="text-xs text-muted-foreground">Nomor otomatis</p>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Tanggal *</label>
                    <input type="date" name="expense_date" value="<?= old('expense_date', date('Y-m-d')) ?>" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>
            </div>

            <!-- Category and Payment Method -->
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Kategori *</label>
                    <select name="category" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $key => $label): ?>
                            <option value="<?= $key ?>" <?= old('category') == $key ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Metode Pembayaran *</label>
                    <select name="payment_method" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Metode</option>
                        <option value="CASH" <?= old('payment_method') == 'CASH' ? 'selected' : '' ?>>Tunai</option>
                        <option value="TRANSFER" <?= old('payment_method') == 'TRANSFER' ? 'selected' : '' ?>>Transfer Bank</option>
                        <option value="CHECK" <?= old('payment_method') == 'CHECK' ? 'selected' : '' ?>>Cek/Giro</option>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Deskripsi *</label>
                <input type="text" name="description" value="<?= old('description') ?>" placeholder="Deskripsi biaya..." required maxlength="255" class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
            </div>

            <!-- Amount -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Jumlah *</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground">Rp</span>
                    <input type="number" name="amount" value="<?= old('amount') ?>" placeholder="0" required min="1" step="1" class="w-full h-10 rounded-lg border border-border bg-background px-3 py-2 pl-10 text-right text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 font-medium">
                </div>
            </div>

            <!-- Notes -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Catatan (Opsional)</label>
                <textarea name="notes" rows="3" placeholder="Catatan tambahan..." class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"><?= old('notes') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-3 justify-end">
        <a href="<?= base_url('finance/expenses') ?>" class="h-10 px-6 rounded-lg border border-border/50 font-medium text-foreground hover:bg-muted transition">
            Batal
        </a>
        <button type="submit" class="h-10 px-6 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 transition flex items-center gap-2">
            <?= icon('Save', 'h-4 w-4') ?>
            Simpan Biaya
        </button>
    </div>
</form>

<?= $this->endSection() ?>
