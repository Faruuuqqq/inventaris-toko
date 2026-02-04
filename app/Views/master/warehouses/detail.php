<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <svg class="h-8 w-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m0 0v10l8 4"/>
            </svg>
            Detail Gudang
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Informasi detail gudang penyimpanan</p>
    </div>
    <div class="flex gap-3">
        <a href="<?= base_url('master/warehouses') ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        <?php if (is_admin()): ?>
        <a href="<?= base_url('master/warehouses/edit/' . $gudang->id) ?>" class="inline-flex items-center justify-center gap-2 h-11 px-6 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition">
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.581m0 0H9m5.581 0a2 2 0 100-4 2 2 0 000 4zM9 7h1.5a1 1 0 001-1V5a1 1 0 00-1-1H9a1 1 0 00-1 1v1a1 1 0 001 1z"/>
            </svg>
            Informasi Gudang
        </h2>
    </div>

    <!-- Content Section -->
    <div class="p-6 space-y-6">
        <!-- Warehouse Name -->
        <div>
            <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Nama Gudang</p>
            <p class="text-2xl font-bold text-foreground mt-2"><?= esc($gudang->name) ?></p>
        </div>

        <!-- Warehouse Code & Address -->
        <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Kode Gudang</p>
                <p class="text-sm font-medium text-foreground mt-1"><?= esc($gudang->code) ?></p>
            </div>

            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Status</p>
                <div class="mt-1">
                    <?php if ($gudang->is_active): ?>
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

            <div class="md:col-span-2">
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Alamat Lengkap</p>
                <p class="text-sm font-medium text-foreground mt-1"><?= esc($gudang->address ?? '-') ?></p>
            </div>
        </div>

        <!-- Timestamps -->
        <div class="grid gap-4 md:grid-cols-2 pt-4 border-t border-border/50">
            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Dibuat Pada</p>
                <p class="text-sm font-medium text-foreground mt-1">
                    <?php if ($gudang->created_at): ?>
                        <?= date('d M Y H:i', strtotime($gudang->created_at)) ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </p>
            </div>

            <div>
                <p class="text-xs text-muted-foreground font-semibold uppercase tracking-wide">Diperbarui</p>
                <p class="text-sm font-medium text-foreground mt-1">
                    <?php if ($gudang->updated_at): ?>
                        <?= date('d M Y H:i', strtotime($gudang->updated_at)) ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
