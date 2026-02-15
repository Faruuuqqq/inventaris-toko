<?php

/**
 * PHP CS Fixer Configuration
 * 
 * Enforces PSR-12 coding standards for the Inventaris Toko project.
 * Compatible with CodeIgniter 4 best practices and AGENTS.md guidelines.
 * 
 * Usage:
 *   composer run lint        # Auto-fix code formatting issues
 *   composer run lint:check  # Check without making changes
 * 
 * Installation:
 *   composer require --dev friendsofphp/php-cs-fixer:^3.59
 */

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setIndent('    ')  // 4 spaces (matches CI4 standard)
    ->setLineEnding("\n")
    ->setRules([
        // PSR Standards
        '@PSR12' => true,
        '@PHP81Migration' => true,

        // Strict Comparisons
        'strict_comparison' => true,           // Use === instead of ==
        'strict_param_evaluation' => true,     // Strict function call evaluation

        // Array & List Formatting
        'array_indentation' => true,            // Proper multi-line array indentation
        'array_syntax' => ['syntax' => 'short'],  // Use [] instead of array()
        'no_trailing_comma_in_singleline' => true,
        'whitespace_after_comma_in_array' => true,

        // Code Quality
        'no_empty_phpdoc' => true,              // Remove empty docblocks
        'no_empty_statement' => true,           // Remove empty statements
        'no_extra_blank_lines' => true,         // Remove extra blank lines
        'no_trailing_whitespace' => true,       // Remove trailing whitespace
        'no_useless_return' => true,            // Remove useless returns
        'single_quote' => true,                 // Use single quotes for strings

        // Spacing & Alignment
        'binary_operator_spaces' => [
            'default' => 'single_space',
        ],
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
                'property' => 'one',
            ],
        ],
        'function_typehint_space' => true,      // Proper spacing in type hints
        'method_argument_space' => true,        // Proper spacing in method arguments
        'no_spaces_around_offset' => true,      // No spaces around array offset
        'object_operator_without_whitespace' => false,

        // Imports
        'no_unused_imports' => true,            // Remove unused imports
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha',
        ],

        // Comments
        'align_multiline_comment' => true,      // Align multi-line comments
        'comment_to_phpdoc' => true,            // Convert comments to phpdoc
        'phpdoc_align' => ['alignment' => 'vertical'],
        'phpdoc_indent' => true,

        // Control Structures
        'control_structure_continuation_position' => [
            'position' => 'same_line',
        ],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            // Include: Only app folder
            ->in([__DIR__ . '/app'])
            
            // Exclude: Non-PHP code and generated files
            ->notPath('Database/Migrations')  // Don't format migrations
            ->notPath('Views')                // Don't format view templates
            ->notPath('Config/Routes.php')    // Don't format routes
            
            // Only PHP files
            ->name('*.php')
            ->notName('*.blade.php')
    );
