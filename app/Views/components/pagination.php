<?php
/**
 * Pagination Partial Component
 * 
 * Renders pagination controls with numbered page buttons
 * Shows: ← Previous | 1 2 3 4 5 | Next →
 * 
 * Expected variables (from PaginationHelper::getPaginationLinks()):
 * - $pagination['currentPage'] - Current page number
 * - $pagination['totalPages'] - Total number of pages
 * - $pagination['from'] - Record number from (e.g., 1)
 * - $pagination['to'] - Record number to (e.g., 20)
 * - $pagination['total'] - Total number of records
 * - $pagination['pages'] - Array of page objects with number, isActive, url
 * - $pagination['hasNextPage'] - Boolean
 * - $pagination['hasPreviousPage'] - Boolean
 * - $pagination['nextPageUrl'] - URL for next page or null
 * - $pagination['previousPageUrl'] - URL for previous page or null
 * - $pagination['showPagination'] - Boolean (hide if only 1 page)
 */
?>

<?php if (!empty($pagination) && $pagination['showPagination'] ?? false): ?>
<div class="flex items-center justify-between border-t border-border/50 bg-muted/20 px-6 py-4 rounded-b-lg">
    <!-- Left: Info Text -->
    <div class="text-sm text-muted-foreground">
        Menampilkan <span class="font-semibold text-foreground"><?= $pagination['from'] ?? 1 ?></span>
        hingga
        <span class="font-semibold text-foreground"><?= $pagination['to'] ?? 1 ?></span>
        dari
        <span class="font-semibold text-foreground"><?= $pagination['total'] ?? 0 ?></span>
        total
    </div>

    <!-- Center: Page Navigation -->
    <div class="flex items-center justify-center gap-1">
        <!-- Previous Button -->
        <?php if ($pagination['hasPreviousPage'] ?? false): ?>
            <a href="<?= $pagination['previousPageUrl'] ?>"
               class="inline-flex items-center justify-center h-9 w-9 rounded border border-border/50 bg-background hover:bg-primary/10 hover:border-primary/30 transition-colors text-sm font-medium text-foreground"
               title="Halaman Sebelumnya">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
        <?php else: ?>
            <span class="inline-flex items-center justify-center h-9 w-9 rounded border border-border/30 bg-muted/30 text-muted-foreground/50 cursor-not-allowed">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </span>
        <?php endif; ?>

        <!-- Page Numbers -->
        <div class="flex items-center gap-1 mx-2">
            <?php foreach ($pagination['pages'] ?? [] as $page): ?>
                <?php if ($page['isActive']): ?>
                    <!-- Active Page -->
                    <span class="inline-flex items-center justify-center h-9 min-w-[36px] rounded border border-primary/50 bg-primary/10 text-primary font-semibold text-sm"
                          title="Halaman <?= $page['number'] ?>">
                        <?= $page['number'] ?>
                    </span>
                <?php else: ?>
                    <!-- Inactive Page -->
                    <a href="<?= $page['url'] ?>"
                       class="inline-flex items-center justify-center h-9 min-w-[36px] rounded border border-border/50 bg-background hover:bg-primary/10 hover:border-primary/30 transition-colors text-sm font-medium text-foreground"
                       title="Halaman <?= $page['number'] ?>">
                        <?= $page['number'] ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Next Button -->
        <?php if ($pagination['hasNextPage'] ?? false): ?>
            <a href="<?= $pagination['nextPageUrl'] ?>"
               class="inline-flex items-center justify-center h-9 w-9 rounded border border-border/50 bg-background hover:bg-primary/10 hover:border-primary/30 transition-colors text-sm font-medium text-foreground"
               title="Halaman Berikutnya">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        <?php else: ?>
            <span class="inline-flex items-center justify-center h-9 w-9 rounded border border-border/30 bg-muted/30 text-muted-foreground/50 cursor-not-allowed">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </span>
        <?php endif; ?>
    </div>

    <!-- Right: Per Page Selector (optional, for future use) -->
    <div class="text-sm text-muted-foreground">
        Halaman <span class="font-semibold text-foreground"><?= $pagination['currentPage'] ?? 1 ?></span>
        dari
        <span class="font-semibold text-foreground"><?= $pagination['totalPages'] ?? 1 ?></span>
    </div>
</div>
<?php endif; ?>
