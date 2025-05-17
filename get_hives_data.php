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
    // Get all hives with basic information
    $query = "SELECT * FROM beehive ORDER BY hiveNumber ASC";
    $hives = selectQuery($query, []);
    
    // For each hive, get the latest health check
    foreach ($hives as &$hive) {
        // Get latest health check
        $healthQuery = "SELECT * FROM hive_health WHERE hiveID = ? ORDER BY checkDate DESC LIMIT 1";
        $healthCheck = selectSingleRow($healthQuery, [$hive['hiveID']]);
        
        if ($healthCheck) {
            $hive['lastHealth'] = $healthCheck;
        }
        
        // Get production summary
        $productionQuery = "SELECT 
                            SUM(quantity) as totalProduction,
                            COUNT(*) as harvestCount,
                            MAX(harvestDate) as lastHarvestDate,
                            GROUP_CONCAT(DISTINCT type) as productTypes
                            FROM honey_production 
                            WHERE hiveID = ?";
        
        $productionSummary = selectSingleRow($productionQuery, [$hive['hiveID']]);
        
        if ($productionSummary && $productionSummary['totalProduction']) {
            $hive['productionSummary'] = $productionSummary;
        }
    }
    
    // Return success response with data
    echo json_encode([
        'success' => true,
        'data' => $hives
    ]);
} catch (Exception $e) {
    // Log error
    error_log("Error fetching hives data: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch hives data: ' . $e->getMessage()
    ]);
}
?>
