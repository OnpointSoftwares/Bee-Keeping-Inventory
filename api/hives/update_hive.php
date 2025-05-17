<?php
/**
 * API Endpoint: Update Hive
 * 
 * Updates an existing hive in the database
 */

// Include database utilities
require_once '../../utils/database_utils.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get JSON data from request body
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Validate required fields
if (!isset($data['hiveID']) || empty($data['hiveID']) ||
    !isset($data['hiveNumber']) || empty($data['hiveNumber']) || 
    !isset($data['location']) || empty($data['location'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Required fields are missing'
    ]);
    exit;
}

try {
    // Prepare data for update
    $hiveData = [
        'hiveNumber' => $data['hiveNumber'],
        'location' => $data['location'],
        'queenAge' => $data['queenAge'] ?? null,
        'notes' => $data['notes'] ?? null
    ];
    
    // Update data
    $result = updateData('beehive', $hiveData, 'hiveID = :hiveID', ['hiveID' => $data['hiveID']]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Hive updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update hive or no changes made'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
