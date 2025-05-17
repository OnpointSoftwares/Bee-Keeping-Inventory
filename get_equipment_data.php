<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database utilities
require_once 'utils/database_utils.php';

// Set header to return JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'User not authenticated'
    ]);
    exit;
}

try {
    // Get all equipment data
    $query = "SELECT * FROM equipment ORDER BY name ASC";
    $equipment = selectQuery($query, []);
    
    // Return success response with data
    echo json_encode([
        'success' => true,
        'data' => $equipment
    ]);
} catch (Exception $e) {
    // Log error
    error_log("Error fetching equipment data: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch equipment data: ' . $e->getMessage()
    ]);
}
?>
