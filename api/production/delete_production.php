<?php
/**
 * API Endpoint: Delete Production Record
 * 
 * Deletes a honey production record from the database
 */

// Include database utilities
require_once '../../utils/database_utils.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Production record ID is required'
    ]);
    exit;
}

$productionId = $_GET['id'];

try {
    // Get database connection
    $db = getDbConnection();
    
    // Start transaction
    beginTransaction($db);
    
    // Check if production record exists
    $checkSql = "SELECT * FROM honey_production WHERE productionID = :productionID";
    $checkParams = [':productionID' => $productionId];
    $production = selectSingleRow($checkSql, $checkParams);
    
    if (!$production) {
        echo json_encode([
            'success' => false,
            'message' => 'Production record not found'
        ]);
        rollbackTransaction($db);
        exit;
    }
    
    // Delete production record
    $deleteSql = "DELETE FROM honey_production WHERE productionID = :productionID";
    $deleteParams = [':productionID' => $productionId];
    $stmt = $db->prepare($deleteSql);
    $result = $stmt->execute($deleteParams);
    
    if ($result) {
        // Commit transaction
        commitTransaction($db);
        
        echo json_encode([
            'success' => true,
            'message' => 'Production record deleted successfully'
        ]);
    } else {
        // Rollback transaction
        rollbackTransaction($db);
        
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete production record'
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
