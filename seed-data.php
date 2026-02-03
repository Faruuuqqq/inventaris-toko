<?php
/**
 * Direct Database Seeding Script
 * Creates test data directly via mysqli
 */

echo "\n╔═══════════════════════════════════════════════════════╗\n";
echo "║   🌱 DATABASE SEEDING - INVENTARIS TOKO              ║\n";
echo "║         Direct MySQL Seeding                         ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

// Read .env file to get database config
$env_file = __DIR__ . '/.env';
$config = [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'db' => 'toko_distributor'
];

if (file_exists($env_file)) {
    $lines = file($env_file);
    foreach ($lines as $line) {
        if (strpos($line, 'database.default.hostname') !== false) {
            preg_match('/=(.*)/', $line, $m);
            $config['host'] = trim($m[1] ?? '');
        }
        if (strpos($line, 'database.default.username') !== false) {
            preg_match('/=(.*)/', $line, $m);
            $config['user'] = trim($m[1] ?? '');
        }
        if (strpos($line, 'database.default.password') !== false) {
            preg_match('/=(.*)/', $line, $m);
            $config['pass'] = trim($m[1] ?? '');
        }
        if (strpos($line, 'database.default.database') !== false) {
            preg_match('/=(.*)/', $line, $m);
            $config['db'] = trim($m[1] ?? '');
        }
    }
}

echo "Database Config:\n";
printf("  Host: %s\n  User: %s\n  DB: %s\n\n", $config['host'], $config['user'], $config['db']);

try {
    // Connect
    $conn = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "✅ Connected to database\n\n";
    
    // Step 1: Users
    echo "▶️  Step 1: Inserting Users...\n";
    $hash = password_hash('password', PASSWORD_DEFAULT);
    $users_sql = "
    INSERT IGNORE INTO users (username, password_hash, fullname, role, is_active, email) VALUES
    ('owner', '$hash', 'Owner', 'OWNER', 1, 'owner@toko.com'),
    ('admin', '$hash', 'Admin', 'ADMIN', 1, 'admin@toko.com'),
    ('sales', '$hash', 'Sales Staff', 'SALES', 1, 'sales@toko.com'),
    ('gudang', '$hash', 'Warehouse Staff', 'GUDANG', 1, 'gudang@toko.com');
    ";
    $conn->multi_query($users_sql);
    while ($conn->next_result()) { }
    echo "✅ Users added\n\n";
    
    // Step 2: Sample Data
    echo "▶️  Step 2: Inserting Sample Data...\n";
    
    // Categories
    $cat_sql = "
    INSERT IGNORE INTO categories (id, name) VALUES
    (1, 'Elektronik'),
    (2, 'Pakaian'),
    (3, 'Makanan'),
    (4, 'Alat Tulis'),
    (5, 'Kesehatan');
    ";
    $conn->query($cat_sql);
    echo "   - Categories\n";
    
    // Warehouses
    $wh_sql = "
    INSERT IGNORE INTO warehouses (id, code, name, address) VALUES
    (1, 'WH-01', 'Gudang Utama', 'Jl. Raya No. 123'),
    (2, 'WH-02', 'Gudang Cabang', 'Jl. Perdagangan No. 45');
    ";
    $conn->query($wh_sql);
    echo "   - Warehouses\n";
    
    // Salespersons
    $sales_sql = "
    INSERT IGNORE INTO salespersons (code, name, phone) VALUES
    ('SP-01', 'Budi Santoso', '081234567890'),
    ('SP-02', 'Siti Rahayu', '081234567891'),
    ('SP-03', 'Ahmad Wijaya', '081234567892');
    ";
    $conn->query($sales_sql);
    echo "   - Salespersons\n";
    
    echo "✅ Sample data added\n\n";
    
    // Display summary
    echo "╔═══════════════════════════════════════════════════════╗\n";
    echo "║              ✅ SEEDING COMPLETE!                    ║\n";
    echo "╚═══════════════════════════════════════════════════════╝\n\n";
    
    // Count records
    $tables = ['users', 'categories', 'warehouses', 'salespersons'];
    echo "📊 DATA SUMMARY:\n";
    foreach ($tables as $table) {
        $result = $conn->query("SELECT COUNT(*) as cnt FROM $table");
        $row = $result->fetch_assoc();
        printf("   %-30s: %3d records\n", ucfirst($table), $row['cnt']);
    }
    
    echo "\n🔑 TEST CREDENTIALS:\n";
    echo "   owner   / password\n";
    echo "   admin   / password\n";
    echo "   sales   / password\n";
    echo "   gudang  / password\n";
    
    echo "\n✨ Ready! Start server with: php spark serve\n\n";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n\n";
    exit(1);
}
