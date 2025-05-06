<?php
require_once(__DIR__ . '/../../model/hive/hive.php');
require_once(__DIR__ . '/../../model/hive/health.php');

class HiveController {
    private $hiveModel;
    private $healthModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->hiveModel = new Hive($db);
        $this->healthModel = new HiveHealth($db);
    }

    public function handleRequest($params = []) {
        try {
            $action = isset($params['action']) ? $params['action'] : '';
            
            switch($action) {
                case 'add':
                    return $this->hiveModel->addHive($params);
                    
                case 'update':
                    return $this->hiveModel->updateHive($params);
                    
                case 'delete':
                    return $this->hiveModel->deleteHive($params['hiveID']);
                    
                case 'getAll':
                    $hives = $this->hiveModel->getAllHives();
                    // Add latest health check info to each hive
                    foreach($hives as &$hive) {
                        $hive['lastHealth'] = $this->healthModel->getLatestHealthCheck($hive['hiveID']);
                    }
                    return ['success' => true, 'data' => $hives];
                    
                case 'getOne':
                    $hive = $this->hiveModel->getHive($params['hiveID']);
                    if (isset($hive['hiveID'])) {
                        $hive['healthHistory'] = $this->healthModel->getHealthHistory($hive['hiveID']);
                        return ['success' => true, 'data' => $hive];
                    }
                    return ['success' => false, 'error' => 'Hive not found'];
                    
                case 'addHealthCheck':
                    return $this->healthModel->addHealthCheck($params);
                    
                case 'getHealthHistory':
                    $history = $this->healthModel->getHealthHistory($params['hiveID']);
                    return ['success' => true, 'data' => $history];
                    
                default:
                    return ['success' => false, 'error' => 'Invalid action specified'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
