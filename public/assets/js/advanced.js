// Advanced Features JavaScript

/**
 * Dark Mode Manager
 */
const DarkMode = {
    init() {
        // Check saved preference or system preference
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
            this.enable();
        }

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                e.matches ? this.enable() : this.disable();
            }
        });
    },

    enable() {
        document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('theme', 'dark');
        this.updateToggle();
    },

    disable() {
        document.documentElement.removeAttribute('data-theme');
        localStorage.setItem('theme', 'light');
        this.updateToggle();
    },

    toggle() {
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        isDark ? this.disable() : this.enable();
    },

    updateToggle() {
        const toggles = document.querySelectorAll('.theme-toggle');
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

        toggles.forEach(toggle => {
            const slider = toggle.querySelector('.theme-toggle-slider');
            if (slider) {
                slider.style.transform = isDark ? 'translateX(1.5rem)' : 'translateX(0)';
            }
        });
    }
};

/**
 * Skeleton Loader Manager
 */
const SkeletonLoader = {
    /**
     * Create skeleton for table
     * @param {number} rows
     * @param {number} columns
     * @returns {string}
     */
    table(rows = 5, columns = 4) {
        let html = '<div class="skeleton-table">';

        // Header
        html += '<div class="skeleton-table-header">';
        for (let i = 0; i < columns; i++) {
            html += '<div class="skeleton"></div>';
        }
        html += '</div>';

        // Body
        html += '<div class="skeleton-table-body">';
        for (let i = 0; i < rows; i++) {
            html += '<div>';
            for (let j = 0; j < columns; j++) {
                html += '<div class="skeleton"></div>';
            }
            html += '</div>';
        }
        html += '</div>';

        html += '</div>';
        return html;
    },

    /**
     * Create skeleton for card
     * @returns {string}
     */
    card() {
        return `
            <div class="skeleton-card-component">
                <div class="skeleton-card-header">
                    <div class="skeleton skeleton-avatar"></div>
                    <div class="flex-1">
                        <div class="skeleton skeleton-title"></div>
                        <div class="skeleton skeleton-text" style="width: 70%;"></div>
                    </div>
                </div>
                <div class="skeleton-card-body">
                    <div class="skeleton skeleton-text"></div>
                    <div class="skeleton skeleton-text"></div>
                    <div class="skeleton skeleton-text" style="width: 80%;"></div>
                </div>
            </div>
        `;
    },

    /**
     * Create skeleton for list
     * @param {number} items
     * @returns {string}
     */
    list(items = 3) {
        let html = '<div class="space-y-4">';
        for (let i = 0; i < items; i++) {
            html += `
                <div class="flex items-center gap-3">
                    <div class="skeleton skeleton-avatar"></div>
                    <div class="flex-1">
                        <div class="skeleton skeleton-text" style="width: 60%;"></div>
                        <div class="skeleton skeleton-text-sm" style="width: 40%;"></div>
                    </div>
                </div>
            `;
        }
        html += '</div>';
        return html;
    },

    /**
     * Show skeleton in element
     * @param {HTMLElement} element
     * @param {string} type
     * @param {Object} options
     */
    show(element, type = 'card', options = {}) {
        const defaults = { rows: 5, columns: 4, items: 3 };
        const config = { ...defaults, ...options };

        element.dataset.originalContent = element.innerHTML;

        switch (type) {
            case 'table':
                element.innerHTML = this.table(config.rows, config.columns);
                break;
            case 'list':
                element.innerHTML = this.list(config.items);
                break;
            default:
                element.innerHTML = this.card();
        }
    },

    /**
     * Hide skeleton and restore content
     * @param {HTMLElement} element
     */
    hide(element) {
        if (element.dataset.originalContent) {
            element.innerHTML = element.dataset.originalContent;
            delete element.dataset.originalContent;
        }
    }
};

/**
 * Table Enhancements
 */
const TableEnhancer = {
    /**
     * Make table sortable
     * @param {HTMLTableElement} table
     */
    makeSortable(table) {
        const headers = table.querySelectorAll('th[data-sortable]');

        headers.forEach((header, index) => {
            header.style.cursor = 'pointer';
            header.innerHTML += ' <svg class="inline h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>';

            header.addEventListener('click', () => {
                this.sortTable(table, index, header);
            });
        });
    },

    /**
     * Sort table by column
     * @param {HTMLTableElement} table
     * @param {number} column
     * @param {HTMLElement} header
     */
    sortTable(table, column, header) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const isAscending = header.dataset.sortDirection !== 'asc';

        // Remove sort direction from all headers
        table.querySelectorAll('th').forEach(th => {
            delete th.dataset.sortDirection;
            const icon = th.querySelector('svg');
            if (icon) icon.style.transform = '';
        });

        // Set new sort direction
        header.dataset.sortDirection = isAscending ? 'asc' : 'desc';
        const icon = header.querySelector('svg');
        if (icon) icon.style.transform = isAscending ? '' : 'rotate(180deg)';

        // Sort rows
        rows.sort((a, b) => {
            const aValue = a.cells[column].textContent.trim();
            const bValue = b.cells[column].textContent.trim();

            // Try to parse as number
            const aNum = parseFloat(aValue.replace(/[^0-9.-]/g, ''));
            const bNum = parseFloat(bValue.replace(/[^0-9.-]/g, ''));

            if (!isNaN(aNum) && !isNaN(bNum)) {
                return isAscending ? aNum - bNum : bNum - aNum;
            }

            // String comparison
            return isAscending
                ? aValue.localeCompare(bValue)
                : bValue.localeCompare(aValue);
        });

        // Reorder rows
        rows.forEach(row => tbody.appendChild(row));
    },

    /**
     * Add search functionality
     * @param {HTMLTableElement} table
     * @param {HTMLInputElement} searchInput
     */
    addSearch(table, searchInput) {
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    },

    /**
     * Add pagination
     * @param {HTMLTableElement} table
     * @param {number} rowsPerPage
     */
    addPagination(table, rowsPerPage = 10) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        let currentPage = 1;
        const totalPages = Math.ceil(rows.length / rowsPerPage);

        const showPage = (page) => {
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            rows.forEach((row, index) => {
                row.style.display = (index >= start && index < end) ? '' : 'none';
            });

            currentPage = page;
            updatePagination();
        };

        const updatePagination = () => {
            const paginationContainer = table.parentElement.querySelector('.pagination-container');
            if (!paginationContainer) return;

            let html = '<ul class="pagination">';

            // Previous button
            html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
            </li>`;

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>`;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }

            // Next button
            html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
            </li>`;

            html += '</ul>';
            paginationContainer.innerHTML = html;

            // Add click handlers
            paginationContainer.querySelectorAll('a.page-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const page = parseInt(link.dataset.page);
                    if (page >= 1 && page <= totalPages) {
                        showPage(page);
                    }
                });
            });
        };

        // Create pagination container
        const paginationContainer = document.createElement('div');
        paginationContainer.className = 'pagination-container mt-4';
        table.parentElement.appendChild(paginationContainer);

        // Show first page
        showPage(1);
    }
};

/**
 * Scroll Animations
 */
const ScrollAnimations = {
    init() {
        const elements = document.querySelectorAll('.scroll-fade-in, .scroll-slide-left, .scroll-slide-right, .scroll-scale');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, {
            threshold: 0.1
        });

        elements.forEach(el => observer.observe(el));
    }
};

/**
 * Notification Badge
 */
const NotificationBadge = {
    /**
     * Update badge count
     * @param {HTMLElement} badge
     * @param {number} count
     */
    update(badge, count) {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = '';
            badge.classList.add('animate-ping');
            setTimeout(() => badge.classList.remove('animate-ping'), 1000);
        } else {
            badge.style.display = 'none';
        }
    }
};

/**
 * Initialize all advanced features
 */
document.addEventListener('DOMContentLoaded', function () {
    // Initialize dark mode
    DarkMode.init();

    // Initialize scroll animations
    ScrollAnimations.init();

    // Theme toggle buttons
    document.querySelectorAll('.theme-toggle').forEach(toggle => {
        toggle.addEventListener('click', () => DarkMode.toggle());
    });

    // Auto-initialize sortable tables
    document.querySelectorAll('table[data-sortable]').forEach(table => {
        TableEnhancer.makeSortable(table);
    });

    // Auto-initialize searchable tables
    document.querySelectorAll('[data-table-search]').forEach(input => {
        const tableId = input.dataset.tableSearch;
        const table = document.getElementById(tableId);
        if (table) {
            TableEnhancer.addSearch(table, input);
        }
    });

    // Auto-initialize paginated tables
    document.querySelectorAll('table[data-paginate]').forEach(table => {
        const rowsPerPage = parseInt(table.dataset.paginate) || 10;
        TableEnhancer.addPagination(table, rowsPerPage);
    });
});

// Export for use in other scripts
window.Advanced = {
    DarkMode,
    SkeletonLoader,
    TableEnhancer,
    ScrollAnimations,
    NotificationBadge
};
