<?php
/**
 * API Endpoint: Hive
 * 
 * Central endpoint for hive-related operations
 */

// Include database utilities
require_once '../../utils/database_utils.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get the action from the request
$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    switch ($action) {
        case 'getAll':
            // Get all hives
            $hivesSql = "SELECT * FROM beehive WHERE status = 'Active' ORDER BY hiveNumber";
            $hivesData = selectQuery($hivesSql);
            
            echo json_encode([
                'success' => true,
                'data' => $hivesData
            ]);
            break;
            
        case 'get':
            // Get a specific hive
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                throw new Exception('Hive ID is required');
            }
            
            $hiveId = $_GET['id'];
            $hiveSql = "SELECT * FROM beehive WHERE hiveID = :hiveID";
            $hiveData = selectSingleRow($hiveSql, [':hiveID' => $hiveId]);
            
            if (!$hiveData) {
                throw new Exception('Hive not found');
            }
            
            echo json_encode([
                'success' => true,
                'data' => $hiveData
            ]);
            break;
            
        case 'add':
            // Get JSON data from request body
            $jsonData = file_get_contents('php://input');
            $data = json_decode($jsonData, true);
            
            // Validate required fields
            if (!isset($data['hiveNumber']) || empty($data['hiveNumber']) || 
                !isset($data['location']) || empty($data['location']) ||
                !isset($data['dateEstablished']) || empty($data['dateEstablished'])) {
                throw new Exception('Required fields are missing');
            }
            
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
            
            if (!$result) {
                throw new Exception('Failed to add hive');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Hive added successfully',
                'id' => $result
            ]);
            break;
            
        case 'update':
            // Get JSON data from request body
            $jsonData = file_get_contents('php://input');
            $data = json_decode($jsonData, true);
            
            // Validate required fields
            if (!isset($data['hiveID']) || empty($data['hiveID']) ||
                !isset($data['hiveNumber']) || empty($data['hiveNumber']) || 
                !isset($data['location']) || empty($data['location'])) {
                throw new Exception('Required fields are missing');
            }
            
            // Prepare data for update
            $hiveData = [
                'hiveNumber' => $data['hiveNumber'],
                'location' => $data['location'],
                'queenAge' => $data['queenAge'] ?? null,
                'notes' => $data['notes'] ?? null
            ];
            
            // Update data
            $result = updateData('beehive', $hiveData, 'hiveID = :hiveID', ['hiveID' => $data['hiveID']]);
            
            if (!$result) {
                throw new Exception('Failed to update hive or no changes made');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Hive updated successfully'
            ]);
            break;
            
        case 'delete':
            // Check if ID is provided
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                throw new Exception('Hive ID is required');
            }
            
            $hiveId = $_GET['id'];
            
            // Get database connection
            $db = getDbConnection();
            
            // Start transaction
            beginTransaction($db);
            
            // Check if hive exists
            $checkSql = "SELECT * FROM beehive WHERE hiveID = :hiveID";
            $checkParams = [':hiveID' => $hiveId];
            $hive = selectSingleRow($checkSql, $checkParams);
            
            if (!$hive) {
                throw new Exception('Hive not found');
            }
            
            // Update hive status to 'Inactive' (soft delete)
            $updateSql = "UPDATE beehive SET status = 'Inactive' WHERE hiveID = :hiveID";
            $updateParams = [':hiveID' => $hiveId];
            $stmt = $db->prepare($updateSql);
            $result = $stmt->execute($updateParams);
            
            if (!$result) {
                // Rollback transaction
                rollbackTransaction($db);
                throw new Exception('Failed to delete hive');
            }
            
            // Commit transaction
            commitTransaction($db);
            
            echo json_encode([
                'success' => true,
                'message' => 'Hive deleted successfully'
            ]);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
