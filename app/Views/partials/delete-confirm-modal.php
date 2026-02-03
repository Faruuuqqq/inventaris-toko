<?php
/**
 * Delete Confirmation Modal
 * 
 * Usage (in your view):
 * <?= view('partials/delete-confirm-modal') ?>
 * 
 * Then use in your code:
 * ModalManager.delete('Product Name', () => {
 *     // Submit the delete form or call delete API
 *     fetch('/api/products/123', { method: 'DELETE' })
 *         .then(() => ModalManager.success('Data berhasil dihapus', () => location.reload()))
 *         .catch(e => ModalManager.error('Gagal menghapus: ' + e.message));
 * });
 */
?>

<div x-data="{ open: false }" class="delete-modal">
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
         
        <div class="card max-w-sm w-full"
             @click.stop>
             
            <!-- Modal Header -->
            <div class="card-header bg-destructive/10 border-b border-destructive/20 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-destructive/20">
                        <?= icon('AlertTriangle', 'h-5 w-5 text-destructive') ?>
                    </div>
                    <h2 class="card-title text-destructive">Hapus Data</h2>
                </div>
                <button @click="open = false"
                        class="flex h-8 w-8 items-center justify-center rounded-lg hover:bg-accent hover:text-accent-foreground transition-colors"
                        aria-label="Close modal">
                    <?= icon('X', 'h-5 w-5') ?>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="card-content">
                <p class="text-foreground">
                    Apakah Anda yakin ingin menghapus <strong id="deleteItemName">item ini</strong>?
                </p>
                <p class="text-xs text-destructive/70 mt-3 bg-destructive/10 p-2 rounded">
                    ⚠️ Tindakan ini tidak dapat dibatalkan!
                </p>
            </div>
            
            <!-- Modal Footer -->
            <div class="border-t border-border px-6 py-4 flex items-center justify-end gap-3">
                <button class="btn btn-ghost"
                        @click="open = false">
                    Batal
                </button>
                <button id="deleteConfirmBtn" 
                        class="btn btn-destructive"
                        onclick="ModalManager.close('delete-modal')">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>
