<?php
/**
 * Test database connection and basic functionality
 */

echo "=== Application Test ===\n";

// Test database connection
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=toko_distributor",
        'root',
        ''
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Database connection successful\n";
    
    // Check users
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    echo "✓ Users table has $userCount records\n";
    
    // Show first user
    $user = $pdo->query("SELECT username, fullname, role FROM users LIMIT 1")->fetch();
    if ($user) {
        echo "✓ First user: {$user['username']} ({$user['role']})\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

// Test CI4 environment
echo "\n=== CodeIgniter Environment ===\n";

// Check if .env is readable
if (file_exists('.env')) {
    echo "✓ .env file exists\n";
    $envContent = file_get_contents('.env');
    preg_match('/app\.baseURL\s*=\s*[\'"]?([^\'"\n]+)/', $envContent, $baseUrl);
    if (!empty($baseUrl[1])) {
        echo "✓ Base URL: " . trim($baseUrl[1]) . "\n";
    }
    preg_match('/database\.default\.database\s*=\s*[\'"]?([^\'"\n]+)/', $envContent, $dbName);
    if (!empty($dbName[1])) {
        echo "✓ Database name: " . trim($dbName[1]) . "\n";
    }
} else {
    echo "❌ .env file not found\n";
}

// Check writable directory
if (is_dir('writable') && is_writable('writable')) {
    echo "✓ Writable directory is accessible\n";
} else {
    echo "❌ Writable directory issue\n";
}

echo "\n=== URLs to Test ===\n";
echo "1. Main: http://localhost/inventaris-toko/public/\n";
echo "2. Login: http://localhost/inventaris-toko/public/login\n";
echo "3. Dashboard: http://localhost/inventaris-toko/public/dashboard\n";
echo "\nDefault credentials:\n";
echo "- Username: owner, Password: password\n";
echo "- Username: admin, Password: password\n";