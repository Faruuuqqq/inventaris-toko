<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl">
    <!-- Page Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-foreground">Edit Produk</h2>
        <p class="mt-1 text-muted-foreground">Perbarui informasi produk</p>
    </div>

    <!-- Form Card -->
    <div class="rounded-xl border border-border/50 bg-surface p-6">
        <form action="<?= base_url('master/products/' . $product->id) ?>" method="POST" class="space-y-5">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            
            <!-- Row 1: SKU & Name -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="sku">SKU *</label>
                    <input 
                        type="text" 
                        name="sku" 
                        id="sku" 
                        required 
                        value="<?= esc($product->sku) ?>"
                        placeholder="Contoh: PRD-001"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="name">Nama Produk *</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        required 
                        value="<?= esc($product->name) ?>"
                        placeholder="Contoh: Produk Berkualitas"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
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
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories ?? [] as $category): ?>
                        <option value="<?= $category['id'] ?? $category->id ?>" <?= ($product->category_id == ($category['id'] ?? $category->id)) ? 'selected' : '' ?>>
                            <?= esc($category['name'] ?? $category->name) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="unit">Satuan *</label>
                    <input 
                        type="text" 
                        name="unit" 
                        id="unit" 
                        required 
                        value="<?= esc($product->unit) ?>"
                        placeholder="Contoh: PCS, BOX, KG"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
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
                        value="<?= esc($product->price_buy) ?>"
                        placeholder="Contoh: 50000"
                        step="0.01"
                        min="0"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-foreground" for="price_sell">Harga Jual *</label>
                    <input 
                        type="number" 
                        name="price_sell" 
                        id="price_sell" 
                        required 
                        value="<?= esc($product->price_sell) ?>"
                        placeholder="Contoh: 75000"
                        step="0.01"
                        min="0"
                        class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                </div>
            </div>

            <!-- Min Stock Alert -->
            <div class="space-y-2">
                <label class="text-sm font-semibold text-foreground" for="min_stock_alert">Minimal Stok Alert</label>
                <input 
                    type="number" 
                    name="min_stock_alert" 
                    id="min_stock_alert" 
                    value="<?= esc($product->min_stock_alert) ?>"
                    placeholder="Contoh: 10"
                    step="1"
                    min="0"
                    class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                >
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
