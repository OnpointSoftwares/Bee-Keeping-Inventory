<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once(__DIR__ . '/../../inc/config/db.php');

class HoneyProduction {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function addProduction($data) {
        error_log('Adding production (model): ' . json_encode($data));
    
        // Validate required fields
        if (!isset($data['hiveID'], $data['type'], $data['quantity'])) {
            return ['success' => false, 'error' => 'Missing required fields'];
        }
    
        $query = "INSERT INTO honey_production (hiveID, harvestDate, quantity, type, quality, notes) 
                  VALUES (:hiveID, :harvestDate, :quantity, :type, :quality, :notes)";
    
        try {
            // Set default values for optional fields
            $productionData = [
                'hiveID'       => $data['hiveID'],
                'harvestDate'  => $data['harvestDate'] ?? null,
                'quantity'     => (int)$data['quantity'],
                'type'         => $data['type'],
                'quality'      => $data['quality'] ?? null,
                'notes'        => $data['notes'] ?? null
            ];
    
            error_log('Production data (prepared): ' . json_encode($productionData));
    
            $stmt = $this->conn->prepare($query);
    
            // Bind parameters
            $stmt->bindParam(':hiveID', $productionData['hiveID']);
            $stmt->bindParam(':harvestDate', $productionData['harvestDate']);
            $stmt->bindParam(':quantity', $productionData['quantity'], PDO::PARAM_INT);
            $stmt->bindParam(':type', $productionData['type']);
            $stmt->bindParam(':quality', $productionData['quality']);
            $stmt->bindParam(':notes', $productionData['notes']);
    
            if ($stmt->execute()) {
                $insertedId = $this->conn->lastInsertId();
                return [
                    'success' => true,
                    'message' => 'Production record added successfully',
                    'id'      => $insertedId
                ];
            } else {
                error_log('Failed to add production record');
                return ['success' => false, 'error' => 'Failed to add production record'];
            }
    
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
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
            error_log('Database error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getAllProduction() {
        $query = "SELECT * FROM honey_production ORDER BY productionID DESC";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
}
