<?php
/**
 * API Endpoint: Health
 * 
 * Central endpoint for health-related operations
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
            // Get all health checks
            $sql = "SELECT h.*, b.hiveNumber 
                    FROM hive_health h
                    JOIN beehive b ON h.hiveID = b.hiveID
                    ORDER BY h.checkDate DESC";
            
            $healthChecks = selectQuery($sql);
            
            // Process health checks to add status and issues
            foreach ($healthChecks as &$check) {
                // Determine health status
                $healthStatus = 'Healthy';
                $statusClass = 'bg-success';
                $issues = [];
                
                if (!empty($check['diseaseSymptoms'])) {
                    $issues[] = 'Disease';
                }
                
                if (!empty($check['pestProblems'])) {
                    $issues[] = 'Pests';
                }
                
                if ($check['queenPresent'] == 0) {
                    $issues[] = 'No Queen';
                }
                
                if ($check['colonyStrength'] < 5) {
                    $issues[] = 'Low Strength';
                }
                
                if (!empty($issues)) {
                    $healthStatus = 'Issues Detected';
                    $statusClass = 'bg-warning';
                    
                    if ($check['colonyStrength'] < 3 || ($check['queenPresent'] == 0 && !empty($check['diseaseSymptoms']))) {
                        $healthStatus = 'Critical';
                        $statusClass = 'bg-danger';
                    }
                }
                
                $check['healthStatus'] = $healthStatus;
                $check['statusClass'] = $statusClass;
                $check['issues'] = $issues;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $healthChecks
            ]);
            break;
            
        case 'get':
            // Get a specific health check
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                throw new Exception('Health check ID is required');
            }
            
            $healthId = $_GET['id'];
            
            $sql = "SELECT h.*, b.hiveNumber 
                    FROM hive_health h
                    JOIN beehive b ON h.hiveID = b.hiveID
                    WHERE h.healthID = :healthID";
            
            $healthCheck = selectSingleRow($sql, [':healthID' => $healthId]);
            
            if (!$healthCheck) {
                throw new Exception('Health check not found');
            }
            
            echo json_encode([
                'success' => true,
                'data' => $healthCheck
            ]);
            break;
            
        case 'add':
            // Get JSON data from request body
            $jsonData = file_get_contents('php://input');
            $data = json_decode($jsonData, true);
            
            // Validate required fields
            if (!isset($data['hiveID']) || empty($data['hiveID']) || 
                !isset($data['checkDate']) || empty($data['checkDate']) ||
                !isset($data['colonyStrength'])) {
                throw new Exception('Required fields are missing');
            }
            
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
            
            if (!$result) {
                throw new Exception('Failed to add health check');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Health check added successfully',
                'id' => $result
            ]);
            break;
            
        case 'update':
            // Get JSON data from request body
            $jsonData = file_get_contents('php://input');
            $data = json_decode($jsonData, true);
            
            // Validate required fields
            if (!isset($data['healthID']) || empty($data['healthID']) ||
                !isset($data['hiveID']) || empty($data['hiveID']) || 
                !isset($data['checkDate']) || empty($data['checkDate']) ||
                !isset($data['colonyStrength'])) {
                throw new Exception('Required fields are missing');
            }
            
            // Prepare data for update
            $healthData = [
                'hiveID' => $data['hiveID'],
                'checkDate' => $data['checkDate'],
                'queenPresent' => $data['queenPresent'] ?? 0,
                'colonyStrength' => $data['colonyStrength'],
                'diseaseSymptoms' => $data['diseaseSymptoms'] ?? '',
                'pestProblems' => $data['pestProblems'] ?? '',
                'notes' => $data['notes'] ?? ''
            ];
            
            // Update data
            $result = updateData('hive_health', $healthData, 'healthID = :healthID', ['healthID' => $data['healthID']]);
            
            if (!$result) {
                throw new Exception('Failed to update health check or no changes made');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Health check updated successfully'
            ]);
            break;
            
        case 'delete':
            // Check if ID is provided
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                throw new Exception('Health check ID is required');
            }
            
            $healthId = $_GET['id'];
            
            // Get database connection
            $db = getDbConnection();
            
            // Start transaction
            beginTransaction($db);
            
            // Check if health check exists
            $checkSql = "SELECT * FROM hive_health WHERE healthID = :healthID";
            $checkParams = [':healthID' => $healthId];
            $healthCheck = selectSingleRow($checkSql, $checkParams);
            
            if (!$healthCheck) {
                throw new Exception('Health check not found');
            }
            
            // Delete health check
            $deleteSql = "DELETE FROM hive_health WHERE healthID = :healthID";
            $deleteParams = [':healthID' => $healthId];
            $stmt = $db->prepare($deleteSql);
            $result = $stmt->execute($deleteParams);
            
            if (!$result) {
                // Rollback transaction
                rollbackTransaction($db);
                throw new Exception('Failed to delete health check');
            }
            
            // Commit transaction
            commitTransaction($db);
            
            echo json_encode([
                'success' => true,
                'message' => 'Health check deleted successfully'
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
