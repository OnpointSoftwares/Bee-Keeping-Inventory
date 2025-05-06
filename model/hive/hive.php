<?php
require_once(__DIR__ . '/../../inc/config/db.php');
class Hive {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function addHive($data) {
        $query = "INSERT INTO beehive (hiveNumber, location, dateEstablished, queenAge, notes, status) 
                 VALUES (:hiveNumber, :location, :dateEstablished, :queenAge, :notes, 'Active')";
        
        try {
            $hiveNumber = $data['hiveNumber'];
            $location = $data['location'];
            $dateEstablished = $data['dateEstablished'] ?? date('Y-m-d');
            $queenAge = $data['queenAge'] ?? null;
            $notes = $data['notes'] ?? '';
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':hiveNumber', $hiveNumber);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':dateEstablished', $dateEstablished);
            $stmt->bindParam(':queenAge', $queenAge);
            $stmt->bindParam(':notes', $notes);
            
            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Hive added successfully'];
            }
            return ['success' => false, 'error' => 'Failed to add hive'];
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function updateHive($data) {
        $query = "UPDATE beehive 
                 SET hiveNumber = :hiveNumber, 
                     location = :location, 
                     queenAge = :queenAge, 
                     notes = :notes 
                 WHERE hiveID = :hiveID";
        
        try {
            $hiveID = $data['hiveID'];
            $hiveNumber = $data['hiveNumber'];
            $location = $data['location'];
            $queenAge = $data['queenAge'] ?? null;
            $notes = $data['notes'] ?? '';
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':hiveID', $hiveID);
            $stmt->bindParam(':hiveNumber', $hiveNumber);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':queenAge', $queenAge);
            $stmt->bindParam(':notes', $notes);
            
            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Hive updated successfully'];
            }
            return ['success' => false, 'error' => 'Failed to update hive'];
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getAllHives() {
        $query = "SELECT * FROM beehive WHERE status = 'Active' ORDER BY hiveNumber";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getHive($hiveID) {
        $query = "SELECT * FROM beehive WHERE hiveID = :hiveID";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':hiveID', $hiveID);
            $stmt->execute();
            $hive = $stmt->fetch(PDO::FETCH_ASSOC);
            return $hive ? $hive : ['success' => false, 'error' => 'Hive not found'];
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function deleteHive($hiveID) {
        $query = "UPDATE beehive SET status = 'Inactive' WHERE hiveID = :hiveID";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':hiveID', $hiveID);
            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Hive deleted successfully'];
            }
            return ['success' => false, 'error' => 'Failed to delete hive'];
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
}
