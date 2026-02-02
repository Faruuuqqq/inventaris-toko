<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-foreground flex items-center gap-3">
            <?= icon('FileText', 'h-8 w-8 text-primary') ?>
            Detail Kontra Bon
        </h1>
        <p class="text-sm text-muted-foreground mt-1">Informasi lengkap kontra bon</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="<?= base_url('finance/kontra-bon') ?>" class="inline-flex items-center justify-center gap-2 h-10 px-4 border border-border/50 text-foreground font-medium rounded-lg hover:bg-muted transition whitespace-nowrap text-sm">
            <?= icon('ArrowLeft', 'h-4 w-4') ?>
            Kembali
        </a>
        <a href="<?= base_url('finance/kontra-bon/edit/' . $kontraBon['id']) ?>" class="inline-flex items-center justify-center gap-2 h-10 px-4 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition whitespace-nowrap text-sm">
            <?= icon('Edit', 'h-4 w-4') ?>
            Edit
        </a>
        <a href="<?= base_url('finance/kontra-bon/pdf/' . $kontraBon['id']) ?>" target="_blank" class="inline-flex items-center justify-center gap-2 h-10 px-4 bg-destructive text-white font-medium rounded-lg hover:bg-destructive/90 transition whitespace-nowrap text-sm">
            <?= icon('FileDown', 'h-4 w-4') ?>
            Export PDF
        </a>
    </div>
</div>

<!-- Alert Messages -->
<?php if (session()->getFlashdata('success')): ?>
<div class="mb-4 rounded-lg border border-success/50 bg-success/10 p-4 flex items-start gap-3">
    <?= icon('CheckCircle', 'h-5 w-5 text-success flex-shrink-0 mt-0.5') ?>
    <div class="flex-1">
        <p class="text-sm font-medium text-success"><?= session()->getFlashdata('success') ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Main Content Grid -->
<div class="grid gap-6 lg:grid-cols-3">
    <!-- Kontra Bon Details (2/3 width) -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Document Information -->
        <div class="rounded-lg border bg-card shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('FileText', 'h-5 w-5 text-primary') ?>
                    Informasi Dokumen
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <!-- Document Number -->
                <div class="flex justify-between items-start py-3 border-b border-border/30">
                    <div class="space-y-1">
                        <p class="text-sm text-muted-foreground">No. Dokumen</p>
                        <p class="text-base font-mono font-semibold text-foreground"><?= esc($kontraBon['document_number']) ?></p>
                    </div>
                    <!-- Status Badge -->
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium border
                        <?php if ($kontraBon['status'] == 'PAID'): ?>
                            bg-success/10 text-success border-success/30
                        <?php elseif ($kontraBon['status'] == 'PENDING'): ?>
                            bg-warning/10 text-warning border-warning/30
                        <?php else: ?>
                            bg-destructive/10 text-destructive border-destructive/30
                        <?php endif; ?>">
                        <?= esc($kontraBon['status']) ?>
                    </span>
                </div>

                <!-- Total Amount -->
                <div class="py-3 border-b border-border/30">
                    <p class="text-sm text-muted-foreground mb-1">Total Jumlah</p>
                    <p class="text-2xl font-bold text-foreground"><?= format_currency($kontraBon['total_amount']) ?></p>
                </div>

                <!-- Due Date -->
                <div class="py-3 border-b border-border/30">
                    <p class="text-sm text-muted-foreground mb-1">Tanggal Jatuh Tempo</p>
                    <p class="text-base font-medium text-foreground flex items-center gap-2">
                        <?= icon('Calendar', 'h-4 w-4 text-muted-foreground') ?>
                        <?= $kontraBon['due_date'] ? date('d M Y', strtotime($kontraBon['due_date'])) : '-' ?>
                    </p>
                </div>

                <!-- Created Date -->
                <div class="py-3 border-b border-border/30">
                    <p class="text-sm text-muted-foreground mb-1">Tanggal Dibuat</p>
                    <p class="text-base font-medium text-foreground flex items-center gap-2">
                        <?= icon('Clock', 'h-4 w-4 text-muted-foreground') ?>
                        <?= date('d M Y H:i', strtotime($kontraBon['created_at'])) ?>
                    </p>
                </div>

                <!-- Updated Date -->
                <?php if ($kontraBon['updated_at']): ?>
                <div class="py-3">
                    <p class="text-sm text-muted-foreground mb-1">Terakhir Diperbarui</p>
                    <p class="text-base font-medium text-foreground flex items-center gap-2">
                        <?= icon('Clock', 'h-4 w-4 text-muted-foreground') ?>
                        <?= date('d M Y H:i', strtotime($kontraBon['updated_at'])) ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Notes Section -->
        <?php if (!empty($kontraBon['notes'])): ?>
        <div class="rounded-lg border bg-card shadow-sm overflow-hidden">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('StickyNote', 'h-5 w-5 text-primary') ?>
                    Catatan
                </h2>
            </div>
            <div class="p-6">
                <p class="text-sm text-foreground whitespace-pre-wrap"><?= esc($kontraBon['notes']) ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Customer Information (1/3 width) -->
    <div class="lg:col-span-1">
        <div class="rounded-lg border bg-card shadow-sm overflow-hidden sticky top-6">
            <div class="p-6 border-b border-border/50 bg-muted/30">
                <h2 class="text-lg font-semibold text-foreground flex items-center gap-2">
                    <?= icon('User', 'h-5 w-5 text-primary') ?>
                    Informasi Customer
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <!-- Customer Name -->
                <div>
                    <p class="text-sm text-muted-foreground mb-1">Nama Customer</p>
                    <p class="text-base font-semibold text-foreground"><?= esc($kontraBon['customer_name']) ?></p>
                </div>

                <!-- Customer Email -->
                <?php if (!empty($kontraBon['customer_email'])): ?>
                <div class="pt-4 border-t border-border/30">
                    <p class="text-sm text-muted-foreground mb-1">Email</p>
                    <p class="text-sm font-medium text-foreground flex items-center gap-2">
                        <?= icon('Mail', 'h-4 w-4 text-muted-foreground') ?>
                        <a href="mailto:<?= esc($kontraBon['customer_email']) ?>" class="text-primary hover:underline">
                            <?= esc($kontraBon['customer_email']) ?>
                        </a>
                    </p>
                </div>
                <?php endif; ?>

                <!-- Customer Phone -->
                <?php if (!empty($kontraBon['customer_phone'])): ?>
                <div class="pt-4 border-t border-border/30">
                    <p class="text-sm text-muted-foreground mb-1">Telepon</p>
                    <p class="text-sm font-medium text-foreground flex items-center gap-2">
                        <?= icon('Phone', 'h-4 w-4 text-muted-foreground') ?>
                        <a href="tel:<?= esc($kontraBon['customer_phone']) ?>" class="text-primary hover:underline">
                            <?= esc($kontraBon['customer_phone']) ?>
                        </a>
                    </p>
                </div>
                <?php endif; ?>

                <!-- Customer Address -->
                <?php if (!empty($kontraBon['customer_address'])): ?>
                <div class="pt-4 border-t border-border/30">
                    <p class="text-sm text-muted-foreground mb-1">Alamat</p>
                    <p class="text-sm text-foreground flex items-start gap-2">
                        <?= icon('MapPin', 'h-4 w-4 text-muted-foreground mt-0.5 flex-shrink-0') ?>
                        <span class="whitespace-pre-wrap"><?= esc($kontraBon['customer_address']) ?></span>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
