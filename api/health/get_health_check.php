<?php
/**
 * API Endpoint: Get Health Check Details
 * 
 * Retrieves detailed information about a specific health check
 */

// Include database utilities
require_once '../../utils/database_utils.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Health check ID is required'
    ]);
    exit;
}

$healthId = $_GET['id'];

try {
    // Get database connection
    $db = getDbConnection();
    
    // Prepare query to get health check with hive number
    $query = "SELECT h.*, b.hiveNumber 
              FROM hive_health h
              JOIN beehive b ON h.hiveID = b.hiveID
              WHERE h.healthID = :healthID";
    
    // Execute query
    $stmt = $db->prepare($query);
    $stmt->bindParam(':healthID', $healthId);
    $stmt->execute();
    
    // Fetch health check data
    $healthCheck = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($healthCheck) {
        echo json_encode([
            'success' => true,
            'data' => $healthCheck
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Health check not found'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
