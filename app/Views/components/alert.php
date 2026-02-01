<?php
/**
 * Modern Alert Component - Notification Messages
 * Professional design with smooth animations
 * 
 * Usage: 
 * <?= view('components/alert', [
 *     'type' => 'success',  // success, error, warning, info
 *     'title' => 'Success!',
 *     'message' => 'Operation completed successfully',
 *     'dismissible' => true
 * ]) ?>
 * 
 * @param string $type - success, error, warning, info (default: info)
 * @param string $message - Alert message
 * @param string $title - Optional title
 * @param bool $dismissible - Show close button (default: false)
 */

$type = $type ?? 'info';
$message = $message ?? '';
$title = $title ?? '';
$dismissible = $dismissible ?? true;

$typeClasses = [
    'success' => 'alert-success',
    'error' => 'alert-error',
    'warning' => 'alert-warning',
    'info' => 'alert-info'
];

$icons = [
    'success' => 'CheckCircle',
    'error' => 'AlertCircle',
    'warning' => 'AlertTriangle',
    'info' => 'Info'
];

$alertClass = $typeClasses[$type] ?? 'alert-info';
$icon = $icons[$type] ?? 'Info';
?>

<div class="alert <?= $alertClass ?>" 
     x-data="{ open: true }"
     x-show="open"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y--4"
     x-transition:enter-end="opacity-100 translate-y-0">
    <div class="flex items-start gap-3">
        <div class="flex h-5 w-5 flex-shrink-0 items-center justify-center">
            <?= icon($icon, 'h-5 w-5') ?>
        </div>
        <div class="flex-1">
            <?php if ($title): ?>
                <h4 class="font-semibold leading-none"><?= esc($title) ?></h4>
            <?php endif; ?>
            <?php if ($message): ?>
                <p class="text-sm opacity-80 mt-1"><?= esc($message) ?></p>
            <?php endif; ?>
        </div>
        <?php if ($dismissible): ?>
            <button @click="open = false"
                    class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded hover:opacity-70 transition-opacity"
                    aria-label="Close alert">
                <?= icon('X', 'h-4 w-4') ?>
            </button>
        <?php endif; ?>
    </div>
</div>
