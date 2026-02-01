<?php
// Setup database and insert initial data
$pdo = new PDO('mysql:host=localhost', 'root', '');

// Drop and recreate database
$pdo->exec('DROP DATABASE IF EXISTS inventaris_toko');
$pdo->exec('CREATE DATABASE inventaris_toko CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
echo "Database created\n";

// Connect to the new database
$pdo = new PDO('mysql:host=localhost;dbname=inventaris_toko', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Execute SQL schema
$sql = file_get_contents('database_schema.sql');
$pdo->exec($sql);
echo "Schema executed\n";

// Insert users with generated passwords
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
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, fullname, role, is_active, email) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $user['username'],
        $hash,
        $user['fullname'],
        $user['role'],
        1,
        $user['email']
    ]);
    echo "User {$user['username']} inserted\n";
}

echo "\nDatabase setup complete!\n";
echo "Default credentials:\n";
echo "  owner / owner123\n";
echo "  admin / admin123\n";
echo "  gudang / gudang123\n";
echo "  sales / sales123\n";
