<?php
/**
 * Password Hash Generator
 * Run this script to generate a password hash for admin users
 * Usage: php generate_password.php [password]
 */

$password = $argv[1] ?? 'admin123';

echo "Password: $password\n";
echo "Hash: " . password_hash($password, PASSWORD_DEFAULT) . "\n";
echo "\nTo update in database:\n";
echo "UPDATE admins SET password_hash = '<hash>' WHERE email = 'admin@walkaddis.com';\n";

