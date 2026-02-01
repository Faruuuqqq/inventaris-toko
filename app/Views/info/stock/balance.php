<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- Balance Table -->
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="p-6">
        <div class="mb-4">
            <h3 class="text-xl font-semibold">Saldo Stok</h3>
            <p class="text-sm text-muted-foreground">Total stok dan nilai persediaan</p>
        </div>

        <div class="grid gap-4 md:grid-cols-3 mb-6">
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-4">
                <h4 class="font-medium">Total Produk</h4>
                <p class="text-2xl font-bold text-primary"><?= count($productStocks) ?></p>
            </div>
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-4">
                <h4 class="font-medium">Total Stok</h4>
                <p class="text-2xl font-bold"><?= $totalStock ?></p>
            </div>
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-4">
                <h4 class="font-medium">Total Nilai</h4>
                <p class="text-2xl font-bold text-primary">Rp <?= number_format($totalValue, 0, ',', '.') ?></p>
            </div>
        </div>

        <!-- Products Stock Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Total Stok</th>
                    <th>Detail Stok per Gudang</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productStocks as $productName => $product): ?>
                <tr>
                    <td class="font-medium"><?= is_array($product) ? $product['name'] : $product->name ?></td>
                    <td><?= is_array($product) ? $product['total_stock'] : $product->total_stock ?></td>
                    <td>
                        <?php $warehouses = is_array($product) ? $product['warehouses'] : $product->warehouses; ?>
                        <?php foreach ($warehouses as $warehouse): ?>
                        <span class="inline-block rounded px-2 py-1 bg-secondary text-secondary-foreground mr-2">
                            <?= is_array($warehouse) ? $warehouse['warehouse'] : $warehouse->warehouse ?>: <?= is_array($warehouse) ? $warehouse['quantity'] : $warehouse->quantity ?>
                        </span>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
