<!-- Summary -->
<div class="mb-6 grid gap-4 md:grid-cols-4">
    <?php helper('ui_helper'); ?>
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-4">
            <p class="text-sm text-muted-foreground">Total Produk</p>
            <p class="text-2xl font-bold"><?= $totalProducts ?></p>
        </div>
    </div>
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-4">
            <p class="text-sm text-muted-foreground">Total Kategori</p>
            <p class="text-2xl font-bold"><?= $totalCategories ?></p>
        </div>
    </div>
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-4">
            <p class="text-sm text-muted-foreground">Total Stok</p>
            <p class="text-2xl font-bold"><?= $totalStock ?></p>
        </div>
    </div>
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
        <div class="p-4">
            <p class="text-sm text-muted-foreground">Nilai Persediaan</p>
            <p class="text-2xl font-bold text-primary"><?= format_currency($totalValue) ?></p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex gap-4">
        <div class="relative w-full sm:w-72">
            <?= icon('Search', 'absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground') ?>
            <input type="text" placeholder="Cari produk..." class="w-64 rounded-md border border-input bg-background pl-9 pr-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
        </div>
        <select class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
            <option value="">Semua Kategori</option>
            <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button 
        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
        onclick="document.getElementById('productModal').classList.remove('hidden')"
    >
        <?= icon('Plus', 'mr-2 h-4 w-4') ?>
        Tambah Produk
    </button>
</div>

<!-- Products Table -->
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="p-0">
        <table class="w-full caption-bottom text-sm">
            <thead class="[&_tr]:border-b">
                <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                    <th class="pb-3 font-medium">Kode</th>
                    <th class="pb-3 font-medium">Nama Produk</th>
                    <th class="pb-3 font-medium">Kategori</th>
                    <th class="pb-3 font-medium text-right">Harga Beli</th>
                    <th class="pb-3 font-medium text-right">Harga Jual</th>
                    <th class="pb-3 font-medium text-center">Stok</th>
                    <th class="pb-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="[&_tr:last-child]:border-0">
                <?php foreach ($products as $product): ?>
                <tr class="border-b last:border-0">
                    <td class="font-medium text-primary"><?= $product['sku'] ?></td>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded bg-muted">
                                <?= icon('Package', 'h-4 w-4 text-muted-foreground') ?>
                            </div>
                            <span><?= $product['name'] ?></span>
                        </div>
                    </td>
                    <td>
                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium bg-secondary/10 text-secondary"><?= $product['category_name'] ?></span>
                    </td>
                    <td class="text-right"><?= format_currency($product['price_buy']) ?></td>
                    <td class="text-right font-medium"><?= format_currency($product['price_sell']) ?></td>
                    <td class="text-center">
                        <span class="font-medium">
                            <?= $this->getProductTotalStock($product['id']) ?> <?= $product['unit'] ?>
                        </span>
                    </td>
                    <td class="text-right">
                        <div class="flex justify-end gap-1">
                            <button class="inline-flex items-center justify-center rounded-md p-2 text-muted-foreground hover:bg-accent hover:text-accent-foreground h-8 w-8">
                                <?= icon('Pencil', 'h-4 w-4') ?>
                            </button>
                            <button class="inline-flex items-center justify-center rounded-md p-2 text-destructive hover:bg-accent hover:text-accent-foreground h-8 w-8">
                                <?= icon('Trash2', 'h-4 w-4') ?>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Product Modal (Simplified without Alpine for now) -->
<div id="productModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="rounded-lg border bg-card text-card-foreground shadow-sm w-full max-w-md">
        <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="text-2xl font-semibold leading-none tracking-tight">Tambah Produk Baru</h3>
        </div>
        <div class="p-6 pt-0">
            <form action="/master/products" method="post" class="space-y-4">
                <div class="space-y-2">
                    <label for="sku" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">SKU</label>
                    <input type="text" name="sku" id="sku" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" placeholder="Masukkan SKU" required>
                </div>
                <div class="space-y-2">
                    <label for="name" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Nama Produk</label>
                    <input type="text" name="name" id="name" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" placeholder="Masukkan nama produk" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="category_id" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Kategori</label>
                        <select name="category_id" id="category_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" required>
                            <option value="">Pilih kategori</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label for="unit" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Satuan</label>
                        <input type="text" name="unit" id="unit" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" placeholder="Pcs, Kg, dll" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="price_buy" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Harga Beli</label>
                        <input type="number" name="price_buy" id="price_buy" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" placeholder="0" step="0.01" required>
                    </div>
                    <div class="space-y-2">
                        <label for="price_sell" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Harga Jual</label>
                        <input type="number" name="price_sell" id="price_sell" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" placeholder="0" step="0.01" required>
                    </div>
                </div>
                <div class="space-y-2">
                    <label for="min_stock_alert" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Stok Minimum</label>
                    <input type="number" name="min_stock_alert" id="min_stock_alert" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm" placeholder="10" required>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2" onclick="document.getElementById('productModal').classList.add('hidden')">
                        Batal
                    </button>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>