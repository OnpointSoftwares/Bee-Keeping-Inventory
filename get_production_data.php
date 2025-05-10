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
    // Get all production data with hive information
    $query = "SELECT p.*, b.hiveNumber 
              FROM honey_production p 
              LEFT JOIN beehive b ON p.hiveID = b.hiveID 
              ORDER BY p.harvestDate DESC";
    
    $production = selectQuery($query, []);
    
    // Return success response with data
    echo json_encode([
        'success' => true,
        'data' => $production
    ]);
} catch (Exception $e) {
    // Log error
    error_log("Error fetching production data: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch production data: ' . $e->getMessage()
    ]);
}
?>
