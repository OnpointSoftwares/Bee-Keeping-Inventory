<?php
/**
 * API Endpoint: Delete Hive
 * 
 * Deletes a hive from the database (soft delete by updating status)
 */

// Include database utilities
require_once '../../utils/database_utils.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Hive ID is required'
    ]);
    exit;
}

$hiveId = $_GET['id'];

try {
    // Get database connection
    $db = getDbConnection();
    
    // Start transaction
    beginTransaction($db);
    
    // Check if hive exists
    $checkSql = "SELECT * FROM beehive WHERE hiveID = :hiveID";
    $checkParams = [':hiveID' => $hiveId];
    $hive = selectSingleRow($checkSql, $checkParams);
    
    if (!$hive) {
        echo json_encode([
            'success' => false,
            'message' => 'Hive not found'
        ]);
        rollbackTransaction($db);
        exit;
    }
    
    // Update hive status to 'Inactive' (soft delete)
    $updateSql = "UPDATE beehive SET status = 'Inactive' WHERE hiveID = :hiveID";
    $updateParams = [':hiveID' => $hiveId];
    $stmt = $db->prepare($updateSql);
    $result = $stmt->execute($updateParams);
    
    if ($result) {
        // Commit transaction
        commitTransaction($db);
        
        echo json_encode([
            'success' => true,
            'message' => 'Hive deleted successfully'
        ]);
    } else {
        // Rollback transaction
        rollbackTransaction($db);
        
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete hive'
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
