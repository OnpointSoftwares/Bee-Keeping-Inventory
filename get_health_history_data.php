<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database utilities
require_once 'utils/database_utils.php';

// Set header to return JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'User not authenticated'
    ]);
    exit;
}

try {
    // Get health status distribution
    $statusQuery = "SELECT 
                    CASE 
                        WHEN colonyStrength < 3 OR (queenPresent = 0 AND diseaseSymptoms != '') THEN 'Critical'
                        WHEN colonyStrength < 5 OR queenPresent = 0 OR diseaseSymptoms != '' OR pestProblems != '' THEN 'Issues Detected'
                        ELSE 'Healthy'
                    END as status,
                    COUNT(*) as count
                    FROM hive_health
                    GROUP BY 
                    CASE 
                        WHEN colonyStrength < 3 OR (queenPresent = 0 AND diseaseSymptoms != '') THEN 'Critical'
                        WHEN colonyStrength < 5 OR queenPresent = 0 OR diseaseSymptoms != '' OR pestProblems != '' THEN 'Issues Detected'
                        ELSE 'Healthy'
                    END
                    ORDER BY count DESC";
    $statusDistribution = selectQuery($statusQuery, []);
    
    // Get colony strength trend
    $strengthQuery = "SELECT checkDate, colonyStrength 
                     FROM hive_health 
                     ORDER BY checkDate ASC";
    $strengthTrend = selectQuery($strengthQuery, []);
    
    // Get issues breakdown
    $issuesQuery = "SELECT 'Disease Symptoms' as issue, COUNT(*) as count 
                   FROM hive_health 
                   WHERE diseaseSymptoms != '' 
                   UNION ALL
                   SELECT 'Pest Problems' as issue, COUNT(*) as count 
                   FROM hive_health 
                   WHERE pestProblems != '' 
                   UNION ALL
                   SELECT 'Queen Absent' as issue, COUNT(*) as count 
                   FROM hive_health 
                   WHERE queenPresent = 0 
                   UNION ALL
                   SELECT 'Low Strength' as issue, COUNT(*) as count 
                   FROM hive_health 
                   WHERE colonyStrength < 5 
                   ORDER BY count DESC";
    $issuesBreakdown = selectQuery($issuesQuery, []);
    
    // Get hive comparison data
    $hiveComparisonQuery = "SELECT b.hiveID, b.hiveNumber, 
                           AVG(h.colonyStrength) as avgStrength,
                           COUNT(CASE WHEN h.diseaseSymptoms != '' OR h.pestProblems != '' OR h.queenPresent = 0 THEN 1 END) as issueCount
                           FROM beehive b
                           LEFT JOIN hive_health h ON b.hiveID = h.hiveID
                           GROUP BY b.hiveID, b.hiveNumber
                           ORDER BY avgStrength DESC";
    $hiveComparison = selectQuery($hiveComparisonQuery, []);
    
    // Return success response with data
    echo json_encode([
        'success' => true,
        'data' => [
            'statusDistribution' => $statusDistribution,
            'strengthTrend' => $strengthTrend,
            'issuesBreakdown' => $issuesBreakdown,
            'hiveComparison' => $hiveComparison
        ]
    ]);
} catch (Exception $e) {
    // Log error
    error_log("Error fetching health history data: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch health history data: ' . $e->getMessage()
    ]);
}
?>
