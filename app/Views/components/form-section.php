<?php
/**
 * Form Section Component - Structured form section with header
 * 
 * Usage:
 * <?= view('components/form-section', [
 *     'title' => 'Personal Information',
 *     'description' => 'Update your personal details',
 *     'content' => '<input type="text" name="name">'
 * ]) ?>
 */

$title = $title ?? '';
$description = $description ?? '';
$content = $content ?? ($slot ?? '');
$class = $class ?? '';
$collapsible = $collapsible ?? false;
$id = $id ?? 'form-section-' . uniqid();
?>

<div class="rounded-lg border border-border bg-card overflow-hidden <?= $class ?>">
    <div class="px-6 py-4 border-b border-border/50 bg-muted/30 flex items-start justify-between">
        <div>
            <?php if ($title): ?>
                <h3 class="font-semibold text-foreground text-lg"><?= esc($title) ?></h3>
            <?php endif; ?>
            <?php if ($description): ?>
                <p class="mt-1 text-sm text-muted-foreground"><?= esc($description) ?></p>
            <?php endif; ?>
        </div>
        
        <?php if ($collapsible): ?>
            <button type="button"
                    class="flex h-6 w-6 items-center justify-center rounded hover:bg-muted transition-colors"
                    x-data="{ open: true }"
                    @click="open = !open">
                <svg x-show="open" class="h-5 w-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
                <svg x-show="!open" class="h-5 w-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        <?php endif; ?>
    </div>
    
    <div class="p-6 space-y-6">
        <?= $content ?>
    </div>
</div>
