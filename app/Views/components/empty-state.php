<?php
/**
 * Empty State Component - Display when no data available
 *
 * Usage:
 * <?= view('components/empty-state', [
 *     'icon' => 'Package',
 *     'title' => 'Belum ada data',
 *     'description' => 'Silakan tambahkan data baru untuk memulai',
 *     'action' => [
 *         'text' => 'Tambah Data',
 *         'url' => base_url('create'),
 *         'icon' => 'Plus'
 *     ]
 * ]) ?>
 */

$icon = $icon ?? 'Package';
$title = $title ?? 'Tidak ada data ditemukan';
$description = $description ?? 'Belum ada data yang tersedia untuk halaman ini. Silakan tambahkan data baru atau ubah filter pencarian Anda.';
$action = $action ?? null;
?>

<div class="flex flex-col items-center justify-center py-16 text-center h-full min-h-[400px] animate-in fade-in zoom-in duration-300">
    <div class="relative mb-6 group">
        <div class="absolute inset-0 bg-primary/20 blur-xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
        <div class="relative bg-muted/30 p-8 rounded-full ring-1 ring-border/50 shadow-sm group-hover:shadow-md transition-all duration-300">
            <?= icon($icon ?? 'Package', 'h-12 w-12 text-muted-foreground/60 group-hover:text-primary/80 transition-colors duration-300') ?>
        </div>
    </div>

    <h3 class="text-xl font-bold text-foreground mb-2"><?= esc($title) ?></h3>
    <p class="text-sm text-muted-foreground max-w-sm mx-auto leading-relaxed mb-8">
        <?= esc($description) ?>
    </p>

    <?php if ($action): ?>
        <a href="<?= esc($action['url'] ?? '#') ?>"
           class="inline-flex items-center justify-center gap-2 h-11 px-6 rounded-xl bg-primary text-primary-foreground font-medium shadow-lg shadow-primary/20 hover:bg-primary/90 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
            <?= icon($action['icon'] ?? 'Plus', 'h-5 w-5') ?>
            <span><?= esc($action['text'] ?? 'Tambah Baru') ?></span>
        </a>
    <?php endif; ?>
</div>