<!-- Payables Table -->
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="p-6">
        <div class="mb-4">
            <h3 class="text-xl font-semibold">Saldo Utang</h3>
            <p class="text-sm text-muted-foreground">Daftar utang ke supplier</p>
        </div>
        
        <!-- Summary Card -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-4 mb-6">
            <div class="flex justify-between">
                <h4 class="text-lg font-semibold">Total Utang Seluruhnya</h4>
                <p class="text-2xl font-bold text-primary">Rp <?= number_format($totalPayable, 0, ',', '.') ?></p>
            </div>
        </div>
        
        <!-- Supplier Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Supplier</th>
                    <th>Total Utang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($suppliers as $supplier): ?>
                <tr>
                    <td><?= $supplier['name'] ?></td>
                    <td class="text-right font-medium">Rp <?= number_format($supplier['debt_balance'], 0, ',', '.') ?></td>
                    <td>
                        <div class="flex gap-1">
                            <button class="btn btn-ghost" style="height: 32px; width: 32px; padding: 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 20h.01a2 2 0 01-2-2V5a2 2 0 01-2-2H5a2 2 0 01-2 2v13a2 2 0 01 2 2h13z"/></svg>
                            </button>
                            <a href="/finance/payments/payable?supplier_id=<?= $supplier['id'] ?>" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="4" y="2" width="16" height="20" rx="2" stroke-width="2"/><line x1="8" y1="6" x2="16" y2="6" stroke-width="2"/><line x1="16" y1="14" x2="8" y2="14" stroke-width="2"/><line x1="12" y1="14" x2="12" y2="14" stroke-width="2"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>