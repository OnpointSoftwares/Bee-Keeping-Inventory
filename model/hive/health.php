<?php
require_once __DIR__ . '/../../config/database.php';

class HiveHealthManager {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }
    
    public function addHealthCheck($data) {
        $query = "INSERT INTO hive_health (hiveID, checkDate, queenPresent, colonyStrength, diseaseSymptoms, pestProblems, notes) 
                  VALUES (:hiveID, :checkDate, :queenPresent, :colonyStrength, :diseaseSymptoms, :pestProblems, :notes)";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':hiveID', $data['hiveID']);
            $stmt->bindParam(':checkDate', $data['checkDate']);
            $stmt->bindParam(':queenPresent', $data['queenPresent']);
            $stmt->bindParam(':colonyStrength', $data['colonyStrength']);
            $stmt->bindParam(':diseaseSymptoms', $data['diseaseSymptoms'] ?? '');
            $stmt->bindParam(':pestProblems', $data['pestProblems'] ?? '');
            $stmt->bindParam(':notes', $data['notes'] ?? '');
            
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error adding health check: " . $e->getMessage());
        }
    }
    
    public function getHealthChecks($hiveID) {
        $query = "SELECT * FROM hive_health WHERE hiveID = :hiveID ORDER BY checkDate DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':hiveID', $hiveID);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error retrieving health checks: " . $e->getMessage());
        }
    }
    
    public function getLatestHealthCheck($hiveID) {
        $query = "SELECT * FROM hive_health WHERE hiveID = :hiveID ORDER BY checkDate DESC LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':hiveID', $hiveID);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error retrieving latest health check: " . $e->getMessage());
        }
    }
}
