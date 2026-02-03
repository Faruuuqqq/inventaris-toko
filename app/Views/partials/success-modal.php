<?php
/**
 * Success Notification Modal
 * Auto-closes after 2 seconds
 * 
 * Usage:
 * ModalManager.success('Data berhasil disimpan', () => {
 *     // This callback will be called after 2 seconds when modal closes
 *     window.location.reload();
 * });
 */
?>

<div x-data="{ open: false }" class="success-modal">
    <!-- Modal Backdrop -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-foreground/80 backdrop-blur-sm"
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
         
        <div class="card max-w-sm w-full"
             @click.stop>
             
            <!-- Modal Header -->
            <div class="card-header bg-success/10 border-b border-success/20">
                <h2 class="card-title text-success">Sukses!</h2>
            </div>
            
            <!-- Modal Content -->
            <div class="card-content text-center py-8">
                <div class="text-5xl mb-4 animate-bounce">✅</div>
                <p class="text-foreground font-semibold text-lg mb-2" id="successMessage">
                    Data berhasil disimpan
                </p>
                <p class="text-xs text-muted-foreground">
                    Modal akan tertutup otomatis dalam 2 detik
                </p>
            </div>
        </div>
    </div>
</div>
