<?php
require_once('../inc/config/db.php');

class Equipment {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function addEquipment($data) {
        $query = "INSERT INTO equipment (name, type, quantity, condition_status, purchaseDate, notes, status) 
                 VALUES (:name, :type, :quantity, :condition, :purchaseDate, :notes, 'Active')";
        
        try {
            $name = $data['name'];
            $type = $data['type'];
            $quantity = $data['quantity'];
            $condition = $data['condition_status'];
            $purchaseDate = $data['purchaseDate'];
            $notes = $data['notes'];
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':condition', $condition);
            $stmt->bindParam(':purchaseDate', $purchaseDate);
            $stmt->bindParam(':notes', $notes);
            
            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Equipment added successfully'];
            }
            return ['success' => false, 'error' => 'Failed to add equipment'];
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function updateEquipment($data) {
        $query = "UPDATE equipment 
                 SET name = :name, 
                     type = :type, 
                     quantity = :quantity, 
                     condition_status = :condition, 
                     notes = :notes 
                 WHERE equipmentID = :equipmentID";
        
        try {
            $equipmentID = $data['equipmentID'];
            $name = $data['name'];
            $type = $data['type'];
            $quantity = $data['quantity'];
            $condition = $data['condition_status'];
            $notes = $data['notes'];
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':equipmentID', $equipmentID);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':condition', $condition);
            $stmt->bindParam(':notes', $notes);
            
            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Equipment updated successfully'];
            }
            return ['success' => false, 'error' => 'Failed to update equipment'];
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getAllEquipment() {
        $query = "SELECT * FROM equipment WHERE status = 'Active' ORDER BY type, name";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getEquipmentByType($type) {
        $query = "SELECT * FROM equipment WHERE type = :type AND status = 'Active' ORDER BY name";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':type', $type);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function deleteEquipment($equipmentID) {
        $query = "UPDATE equipment SET status = 'Inactive' WHERE equipmentID = :equipmentID";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':equipmentID', $equipmentID);
            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Equipment deleted successfully'];
            }
            return ['success' => false, 'error' => 'Failed to delete equipment'];
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getInventoryReport() {
        $query = "SELECT type, 
                        COUNT(*) as itemCount,
                        SUM(quantity) as totalQuantity,
                        GROUP_CONCAT(DISTINCT condition_status) as conditions
                 FROM equipment 
                 WHERE status = 'Active'
                 GROUP BY type
                 ORDER BY type";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $report = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalItems = array_reduce($report, function($carry, $item) {
                return $carry + $item['totalQuantity'];
            }, 0);
            
            return [
                'success' => true,
                'report' => $report,
                'totalItems' => $totalItems
            ];
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
}
