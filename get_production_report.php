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
    // Get production summary by hive
    $hiveQuery = "SELECT b.hiveID, b.hiveNumber, SUM(p.quantity) as totalQuantity, COUNT(*) as harvestCount 
                  FROM honey_production p 
                  JOIN beehive b ON p.hiveID = b.hiveID 
                  GROUP BY p.hiveID 
                  ORDER BY totalQuantity DESC";
    $byHive = selectQuery($hiveQuery, []);
    
    // Get production summary by honey type
    $typeQuery = "SELECT type, SUM(quantity) as totalQuantity, COUNT(*) as harvestCount 
                 FROM honey_production 
                 GROUP BY type 
                 ORDER BY totalQuantity DESC";
    $byType = selectQuery($typeQuery, []);
    
    // Get production summary by month
    $monthQuery = "SELECT DATE_FORMAT(harvestDate, '%Y-%m') as month, 
                  SUM(quantity) as totalQuantity, 
                  COUNT(*) as harvestCount 
                  FROM honey_production 
                  GROUP BY DATE_FORMAT(harvestDate, '%Y-%m') 
                  ORDER BY month";
    $byMonth = selectQuery($monthQuery, []);
    
    // Get production summary by quality
    $qualityQuery = "SELECT quality, SUM(quantity) as totalQuantity, COUNT(*) as harvestCount 
                    FROM honey_production 
                    GROUP BY quality 
                    ORDER BY totalQuantity DESC";
    $byQuality = selectQuery($qualityQuery, []);
    
    // Get total production
    $totalQuery = "SELECT SUM(quantity) as totalProduction, COUNT(*) as totalHarvests 
                  FROM honey_production";
    $totalResult = selectSingleRow($totalQuery, []);
    $totalProduction = $totalResult ? $totalResult['totalProduction'] : 0;
    $totalHarvests = $totalResult ? $totalResult['totalHarvests'] : 0;
    
    // Return success response with data
    echo json_encode([
        'success' => true,
        'data' => [
            'byHive' => $byHive,
            'byType' => $byType,
            'byMonth' => $byMonth,
            'byQuality' => $byQuality,
            'totalProduction' => $totalProduction,
            'totalHarvests' => $totalHarvests
        ]
    ]);
} catch (Exception $e) {
    // Log error
    error_log("Error fetching production report: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch production report: ' . $e->getMessage()
    ]);
}
?>
