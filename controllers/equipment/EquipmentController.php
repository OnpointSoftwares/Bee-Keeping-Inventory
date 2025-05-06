<?php
require_once __DIR__ . '/../../model/equipment/equipment.php';

class EquipmentController {
    private $equipmentModel;
    
    public function __construct($db) {
        $this->equipmentModel = new Equipment($db);
    }
    
    public function handleRequest() {
        $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
        
        switch($action) {
            case 'add':
                return $this->addEquipment();
            case 'update':
                return $this->updateEquipment();
            case 'delete':
                return $this->deleteEquipment();
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
    
    private function addEquipment() {
        try {
            $data = [
                'name' => $_POST['name'] ?? '',
                'type' => $_POST['type'] ?? '',
                'quantity' => $_POST['quantity'] ?? 0,
                'condition_status' => $_POST['condition'] ?? 'New',
                'purchaseDate' => $_POST['purchaseDate'] ?? date('Y-m-d'),
                'notes' => $_POST['notes'] ?? ''
            ];
            
            return $this->equipmentModel->addEquipment($data);
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    private function updateEquipment() {
        try {
            $data = [
                'equipmentID' => $_POST['equipmentID'] ?? 0,
                'name' => $_POST['name'] ?? '',
                'type' => $_POST['type'] ?? '',
                'quantity' => $_POST['quantity'] ?? 0,
                'condition_status' => $_POST['condition'] ?? 'Used',
                'notes' => $_POST['notes'] ?? ''
            ];
            
            return $this->equipmentModel->updateEquipment($data);
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    private function deleteEquipment() {
        try {
            $equipmentID = $_POST['equipmentID'] ?? 0;
            return $this->equipmentModel->deleteEquipment($equipmentID);
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function getAllEquipment() {
        try {
            return $this->equipmentModel->getAllEquipment();
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    private function getByType() {
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
