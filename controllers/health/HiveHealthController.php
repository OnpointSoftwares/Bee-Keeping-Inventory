<?php
/**
 * Hive Health Controller
 * 
 * This controller handles all beehive health-related operations
 */
require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../model/health/HiveHealth.php';

class HiveHealthController extends BaseController {
    private $healthModel;

    public function __construct() {
        $this->healthModel = new HiveHealth();
    }

    public function handleRequest($action, $params) {
        switch ($action) {
            case 'add':
                return $this->addHealthCheck($params);
            case 'update':
                return $this->updateHealthCheck($params);
            case 'delete':
                return $this->deleteHealthCheck($params);
            case 'getAll':
                return $this->getAllHealthChecks();
            case 'getById':
                return $this->getHealthCheckById($params);
            case 'getByHive':
                return $this->getHealthChecksByHive($params);
            case 'getByDateRange':
                return $this->getHealthChecksByDateRange($params);
            case 'getSummary':
                return $this->getHealthSummary();
            case 'getIssues':
                return $this->getHivesWithHealthIssues();
            default:
                return $this->error('Invalid action');
        }
    }

    private function addHealthCheck($params) {
        $validation = $this->validateParams($params, ['hiveID', 'checkDate']);
        if ($validation !== true) {
            return $this->error($validation);
        }

        try {
            $result = $this->healthModel->addHealthCheck($params);
            return $this->success(['id' => $result], 'Health check added successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function updateHealthCheck($params) {
        $validation = $this->validateParams($params, ['healthID', 'hiveID', 'checkDate']);
        if ($validation !== true) {
            return $this->error($validation);
        }

        try {
            $result = $this->healthModel->updateHealthCheck($params);
            return $this->success(null, 'Health check updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function deleteHealthCheck($params) {
        $validation = $this->validateParams($params, ['healthID']);
        if ($validation !== true) {
            return $this->error($validation);
        }

        try {
            $result = $this->healthModel->deleteHealthCheck($params['healthID']);
            return $this->success(null, 'Health check deleted successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function getAllHealthChecks() {
        try {
            $data = $this->healthModel->getAllHealthChecks();
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function getHealthCheckById($params) {
        $validation = $this->validateParams($params, ['healthID']);
        if ($validation !== true) {
            return $this->error($validation);
        }

        try {
            $data = $this->healthModel->getHealthCheckById($params['healthID']);
            if (!$data) {
                return $this->error('Health check not found', 404);
            }
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function getHealthChecksByHive($params) {
        $validation = $this->validateParams($params, ['hiveID']);
        if ($validation !== true) {
            return $this->error($validation);
        }

        try {
            $data = $this->healthModel->getHealthChecksByHive($params['hiveID']);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function getHealthChecksByDateRange($params) {
        $validation = $this->validateParams($params, ['startDate', 'endDate']);
        if ($validation !== true) {
            return $this->error($validation);
        }

        try {
            $data = $this->healthModel->getHealthChecksByDateRange($params['startDate'], $params['endDate']);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function getHealthSummary() {
        try {
            $data = $this->healthModel->getHealthSummary();
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function getHivesWithHealthIssues() {
        try {
            $data = $this->healthModel->getHivesWithHealthIssues();
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
?>
