<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('Package', 'h-8 w-8 text-primary') ?>
            Detail Produk
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Informasi lengkap produk</p>
    </div>
    <div class="flex gap-3">
        <a href="<?= base_url('master/products') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
            <?= icon('ArrowLeft', 'h-5 w-5') ?>
            Kembali
        </a>
        <?php if (is_admin()): ?>
        <a href="<?= base_url('master/products/edit/' . $product->id) ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition">
            <?= icon('Edit', 'h-5 w-5') ?>
            Edit
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid gap-6 lg:grid-cols-3">
    <!-- Left Column: Product Details (2/3) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Product Information Card -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('Package', 'h-5 w-5 text-primary') ?>
                    Informasi Produk
                </h2>
            </div>

            <div class="p-6 space-y-6">
                <!-- Product Name -->
                <div>
                    <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Nama Produk</p>
                    <p class="text-2xl font-bold text-foreground mt-2"><?= $product->name ?></p>
                </div>

                <!-- Product Details -->
                <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">SKU</p>
                        <p class="text-sm font-mono font-medium text-foreground mt-1"><?= $product->sku ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Kategori</p>
                        <p class="text-sm font-medium text-foreground mt-1"><?= $product->category->name ?? '-' ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Satuan</p>
                        <p class="text-sm font-medium text-foreground mt-1"><?= $product->unit ?? '-' ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Minimal Stok</p>
                        <p class="text-sm font-medium text-foreground mt-1"><?= $product->min_stock_alert ?? '0' ?></p>
                    </div>
                </div>

                <!-- Pricing Information -->
                <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Harga Beli</p>
                        <p class="text-lg font-bold text-foreground mt-1"><?= format_currency($product->price_buy) ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Harga Jual</p>
                        <p class="text-lg font-bold text-success mt-1"><?= format_currency($product->price_sell) ?></p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Margin</p>
                        <p class="text-lg font-bold text-foreground mt-1">
                            <?php 
                                $margin = (($product->price_sell - $product->price_buy) / $product->price_buy * 100);
                                echo round($margin, 2) . '%';
                            ?>
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Status</p>
                        <p class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-success/10 text-success">
                                Aktif
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Creation Info -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 space-y-3 text-sm text-muted-foreground">
                <div class="flex justify-between">
                    <span>Dibuat pada:</span>
                    <span class="text-foreground font-medium"><?= format_date($product->created_at) ?></span>
                </div>
                <div class="flex justify-between">
                    <span>Diperbarui pada:</span>
                    <span class="text-foreground font-medium"><?= format_date($product->updated_at) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Quick Actions (1/3) -->
    <div class="space-y-6">
        
        <!-- Quick Actions -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('Zap', 'h-5 w-5 text-primary') ?>
                    Aksi Cepat
                </h2>
            </div>

            <div class="p-6 space-y-3">
                <a href="<?= base_url('master/products/edit/' . $product->id) ?>" class="w-full h-10 rounded-lg bg-primary text-white font-medium flex items-center justify-center hover:bg-primary/90 transition">
                    <?= icon('Edit', 'h-5 w-5 mr-2') ?>
                    Edit Produk
                </a>

                <a href="<?= base_url('info/stock?product_id=' . $product->id) ?>" class="w-full h-10 rounded-lg border border-border/50 text-foreground font-medium flex items-center justify-center hover:bg-muted transition">
                    <?= icon('BarChart3', 'h-5 w-5 mr-2') ?>
                    Lihat Stok
                </a>
            </div>
        </div>

        <!-- Product Stats -->
        <div class="rounded-lg border bg-surface shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('TrendingUp', 'h-5 w-5 text-primary') ?>
                    Ringkasan
                </h2>
            </div>

            <div class="p-6 space-y-4">
                <div class="p-4 rounded-lg bg-primary/5 border border-primary/20">
                    <p class="text-xs text-primary font-semibold uppercase">Keuntungan per Unit</p>
                    <p class="text-xl font-bold text-primary mt-2">
                        <?= format_currency($product->price_sell - $product->price_buy) ?>
                    </p>
                </div>

                <div class="p-4 rounded-lg bg-muted/30 border border-border/50">
                    <p class="text-xs text-muted-foreground font-semibold uppercase">Total Stok (Semua Gudang)</p>
                    <p class="text-xl font-bold text-foreground mt-2">
                        <?= $totalStock ?? 0 ?> <?= $product->unit ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
