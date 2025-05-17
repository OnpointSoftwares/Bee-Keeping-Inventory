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
    // Get production data for dashboard
    $productionQuery = "SELECT p.*, b.hiveNumber 
                       FROM honey_production p 
                       LEFT JOIN beehive b ON p.hiveID = b.hiveID 
                       ORDER BY p.harvestDate DESC 
                       LIMIT 10";
    $production = selectQuery($productionQuery, []);
    
    // Get equipment summary
    $equipmentQuery = "SELECT type, SUM(quantity) as totalQuantity 
                      FROM equipment 
                      GROUP BY type 
                      ORDER BY totalQuantity DESC";
    $equipment = selectQuery($equipmentQuery, []);
    
    // Get hive health summary
    $healthQuery = "SELECT h.*, b.hiveNumber 
                   FROM hive_health h 
                   JOIN beehive b ON h.hiveID = b.hiveID 
                   ORDER BY h.checkDate DESC 
                   LIMIT 10";
    $hiveHealth = selectQuery($healthQuery, []);
    
    // Get total production
    $totalQuery = "SELECT SUM(quantity) as totalProduction FROM honey_production";
    $totalResult = selectSingleRow($totalQuery, []);
    $totalProduction = $totalResult ? $totalResult['totalProduction'] : 0;
    
    // Get total equipment
    $totalEquipmentQuery = "SELECT SUM(quantity) as totalEquipment FROM equipment";
    $totalEquipmentResult = selectSingleRow($totalEquipmentQuery, []);
    $totalEquipment = $totalEquipmentResult ? $totalEquipmentResult['totalEquipment'] : 0;
    
    // Get total hives
    $totalHivesQuery = "SELECT COUNT(*) as totalHives FROM beehive";
    $totalHivesResult = selectSingleRow($totalHivesQuery, []);
    $totalHives = $totalHivesResult ? $totalHivesResult['totalHives'] : 0;
    
    // Return success response with data
    echo json_encode([
        'success' => true,
        'data' => [
            'production' => $production,
            'equipment' => $equipment,
            'hiveHealth' => $hiveHealth,
            'totalProduction' => $totalProduction,
            'totalEquipment' => $totalEquipment,
            'totalHives' => $totalHives
        ]
    ]);
} catch (Exception $e) {
    // Log error
    error_log("Error fetching dashboard data: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch dashboard data: ' . $e->getMessage()
    ]);
}
?>
