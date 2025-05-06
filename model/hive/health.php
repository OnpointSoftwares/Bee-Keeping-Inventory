<?php
class HiveHealth {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function addHealthCheck($data) {
        $query = "INSERT INTO hive_health (hiveID, checkDate, queenPresent, colonyStrength, diseaseSymptoms, pestProblems, notes) 
                 VALUES (:hiveID, :checkDate, :queenPresent, :colonyStrength, :diseaseSymptoms, :pestProblems, :notes)";
        
        try {
            $hiveID = $data['hiveID'];
            $checkDate = $data['checkDate'];
            $queenPresent = $data['queenPresent'] ?? 0;
            $colonyStrength = $data['colonyStrength'];
            $diseaseSymptoms = $data['diseaseSymptoms'] ?? '';
            $pestProblems = $data['pestProblems'] ?? '';
            $notes = $data['notes'] ?? '';
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':hiveID', $hiveID);
            $stmt->bindParam(':checkDate', $checkDate);
            $stmt->bindParam(':queenPresent', $queenPresent);
            $stmt->bindParam(':colonyStrength', $colonyStrength);
            $stmt->bindParam(':diseaseSymptoms', $diseaseSymptoms);
            $stmt->bindParam(':pestProblems', $pestProblems);
            $stmt->bindParam(':notes', $notes);
            
            if($stmt->execute()) {
                return ['success' => true, 'message' => 'Health check added successfully'];
            }
            return ['success' => false, 'error' => 'Failed to add health check'];
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getHealthHistory($hiveID) {
        $query = "SELECT * FROM hive_health WHERE hiveID = :hiveID ORDER BY checkDate DESC";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':hiveID', $hiveID);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getLatestHealthCheck($hiveID) {
        $query = "SELECT * FROM hive_health WHERE hiveID = :hiveID ORDER BY checkDate DESC LIMIT 1";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':hiveID', $hiveID);
            $stmt->execute();
            $check = $stmt->fetch(PDO::FETCH_ASSOC);
            return $check ? $check : null;
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function getHealthReport($startDate, $endDate) {
        $query = "SELECT h.*, b.hiveNumber 
                 FROM hive_health h 
                 JOIN beehive b ON h.hiveID = b.hiveID 
                 WHERE h.checkDate BETWEEN :startDate AND :endDate 
                 ORDER BY h.checkDate DESC";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':startDate', $startDate);
            $stmt->bindParam(':endDate', $endDate);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
}
