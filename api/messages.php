<?php
require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

switch ($method) {
    case 'GET':
        // Get all organizer contact messages (admin only)
        $query = "SELECT * FROM organizer_contacts ORDER BY created_at DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $messages = $stmt->fetchAll();
        
        // Format dates for JSON
        foreach ($messages as &$message) {
            $message['id'] = (int)$message['id'];
        }
        
        sendResponse(['messages' => $messages]);
        break;
        
    case 'DELETE':
        // Delete a message (admin only)
        $messageId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        
        if (!$messageId) {
            sendError('Message ID is required');
        }
        
        $stmt = $pdo->prepare("DELETE FROM organizer_contacts WHERE id = :id");
        $stmt->execute([':id' => $messageId]);
        
        if ($stmt->rowCount() > 0) {
            sendResponse(['message' => 'Message deleted successfully']);
        } else {
            sendError('Message not found', 404);
        }
        break;
        
    default:
        sendError('Method not allowed', 405);
        break;
}


