<?php
class HealthModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addHealthRecord($data) {
        $query = "INSERT INTO health_records (hiveID, healthStatus, date, notes) VALUES (:hiveID, :healthStatus, :date, :notes)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hiveID', $data['hiveID']);
        $stmt->bindParam(':healthStatus', $data['healthStatus']);
        $stmt->bindParam(':date', $data['date']);
        $stmt->bindParam(':notes', $data['notes']);
        return $stmt->execute();
    }

    public function getHealthRecords($hiveID) {
        $query = "SELECT * FROM health_records WHERE hiveID = :hiveID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':hiveID', $hiveID);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}