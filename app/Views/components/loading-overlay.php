<!-- Loading Overlay Component -->
<div
    id="global-loading-overlay"
    class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity duration-300"
    style="display: none;"
>
    <div class="flex flex-col items-center justify-center p-6 bg-surface rounded-xl shadow-xl border border-border/50 max-w-sm w-full mx-4">
        <!-- Spinner -->
        <div class="relative w-16 h-16 mb-4">
            <div class="absolute inset-0 border-4 border-primary/20 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-primary rounded-full border-t-transparent animate-spin"></div>
        </div>

        <!-- Text -->
        <h3 class="text-lg font-bold text-foreground mb-1" id="loading-title">Memproses...</h3>
        <p class="text-sm text-muted-foreground text-center" id="loading-message">Mohon tunggu sebentar, sistem sedang memproses permintaan Anda.</p>
    </div>
</div>

<script>
    const LoadingOverlay = {
        element: null,
        titleEl: null,
        messageEl: null,

        init() {
            this.element = document.getElementById('global-loading-overlay');
            this.titleEl = document.getElementById('loading-title');
            this.messageEl = document.getElementById('loading-message');
        },

        show(title = 'Memproses...', message = 'Mohon tunggu sebentar...') {
            if (!this.element) this.init();
            if (this.titleEl) this.titleEl.textContent = title;
            if (this.messageEl) this.messageEl.textContent = message;

            this.element.style.display = 'flex';
            // Slight delay to allow display:flex to apply before opacity transition if we added one
            requestAnimationFrame(() => {
                this.element.classList.remove('opacity-0');
            });
        },

        hide() {
            if (!this.element) this.init();
            this.element.classList.add('opacity-0');
            setTimeout(() => {
                this.element.style.display = 'none';
            }, 300);
        }
    };

    // Attach to window for global access
    window.Loading = LoadingOverlay;

    // Optional: Auto-hide on page load if it stuck
    window.addEventListener('load', () => {
        if (window.Loading) window.Loading.hide();
    });
</script>