<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('DollarSign', 'h-8 w-8 text-primary') ?>
            Edit Biaya
        </h1>
        <p class="text-sm text-muted-foreground mt-1"><?= $subtitle ?? 'Perbarui informasi biaya operasional' ?></p>
    </div>
    <a href="<?= base_url('/finance/expenses') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ArrowLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Error Messages -->
<?php if (session()->getFlashdata('errors')): ?>
    <div class="mb-6 rounded-lg border border-destructive/30 bg-destructive/5 p-4">
        <div class="flex gap-3">
            <?= icon('AlertTriangle', 'h-5 w-5 text-destructive flex-shrink-0 mt-0.5') ?>
            <div class="flex-1">
                <h3 class="font-semibold text-destructive mb-2">Terjadi kesalahan</h3>
                <ul class="space-y-1 text-sm text-destructive/90">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li>• <?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Form Card -->
<div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <?= icon('FileText', 'h-5 w-5 text-primary') ?>
            Form Edit Biaya
        </h2>
    </div>

    <div class="p-6">
        <form action="<?= base_url('/finance/expenses/update/' . $expense->id) ?>" method="post" class="space-y-6">
            <?= csrf_field() ?>

            <!-- Row 1: Nomor Biaya & Tanggal -->
            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-2">
                    <label for="expense_number" class="text-sm font-medium text-foreground">Nomor Biaya</label>
                    <input type="text" id="expense_number" class="h-10 w-full rounded-lg border border-border bg-muted px-3 py-2 text-sm text-muted-foreground cursor-not-allowed" value="<?= $expense->expense_number ?>" readonly>
                    <p class="text-xs text-muted-foreground">Auto-generated</p>
                </div>

                <div class="space-y-2">
                    <label for="expense_date" class="text-sm font-medium text-foreground">Tanggal <span class="text-destructive">*</span></label>
                    <input type="date" id="expense_date" name="expense_date" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50" value="<?= old('expense_date', date('Y-m-d', strtotime($expense->expense_date))) ?>" required>
                </div>
            </div>

            <!-- Row 2: Kategori & Metode Pembayaran -->
            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-2">
                    <label for="category" class="text-sm font-medium text-foreground">Kategori <span class="text-destructive">*</span></label>
                    <select id="category" name="category" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $key => $label): ?>
                            <option value="<?= $key ?>" <?= old('category', $expense->category) == $key ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="payment_method" class="text-sm font-medium text-foreground">Metode Pembayaran <span class="text-destructive">*</span></label>
                    <select id="payment_method" name="payment_method" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50" required>
                        <option value="">Pilih Metode</option>
                        <option value="CASH" <?= old('payment_method', $expense->payment_method) == 'CASH' ? 'selected' : '' ?>>Tunai</option>
                        <option value="TRANSFER" <?= old('payment_method', $expense->payment_method) == 'TRANSFER' ? 'selected' : '' ?>>Transfer</option>
                        <option value="CHECK" <?= old('payment_method', $expense->payment_method) == 'CHECK' ? 'selected' : '' ?>>Cek/Giro</option>
                    </select>
                </div>
            </div>

            <!-- Row 3: Deskripsi -->
            <div class="space-y-2">
                <label for="description" class="text-sm font-medium text-foreground">Deskripsi <span class="text-destructive">*</span></label>
                <input type="text" id="description" name="description" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50" value="<?= old('description', $expense->description) ?>" placeholder="Deskripsi biaya" required maxlength="255">
                <p class="text-xs text-muted-foreground">Maksimal 255 karakter</p>
            </div>

            <!-- Row 4: Jumlah -->
            <div class="space-y-2">
                <label for="amount" class="text-sm font-medium text-foreground">Jumlah (Rp) <span class="text-destructive">*</span></label>
                <input type="number" id="amount" name="amount" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50" value="<?= old('amount', $expense->amount) ?>" placeholder="0" required min="1" step="1">
            </div>

            <!-- Row 5: Catatan -->
            <div class="space-y-2">
                <label for="notes" class="text-sm font-medium text-foreground">Catatan</label>
                <textarea id="notes" name="notes" rows="4" placeholder="Catatan tambahan (opsional)" class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 resize-vertical"><?= old('notes', $expense->notes) ?></textarea>
                <p class="text-xs text-muted-foreground">Catatan ini hanya untuk referensi internal</p>
            </div>

            <!-- Divider -->
            <div class="border-t border-border/50"></div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 justify-end">
                <a href="<?= base_url('/finance/expenses') ?>" class="inline-flex items-center justify-center h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition">
                    <?= icon('Save', 'h-5 w-5') ?>
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
