<?php
require_once '../../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['email']) || !isset($data['password'])) {
        sendError('Email and password are required');
    }
    
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email");
    $stmt->execute([':email' => $data['email']]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($data['password'], $admin['password_hash'])) {
        // In production, use proper session management or JWT tokens
        sendResponse([
            'message' => 'Login successful',
            'admin' => [
                'id' => (int)$admin['id'],
                'username' => $admin['username'],
                'email' => $admin['email']
            ]
        ]);
    } else {
        sendError('Invalid credentials', 401);
    }
} else {
    sendError('Method not allowed', 405);
}




