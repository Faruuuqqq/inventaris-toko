<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl">
    <!-- Page Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-foreground">Tambah Produk</h2>
        <p class="mt-1 text-muted-foreground">Tambahkan produk baru ke katalog</p>
    </div>

    <!-- Form Card -->
    <div class="rounded-xl border border-border/50 bg-surface p-6">
        <form action="<?= base_url('master/products') ?>" method="POST" class="space-y-5" onsubmit="window.Loading.show('Menyimpan Data', 'Mohon tunggu, sedang menyimpan produk baru...')">
            <?= csrf_field() ?>

            <!-- Row 1: SKU & Name -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="sku">SKU *</label>
                    <input
                        type="text"
                        name="sku"
                        id="sku"
                        required
                        value="<?= old('sku') ?>"
                        placeholder="Contoh: PRD-001"
                        class="flex h-10 w-full rounded-lg border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all <?= session('errors.sku') ? 'border-destructive focus-visible:ring-destructive/50' : 'border-border' ?>"
                    >
                    <?php if (session('errors.sku')) : ?>
                        <p class="text-xs text-destructive mt-1"><?= session('errors.sku') ?></p>
                    <?php endif ?>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="name">Nama Produk *</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        required
                        value="<?= old('name') ?>"
                        placeholder="Contoh: Produk Berkualitas"
                        class="flex h-10 w-full rounded-lg border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all <?= session('errors.name') ? 'border-destructive focus-visible:ring-destructive/50' : 'border-border' ?>"
                    >
                    <?php if (session('errors.name')) : ?>
                        <p class="text-xs text-destructive mt-1"><?= session('errors.name') ?></p>
                    <?php endif ?>
                </div>
            </div>

            <!-- Row 2: Category & Unit -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="category_id">Kategori *</label>
                    <select
                        name="category_id"
                        id="category_id"
                        required
                        class="flex h-10 w-full rounded-lg border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all <?= session('errors.category_id') ? 'border-destructive focus-visible:ring-destructive/50' : 'border-border' ?>"
                    >
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories ?? [] as $category): ?>
                        <option value="<?= $category->id ?>" <?= old('category_id') == $category->id ? 'selected' : '' ?>>
                            <?= esc($category->name) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (session('errors.category_id')) : ?>
                        <p class="text-xs text-destructive mt-1"><?= session('errors.category_id') ?></p>
                    <?php endif ?>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="unit">Satuan *</label>
                    <input
                        type="text"
                        name="unit"
                        id="unit"
                        required
                        value="<?= old('unit') ?>"
                        placeholder="Contoh: PCS, BOX, KG"
                        class="flex h-10 w-full rounded-lg border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all <?= session('errors.unit') ? 'border-destructive focus-visible:ring-destructive/50' : 'border-border' ?>"
                    >
                    <?php if (session('errors.unit')) : ?>
                        <p class="text-xs text-destructive mt-1"><?= session('errors.unit') ?></p>
                    <?php endif ?>
                </div>
            </div>

            <!-- Row 3: Buy Price & Sell Price -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="price_buy">Harga Beli *</label>
                    <input
                        type="number"
                        name="price_buy"
                        id="price_buy"
                        required
                        value="<?= old('price_buy', '0') ?>"
                        placeholder="Contoh: 50000"
                        step="0.01"
                        min="0"
                        class="flex h-10 w-full rounded-lg border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all <?= session('errors.price_buy') ? 'border-destructive focus-visible:ring-destructive/50' : 'border-border' ?>"
                    >
                    <?php if (session('errors.price_buy')) : ?>
                        <p class="text-xs text-destructive mt-1"><?= session('errors.price_buy') ?></p>
                    <?php endif ?>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="price_sell">Harga Jual *</label>
                    <input
                        type="number"
                        name="price_sell"
                        id="price_sell"
                        required
                        value="<?= old('price_sell', '0') ?>"
                        placeholder="Contoh: 75000"
                        step="0.01"
                        min="0"
                        class="flex h-10 w-full rounded-lg border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all <?= session('errors.price_sell') ? 'border-destructive focus-visible:ring-destructive/50' : 'border-border' ?>"
                    >
                    <?php if (session('errors.price_sell')) : ?>
                        <p class="text-xs text-destructive mt-1"><?= session('errors.price_sell') ?></p>
                    <?php endif ?>
                </div>
            </div>

            <!-- Min Stock Alert -->
            <div class="space-y-2">
                <label class="text-sm font-semibold text-foreground" for="min_stock_alert">Minimal Stok Alert</label>
                <input
                    type="number"
                    name="min_stock_alert"
                    id="min_stock_alert"
                    value="<?= old('min_stock_alert', '10') ?>"
                    placeholder="Contoh: 10"
                    step="1"
                    min="0"
                    class="flex h-10 w-full rounded-lg border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all <?= session('errors.min_stock_alert') ? 'border-destructive focus-visible:ring-destructive/50' : 'border-border' ?>"
                >
                <?php if (session('errors.min_stock_alert')) : ?>
                    <p class="text-xs text-destructive mt-1"><?= session('errors.min_stock_alert') ?></p>
                <?php endif ?>
            </div>

            <!-- Form Footer -->
            <div class="flex gap-3 justify-end pt-4 border-t border-border/50">
                <a href="<?= base_url('master/products') ?>" class="inline-flex items-center justify-center rounded-lg border border-border bg-muted/30 text-foreground hover:bg-muted transition h-10 px-6 gap-2 text-sm font-semibold">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary-light transition h-10 px-6 gap-2 text-sm font-semibold">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
