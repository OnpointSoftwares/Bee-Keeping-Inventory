<?php
/**
 * Data Loader
 * 
 * This file handles all data loading for the Beekeeping Inventory Management System
 * using the standardized model classes
 */

// Include model classes
require_once __DIR__ . '/../model/production/production.php';
require_once __DIR__ . '/../model/hive/Beehive.php';
require_once __DIR__ . '/../model/equipment/Equipment.php';
require_once __DIR__ . '/../model/health/HiveHealth.php';

// Initialize models
$productionModel = new Production();
$beehiveModel = new Beehive();
$equipmentModel = new Equipment();
$healthModel = new HiveHealth();

// Get production data
$productionData = $productionModel->getAllProduction();

// Calculate total honey production
$totalHoneyResult = $productionModel->executeQuery("SELECT SUM(quantity) as totalHoney FROM honey_production", [], false);
$totalHoney = $totalHoneyResult['totalHoney'] ?? 0;

// Get production by type
$productionByTypeData = $productionModel->executeQuery("SELECT type, SUM(quantity) as totalQuantity FROM honey_production GROUP BY type");

// Get hives data
$hivesData = $beehiveModel->getAllBeehives();

// Get equipment data
$equipmentData = $equipmentModel->findBy("status = :status", ['status' => 'Active']);

// Get equipment summary for chart
$equipmentSummaryData = $equipmentModel->executeQuery("SELECT type, SUM(quantity) as totalQuantity FROM equipment WHERE status = 'Active' GROUP BY type");

// Get health check data
$healthCheckData = $healthModel->getAllHealthChecks();

// Get health summary data
$healthSummary = $healthModel->executeQuery("SELECT 
                     COUNT(*) as totalChecks,
                     SUM(CASE WHEN colonyStrength = 'Strong' THEN 1 ELSE 0 END) as strongCount,
                     SUM(CASE WHEN colonyStrength = 'Medium' THEN 1 ELSE 0 END) as mediumCount,
                     SUM(CASE WHEN colonyStrength = 'Weak' THEN 1 ELSE 0 END) as weakCount,
                     SUM(CASE WHEN colonyStrength = 'Critical' THEN 1 ELSE 0 END) as criticalCount,
                     SUM(CASE WHEN queenPresent = 'Yes' THEN 1 ELSE 0 END) as queenPresentCount,
                     SUM(CASE WHEN diseaseSymptoms != 'None' THEN 1 ELSE 0 END) as diseaseCount,
                     SUM(CASE WHEN pestProblems != 'None' THEN 1 ELSE 0 END) as pestCount
                     FROM hive_health", [], false);

// Get latest health check for each hive
$latestHealthChecks = $healthModel->executeQuery("SELECT h1.*, b.hiveNumber
                         FROM hive_health h1
                         JOIN beehive b ON h1.hiveID = b.hiveID
                         JOIN (
                             SELECT hiveID, MAX(checkDate) as maxDate
                             FROM hive_health
                             GROUP BY hiveID
                         ) h2 ON h1.hiveID = h2.hiveID AND h1.checkDate = h2.maxDate
                         ORDER BY b.hiveNumber");

// Get health timeline data for issues
$healthTimelineData = [];
foreach ($healthCheckData as $check) {
    if ($check['diseaseSymptoms'] != 'None' || $check['pestProblems'] != 'None' || 
        $check['queenPresent'] == 'No' || $check['colonyStrength'] == 'Weak' || 
        $check['colonyStrength'] == 'Critical') {
        
        $statusClass = 'bg-warning';
        $issueDescription = '';
        
        if ($check['colonyStrength'] == 'Critical' || ($check['queenPresent'] == 'No' && $check['diseaseSymptoms'] != 'None')) {
            $statusClass = 'bg-danger';
        }
        
        if ($check['diseaseSymptoms'] != 'None') {
            $issueDescription .= 'Disease: ' . $check['diseaseSymptoms'] . '. ';
        }
        
        if ($check['pestProblems'] != 'None') {
            $issueDescription .= 'Pests: ' . $check['pestProblems'] . '. ';
        }
        
        if ($check['queenPresent'] == 'No') {
            $issueDescription .= 'Queen absent. ';
        }
        
        if ($check['colonyStrength'] == 'Weak' || $check['colonyStrength'] == 'Critical') {
            $issueDescription .= 'Low colony strength (' . $check['colonyStrength'] . '). ';
        }
        
        $healthTimelineData[] = [
            'hiveNumber' => $check['hiveNumber'],
            'checkDate' => $check['checkDate'],
            'statusClass' => $statusClass,
            'issueDescription' => $issueDescription
        ];
    }
}

// Generate health recommendations based on data
$healthRecommendations = [];
foreach ($latestHealthChecks as $check) {
    if ($check['queenPresent'] == 'No') {
        $healthRecommendations[] = [
            'title' => 'Queen Replacement Needed',
            'description' => 'This hive has no queen present. Consider replacing the queen as soon as possible.',
            'priority' => 'High',
            'hiveNumber' => $check['hiveNumber']
        ];
    }
    
    if ($check['diseaseSymptoms'] != 'None') {
        $healthRecommendations[] = [
            'title' => 'Disease Treatment Required',
            'description' => 'Disease symptoms detected: ' . $check['diseaseSymptoms'],
            'priority' => 'High',
            'hiveNumber' => $check['hiveNumber']
        ];
    }
    
    if ($check['pestProblems'] != 'None') {
        $healthRecommendations[] = [
            'title' => 'Pest Control Needed',
            'description' => 'Pest problems detected: ' . $check['pestProblems'],
            'priority' => 'Medium',
            'hiveNumber' => $check['hiveNumber']
        ];
    }
    
    if ($check['colonyStrength'] == 'Critical') {
        $healthRecommendations[] = [
            'title' => 'Colony Strength Critical',
            'description' => 'Colony strength is very low. Consider combining with another colony or providing additional support.',
            'priority' => 'High',
            'hiveNumber' => $check['hiveNumber']
        ];
    } else if ($check['colonyStrength'] == 'Weak') {
        $healthRecommendations[] = [
            'title' => 'Colony Strength Needs Improvement',
            'description' => 'Colony strength is below optimal. Consider providing additional feeding or support.',
            'priority' => 'Medium',
            'hiveNumber' => $check['hiveNumber']
        ];
    }
}
?>
