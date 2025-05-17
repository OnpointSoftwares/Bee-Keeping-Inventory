<?php
require_once __DIR__ . '/../../model/hive/health.php';

class HealthController {
    private $healthModel;

    public function __construct() {
        $this->healthModel = new HiveHealth();
    }

    public function handleRequest($action, $params) {
        switch ($action) {
            case 'add':
                return $this->addHealthCheck($params);
            case 'get':
                return $this->getHealthChecks($params);
            default:
                return ['success' => false, 'error' => 'Invalid action'];
        }
    }

    private function addHealthCheck($params) {
        if (!isset($params['hiveID'], $params['checkDate'], $params['queenPresent'], $params['colonyStrength'])) {
            return ['success' => false, 'error' => 'Missing required parameters'];
        }

        try {
            $result = $this->healthModel->addHealthCheck($params);
            return ['success' => true, 'message' => 'Health check added successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function getHealthChecks($params) {
        if (!isset($params['hiveID'])) {
            return ['success' => false, 'error' => 'Missing hive ID'];
        }

        try {
            $data = $this->healthModel->getHealthChecks($params['hiveID']);
            return ['success' => true, 'data' => $data];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
