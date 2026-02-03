<?php
/**
 * Modern Modal Component
 * Alpine.js powered modal with smooth animations and variants
 * 
 * Usage:
 * <?= view('components/modal', [
 *     'id' => 'confirmModal',
 *     'title' => 'Confirm Action',
 *     'content' => 'Are you sure?',
 *     'primaryButton' => ['text' => 'Confirm', 'class' => 'btn-primary'],
 *     'secondaryButton' => ['text' => 'Cancel', 'action' => '@click="open = false"'],
 *     'variant' => 'primary',  // primary, danger, success, warning, info
 *     'icon' => 'AlertCircle'  // Optional icon
 * ]) ?>
 */

$id = $id ?? 'modal_' . uniqid();
$title = $title ?? 'Modal';
$content = $content ?? '';
$subtitle = $subtitle ?? null;
$primaryButton = $primaryButton ?? ['text' => 'Confirm'];
$secondaryButton = $secondaryButton ?? ['text' => 'Cancel'];
$size = $size ?? 'md'; // sm, md, lg, xl
$variant = $variant ?? 'primary'; // primary, danger, success, warning, info
$icon = $icon ?? null;
$isLoading = $isLoading ?? false;

$sizeClasses = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
];

// Variant color classes for header
$variantClasses = [
    'primary' => 'bg-primary/10 border-b border-primary/20',
    'danger' => 'bg-destructive/10 border-b border-destructive/20',
    'success' => 'bg-success/10 border-b border-success/20',
    'warning' => 'bg-warning/10 border-b border-warning/20',
    'info' => 'bg-blue-500/10 border-b border-blue-500/20',
];

// Variant text color
$variantTextClasses = [
    'primary' => 'text-primary',
    'danger' => 'text-destructive',
    'success' => 'text-success',
    'warning' => 'text-warning',
    'info' => 'text-blue-500',
];

$sizeClass = $sizeClasses[$size] ?? 'max-w-md';
$variantClass = $variantClasses[$variant] ?? $variantClasses['primary'];
$variantTextClass = $variantTextClasses[$variant] ?? $variantTextClasses['primary'];
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
          x-cloak
          @keydown.escape="open = false">
          
         <div class="card <?= $sizeClass ?> w-full"
              @click.stop>
              
             <!-- Modal Header with Variant Color -->
             <div class="card-header <?= $variantClass ?> flex items-center justify-between">
                 <div class="flex items-center gap-3 flex-1">
                     <?php if ($icon): ?>
                         <div class="flex h-8 w-8 items-center justify-center rounded-lg <?= str_replace('text-', 'bg-', $variantTextClass) ?>/10">
                             <?= icon($icon, 'h-5 w-5 ' . $variantTextClass) ?>
                         </div>
                     <?php endif; ?>
                     <div>
                         <h2 class="card-title <?= $variantTextClass ?>"><?= $title ?></h2>
                         <?php if ($subtitle): ?>
                             <p class="text-sm text-muted-foreground mt-1"><?= $subtitle ?></p>
                         <?php endif; ?>
                     </div>
                 </div>
                 <button @click="open = false"
                         class="flex h-8 w-8 items-center justify-center rounded-lg hover:bg-accent hover:text-accent-foreground transition-colors flex-shrink-0"
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
