<?php
/**
 * API Endpoint: Add Hive
 * 
 * Adds a new hive to the database
 */

// Include database utilities
require_once '../../utils/database_utils.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get JSON data from request body
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Validate required fields
if (!isset($data['hiveNumber']) || empty($data['hiveNumber']) || 
    !isset($data['location']) || empty($data['location']) ||
    !isset($data['dateEstablished']) || empty($data['dateEstablished'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Required fields are missing'
    ]);
    exit;
}

try {
    // Prepare data for insertion
    $hiveData = [
        'hiveNumber' => $data['hiveNumber'],
        'location' => $data['location'],
        'dateEstablished' => $data['dateEstablished'],
        'queenAge' => $data['queenAge'] ?? null,
        'notes' => $data['notes'] ?? null,
        'status' => 'Active'
    ];
    
    // Insert data
    $result = insertData('beehive', $hiveData);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Hive added successfully',
            'id' => $result
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add hive'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
