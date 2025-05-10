<?php
/**
 * Beehive Controller
 * 
 * This controller handles all beehive-related operations
 */
require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../model/hive/Beehive.php';

class BeehiveController extends BaseController {
    private $beehiveModel;

    public function __construct() {
        $this->beehiveModel = new Beehive();
    }

    public function handleRequest($action, $params) {
        switch ($action) {
            case 'add':
                return $this->addBeehive($params);
            case 'update':
                return $this->updateBeehive($params);
            case 'delete':
                return $this->deleteBeehive($params);
            case 'getAll':
                return $this->getAllBeehives();
            case 'getById':
                return $this->getBeehiveById($params);
            case 'getByStatus':
                return $this->getBeehivesByStatus($params);
            case 'getHealthHistory':
                return $this->getHealthHistory($params);
            case 'getProductionHistory':
                return $this->getProductionHistory($params);
            case 'getSummary':
                return $this->getBeehiveSummary($params);
            default:
                return $this->error('Invalid action');
        }
    }

    private function addBeehive($params) {
        $validation = $this->validateParams($params, ['hiveNumber', 'location', 'dateEstablished']);
        if ($validation !== true) {
            return $this->error($validation);
        }

        try {
            $result = $this->beehiveModel->addBeehive($params);
            return $this->success(['id' => $result], 'Beehive added successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function updateBeehive($params) {
        $validation = $this->validateParams($params, ['hiveID', 'hiveNumber', 'location']);
        if ($validation !== true) {
            return $this->error($validation);
        }

        try {
            $result = $this->beehiveModel->updateBeehive($params);
            return $this->success(null, 'Beehive updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function deleteBeehive($params) {
        $validation = $this->validateParams($params, ['hiveID']);
        if ($validation !== true) {
            return $this->error($validation);
        }

        try {
            $result = $this->beehiveModel->deleteBeehive($params['hiveID']);
            return $this->success(null, 'Beehive deleted successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function getAllBeehives() {
        try {
            $data = $this->beehiveModel->getAllBeehives();
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function getBeehiveById($params) {
        $validation = $this->validateParams($params, ['hiveID']);
        if ($validation !== true) {
            return $this->error($validation);
        }

        try {
            $data = $this->beehiveModel->getBeehiveById($params['hiveID']);
            if (!$data) {
                return $this->error('Beehive not found', 404);
            }
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function getBeehivesByStatus($params) {
        $validation = $this->validateParams($params, ['status']);
        if ($validation !== true) {
            return $this->error($validation);
        }

        try {
            $data = $this->beehiveModel->getBeehivesByStatus($params['status']);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function getHealthHistory($params) {
        $validation = $this->validateParams($params, ['hiveID']);
        if ($validation !== true) {
            return $this->error($validation);
        }

        try {
            $data = $this->beehiveModel->getHealthHistory($params['hiveID']);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function getProductionHistory($params) {
        $validation = $this->validateParams($params, ['hiveID']);
        if ($validation !== true) {
            return $this->error($validation);
        }

        try {
            $data = $this->beehiveModel->getProductionHistory($params['hiveID']);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function getBeehiveSummary($params) {
        $validation = $this->validateParams($params, ['hiveID']);
        if ($validation !== true) {
            return $this->error($validation);
        }

        try {
            $data = $this->beehiveModel->getBeehiveSummary($params['hiveID']);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
?>
