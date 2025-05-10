<?php
// Direct script to add production data to the database
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/production_errors.log');

header('Content-Type: application/json');

// Allow CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

// Get POST data
$postData = file_get_contents('php://input');
$data = json_decode($postData, true);

// Log the received data
error_log('Received data: ' . print_r($data, true));

// Validate required fields
$requiredFields = ['hiveID', 'harvestDate', 'quantity', 'type', 'quality'];
foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
        exit();
    }
}

try {
    // Connect to database
    require_once 'config/database.php';
    $database = new Database();
    $conn = $database->connect();
    
    // Prepare the SQL statement
    $sql = "INSERT INTO honey_production (hiveID, harvestDate, quantity, type, quality, notes) 
            VALUES (:hiveID, :harvestDate, :quantity, :type, :quality, :notes)";
    
    $stmt = $conn->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(':hiveID', $data['hiveID'], PDO::PARAM_INT);
    $stmt->bindParam(':harvestDate', $data['harvestDate']);
    $stmt->bindParam(':quantity', $data['quantity']);
    $stmt->bindParam(':type', $data['type']);
    $stmt->bindParam(':quality', $data['quality']);
    $stmt->bindParam(':notes', $data['notes'] ?? '');
    
    // Execute the statement
    if ($stmt->execute()) {
        $productionID = $conn->lastInsertId();
        echo json_encode([
            'success' => true, 
            'message' => 'Production record added successfully',
            'id' => $productionID
        ]);
    } else {
        error_log('Database error: ' . print_r($stmt->errorInfo(), true));
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to add production record']);
    }
} catch (Exception $e) {
    error_log('Exception: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
