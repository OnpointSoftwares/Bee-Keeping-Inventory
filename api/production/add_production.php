<?php
/**
 * API Endpoint: Add Production Record
 * 
 * Adds a new honey production record to the database
 */

// Include production model
require_once '../../model/production/production.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get JSON data from request body
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Validate required fields
if (!isset($data['hiveID']) || empty($data['hiveID']) || 
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
    // Initialize production model
    $productionModel = new Production();
    
    // Prepare data for insertion
    $productionData = [
        'hiveID' => $data['hiveID'],
        'harvestDate' => $data['harvestDate'],
        'quantity' => $data['quantity'],
        'type' => $data['type'],
        'quality' => $data['quality'] ?? 'Standard',
        'notes' => $data['notes'] ?? ''
    ];
    
    // Add production record
    $result = $productionModel->addProduction($productionData);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Production record added successfully',
            'id' => $result
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add production record'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
