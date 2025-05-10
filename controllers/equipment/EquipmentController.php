<?php
require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../model/equipment/Equipment.php';

class EquipmentController extends BaseController {
    private $equipmentModel;
    
    public function __construct() {
        $this->equipmentModel = new Equipment();
    }
    
    public function handleRequest($action, $params) {
        switch($action) {
            case 'add':
                return $this->addEquipment($params);
            case 'update':
                return $this->updateEquipment($params);
            case 'delete':
                return $this->deleteEquipment($params);
            case 'getAll':
                return $this->getAllEquipment();
            case 'getById':
                return $this->getEquipmentById($params);
            case 'getByType':
                return $this->getEquipmentByType($params);
            case 'getByCondition':
                return $this->getEquipmentByCondition($params);
            case 'getInventorySummary':
                return $this->getInventorySummary();
            default:
                return $this->error('Invalid action');
        }
    }
    
    private function addEquipment($params) {
        $validation = $this->validateParams($params, ['name', 'type', 'quantity']);
        if ($validation !== true) {
            return $this->error($validation);
        }
        
        try {
            $result = $this->equipmentModel->addEquipment($params);
            return $this->success(['id' => $result], 'Equipment added successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
    
    private function updateEquipment($params) {
        $validation = $this->validateParams($params, ['equipmentID', 'name', 'type', 'quantity']);
        if ($validation !== true) {
            return $this->error($validation);
        }
        
        try {
            $result = $this->equipmentModel->updateEquipment($params);
            return $this->success(null, 'Equipment updated successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
    
    private function deleteEquipment($params) {
        $validation = $this->validateParams($params, ['equipmentID']);
        if ($validation !== true) {
            return $this->error($validation);
        }
        
        try {
            $result = $this->equipmentModel->deleteEquipment($params['equipmentID']);
            return $this->success(null, 'Equipment deleted successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
    
    private function getAllEquipment() {
        try {
            $equipment = $this->equipmentModel->getAllEquipment();
            return $this->success($equipment);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
    
    private function getEquipmentById($params) {
        $validation = $this->validateParams($params, ['equipmentID']);
        if ($validation !== true) {
            return $this->error($validation);
        }
        
        try {
            $equipment = $this->equipmentModel->getEquipmentById($params['equipmentID']);
            if (!$equipment) {
                return $this->error('Equipment not found', 404);
            }
            return $this->success($equipment);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
    
    private function getEquipmentByType($params) {
        $validation = $this->validateParams($params, ['type']);
        if ($validation !== true) {
            return $this->error($validation);
        }
        
        try {
            $equipment = $this->equipmentModel->getEquipmentByType($params['type']);
            return $this->success($equipment);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
    
    private function getEquipmentByCondition($params) {
        $validation = $this->validateParams($params, ['condition']);
        if ($validation !== true) {
            return $this->error($validation);
        }
        
        try {
            $equipment = $this->equipmentModel->getEquipmentByCondition($params['condition']);
            return $this->success($equipment);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
    
    private function getInventorySummary() {
        try {
            $summary = $this->equipmentModel->getInventorySummary();
            return $this->success($summary);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
