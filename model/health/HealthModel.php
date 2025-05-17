<?php
class HiveHealth {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addHealthCheck($data) {
        try {
            $query = "INSERT INTO hive_health (
                hiveID, 
                checkDate, 
                queenPresent,
                colonyStrength,
                diseaseSymptoms,
                pestProblems,
                notes
            ) VALUES (
                :hiveID, 
                :checkDate, 
                :queenPresent,
                :colonyStrength,
                :diseaseSymptoms,
                :pestProblems,
                :notes
            )";

            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':hiveID', $data['hiveID']);
            $stmt->bindParam(':checkDate', $data['checkDate']);
            $stmt->bindParam(':queenPresent', $data['queenPresent']);
            $stmt->bindParam(':colonyStrength', $data['colonyStrength']);
            $stmt->bindParam(':diseaseSymptoms', $data['diseaseSymptoms']);
            $stmt->bindParam(':pestProblems', $data['pestProblems']);
            $stmt->bindParam(':notes', $data['notes']);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Health check added successfully'];
            } else {
                return ['success' => false, 'error' => 'Failed to add health record'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getHealthHistory($hiveID) {
        try {
            $query = "SELECT h.*, hv.hiveNumber 
                     FROM hive_health h
                     LEFT JOIN hives hv ON h.hiveID = hv.hiveID
                     WHERE h.hiveID = :hiveID
                     ORDER BY h.checkDate DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':hiveID', $hiveID);
            
            if ($stmt->execute()) {
                $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $records;
            } else {
                return [];
            }
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getLatestHealthCheck($hiveID) {
        try {
            $query = "SELECT * FROM hive_health 
                     WHERE hiveID = :hiveID 
                     ORDER BY checkDate DESC 
                     LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':hiveID', $hiveID);
            
            if ($stmt->execute()) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                return null;
            }
        } catch (PDOException $e) {
            return null;
        }
    }
}