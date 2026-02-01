<?php
/**
 * Modern Modal Component
 * Alpine.js powered modal with smooth animations
 * 
 * Usage:
 * <?= view('components/modal', [
 *     'id' => 'confirmModal',
 *     'title' => 'Confirm Action',
 *     'content' => 'Are you sure?',
 *     'primaryButton' => ['text' => 'Confirm', 'class' => 'btn-primary'],
 *     'secondaryButton' => ['text' => 'Cancel', 'action' => '@click="open = false"']
 * ]) ?>
 */

$id = $id ?? 'modal_' . uniqid();
$title = $title ?? 'Modal';
$content = $content ?? '';
$subtitle = $subtitle ?? null;
$primaryButton = $primaryButton ?? ['text' => 'Confirm'];
$secondaryButton = $secondaryButton ?? ['text' => 'Cancel'];
$size = $size ?? 'md'; // sm, md, lg, xl

$sizeClasses = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
];

$sizeClass = $sizeClasses[$size] ?? 'max-w-md';
?>

<!-- Modal Container -->
<div x-data="{ open: false }" 
     @keydown.escape="open = false"
     class="<?= $id ?>">
     
    <!-- Modal Backdrop -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-foreground/80 backdrop-blur-sm"
         @click="open = false"
         x-cloak>
    </div>
    
    <!-- Modal Dialog -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-cloak>
         
        <div class="card <?= $sizeClass ?> w-full"
             @click.stop>
             
            <!-- Modal Header -->
            <div class="card-header flex items-center justify-between">
                <div>
                    <h2 class="card-title"><?= $title ?></h2>
                    <?php if ($subtitle): ?>
                        <p class="text-sm text-muted-foreground mt-1"><?= $subtitle ?></p>
                    <?php endif; ?>
                </div>
                <button @click="open = false"
                        class="flex h-8 w-8 items-center justify-center rounded-lg hover:bg-accent hover:text-accent-foreground transition-colors"
                        aria-label="Close modal">
                    <?= icon('X', 'h-5 w-5') ?>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="card-content">
                <?= $content ?>
            </div>
            
            <!-- Modal Footer -->
            <div class="border-t border-border px-6 py-4 flex items-center justify-end gap-3">
                <?php if (isset($secondaryButton['text'])): ?>
                    <button class="btn btn-ghost"
                            @click="open = false"
                            <?= $secondaryButton['action'] ?? '' ?>>
                        <?= $secondaryButton['text'] ?>
                    </button>
                <?php endif; ?>
                
                <?php if (isset($primaryButton['text'])): ?>
                    <button class="btn btn-primary"
                            <?= $primaryButton['action'] ?? '' ?>>
                        <?= $primaryButton['text'] ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
