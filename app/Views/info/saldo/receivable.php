<!-- Receivables Summary -->
<div class="rounded-lg border bg-card text-card-foreground shadow-sm">
    <div class="p-6">
        <div class="mb-4">
            <h3 class="text-xl font-semibold">Saldo Piutang</h3>
            <p class="text-sm text-muted-foreground">Daftar piutang customer</p>
        </div>
        
        <!-- Summary Cards -->
        <div class="grid gap-4 md:grid-cols-4 mb-6">
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-4">
                <h4 class="font-medium">0-30 Hari</h4>
                <p class="text-sm text-muted-foreground"><?= count($agingData['0-30']['customers']) ?></p>
                <p class="text-xl font-bold">Rp <?= number_format($agingData['0-30']['total'], 0, ',', '.') ?></p>
            </div>
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-4">
                <h4 class="font-medium">31-60 Hari</h4>
                <p class="text-sm text-muted-foreground"><?= count($agingData['31-60']['customers']) ?></p>
                <p class="text-xl font-bold">Rp <?= number_format($agingData['31-60']['total'], 0, ',', '.') ?></p>
            </div>
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-4">
                <h4 class="font-medium">61-90 Hari</h4>
                <p class="text-sm text-muted-foreground"><?= count($agingData['61-90']['customers']) ?></p>
                <p class="text-xl font-bold">Rp <?= number_format($agingData['61-90']['total'], 0, ',', '.') ?></p>
            </div>
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-4">
                <h4 class="font-medium">90+ Hari</h4>
                <p class="text-sm text-muted-foreground"><?= count($agingData['90+']['customers']) ?></p>
                <p class="text-xl font-bold">Rp <?= number_format($agingData['90+']['total'], 0, ',', '.') ?></p>
            </div>
        </div>
        
        <!-- Total Summary -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-4 mb-6">
            <div class="flex justify-between">
                <h4 class="text-lg font-semibold">Total Piutang Seluruhnya</h4>
                <p class="text-2xl font-bold text-primary">Rp <?= number_format($totalReceivable, 0, ',', '.') ?></p>
            </div>
        </div>
        
        <!-- Customer Detail Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Customer</th>
                    <th>Total Piutang</th>
                    <th>Invoice Terakhir</th>
                    <th>Hari Jatuh Tempo</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><?= $customer['name'] ?></td>
                    <td class="text-right font-medium">Rp <?= number_format($customer['receivable_balance'], 0, ',', '.') ?></td>
                    <td><?= $customer['last_invoice'] ?? '-' ?></td>
                    <td><?= $customer['days_overdue'] ?? '-' ?></td>
                    <td>
                        <div class="flex gap-1">
                            <button class="btn btn-ghost" style="height: 32px; width: 32px; padding: 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 20h.01a2 2 0 01-2-2V5a2 2 0 01-2-2H5a2 2 0 01-2 2v13a2 2 0 01 2 2h13z"/></svg>
                            </button>
                            <a href="/finance/payments/receivable?customer_id=<?= $customer['id'] ?>" class="btn btn-primary">
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