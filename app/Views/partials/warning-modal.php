<?php
/**
 * Warning Confirmation Modal
 * For dangerous actions with consequences
 * 
 * Usage:
 * ModalManager.warning(
 *     'Nonaktifkan User',
 *     'Menghapus user ini akan membatalkan semua transaksi yang sedang berjalan. Pastikan Anda sudah yakin.',
 *     () => {
 *         // Submit the action
 *         fetch('/api/users/123/disable', { method: 'POST' })
 *             .then(() => ModalManager.success('User berhasil dinonaktifkan'))
 *             .catch(e => ModalManager.error(e.message));
 *     },
 *     'Ya, Nonaktifkan'
 * );
 */
?>

<div x-data="{ open: false }" class="warning-modal">
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
         
        <div class="card max-w-md w-full"
             @click.stop>
             
            <!-- Modal Header -->
            <div class="card-header bg-warning/10 border-b border-warning/20 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-warning/20">
                        <?= icon('AlertCircle', 'h-5 w-5 text-warning') ?>
                    </div>
                    <h2 class="card-title text-warning" id="warningTitle">Peringatan</h2>
                </div>
                <button @click="open = false"
                        class="flex h-8 w-8 items-center justify-center rounded-lg hover:bg-accent hover:text-accent-foreground transition-colors"
                        aria-label="Close modal">
                    <?= icon('X', 'h-5 w-5') ?>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="card-content">
                <p class="text-foreground font-medium mb-3" id="warningMessage">
                    Tindakan ini memiliki konsekuensi penting yang perlu Anda pertimbangkan.
                </p>
                <p class="text-sm bg-warning/10 text-warning/80 p-3 rounded border border-warning/30">
                    ⚠️ Pastikan Anda sudah memahami konsekuensi sebelum melanjutkan.
                </p>
            </div>
            
            <!-- Modal Footer -->
            <div class="border-t border-border px-6 py-4 flex items-center justify-end gap-3">
                <button class="btn btn-ghost"
                        @click="open = false">
                    Batal
                </button>
                <button id="warningProceedBtn"
                        class="btn bg-warning text-white hover:bg-warning/90"
                        onclick="ModalManager.close('warning-modal')">
                    Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>
