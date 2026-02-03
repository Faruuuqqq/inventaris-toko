<?php
/**
 * 🌱 Database Seeder Runner
 * Populate test data into Inventaris Toko database
 */

echo "\n";
echo "╔═══════════════════════════════════════════════════════╗\n";
echo "║   🌱 DATABASE SEEDING - INVENTARIS TOKO              ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

try {
    // Define paths BEFORE loading anything
    define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
    define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);
    define('APPPATH', __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);
    define('BASEPATH', __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'codeigniter4' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR);
    define('SYSTEMPATH', BASEPATH . 'system' . DIRECTORY_SEPARATOR);
    define('WRITEPATH', __DIR__ . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR);
    define('ENVIRONMENT', 'development');
    
    // Load composer autoloader
    require_once __DIR__ . '/vendor/autoload.php';
    
    echo "✅ Dependencies loaded\n";
    echo "⏳ Connecting to database...\n\n";
    
    // Load database config
    require_once APPPATH . 'Config/Database.php';
    $dbConfig = new \Config\Database();
    $db = $dbConfig->connect();
    
    echo "✅ Connected to: " . $db->getDatabase() . "\n\n";
    
    // Load and run seeders
    $seeders = [
        'InitialDataSeeder' => 'Users, Categories, Warehouses, Salespersons',
        'Phase4TestDataSeeder' => 'Products, Customers, Suppliers',
        'SalesDataSeeder' => 'Transactions & Sales Data',
    ];
    
    $step = 1;
    foreach ($seeders as $seederClass => $description) {
        echo "▶️  Step $step: Running $seederClass\n";
        echo "     ($description)\n";
        
        $path = APPPATH . 'Database/Seeds/' . $seederClass . '.php';
        
        if (!file_exists($path)) {
            throw new \Exception("Seeder not found: $path");
        }
        
        require_once $path;
        
        $fullClass = 'App\\Database\\Seeds\\' . $seederClass;
        $seeder = new $fullClass();
        $seeder->db = $db;
        $seeder->run();
        
        echo "✅ Step $step complete!\n\n";
        $step++;
    }
    
    // Display summary
    echo "╔═══════════════════════════════════════════════════════╗\n";
    echo "║              ✅ SEEDING COMPLETE!                    ║\n";
    echo "╚═══════════════════════════════════════════════════════╝\n\n";
    
    echo "📊 DATA SUMMARY:\n";
    
    $tables = [
        'users' => 'Users',
        'categories' => 'Categories',
        'warehouses' => 'Warehouses',
        'products' => 'Products',
        'customers' => 'Customers',
        'suppliers' => 'Suppliers',
        'salespersons' => 'Salespersons',
        'sales_transactions' => 'Sales (Tunai)',
        'credit_transactions' => 'Sales (Kredit)',
    ];
    
    echo "\n   Table Name                         Count\n";
    echo "   " . str_repeat("-", 50) . "\n";
    
    foreach ($tables as $table => $label) {
        try {
            $count = $db->table($table)->countAll();
            printf("   %-35s %5d\n", $label, $count);
        } catch (\Exception $e) {
            printf("   %-35s (error)\n", $label);
        }
    }
    
    echo "\n🔑 TEST CREDENTIALS (for login):\n";
    echo "   Username: owner    | Password: password (OWNER - Full Access)\n";
    echo "   Username: admin    | Password: password (ADMIN - Transactions)\n";
    echo "   Username: sales    | Password: password (SALES - Sales Only)\n";
    echo "   Username: gudang   | Password: password (GUDANG - Warehouse)\n";
    
    echo "\n🚀 NEXT STEPS:\n";
    echo "   1. Start server:   php spark serve\n";
    echo "   2. Open browser:   http://localhost:8080\n";
    echo "   3. Login as:       owner / password\n";
    echo "   4. Explore:        Dashboard, Master Data, Transactions\n";
    echo "   5. Test API:       Import Postman collection from docs/api/\n";
    
    echo "\n✨ You're all set! The application is ready to use.\n\n";
    
} catch (\Throwable $e) {
    echo "\n❌ ERROR:\n";
    echo "   " . $e->getMessage() . "\n";
    echo "   (File: " . $e->getFile() . " Line: " . $e->getLine() . ")\n\n";
    
    echo "💡 TROUBLESHOOTING:\n";
    echo "   1. Is database created? (toko_distributor)\n";
    echo "   2. Are .env credentials correct?\n";
    echo "   3. Try running migrations first: php spark migrate\n";
    echo "   4. Make sure MySQL/MariaDB service is running\n\n";
    
    exit(1);
}
?>
