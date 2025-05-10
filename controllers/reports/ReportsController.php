<?php
/**
 * Reports Controller
 * Handles all report generation requests
 */
class ReportsController {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        require_once __DIR__ . '/../../utils/database_utils.php';
        
        $database = new Database();
        $this->db = $database->connect();
    }
    
    /**
     * Handle incoming requests
     */
    public function handleRequest($action, $params = []) {
        switch ($action) {
            case 'getProductionReport':
                return $this->getProductionReport($params);
            case 'getEquipmentReport':
                return $this->getEquipmentReport($params);
            case 'getHealthReport':
                return $this->getHealthReport($params);
            case 'generateCustomReport':
                return $this->generateCustomReport($params);
            default:
                return ['success' => false, 'error' => 'Invalid action'];
        }
    }
    
    /**
     * Get production report data
     */
    public function getProductionReport($params) {
        try {
            // Get date ranges
            $startDate = isset($params['startDate']) ? $params['startDate'] : date('Y-m-01'); // First day of current month
            $endDate = isset($params['endDate']) ? $params['endDate'] : date('Y-m-d'); // Today
            
            // Get monthly data
            $monthlyStart = date('Y-m-01'); // First day of current month
            $monthlyEnd = date('Y-m-d'); // Today
            
            $monthlyQuery = "SELECT 
                SUM(quantity) as totalProduction,
                AVG(quality) as avgQuality,
                COUNT(DISTINCT hiveID) as activeHives
                FROM honey_production
                WHERE harvestDate BETWEEN :startDate AND :endDate";
                
            $monthlyStmt = $this->db->prepare($monthlyQuery);
            $monthlyStmt->bindParam(':startDate', $monthlyStart);
            $monthlyStmt->bindParam(':endDate', $monthlyEnd);
            $monthlyStmt->execute();
            $monthlyData = $monthlyStmt->fetch(PDO::FETCH_ASSOC);
            
            // Get yearly data
            $yearlyStart = date('Y-01-01'); // First day of current year
            $yearlyEnd = date('Y-m-d'); // Today
            
            $yearlyStmt = $this->db->prepare($monthlyQuery); // Same query structure, different dates
            $yearlyStmt->bindParam(':startDate', $yearlyStart);
            $yearlyStmt->bindParam(':endDate', $yearlyEnd);
            $yearlyStmt->execute();
            $yearlyData = $yearlyStmt->fetch(PDO::FETCH_ASSOC);
            
            // Format the data
            $report = [
                'monthly' => [
                    'totalProduction' => round($monthlyData['totalProduction'] ?? 0, 2),
                    'avgQuality' => round($monthlyData['avgQuality'] ?? 0, 1),
                    'activeHives' => $monthlyData['activeHives'] ?? 0
                ],
                'yearly' => [
                    'totalProduction' => round($yearlyData['totalProduction'] ?? 0, 2),
                    'avgQuality' => round($yearlyData['avgQuality'] ?? 0, 1),
                    'activeHives' => $yearlyData['activeHives'] ?? 0
                ]
            ];
            
            return ['success' => true, 'report' => $report];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Get equipment report data
     */
    public function getEquipmentReport($params) {
        try {
            $query = "SELECT 
                type,
                COUNT(*) as totalCount,
                SUM(CASE WHEN condition_status = 'Good' THEN 1 ELSE 0 END) as goodCondition,
                SUM(CASE WHEN condition_status != 'Good' THEN 1 ELSE 0 END) as needsAttention
                FROM equipment
                GROUP BY type";
                
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $report = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return ['success' => true, 'report' => $report];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Get health report data
     */
    public function getHealthReport($params) {
        try {
            // Get colony strength metrics
            $strengthQuery = "SELECT 
                SUM(CASE WHEN colonyStrength >= 7 THEN 1 ELSE 0 END) as good,
                SUM(CASE WHEN colonyStrength >= 4 AND colonyStrength < 7 THEN 1 ELSE 0 END) as warning,
                SUM(CASE WHEN colonyStrength < 4 THEN 1 ELSE 0 END) as critical
                FROM hive_health
                WHERE checkDate >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
                
            $strengthStmt = $this->db->prepare($strengthQuery);
            $strengthStmt->execute();
            $strengthData = $strengthStmt->fetch(PDO::FETCH_ASSOC);
            
            // Get disease status metrics
            $diseaseQuery = "SELECT 
                SUM(CASE WHEN diseaseSymptoms = 'None' THEN 1 ELSE 0 END) as good,
                SUM(CASE WHEN diseaseSymptoms = 'Mild' THEN 1 ELSE 0 END) as warning,
                SUM(CASE WHEN diseaseSymptoms IN ('Moderate', 'Severe') THEN 1 ELSE 0 END) as critical
                FROM hive_health
                WHERE checkDate >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
                
            $diseaseStmt = $this->db->prepare($diseaseQuery);
            $diseaseStmt->execute();
            $diseaseData = $diseaseStmt->fetch(PDO::FETCH_ASSOC);
            
            // Compile the report
            $report = [
                'colonyStrength' => $strengthData,
                'diseaseStatus' => $diseaseData,
                'foodStores' => [
                    'good' => 0,
                    'warning' => 0,
                    'critical' => 0
                ] // Placeholder for food stores data
            ];
            
            return ['success' => true, 'report' => $report];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Generate a custom report based on user parameters
     */
    public function generateCustomReport($params) {
        try {
            $reportType = $params['reportType'] ?? '';
            $startDate = $params['startDate'] ?? date('Y-m-01');
            $endDate = $params['endDate'] ?? date('Y-m-d');
            
            if (empty($reportType)) {
                return ['success' => false, 'error' => 'Report type is required'];
            }
            
            $report = [];
            
            switch ($reportType) {
                case 'honeyProduction':
                    // Get production data by hive
                    $query = "SELECT 
                        h.hiveNumber,
                        SUM(p.quantity) as totalProduction,
                        AVG(p.quality) as avgQuality,
                        COUNT(p.productionID) as harvestCount
                        FROM honey_production p
                        JOIN beehive h ON p.hiveID = h.hiveID
                        WHERE p.harvestDate BETWEEN :startDate AND :endDate
                        GROUP BY p.hiveID
                        ORDER BY totalProduction DESC";
                        
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':startDate', $startDate);
                    $stmt->bindParam(':endDate', $endDate);
                    $stmt->execute();
                    $report['byHive'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Get production data by type
                    $typeQuery = "SELECT 
                        type,
                        SUM(quantity) as totalProduction,
                        AVG(quality) as avgQuality
                        FROM honey_production
                        WHERE harvestDate BETWEEN :startDate AND :endDate
                        GROUP BY type
                        ORDER BY totalProduction DESC";
                        
                    $typeStmt = $this->db->prepare($typeQuery);
                    $typeStmt->bindParam(':startDate', $startDate);
                    $typeStmt->bindParam(':endDate', $endDate);
                    $typeStmt->execute();
                    $report['byType'] = $typeStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Format the data for better display
                    foreach ($report['byHive'] as &$hive) {
                        $hive['totalProduction'] = round($hive['totalProduction'], 2);
                        $hive['avgQuality'] = round($hive['avgQuality'], 1);
                    }
                    
                    foreach ($report['byType'] as &$type) {
                        $type['totalProduction'] = round($type['totalProduction'], 2);
                        $type['avgQuality'] = round($type['avgQuality'], 1);
                    }
                    break;
                    
                case 'hiveHealth':
                    // Get health data by hive
                    $query = "SELECT 
                        h.hiveNumber,
                        AVG(hh.colonyStrength) as avgStrength,
                        MAX(CASE WHEN hh.diseaseSymptoms != 'None' THEN 1 ELSE 0 END) as hasDisease,
                        MAX(CASE WHEN hh.pestProblems != 'None' THEN 1 ELSE 0 END) as hasPests,
                        COUNT(hh.healthID) as checkCount
                        FROM hive_health hh
                        JOIN beehive h ON hh.hiveID = h.hiveID
                        WHERE hh.checkDate BETWEEN :startDate AND :endDate
                        GROUP BY hh.hiveID
                        ORDER BY avgStrength DESC";
                        
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':startDate', $startDate);
                    $stmt->bindParam(':endDate', $endDate);
                    $stmt->execute();
                    $report['byHive'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Format the data
                    foreach ($report['byHive'] as &$hive) {
                        $hive['avgStrength'] = round($hive['avgStrength'], 1);
                        $hive['status'] = $hive['avgStrength'] >= 7 ? 'Good' : ($hive['avgStrength'] >= 4 ? 'Warning' : 'Critical');
                    }
                    break;
                    
                case 'equipmentUsage':
                    // Get equipment usage data
                    $query = "SELECT 
                        type,
                        COUNT(*) as totalCount,
                        SUM(CASE WHEN condition_status = 'Good' THEN 1 ELSE 0 END) as goodCondition,
                        SUM(CASE WHEN condition_status = 'Fair' THEN 1 ELSE 0 END) as fairCondition,
                        SUM(CASE WHEN condition_status = 'Poor' THEN 1 ELSE 0 END) as poorCondition,
                        AVG(DATEDIFF(CURDATE(), purchaseDate) / 365) as avgAge
                        FROM equipment
                        GROUP BY type
                        ORDER BY totalCount DESC";
                        
                    $stmt = $this->db->prepare($query);
                    $stmt->execute();
                    $report['byType'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Format the data
                    foreach ($report['byType'] as &$type) {
                        $type['avgAge'] = round($type['avgAge'], 1);
                    }
                    break;
                    
                default:
                    return ['success' => false, 'error' => 'Invalid report type'];
            }
            
            return [
                'success' => true, 
                'report' => $report,
                'params' => [
                    'reportType' => $reportType,
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ]
            ];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
?>
