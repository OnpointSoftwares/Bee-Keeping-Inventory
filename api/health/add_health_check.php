<?php
/**
 * API Endpoint: Add Health Check
 * 
 * Adds a new health check to the database
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
    !isset($data['checkDate']) || empty($data['checkDate']) ||
    !isset($data['colonyStrength'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Required fields are missing'
    ]);
    exit;
}

try {
    // Prepare data for insertion
    $healthData = [
        'hiveID' => $data['hiveID'],
        'checkDate' => $data['checkDate'],
        'queenPresent' => $data['queenPresent'] ?? 0,
        'colonyStrength' => $data['colonyStrength'],
        'diseaseSymptoms' => $data['diseaseSymptoms'] ?? '',
        'pestProblems' => $data['pestProblems'] ?? '',
        'notes' => $data['notes'] ?? ''
    ];
    
    // Insert data
    $result = insertData('hive_health', $healthData);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Health check added successfully',
            'id' => $result
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add health check'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
