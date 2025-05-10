<?php
require_once __DIR__ . '/../../model/hive/hive.php';
require_once __DIR__ . '/../../model/hive/health.php';
require_once __DIR__ . '/../../model/production/production.php';

class HiveController {
    private $hiveModel;
    private $healthModel;
    private $productionModel;

    public function __construct() {
        $this->hiveModel = new Hive();
        $this->healthModel = new HiveHealth();
        $this->productionModel = new Production();
    }

    public function handleRequest($action, $params) {
        switch ($action) {
            case 'add':
                return $this->addHive($params);
            case 'update':
                return $this->updateHive($params);
            case 'delete':
                return $this->deleteHive($params);
            case 'getAll':
                return $this->getAllHives();
            case 'getOne':
                return $this->getHive($params);
            default:
                return ['success' => false, 'error' => 'Invalid action'];
        }
    }

    private function addHive($params) {
        if (!isset($params['hiveNumber'], $params['location'])) {
            return ['success' => false, 'error' => 'Missing required parameters'];
        }

        try {
            $result = $this->hiveModel->addHive($params);
            return ['success' => true, 'message' => 'Hive added successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function updateHive($params) {
        if (!isset($params['hiveID'])) {
            return ['success' => false, 'error' => 'Missing hive ID'];
        }

        try {
            $result = $this->hiveModel->updateHive($params);
            return ['success' => true, 'message' => 'Hive updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function deleteHive($params) {
        if (!isset($params['hiveID'])) {
            return ['success' => false, 'error' => 'Missing hive ID'];
        }

        try {
            $result = $this->hiveModel->deleteHive($params['hiveID']);
            return ['success' => true, 'message' => 'Hive deleted successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function getAllHives() {
        try {
            $hives = $this->hiveModel->getAllHives();
            
            // Enrich each hive with its latest health check and production summary
            foreach ($hives as &$hive) {
                $hive['lastHealth'] = $this->healthModel->getLatestHealthCheck($hive['hiveID']);
                $hive['productionSummary'] = $this->getProductionSummary($hive['hiveID']);
            }
            
            return ['success' => true, 'data' => $hives];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function getHive($params) {
        if (!isset($params['hiveID'])) {
            return ['success' => false, 'error' => 'Missing hive ID'];
        }

        try {
            $hive = $this->hiveModel->getHive($params['hiveID']);
            if (!$hive) {
                return ['success' => false, 'error' => 'Hive not found'];
            }

            // Get health history
            $hive['healthHistory'] = $this->healthModel->getHealthChecks($params['hiveID']);
            
            // Get production history
            $hive['productionHistory'] = $this->getProductionHistory($params['hiveID']);
            
            // Get latest health check
            $hive['lastHealth'] = $this->healthModel->getLatestHealthCheck($params['hiveID']);
            
            // Get production summary
            $hive['productionSummary'] = $this->getProductionSummary($params['hiveID']);

            return ['success' => true, 'data' => $hive];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function getProductionHistory($hiveID) {
        try {
            // Get all production records for the hive
            $query = "SELECT * FROM production WHERE hiveID = :hiveID ORDER BY harvestDate DESC";
            $stmt = $this->productionModel->conn->prepare($query);
            $stmt->bindParam(':hiveID', $hiveID);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getProductionSummary($hiveID) {
        try {
            // Get summary of production for the hive
            $query = "SELECT 
                        SUM(quantity) as totalProduction,
                        COUNT(*) as harvestCount,
                        MAX(harvestDate) as lastHarvestDate,
                        GROUP_CONCAT(DISTINCT type) as productTypes
                     FROM production 
                     WHERE hiveID = :hiveID";
            $stmt = $this->productionModel->conn->prepare($query);
            $stmt->bindParam(':hiveID', $hiveID);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }
}
