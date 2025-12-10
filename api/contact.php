<?php
require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required = ['organizer_name', 'email', 'message'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            sendError("Field '$field' is required");
        }
    }
    
    // Validate email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        sendError('Invalid email address');
    }
    
    $query = "INSERT INTO organizer_contacts (organizer_name, email, phone, organization, message)
              VALUES (:organizer_name, :email, :phone, :organization, :message)";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':organizer_name' => $data['organizer_name'],
        ':email' => $data['email'],
        ':phone' => $data['phone'] ?? null,
        ':organization' => $data['organization'] ?? null,
        ':message' => $data['message']
    ]);
    
    sendResponse(['message' => 'Your message has been submitted successfully. We will contact you soon.'], 201);
} else {
    sendError('Method not allowed', 405);
}




