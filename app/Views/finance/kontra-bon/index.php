<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground"><?= $title ?></h2>
            <p class="mt-1 text-sm text-muted-foreground"><?= $subtitle ?></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= base_url('finance/kontra-bon/create') ?>" 
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-primary/90 hover:shadow-md">
                <?= icon('Plus', 'h-5 w-5') ?>
                Tambah Kontra Bon
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="mb-6 grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Total -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Total Kontra Bon</p>
                <p class="mt-2 text-2xl font-bold text-foreground"><?= number_format($stats['total'] ?? 0) ?></p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                <?= icon('FileText', 'h-6 w-6 text-primary') ?>
            </div>
        </div>
    </div>

    <!-- Pending -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Pending</p>
                <p class="mt-2 text-2xl font-bold text-warning"><?= number_format($stats['pending'] ?? 0) ?></p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning/10">
                <?= icon('Clock', 'h-6 w-6 text-warning') ?>
            </div>
        </div>
    </div>

    <!-- Paid -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Paid</p>
                <p class="mt-2 text-2xl font-bold text-success"><?= number_format($stats['paid'] ?? 0) ?></p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-success/10">
                <?= icon('CheckCircle', 'h-6 w-6 text-success') ?>
            </div>
        </div>
    </div>

    <!-- Total Amount -->
    <div class="card p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-muted-foreground">Total Amount</p>
                <p class="mt-2 text-2xl font-bold text-foreground"><?= format_currency($stats['total_amount'] ?? 0) ?></p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-secondary/10">
                <?= icon('DollarSign', 'h-6 w-6 text-secondary') ?>
            </div>
        </div>
    </div>
</div>

<!-- Alerts -->
<?php if (session()->has('success')): ?>
<div class="mb-6 rounded-lg border border-success/20 bg-success/10 p-4">
    <div class="flex items-start gap-3">
        <?= icon('CheckCircle', 'mt-0.5 h-5 w-5 flex-shrink-0 text-success') ?>
        <p class="text-sm font-medium text-success"><?= session('success') ?></p>
    </div>
</div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
<div class="mb-6 rounded-lg border border-destructive/20 bg-destructive/10 p-4">
    <div class="flex items-start gap-3">
        <?= icon('AlertCircle', 'mt-0.5 h-5 w-5 flex-shrink-0 text-destructive') ?>
        <p class="text-sm font-medium text-destructive"><?= session('error') ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Data Table -->
<div class="card">
    <div class="w-full overflow-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border bg-muted/40">
                    <th class="px-6 py-4 text-left font-semibold text-foreground">No. Dokumen</th>
                    <th class="px-6 py-4 text-left font-semibold text-foreground">Customer</th>
                    <th class="px-6 py-4 text-left font-semibold text-foreground">Tanggal</th>
                    <th class="px-6 py-4 text-left font-semibold text-foreground">Jatuh Tempo</th>
                    <th class="px-6 py-4 text-right font-semibold text-foreground">Total Amount</th>
                    <th class="px-6 py-4 text-center font-semibold text-foreground">Status</th>
                    <th class="px-6 py-4 text-center font-semibold text-foreground">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($kontraBons)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <?= icon('FileText', 'h-16 w-16 text-muted-foreground/30') ?>
                            <p class="font-medium text-muted-foreground">Belum ada data kontra bon</p>
                            <p class="text-sm text-muted-foreground/70">Klik tombol "Tambah Kontra Bon" untuk membuat yang baru</p>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($kontraBons as $kb): ?>
                    <tr class="border-b border-border/50 transition-colors hover:bg-muted/30">
                        <td class="px-6 py-4">
                            <span class="font-semibold text-primary"><?= esc($kb['document_number']) ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-medium text-foreground"><?= esc($kb['customer_name']) ?></span>
                                <?php if (!empty($kb['customer_phone'])): ?>
                                <span class="text-xs text-muted-foreground"><?= esc($kb['customer_phone']) ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-muted-foreground">
                            <?= date('d M Y', strtotime($kb['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-muted-foreground">
                            <?= $kb['due_date'] ? date('d M Y', strtotime($kb['due_date'])) : '-' ?>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <span class="font-bold text-foreground"><?= format_currency($kb['total_amount']) ?></span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php
                            $statusClass = match($kb['status']) {
                                'PAID' => 'bg-success/10 text-success border-success/30',
                                'PENDING' => 'bg-warning/10 text-warning border-warning/30',
                                'CANCELLED' => 'bg-destructive/10 text-destructive border-destructive/30',
                                default => 'bg-muted/10 text-muted-foreground border-border',
                            };
                            ?>
                            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap <?= $statusClass ?>">
                                <?= esc($kb['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?= base_url('finance/kontra-bon/detail/' . $kb['id']) ?>" 
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-secondary/10 text-secondary transition-colors hover:bg-secondary/20"
                                   title="Detail">
                                    <?= icon('Eye', 'h-4 w-4') ?>
                                </a>
                                <a href="<?= base_url('finance/kontra-bon/edit/' . $kb['id']) ?>" 
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary transition-colors hover:bg-primary/20"
                                   title="Edit">
                                    <?= icon('Edit', 'h-4 w-4') ?>
                                </a>
                                <a href="<?= base_url('finance/kontra-bon/pdf/' . $kb['id']) ?>" 
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-success/10 text-success transition-colors hover:bg-success/20"
                                   title="Export PDF"
                                   target="_blank">
                                    <?= icon('Download', 'h-4 w-4') ?>
                                </a>
                                <form action="<?= base_url('finance/kontra-bon/delete/' . $kb['id']) ?>" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus kontra bon ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" 
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-destructive/10 text-destructive transition-colors hover:bg-destructive/20"
                                            title="Hapus">
                                        <?= icon('Trash2', 'h-4 w-4') ?>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
