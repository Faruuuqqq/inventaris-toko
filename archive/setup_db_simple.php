<?php
/**
 * Simple Database Setup for Inventaris Toko
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
    // Connect to MySQL
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']}",
        $dbConfig['username'],
        $dbConfig['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connected to MySQL\n";
    
    // Drop and recreate database
    $pdo->exec("DROP DATABASE IF EXISTS `{$dbConfig['database']}`");
    $pdo->exec("CREATE DATABASE `{$dbConfig['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database '{$dbConfig['database']}' created\n";
    
    // Select the database
    $pdo->exec("USE `{$dbConfig['database']}`");
    
    // Read the final database schema
    $sqlFile = __DIR__ . '/database_final.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("Database file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Remove DROP statements since we're using fresh database
    $sql = preg_replace('/DROP TABLE IF EXISTS.*?;/s', '', $sql);
    
    // Execute the entire SQL at once
    try {
        $pdo->exec($sql);
        echo "✓ Database schema imported\n";
    } catch (PDOException $e) {
        echo "⚠ Import warning: " . $e->getMessage() . "\n";
        
        // Try statement by statement
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^--/', $statement)) {
                try {
                    $pdo->exec($statement);
                    if (preg_match('/CREATE TABLE `([^`]+)`/', $statement, $match)) {
                        echo "✓ Created table: {$match[1]}\n";
                    }
                } catch (PDOException $e) {
                    echo "⚠ Warning on statement: " . substr($statement, 0, 50) . "...\n";
                }
            }
        }
    }
    
    echo "✓ Database schema imported\n";
    
    // Check if tables exist
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "✓ Created " . count($tables) . " tables\n";
    
    // Check users
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    echo "✓ Created $userCount default users\n";
    
    echo "\n=== Database Setup Complete! ===\n";
    echo "Database: {$dbConfig['database']}\n";
    echo "Tables: " . implode(', ', array_slice($tables, 0, 5)) . "...\n";
    echo "Default Login: owner/password or admin/password\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Please check:\n";
    echo "1. MySQL/MariaDB service is running\n";
    echo "2. User 'root' has privileges\n";
    echo "3. Database files exist\n";
}