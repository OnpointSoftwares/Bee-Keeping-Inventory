<?php
require_once __DIR__ . '/../../config/database.php';

class Hive {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
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
            
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error adding hive: " . $e->getMessage());
        }
    }
    
    public function updateHive($data) {
        if (!isset($data['hiveID'])) {
            throw new Exception("Hive ID is required");
        }
        
        $query = "UPDATE beehive 
                 SET hiveNumber = :hiveNumber,
                     location = :location,
                     dateEstablished = :dateEstablished,
                     queenAge = :queenAge,
                     notes = :notes,
                     status = :status
                 WHERE hiveID = :hiveID";
                 
        try {
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':hiveID', $data['hiveID']);
            $stmt->bindParam(':hiveNumber', $data['hiveNumber']);
            $stmt->bindParam(':location', $data['location']);
            $stmt->bindParam(':dateEstablished', $data['dateEstablished']);
            $stmt->bindParam(':queenAge', $data['queenAge']);
            $stmt->bindParam(':notes', $data['notes']);
            $stmt->bindParam(':status', $data['status']);
            
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error updating hive: " . $e->getMessage());
        }
    }
    
    public function deleteHive($hiveID) {
        $query = "DELETE FROM beehive WHERE hiveID = :hiveID";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':hiveID', $hiveID);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error deleting hive: " . $e->getMessage());
        }
    }
    
    public function getAllHives() {
        $query = "SELECT * FROM beehive ORDER BY hiveNumber";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error retrieving hives: " . $e->getMessage());
        }
    }
    
    public function getHive($hiveID) {
        $query = "SELECT * FROM beehive WHERE hiveID = :hiveID";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':hiveID', $hiveID);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error retrieving hive: " . $e->getMessage());
        }
    }
}
