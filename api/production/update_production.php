<?php
/**
 * API Endpoint: Update Production Record
 * 
 * Updates an existing honey production record in the database
 */

// Include database utilities
require_once '../../utils/database_utils.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get JSON data from request body
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Validate required fields
if (!isset($data['productionID']) || empty($data['productionID']) ||
    !isset($data['hiveID']) || empty($data['hiveID']) || 
    !isset($data['harvestDate']) || empty($data['harvestDate']) ||
    !isset($data['quantity']) || $data['quantity'] <= 0 ||
    !isset($data['type']) || empty($data['type'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Required fields are missing or invalid'
    ]);
    exit;
}

try {
    // Prepare data for update
    $productionData = [
        'hiveID' => $data['hiveID'],
        'harvestDate' => $data['harvestDate'],
        'quantity' => $data['quantity'],
        'type' => $data['type'],
        'quality' => $data['quality'] ?? 'Standard',
        'notes' => $data['notes'] ?? ''
    ];
    
    // Update data
    $result = updateData('honey_production', $productionData, 'productionID = :productionID', ['productionID' => $data['productionID']]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Production record updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update production record or no changes made'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
