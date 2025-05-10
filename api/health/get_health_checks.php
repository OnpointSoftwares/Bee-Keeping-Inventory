<?php
/**
 * API Endpoint: Get Health Checks
 * 
 * Retrieves all health checks or health checks for a specific hive if hiveID is provided
 */

// Include database utilities
require_once '../../utils/database_utils.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Build the base query
    $sql = "SELECT h.*, b.hiveNumber 
            FROM hive_health h
            JOIN beehive b ON h.hiveID = b.hiveID";
    $params = [];
    
    // Check if hiveID is provided
    if (isset($_GET['hiveID']) && !empty($_GET['hiveID'])) {
        $sql .= " WHERE h.hiveID = :hiveID";
        $params[':hiveID'] = $_GET['hiveID'];
    }
    
    // Check if date range is provided
    if (isset($_GET['startDate']) && !empty($_GET['startDate'])) {
        $sql .= isset($_GET['hiveID']) ? " AND" : " WHERE";
        $sql .= " h.checkDate >= :startDate";
        $params[':startDate'] = $_GET['startDate'];
    }
    
    if (isset($_GET['endDate']) && !empty($_GET['endDate'])) {
        $sql .= isset($_GET['hiveID']) || isset($_GET['startDate']) ? " AND" : " WHERE";
        $sql .= " h.checkDate <= :endDate";
        $params[':endDate'] = $_GET['endDate'];
    }
    
    // Add order by
    $sql .= " ORDER BY h.checkDate DESC";
    
    // Execute query
    $healthChecks = selectQuery($sql, $params);
    
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
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
