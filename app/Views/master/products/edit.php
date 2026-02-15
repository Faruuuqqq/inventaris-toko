<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div x-data="editManager()" class="max-w-2xl">
    <!-- Page Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-foreground">Edit Produk</h2>
        <p class="mt-1 text-muted-foreground">Perbarui informasi produk</p>
    </div>

    <!-- Form Card -->
    <div class="rounded-xl border border-border/50 bg-surface p-6">
        <form @submit.prevent="submitForm" action="<?= base_url('master/products/' . $product->id) ?>" method="POST" class="space-y-5">
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
                        :class="{'border-destructive': errors.sku}"
                        class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                    <span x-show="errors.sku" class="text-destructive text-xs mt-1" x-text="errors.sku"></span>
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
                        :class="{'border-destructive': errors.name}"
                        class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                    <span x-show="errors.name" class="text-destructive text-xs mt-1" x-text="errors.name"></span>
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
                        :class="{'border-destructive': errors.category_id}"
                        class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories ?? [] as $category): ?>
                        <option value="<?= $category['id'] ?? $category->id ?>" <?= ($product->category_id == ($category['id'] ?? $category->id)) ? 'selected' : '' ?>>
                            <?= esc($category['name'] ?? $category->name) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <span x-show="errors.category_id" class="text-destructive text-xs mt-1" x-text="errors.category_id"></span>
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
                        :class="{'border-destructive': errors.unit}"
                        class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                    <span x-show="errors.unit" class="text-destructive text-xs mt-1" x-text="errors.unit"></span>
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
                        :class="{'border-destructive': errors.price_buy}"
                        class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                    <span x-show="errors.price_buy" class="text-destructive text-xs mt-1" x-text="errors.price_buy"></span>
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
                        :class="{'border-destructive': errors.price_sell}"
                        class="flex h-11 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                    >
                    <span x-show="errors.price_sell" class="text-destructive text-xs mt-1" x-text="errors.price_sell"></span>
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
                    :class="{'border-destructive': errors.min_stock_alert}"
                    class="flex h-10 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/50 transition-all"
                >
                <span x-show="errors.min_stock_alert" class="text-destructive text-xs mt-1" x-text="errors.min_stock_alert"></span>
            </div>

            <!-- Form Footer -->
            <div class="flex gap-3 justify-end pt-4 border-t border-border/50">
            <a href="<?= base_url('master/products') ?>" class="inline-flex items-center justify-center rounded-lg border border-border bg-muted/30 text-foreground hover:bg-muted transition h-11 px-6 gap-2 text-sm font-semibold">
                <?= icon('X', 'h-5 w-5') ?>
                Batal
            </a>
            <button 
                type="submit" 
                :disabled="isSubmitting"
                class="inline-flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary-light transition h-11 px-6 gap-2 text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!isSubmitting"><?= icon('Check', 'h-5 w-5') ?></span>
                <span x-show="isSubmitting" class="inline-flex items-center gap-2">
                    <span class="animate-spin">⚙️</span>
                </span>
                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
            </button>
            </div>
        </form>
    </div>
</div>

<script>
function editManager() {
    return {
        isSubmitting: false,
        errors: {},

        async submitForm(event) {
            event.preventDefault();
            const form = event.target;
            
            // Clear previous errors
            this.errors = {};
            this.isSubmitting = true;

            try {
                const formData = new FormData(form);
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok || response.status === 201) {
                    // Success
                    ModalManager.success('Data produk berhasil diperbarui', () => {
                        // Redirect back to list
                        window.location.href = `<?= base_url('master/products') ?>`;
                    });
                } else if (response.status === 422) {
                    // Validation error
                    const data = await response.json();
                    if (data.errors) {
                        this.errors = data.errors;
                    }
                    // Show generic message, field errors will be displayed inline
                    ModalManager.error('Silakan periksa kembali data yang Anda masukkan. Lihat pesan kesalahan di setiap field.');
                } else {
                    // Other error
                    const data = await response.json();
                    ModalManager.error(data.message || 'Gagal menyimpan data. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('Form submission error:', error);
                ModalManager.error('Terjadi kesalahan: ' + error.message);
            } finally {
                this.isSubmitting = false;
            }
        }
    }
}
</script>

<?= $this->endSection() ?>
