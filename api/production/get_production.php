<?php
/**
 * API Endpoint: Get Production Records
 * 
 * Retrieves all production records or production records for a specific hive if hiveID is provided
 */

// Include database utilities
require_once '../../utils/database_utils.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Build the base query
    $sql = "SELECT p.*, b.hiveNumber 
            FROM honey_production p
            JOIN beehive b ON p.hiveID = b.hiveID";
    $params = [];
    
    // Check if hiveID is provided
    if (isset($_GET['hiveID']) && !empty($_GET['hiveID'])) {
        $sql .= " WHERE p.hiveID = :hiveID";
        $params[':hiveID'] = $_GET['hiveID'];
    }
    
    // Check if date range is provided
    if (isset($_GET['startDate']) && !empty($_GET['startDate'])) {
        $sql .= isset($_GET['hiveID']) ? " AND" : " WHERE";
        $sql .= " p.harvestDate >= :startDate";
        $params[':startDate'] = $_GET['startDate'];
    }
    
    if (isset($_GET['endDate']) && !empty($_GET['endDate'])) {
        $sql .= isset($_GET['hiveID']) || isset($_GET['startDate']) ? " AND" : " WHERE";
        $sql .= " p.harvestDate <= :endDate";
        $params[':endDate'] = $_GET['endDate'];
    }
    
    // Check if honey type is provided
    if (isset($_GET['type']) && !empty($_GET['type'])) {
        $sql .= (isset($_GET['hiveID']) || isset($_GET['startDate']) || isset($_GET['endDate'])) ? " AND" : " WHERE";
        $sql .= " p.type = :type";
        $params[':type'] = $_GET['type'];
    }
    
    // Add order by
    $sql .= " ORDER BY p.harvestDate DESC";
    
    // Execute query
    $productionRecords = selectQuery($sql, $params);
    
    // Get summary data
    $totalQuantity = 0;
    $typeQuantities = [];
    
    foreach ($productionRecords as $record) {
        $totalQuantity += $record['quantity'];
        
        if (!isset($typeQuantities[$record['type']])) {
            $typeQuantities[$record['type']] = 0;
        }
        
        $typeQuantities[$record['type']] += $record['quantity'];
    }
    
    // Format type quantities for response
    $typeData = [];
    foreach ($typeQuantities as $type => $quantity) {
        $typeData[] = [
            'type' => $type,
            'quantity' => $quantity
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $productionRecords,
        'summary' => [
            'totalQuantity' => $totalQuantity,
            'recordCount' => count($productionRecords),
            'averageQuantity' => count($productionRecords) > 0 ? $totalQuantity / count($productionRecords) : 0,
            'byType' => $typeData
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
