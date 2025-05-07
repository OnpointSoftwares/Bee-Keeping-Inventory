<?php
require_once __DIR__ . '/../../model/equipment/equipment.php';

class EquipmentController {
    public $equipmentModel;
    public $db;
    
    public function __construct($db) {
        $this->equipmentModel = new Equipment($db);
        $this->db = $db;
    }
    
    public function handleRequest($input) {
        $action = isset($input['action']) ? $input['action'] : '';
        switch($action) {
            case 'add':
                return $this->addEquipment($input);
            case 'update':
                return $this->updateEquipment($input);
            case 'delete':
                return $this->deleteEquipment($input);
            case 'getAll':
                return $this->getAllEquipment();
            case 'getByType':
                return $this->getByType();
            case 'getInventoryReport':
                return $this->getInventoryReport();
            default:
                return ['success' => false, 'error' => 'Invalid action'];
        }
    }
    
    public function addEquipment($input) {
        try {
            $data = [
                'name' => $input['name'] ?? '',
                'type' => $input['type'] ?? '',
                'quantity' => $input['quantity'] ?? 0,
                'condition_status' => $input['condition'] ?? 'New',
                'purchaseDate' => $input['purchaseDate'] ?? date('Y-m-d'),
                'notes' => $input['notes'] ?? ''
            ];
            
            return $this->equipmentModel->addEquipment($data);
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function updateEquipment($input) {
        try {
            $data = [
                'equipmentID' => $input['equipmentID'] ?? 0,
                'name' => $input['name'] ?? '',
                'type' => $input['type'] ?? '',
                'quantity' => $input['quantity'] ?? 0,
                'condition_status' => $input['condition'] ?? 'Used',
                'notes' => $input['notes'] ?? ''
            ];
            
            return $this->equipmentModel->updateEquipment($data);
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function deleteEquipment($input) {
        try {
            $equipmentID = $input['equipmentID'] ?? 0;
            return $this->equipmentModel->deleteEquipment($equipmentID);
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function getAllEquipment() {
        try {
            $equipment = $this->equipmentModel->getAllEquipment();
            error_log('Fetched equipment: ' . print_r($equipment, true)); // Log the fetched equipment
            return ['success' => true, 'data' => $equipment];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function getByType() {
        try {
            $type = $_GET['type'] ?? '';
            return $this->equipmentModel->getByType($type);
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function getInventoryReport() {
        try {
            return $this->equipmentModel->getInventoryReport();
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
