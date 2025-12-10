<?php
// Test database connection
// Usage: php test_db.php

echo "Testing MySQL connection...\n\n";

$host = 'localhost';
$dbname = 'walk_addis';
$username = 'root';
$password = ''; // <-- Enter your MySQL root password here

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "✓ Connection successful!\n";
    echo "✓ Database 'walk_addis' is accessible\n\n";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM events");
    $result = $stmt->fetch();
    echo "✓ Found {$result['count']} events in database\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
    $result = $stmt->fetch();
    echo "✓ Found {$result['count']} categories in database\n";
    
} catch (PDOException $e) {
    echo "✗ Connection failed!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "Please check:\n";
    echo "1. MySQL is running\n";
    echo "2. The password in this file matches your MySQL root password\n";
    echo "3. The database 'walk_addis' exists\n";
    exit(1);
}

