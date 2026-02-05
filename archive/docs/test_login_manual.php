<?php
require __DIR__ . '/vendor/autoload.php';

$db = \Config\Database::connect();

// Check password hash
$user = $db->table('users')->where('username', 'owner')->get()->getRowArray();
echo "User found: " . ($user ? 'YES' : 'NO') . "\n";
echo "Password hash: " . substr($user['password_hash'], 0, 20) . "...\n";

// Test password verification
$password = 'password';
$verify = password_verify($password, $user['password_hash']);
echo "Password 'password' verification: " . ($verify ? 'SUCCESS' : 'FAILED') . "\n";

// Test new hash
$newHash = password_hash('password', PASSWORD_BCRYPT);
echo "New hash for 'password': " . substr($newHash, 0, 20) . "...\n";
echo "New hash verification: " . (password_verify('password', $newHash) ? 'SUCCESS' : 'FAILED') . "\n";

// Update password if needed
if (!$verify) {
    echo "Updating password hash...\n";
    $db->table('users')->where('username', 'owner')->update([
        'password_hash' => $newHash
    ]);
    echo "Password updated.\n";
}
