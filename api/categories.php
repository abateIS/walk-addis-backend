<?php
require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

if ($method === 'GET') {
    $query = "SELECT * FROM categories ORDER BY name ASC";
    $stmt = $pdo->query($query);
    $categories = $stmt->fetchAll();
    
    foreach ($categories as &$category) {
        $category['id'] = (int)$category['id'];
    }
    
    sendResponse(['categories' => $categories]);
} else {
    sendError('Method not allowed', 405);
}




