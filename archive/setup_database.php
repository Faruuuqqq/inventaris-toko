<?php
/**
 * Database Setup Script
 * Membuat database dan import schema untuk aplikasi Inventaris Toko
 */

echo "=== Database Setup Script ===\n";

// Database configuration
$dbConfig = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'toko_distributor'
];

try {
    // Connect to MySQL without database
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']}",
        $dbConfig['username'],
        $dbConfig['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connected to MySQL\n";
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database '{$dbConfig['database']}' created/verified\n";
    
    // Connect to the specific database
    $pdo->exec("USE `{$dbConfig['database']}`");
echo "Database created\n";

// Connect to new database
$mysqli->select_db('inventaris_toko');

// Read and split SQL
$sql = file_get_contents('database_schema.sql');
$statements = explode(';', $sql);

$executed = 0;
foreach ($statements as $stmt) {
    $stmt = trim($stmt);
    if (!empty($stmt) && !preg_match('/^--/', $stmt)) {
        if ($mysqli->query($stmt)) {
            $executed++;
        } else {
            echo "Error: " . $mysqli->error . "\n";
        }
    }
}

echo "Executed $executed statements\n";

// Insert users manually
echo "\nInserting users...\n";
$users = [
    [
        'username' => 'owner',
        'password' => 'owner123',
        'fullname' => 'Owner',
        'role' => 'OWNER',
        'email' => 'owner@toko.com'
    ],
    [
        'username' => 'admin',
        'password' => 'admin123',
        'fullname' => 'Administrator',
        'role' => 'ADMIN',
        'email' => 'admin@toko.com'
    ],
    [
        'username' => 'gudang',
        'password' => 'gudang123',
        'fullname' => 'Staff Gudang',
        'role' => 'GUDANG',
        'email' => 'gudang@toko.com'
    ],
    [
        'username' => 'sales',
        'password' => 'sales123',
        'fullname' => 'Salesman',
        'role' => 'SALES',
        'email' => 'sales@toko.com'
    ]
];

foreach ($users as $user) {
    $hash = password_hash($user['password'], PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("INSERT INTO users (username, password_hash, fullname, role, is_active, email) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssis', 
        $user['username'], 
        $hash, 
        $user['fullname'], 
        $user['role'], 
        1, 
        $user['email']
    );
    $stmt->execute();
    echo "User {$user['username']} inserted\n";
}

// Check tables
$result = $mysqli->query('SHOW TABLES');
echo "\nTables: " . $result->num_rows . "\n";

// Check users
$result = $mysqli->query('SELECT COUNT(*) FROM users');
echo "Users count: " . $result->fetch_row()[0] . "\n";

// Check other tables
$check_tables = ['warehouses', 'categories', 'products', 'product_stocks', 'customers', 'suppliers', 'salespersons'];
foreach ($check_tables as $table) {
    $result = $mysqli->query("SELECT COUNT(*) FROM $table");
    echo "$table: " . $result->fetch_row()[0] . "\n";
}

echo "\nDatabase setup complete!\n";
echo "\nDefault credentials:\n";
echo "  owner / owner123\n";
echo "  admin / admin123\n";
echo "  gudang / gudang123\n";
echo "  sales / sales123\n";

$mysqli->close();
