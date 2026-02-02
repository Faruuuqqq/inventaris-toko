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
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
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
                <svg class="h-6 w-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
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
                <svg class="h-6 w-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
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
                <svg class="h-6 w-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
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
                <svg class="h-6 w-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Alerts -->
<?php if (session()->has('success')): ?>
<div class="mb-6 rounded-lg border border-success/20 bg-success/10 p-4">
    <div class="flex items-start gap-3">
        <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm font-medium text-success"><?= session('success') ?></p>
    </div>
</div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
<div class="mb-6 rounded-lg border border-destructive/20 bg-destructive/10 p-4">
    <div class="flex items-start gap-3">
        <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
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
                            <svg class="h-16 w-16 text-muted-foreground/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
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
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="<?= base_url('finance/kontra-bon/edit/' . $kb['id']) ?>" 
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary transition-colors hover:bg-primary/20"
                                   title="Edit">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <a href="<?= base_url('finance/kontra-bon/pdf/' . $kb['id']) ?>" 
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-success/10 text-success transition-colors hover:bg-success/20"
                                   title="Export PDF"
                                   target="_blank">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </a>
                                <form action="<?= base_url('finance/kontra-bon/delete/' . $kb['id']) ?>" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus kontra bon ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" 
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-destructive/10 text-destructive transition-colors hover:bg-destructive/20"
                                            title="Hapus">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
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
