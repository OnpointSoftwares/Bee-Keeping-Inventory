<?php
/**
 * API Endpoint: Get Hives
 * 
 * Retrieves all hives or a specific hive if ID is provided
 */

// Include database utilities
require_once '../../utils/database_utils.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if ID is provided for a specific hive
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $hiveId = $_GET['id'];
    
    try {
        // Get database connection
        $db = getDbConnection();
        
        // Prepare query to get specific hive
        $query = "SELECT * FROM beehive WHERE hiveID = :hiveID";
        
        // Execute query
        $stmt = $db->prepare($query);
        $stmt->bindParam(':hiveID', $hiveId);
        $stmt->execute();
        
        // Fetch hive data
        $hive = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($hive) {
            echo json_encode([
                'success' => true,
                'data' => $hive
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Hive not found'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    // Get all hives
    try {
        // Get hives data
        $hivesSql = "SELECT * FROM beehive ORDER BY hiveNumber";
        $hivesData = selectQuery($hivesSql);
        
        echo json_encode([
            'success' => true,
            'data' => $hivesData
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}
?>
