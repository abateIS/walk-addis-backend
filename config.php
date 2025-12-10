<?php
// Database configuration
// IMPORTANT: Update these values to match your MySQL setup
define('DB_HOST', getenv('DB_HOST'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));


// CORS headers for React frontend
// Allow both localhost (development) and production domain
$allowed_origins = [
    'http://localhost:5173',
    'http://localhost:3000',
    'https://walkaddis.vercel.app', // Update with your Vercel URL
    'https://*.vercel.app' // Allow all Vercel preview deployments
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins) || preg_match('/https:\/\/.*\.vercel\.app$/', $origin)) {
    header('Access-Control-Allow-Origin: ' . $origin);
} else {
    header('Access-Control-Allow-Origin: http://localhost:5173'); // Default for development
}

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit();
    }
}

// Helper function to send JSON response
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

// Helper function to send error response
function sendError($message, $statusCode = 400) {
    sendResponse(['error' => $message], $statusCode);
}

