<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DataAudit extends Controller
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        echo "========================================\n";
        echo "DATA INTEGRITY AUDIT\n";
        echo "========================================\n\n";

        // Check 1: Orphaned sale items (sales that don't exist)
        echo "1. Checking for orphaned SALE ITEMS...\n";
        $orphaned_sale_items = $db->query("
            SELECT COUNT(*) as c FROM sale_items si
            LEFT JOIN sales s ON si.sale_id = s.id
            WHERE s.id IS NULL
        ")->getRow()->c;
        echo "   Found: $orphaned_sale_items orphaned sale items\n\n";

        // Check 2: Orphaned sale items (products that don't exist)
        echo "2. Checking for invalid PRODUCT references in SALE ITEMS...\n";
        $invalid_products_sale = $db->query("
            SELECT COUNT(*) as c FROM sale_items si
            LEFT JOIN products p ON si.product_id = p.id
            WHERE p.id IS NULL
        ")->getRow()->c;
        echo "   Found: $invalid_products_sale invalid product references\n\n";

        // Check 3: Orphaned purchase order items
        echo "3. Checking for orphaned PURCHASE ORDER ITEMS...\n";
        $orphaned_po_items = $db->query("
            SELECT COUNT(*) as c FROM purchase_order_items poi
            LEFT JOIN purchase_orders po ON poi.po_id = po.id_po
            WHERE po.id_po IS NULL
        ")->getRow()->c;
        echo "   Found: $orphaned_po_items orphaned PO items\n\n";

        // Check 4: Sales with invalid customers
        echo "4. Checking for SALES with invalid CUSTOMERS...\n";
        $invalid_customer_sales = $db->query("
            SELECT COUNT(*) as c FROM sales s
            LEFT JOIN customers c ON s.customer_id = c.id
            WHERE c.id IS NULL
        ")->getRow()->c;
        echo "   Found: $invalid_customer_sales sales with invalid customers\n\n";

        // Check 5: Sales with invalid users
        echo "5. Checking for SALES with invalid USERS...\n";
        $invalid_user_sales = $db->query("
            SELECT COUNT(*) as c FROM sales s
            LEFT JOIN users u ON s.user_id = u.id
            WHERE u.id IS NULL
        ")->getRow()->c;
        echo "   Found: $invalid_user_sales sales with invalid users\n\n";

        // Check 6: Stock mutations with invalid products
        echo "6. Checking for STOCK MUTATIONS with invalid PRODUCTS...\n";
        $invalid_product_mutations = $db->query("
            SELECT COUNT(*) as c FROM stock_mutations sm
            LEFT JOIN products p ON sm.product_id = p.id
            WHERE p.id IS NULL
        ")->getRow()->c;
        echo "   Found: $invalid_product_mutations stock mutations with invalid products\n\n";

        // Check 7: Products with invalid categories
        echo "7. Checking for PRODUCTS with invalid CATEGORIES...\n";
        $invalid_category_products = $db->query("
            SELECT COUNT(*) as c FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.category_id IS NOT NULL AND c.id IS NULL
        ")->getRow()->c;
        echo "   Found: $invalid_category_products products with invalid categories\n\n";

        // Summary
        echo "========================================\n";
        echo "SUMMARY\n";
        echo "========================================\n";
        $total_issues = $orphaned_sale_items + $invalid_products_sale + $orphaned_po_items + 
                        $invalid_customer_sales + $invalid_user_sales + $invalid_product_mutations + 
                        $invalid_category_products;

        if ($total_issues == 0) {
            echo "✅ DATA INTEGRITY: ALL CHECKS PASSED\n";
            echo "   No orphaned records or invalid references found\n";
        } else {
            echo "❌ DATA INTEGRITY: $total_issues ISSUES FOUND\n";
            echo "   Please investigate and clean data before proceeding\n";
        }
    }
}
