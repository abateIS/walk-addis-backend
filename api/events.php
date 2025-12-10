<?php
require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getDBConnection();

switch ($method) {
    case 'GET':
        // Get all events or filter by category
        $category = isset($_GET['category']) ? $_GET['category'] : null;
        $status = isset($_GET['status']) ? $_GET['status'] : 'active';
        
        $query = "SELECT e.*, c.name as category_name, c.slug as category_slug 
                  FROM events e 
                  JOIN categories c ON e.category_id = c.id";
        
        $params = [];
        
        // Handle status filter
        if ($status !== 'all') {
            $query .= " WHERE e.status = :status";
            $params[':status'] = $status;
        }
        
        if ($category) {
            $query .= $status !== 'all' ? " AND c.slug = :category" : " WHERE c.slug = :category";
            $params[':category'] = $category;
        }
        
        $query .= " ORDER BY e.is_featured DESC, e.event_date ASC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $events = $stmt->fetchAll();
        
        // Format dates for JSON
        foreach ($events as &$event) {
            $event['id'] = (int)$event['id'];
            $event['category_id'] = (int)$event['category_id'];
            $event['is_featured'] = (bool)$event['is_featured'];
            $event['ticket_price'] = (float)$event['ticket_price'];
        }
        
        sendResponse(['events' => $events]);
        break;
        
    case 'POST':
        // Create new event (admin only)
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['title', 'description', 'category_id', 'venue', 'address', 'event_date', 'organizer_name'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                sendError("Field '$field' is required");
            }
        }
        
        $query = "INSERT INTO events (title, description, category_id, venue, address, event_date, 
                  image_url, ticket_price, ticket_url, contact_email, contact_phone, organizer_name, is_featured)
                  VALUES (:title, :description, :category_id, :venue, :address, :event_date, 
                  :image_url, :ticket_price, :ticket_url, :contact_email, :contact_phone, :organizer_name, :is_featured)";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':category_id' => $data['category_id'],
            ':venue' => $data['venue'],
            ':address' => $data['address'],
            ':event_date' => $data['event_date'],
            ':image_url' => $data['image_url'] ?? null,
            ':ticket_price' => $data['ticket_price'] ?? 0.00,
            ':ticket_url' => $data['ticket_url'] ?? null,
            ':contact_email' => $data['contact_email'] ?? null,
            ':contact_phone' => $data['contact_phone'] ?? null,
            ':organizer_name' => $data['organizer_name'],
            ':is_featured' => $data['is_featured'] ?? false
        ]);
        
        $eventId = $pdo->lastInsertId();
        sendResponse(['message' => 'Event created successfully', 'id' => $eventId], 201);
        break;
        
    case 'DELETE':
        // Delete event (admin only)
        $eventId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        
        if (!$eventId) {
            sendError('Event ID is required');
        }
        
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id");
        $stmt->execute([':id' => $eventId]);
        
        if ($stmt->rowCount() > 0) {
            sendResponse(['message' => 'Event deleted successfully']);
        } else {
            sendError('Event not found', 404);
        }
        break;
        
    default:
        sendError('Method not allowed', 405);
        break;
}




