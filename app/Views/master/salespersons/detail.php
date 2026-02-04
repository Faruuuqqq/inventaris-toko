<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <svg class="h-8 w-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Detail Sales
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Informasi detail salesperson</p>
    </div>
    <div class="flex gap-3">
        <a href="<?= base_url('master/salespersons') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        <?php if (is_admin()): ?>
        <a href="<?= base_url('master/salespersons/edit/' . $sales->id) ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Main Content -->
<div class="rounded-xl border border-border/50 bg-surface overflow-hidden">
    <!-- Header Section -->
    <div class="p-6 border-b border-border/50 bg-muted/30">
        <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
            <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Informasi Sales
        </h2>
    </div>

    <!-- Content Section -->
    <div class="p-6 space-y-6">
        <!-- Salesperson Name -->
        <div>
            <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Nama Sales</p>
            <p class="text-2xl font-bold text-foreground mt-2"><?= esc($sales->name) ?></p>
        </div>

        <!-- Phone & Status -->
        <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Nomor Telepon</p>
                <p class="text-sm font-medium text-foreground mt-1"><?= esc($sales->phone ?? '-') ?></p>
            </div>

            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Status</p>
                <div class="mt-1">
                    <?php if ($sales->is_active): ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-success/10 text-success">
                        ✓ Aktif
                    </span>
                    <?php else: ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-muted/50 text-muted-foreground">
                        Tidak Aktif
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Timestamps -->
        <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Bergabung Sejak</p>
                <p class="text-sm font-medium text-foreground mt-1">
                    <?php if ($sales->created_at): ?>
                        <?= date('d M Y H:i', strtotime($sales->created_at)) ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </p>
            </div>

            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Diperbarui</p>
                <p class="text-sm font-medium text-foreground mt-1">
                    <?php if ($sales->updated_at): ?>
                        <?= date('d M Y H:i', strtotime($sales->updated_at)) ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
