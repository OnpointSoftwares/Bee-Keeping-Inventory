<?php
/**
 * API Endpoint: Equipment
 * 
 * Central endpoint for equipment-related operations
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
            // Get all equipment
            $equipmentSql = "SELECT * FROM equipment WHERE status = 'Active' ORDER BY name";
            $equipmentData = selectQuery($equipmentSql);
            
            // Get summary by type
            $summarySql = "SELECT type, SUM(quantity) as totalQuantity, COUNT(*) as itemCount 
                          FROM equipment 
                          WHERE status = 'Active' 
                          GROUP BY type 
                          ORDER BY totalQuantity DESC";
            $summaryData = selectQuery($summarySql);
            
            echo json_encode([
                'success' => true,
                'data' => $equipmentData,
                'summary' => $summaryData
            ]);
            break;
            
        case 'get':
            // Get a specific equipment item
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                throw new Exception('Equipment ID is required');
            }
            
            $equipmentId = $_GET['id'];
            $equipmentSql = "SELECT * FROM equipment WHERE equipmentID = :equipmentID";
            $equipmentData = selectSingleRow($equipmentSql, [':equipmentID' => $equipmentId]);
            
            if (!$equipmentData) {
                throw new Exception('Equipment not found');
            }
            
            echo json_encode([
                'success' => true,
                'data' => $equipmentData
            ]);
            break;
            
        case 'add':
            // Get JSON data from request body
            $jsonData = file_get_contents('php://input');
            $data = json_decode($jsonData, true);
            
            // Validate required fields
            if (!isset($data['name']) || empty($data['name'])) {
                throw new Exception('Equipment name is required');
            }
            
            if (!isset($data['type']) || empty($data['type'])) {
                throw new Exception('Equipment type is required');
            }
            
            if (!isset($data['quantity']) || !is_numeric($data['quantity'])) {
                throw new Exception('Valid quantity is required');
            }
            
            // Prepare data for insertion
            $insertData = [
                'name' => $data['name'],
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'condition_status' => isset($data['condition_status']) ? $data['condition_status'] : 'Good',
                'purchaseDate' => isset($data['purchaseDate']) && !empty($data['purchaseDate']) ? $data['purchaseDate'] : null,
                'notes' => isset($data['notes']) ? $data['notes'] : null,
                'status' => 'Active'
            ];
            
            // Insert equipment
            $equipmentId = insertData('equipment', $insertData);
            
            echo json_encode([
                'success' => true,
                'message' => 'Equipment added successfully',
                'id' => $equipmentId
            ]);
            break;
            
        case 'update':
            // Get JSON data from request body
            $jsonData = file_get_contents('php://input');
            $data = json_decode($jsonData, true);
            
            // Validate required fields
            if (!isset($data['equipmentID']) || empty($data['equipmentID'])) {
                throw new Exception('Equipment ID is required');
            }
            
            if (!isset($data['name']) || empty($data['name'])) {
                throw new Exception('Equipment name is required');
            }
            
            if (!isset($data['type']) || empty($data['type'])) {
                throw new Exception('Equipment type is required');
            }
            
            if (!isset($data['quantity']) || !is_numeric($data['quantity'])) {
                throw new Exception('Valid quantity is required');
            }
            
            // Prepare data for update
            $updateData = [
                'name' => $data['name'],
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'condition_status' => isset($data['condition_status']) ? $data['condition_status'] : 'Good',
                'purchaseDate' => isset($data['purchaseDate']) && !empty($data['purchaseDate']) ? $data['purchaseDate'] : null,
                'notes' => isset($data['notes']) ? $data['notes'] : null
            ];
            
            // Update equipment
            $where = ['equipmentID' => $data['equipmentID']];
            $updated = updateData('equipment', $updateData, $where);
            
            if (!$updated) {
                throw new Exception('Failed to update equipment');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Equipment updated successfully'
            ]);
            break;
            
        case 'delete':
            // Check if ID is provided
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                throw new Exception('Equipment ID is required');
            }
            
            $equipmentId = $_GET['id'];
            
            // Soft delete by updating status
            $updateData = ['status' => 'Inactive'];
            $where = ['equipmentID' => $equipmentId];
            $deleted = updateData('equipment', $updateData, $where);
            
            if (!$deleted) {
                throw new Exception('Failed to delete equipment');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Equipment deleted successfully'
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
