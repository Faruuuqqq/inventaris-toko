<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('ArrowRightFromLine', 'h-8 w-8 text-primary') ?>
            Buat Penjualan
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Catat penjualan barang ke pelanggan</p>
    </div>
    <a href="<?= base_url('transactions/sales') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap">
        <?= icon('ArrowLeft', 'h-5 w-5') ?>
        Kembali
    </a>
</div>

<!-- Sales Form -->
<form method="post" action="<?= base_url('transactions/sales/store') ?>" x-data="salesForm()" class="space-y-6">
    <?= csrf_field() ?>

    <!-- Header Section -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('FileText', 'h-5 w-5 text-primary') ?>
                Informasi Penjualan
            </h2>
        </div>

        <div class="p-6 space-y-6">
            <!-- Invoice Number, Date, Payment Type -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">No. Invoice</label>
                    <input type="text" value="<?= $invoice_number ?>" disabled class="h-10 w-full rounded-lg border border-border bg-muted px-3 py-2 text-sm text-muted-foreground font-mono">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Tanggal Penjualan *</label>
                    <input type="date" name="tanggal_penjualan" value="<?= old('tanggal_penjualan', date('Y-m-d')) ?>" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Tipe Pembayaran *</label>
                    <select name="tipe_pembayaran" x-model="form.tipe_pembayaran" @change="updatePaymentType()" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Tipe</option>
                        <option value="CASH" <?= selected('CASH', old('tipe_pembayaran')) ?>>Tunai (CASH)</option>
                        <option value="CREDIT" <?= selected('CREDIT', old('tipe_pembayaran')) ?>>Kredit</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Gudang *</label>
                    <select name="id_warehouse" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Gudang</option>
                        <?php foreach ($warehouses as $warehouse): ?>
                            <option value="<?= $warehouse->id ?>" <?= selected($warehouse->id, old('id_warehouse')) ?>>
                                <?= esc($warehouse->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Customer and Salesperson -->
            <div class="grid gap-4 md:grid-cols-2">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Pelanggan *</label>
                    <select name="id_customer" x-model="form.id_customer" required class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Pilih Pelanggan</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer->id ?>" data-credit="<?= $customer->credit_limit ?>">
                                <?= esc($customer->name) ?> (<?= esc($customer->phone) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-foreground">Salesman (Opsional)</label>
                    <select name="id_salesperson" class="h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                        <option value="">Tanpa Salesman</option>
                        <?php foreach ($salespersons as $person): ?>
                            <option value="<?= $person->id ?>" <?= selected($person->id, old('id_salesperson')) ?>>
                                <?= esc($person->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Credit Limit Warning -->
            <div x-show="form.tipe_pembayaran === 'CREDIT'" class="p-4 rounded-lg bg-warning/10 border border-warning/20 flex items-start gap-3">
                <svg class="h-5 w-5 text-warning flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="text-sm text-warning">
                    <p class="font-semibold">Perhatian Kredit</p>
                    <p class="text-xs mt-1">Pastikan total penjualan tidak melebihi batas kredit pelanggan</p>
                </div>
            </div>

            <!-- Notes -->
            <div class="space-y-2">
                <label class="text-sm font-medium text-foreground">Catatan (Opsional)</label>
                <textarea name="catatan" rows="2" placeholder="Catatan tambahan untuk penjualan ini..." class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"><?= old('catatan') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 bg-muted/30 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                <?= icon('Package', 'h-5 w-5 text-primary') ?>
                Produk Penjualan
            </h2>
            <button type="button" @click="addProduct()" class="inline-flex items-center justify-center gap-2 h-9 px-4 bg-primary text-white font-medium text-sm rounded-lg hover:bg-primary/90 transition">
                <?= icon('Plus', 'h-4 w-4') ?>
                Tambah Produk
            </button>
        </div>

        <div class="p-6">
            <div x-show="form.products.length === 0" class="flex flex-col items-center justify-center py-12 text-muted-foreground">
                <?= icon('Package', 'h-12 w-12 mb-3 opacity-50') ?>
                <p class="text-sm">Belum ada produk. Klik "Tambah Produk" untuk memulai</p>
            </div>

            <div x-show="form.products.length > 0" class="relative w-full overflow-auto">
                <table class="w-full text-sm">
                    <th
