<?php

namespace App\Helpers;

/**
 * PaginationHelper - Centralized pagination logic for services
 * 
 * Provides reusable methods for handling pagination across the application.
 * Used by all DataService classes to paginate list data consistently.
 */
class PaginationHelper
{
    /**
     * Default items per page
     */
    public const DEFAULT_PER_PAGE = 20;

    /**
     * Maximum items per page to prevent abuse
     */
    public const MAX_PER_PAGE = 100;

    /**
     * Get safe pagination parameters from request or use defaults
     * 
     * @param int|null $requestPage Page number from request (1-based)
     * @param int|null $requestPerPage Items per page from request
     * @return array ['page' => int, 'perPage' => int]
     */
    public static function getSafeParams(?int $requestPage = null, ?int $requestPerPage = null): array
    {
        // Default to page 1
        $page = max(1, (int)($requestPage ?? 1));

        // Default to 20 per page, cap at MAX_PER_PAGE
        $perPage = max(1, min((int)($requestPerPage ?? self::DEFAULT_PER_PAGE), self::MAX_PER_PAGE));

        return [
            'page' => $page,
            'perPage' => $perPage,
        ];
    }

    /**
     * Build pagination metadata from CodeIgniter pager
     * 
     * Used after calling Model->paginate() to extract pagination info
     * 
     * @param mixed $pager CodeIgniter Pager instance
     * @param int $perPage Items per page
     * @return array Pagination metadata
     */
    public static function getPaginationMeta($pager, int $perPage): array
    {
        return [
            'currentPage' => $pager->getCurrentPage(),
            'totalPages' => $pager->getPageCount(),
            'perPage' => $perPage,
            'total' => $pager->getTotal(),
            'from' => (($pager->getCurrentPage() - 1) * $perPage) + 1,
            'to' => min($pager->getCurrentPage() * $perPage, $pager->getTotal()),
        ];
    }

    /**
     * Build pagination links data for view rendering
     * 
     * Returns array with page links, previous/next URLs, and metadata
     * Optimized for numbered pagination: ← Previous | 1 2 3 4 5 | Next →
     * 
     * @param mixed $pager CodeIgniter Pager instance
     * @param int $perPage Items per page
     * @param int $windowSize Number of page links to show around current page (default 5)
     * @return array Pagination data for views
     */
    public static function getPaginationLinks($pager, int $perPage, int $windowSize = 5): array
    {
        $currentPage = $pager->getCurrentPage();
        $totalPages = $pager->getPageCount();

        // Calculate window of pages to show
        $start = max(1, $currentPage - floor($windowSize / 2));
        $end = min($totalPages, $start + $windowSize - 1);

        // Adjust start if we're near the end
        if ($end - $start + 1 < $windowSize) {
            $start = max(1, $end - $windowSize + 1);
        }

        // Build page links
        $pages = [];
        for ($i = $start; $i <= $end; $i++) {
            $pages[] = [
                'number' => $i,
                'isActive' => $i === $currentPage,
                'url' => $pager->getPageURI($i),
            ];
        }

        return [
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
            'total' => $pager->getTotal(),
            'hasNextPage' => $currentPage < $totalPages,
            'hasPreviousPage' => $currentPage > 1,
            'nextPageUrl' => $currentPage < $totalPages ? $pager->getPageURI($currentPage + 1) : null,
            'previousPageUrl' => $currentPage > 1 ? $pager->getPageURI($currentPage - 1) : null,
            'pages' => $pages,
            'from' => (($currentPage - 1) * $perPage) + 1,
            'to' => min($currentPage * $perPage, $pager->getTotal()),
            'showPagination' => $totalPages > 1, // Only show pagination if more than 1 page
        ];
    }
}
