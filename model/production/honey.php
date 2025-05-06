<?php
require_once(__DIR__ . '/../../inc/config/db.php');

class HoneyProduction {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function addProduction($data) {
        $query = "INSERT INTO honey_production (hiveID, harvestDate, quantity, type, quality, notes) 
                 VALUES (:hiveID, :harvestDate, :quantity, :type, :quality, :notes)";
        
        try {
            $hiveID = $data['hiveID'];
            $harvestDate = $data['harvestDate'] ?? date('Y-m-d');
            $quantity = $data['quantity'];
            $type = $data['type'];
            $quality = $data['quality'] ?? 'Standard';
            $notes = $data['notes'] ?? '';
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':hiveID', $hiveID);
            $stmt->bindParam(':harvestDate', $harvestDate);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':quality', $quality);
            $stmt->bindParam(':notes', $notes);
            
            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Production record added successfully'];
            }
            return ['success' => false, 'error' => 'Failed to add production record'];
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getHiveProduction($hiveID) {
        $query = "SELECT * FROM honey_production WHERE hiveID = :hiveID ORDER BY harvestDate DESC";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':hiveID', $hiveID);
            $stmt->execute();
            return ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getTotalProduction($startDate, $endDate) {
        $query = "SELECT h.hiveNumber, 
                        SUM(hp.quantity) as totalQuantity,
                        COUNT(hp.productionID) as harvestCount
                 FROM honey_production hp
                 JOIN beehive h ON hp.hiveID = h.hiveID
                 WHERE hp.harvestDate BETWEEN :startDate AND :endDate
                 GROUP BY h.hiveID
                 ORDER BY totalQuantity DESC";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':startDate', $startDate);
            $stmt->bindParam(':endDate', $endDate);
            $stmt->execute();
            return ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getProductionByType($startDate, $endDate) {
        $query = "SELECT type, 
                        SUM(quantity) as totalQuantity,
                        COUNT(productionID) as harvestCount
                 FROM honey_production 
                 WHERE harvestDate BETWEEN :startDate AND :endDate
                 GROUP BY type
                 ORDER BY totalQuantity DESC";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':startDate', $startDate);
            $stmt->bindParam(':endDate', $endDate);
            $stmt->execute();
            return ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
}
