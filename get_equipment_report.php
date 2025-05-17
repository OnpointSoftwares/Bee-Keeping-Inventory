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
    // Get equipment summary by type
    $typeQuery = "SELECT type, SUM(quantity) as totalQuantity FROM equipment GROUP BY type ORDER BY totalQuantity DESC";
    $byType = selectQuery($typeQuery, []);
    
    // Get equipment summary by condition
    $conditionQuery = "SELECT condition_status as condition, COUNT(*) as count FROM equipment GROUP BY condition_status ORDER BY count DESC";
    $byCondition = selectQuery($conditionQuery, []);
    
    // Get equipment summary by status
    $statusQuery = "SELECT status, COUNT(*) as count FROM equipment GROUP BY status ORDER BY count DESC";
    $byStatus = selectQuery($statusQuery, []);
    
    // Get total equipment count
    $totalQuery = "SELECT SUM(quantity) as total FROM equipment";
    $totalResult = selectSingleRow($totalQuery, []);
    $totalEquipment = $totalResult ? $totalResult['total'] : 0;
    
    // Return success response with data
    echo json_encode([
        'success' => true,
        'data' => [
            'byType' => $byType,
            'byCondition' => $byCondition,
            'byStatus' => $byStatus,
            'totalEquipment' => $totalEquipment
        ]
    ]);
} catch (Exception $e) {
    // Log error
    error_log("Error fetching equipment report: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch equipment report: ' . $e->getMessage()
    ]);
}
?>
