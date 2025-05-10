<?php
/**
 * API Endpoint: Update Health Check
 * 
 * Updates an existing health check record
 */

// Include database utilities
require_once '../../utils/database_utils.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get JSON data from request body
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Validate required fields
if (!isset($data['healthID']) || empty($data['healthID'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Health check ID is required'
    ]);
    exit;
}

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
    // Get database connection
    $db = getDbConnection();
    
    // Prepare update query
    $query = "UPDATE hive_health 
              SET hiveID = :hiveID,
                  checkDate = :checkDate,
                  queenPresent = :queenPresent,
                  colonyStrength = :colonyStrength,
                  diseaseSymptoms = :diseaseSymptoms,
                  pestProblems = :pestProblems,
                  notes = :notes
              WHERE healthID = :healthID";
    
    // Execute query
    $stmt = $db->prepare($query);
    $stmt->bindParam(':healthID', $data['healthID']);
    $stmt->bindParam(':hiveID', $data['hiveID']);
    $stmt->bindParam(':checkDate', $data['checkDate']);
    $stmt->bindParam(':queenPresent', $data['queenPresent']);
    $stmt->bindParam(':colonyStrength', $data['colonyStrength']);
    $stmt->bindParam(':diseaseSymptoms', $data['diseaseSymptoms']);
    $stmt->bindParam(':pestProblems', $data['pestProblems']);
    $stmt->bindParam(':notes', $data['notes']);
    
    $result = $stmt->execute();
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Health check updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update health check'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
