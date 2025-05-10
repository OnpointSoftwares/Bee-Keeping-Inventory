<?php
/**
 * API Endpoint: Production
 * 
 * Central endpoint for production-related operations
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
            // Get all production records
            $sql = "SELECT p.*, b.hiveNumber 
                    FROM honey_production p
                    JOIN beehive b ON p.hiveID = b.hiveID
                    ORDER BY p.harvestDate DESC";
            
            $productionRecords = selectQuery($sql);
            
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
            break;
            
        case 'get':
            // Get a specific production record
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                throw new Exception('Production record ID is required');
            }
            
            $productionId = $_GET['id'];
            
            $sql = "SELECT p.*, b.hiveNumber 
                    FROM honey_production p
                    JOIN beehive b ON p.hiveID = b.hiveID
                    WHERE p.productionID = :productionID";
            
            $productionRecord = selectSingleRow($sql, [':productionID' => $productionId]);
            
            if (!$productionRecord) {
                throw new Exception('Production record not found');
            }
            
            echo json_encode([
                'success' => true,
                'data' => $productionRecord
            ]);
            break;
            
        case 'add':
            // Get JSON data from request body
            $jsonData = file_get_contents('php://input');
            $data = json_decode($jsonData, true);
            
            // Validate required fields
            if (!isset($data['hiveID']) || empty($data['hiveID']) || 
                !isset($data['harvestDate']) || empty($data['harvestDate']) ||
                !isset($data['quantity']) || $data['quantity'] <= 0 ||
                !isset($data['type']) || empty($data['type'])) {
                throw new Exception('Required fields are missing or invalid');
            }
            
            // Prepare data for insertion
            $productionData = [
                'hiveID' => $data['hiveID'],
                'harvestDate' => $data['harvestDate'],
                'quantity' => $data['quantity'],
                'type' => $data['type'],
                'quality' => $data['quality'] ?? 'Standard',
                'notes' => $data['notes'] ?? ''
            ];
            
            // Insert data
            $result = insertData('honey_production', $productionData);
            
            if (!$result) {
                throw new Exception('Failed to add production record');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Production record added successfully',
                'id' => $result
            ]);
            break;
            
        case 'update':
            // Get JSON data from request body
            $jsonData = file_get_contents('php://input');
            $data = json_decode($jsonData, true);
            
            // Validate required fields
            if (!isset($data['productionID']) || empty($data['productionID']) ||
                !isset($data['hiveID']) || empty($data['hiveID']) || 
                !isset($data['harvestDate']) || empty($data['harvestDate']) ||
                !isset($data['quantity']) || $data['quantity'] <= 0 ||
                !isset($data['type']) || empty($data['type'])) {
                throw new Exception('Required fields are missing or invalid');
            }
            
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
            
            if (!$result) {
                throw new Exception('Failed to update production record or no changes made');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Production record updated successfully'
            ]);
            break;
            
        case 'delete':
            // Check if ID is provided
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                throw new Exception('Production record ID is required');
            }
            
            $productionId = $_GET['id'];
            
            // Get database connection
            $db = getDbConnection();
            
            // Start transaction
            beginTransaction($db);
            
            // Check if production record exists
            $checkSql = "SELECT * FROM honey_production WHERE productionID = :productionID";
            $checkParams = [':productionID' => $productionId];
            $production = selectSingleRow($checkSql, $checkParams);
            
            if (!$production) {
                throw new Exception('Production record not found');
            }
            
            // Delete production record
            $deleteSql = "DELETE FROM honey_production WHERE productionID = :productionID";
            $deleteParams = [':productionID' => $productionId];
            $stmt = $db->prepare($deleteSql);
            $result = $stmt->execute($deleteParams);
            
            if (!$result) {
                // Rollback transaction
                rollbackTransaction($db);
                throw new Exception('Failed to delete production record');
            }
            
            // Commit transaction
            commitTransaction($db);
            
            echo json_encode([
                'success' => true,
                'message' => 'Production record deleted successfully'
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
