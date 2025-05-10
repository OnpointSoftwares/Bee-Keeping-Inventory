<?php
/**
 * API Endpoint: Delete Health Check
 * 
 * Deletes a health check record from the database
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
    
    // Start transaction
    beginTransaction($db);
    
    // Check if health check exists
    $checkSql = "SELECT * FROM hive_health WHERE healthID = :healthID";
    $checkParams = [':healthID' => $healthId];
    $healthCheck = selectSingleRow($checkSql, $checkParams);
    
    if (!$healthCheck) {
        echo json_encode([
            'success' => false,
            'message' => 'Health check not found'
        ]);
        rollbackTransaction($db);
        exit;
    }
    
    // Delete health check
    $deleteSql = "DELETE FROM hive_health WHERE healthID = :healthID";
    $deleteParams = [':healthID' => $healthId];
    $stmt = $db->prepare($deleteSql);
    $result = $stmt->execute($deleteParams);
    
    if ($result) {
        // Commit transaction
        commitTransaction($db);
        
        echo json_encode([
            'success' => true,
            'message' => 'Health check deleted successfully'
        ]);
    } else {
        // Rollback transaction
        rollbackTransaction($db);
        
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete health check'
        ]);
    }
} catch (Exception $e) {
    // Rollback transaction
    if (isset($db)) {
        rollbackTransaction($db);
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
