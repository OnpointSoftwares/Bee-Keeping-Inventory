<?php
/**
 * Hive Health Model
 * 
 * This class handles all database operations related to beehive health checks
 */
require_once __DIR__ . '/../BaseModel.php';

class HiveHealth extends BaseModel {
    protected $table = 'hive_health';
    protected $primaryKey = 'healthID';

    /**
     * Add a new health check record
     * 
     * @param array $params Health check data
     * @return int|bool ID of new health check or false on failure
     */
    public function addHealthCheck($params) {
        return $this->create([
            'hiveID' => $params['hiveID'],
            'checkDate' => $params['checkDate'],
            'queenPresent' => $params['queenPresent'] ?? 'Yes',
            'colonyStrength' => $params['colonyStrength'] ?? 'Medium',
            'diseaseSymptoms' => $params['diseaseSymptoms'] ?? 'None',
            'pestProblems' => $params['pestProblems'] ?? 'None',
            'notes' => $params['notes'] ?? ''
        ]);
    }

    /**
     * Update an existing health check record
     * 
     * @param array $params Health check data
     * @return bool Success or failure
     */
    public function updateHealthCheck($params) {
        $data = [
            'hiveID' => $params['hiveID'],
            'checkDate' => $params['checkDate'],
            'queenPresent' => $params['queenPresent'] ?? 'Yes',
            'colonyStrength' => $params['colonyStrength'] ?? 'Medium',
            'diseaseSymptoms' => $params['diseaseSymptoms'] ?? 'None',
            'pestProblems' => $params['pestProblems'] ?? 'None',
            'notes' => $params['notes'] ?? ''
        ];
        
        return $this->update($params['healthID'], $data);
    }

    /**
     * Delete a health check record
     * 
     * @param int $healthID Health check ID
     * @return bool Success or failure
     */
    public function deleteHealthCheck($healthID) {
        return $this->delete($healthID);
    }

    /**
     * Get all health check records
     * 
     * @return array Health check records
     */
    public function getAllHealthChecks() {
        $query = "SELECT h.*, b.hiveNumber 
                  FROM {$this->table} h
                  JOIN beehive b ON h.hiveID = b.hiveID
                  ORDER BY h.checkDate DESC";
                  
        return $this->executeQuery($query);
    }

    /**
     * Get health check record by ID
     * 
     * @param int $healthID Health check ID
     * @return array|bool Health check data or false if not found
     */
    public function getHealthCheckById($healthID) {
        $query = "SELECT h.*, b.hiveNumber 
                  FROM {$this->table} h
                  JOIN beehive b ON h.hiveID = b.hiveID
                  WHERE h.healthID = :healthID";
                  
        return $this->executeQuery($query, ['healthID' => $healthID], false);
    }

    /**
     * Get health check records by hive
     * 
     * @param int $hiveID Hive ID
     * @return array Health check records
     */
    public function getHealthChecksByHive($hiveID) {
        return $this->findBy('hiveID = :hiveID', ['hiveID' => $hiveID], 'checkDate', 'DESC');
    }

    /**
     * Get health check records by date range
     * 
     * @param string $startDate Start date (YYYY-MM-DD)
     * @param string $endDate End date (YYYY-MM-DD)
     * @return array Health check records
     */
    public function getHealthChecksByDateRange($startDate, $endDate) {
        $query = "SELECT h.*, b.hiveNumber 
                  FROM {$this->table} h
                  JOIN beehive b ON h.hiveID = b.hiveID
                  WHERE h.checkDate BETWEEN :startDate AND :endDate
                  ORDER BY h.checkDate DESC";
                  
        return $this->executeQuery($query, [
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    /**
     * Get health check summary statistics
     * 
     * @return array Summary statistics
     */
    public function getHealthSummary() {
        // Get counts by colony strength
        $byStrengthQuery = "SELECT 
                              colonyStrength, 
                              COUNT(*) as checkCount
                            FROM {$this->table}
                            GROUP BY colonyStrength
                            ORDER BY FIELD(colonyStrength, 'Strong', 'Medium', 'Weak', 'Critical')";
        
        $byStrength = $this->executeQuery($byStrengthQuery);
        
        // Get counts by queen presence
        $byQueenQuery = "SELECT 
                           queenPresent, 
                           COUNT(*) as checkCount
                         FROM {$this->table}
                         GROUP BY queenPresent";
        
        $byQueen = $this->executeQuery($byQueenQuery);
        
        // Get counts by disease symptoms
        $byDiseaseQuery = "SELECT 
                             diseaseSymptoms, 
                             COUNT(*) as checkCount
                           FROM {$this->table}
                           WHERE diseaseSymptoms != 'None'
                           GROUP BY diseaseSymptoms
                           ORDER BY checkCount DESC";
        
        $byDisease = $this->executeQuery($byDiseaseQuery);
        
        // Get counts by pest problems
        $byPestQuery = "SELECT 
                          pestProblems, 
                          COUNT(*) as checkCount
                        FROM {$this->table}
                        WHERE pestProblems != 'None'
                        GROUP BY pestProblems
                        ORDER BY checkCount DESC";
        
        $byPest = $this->executeQuery($byPestQuery);
        
        return [
            'byStrength' => $byStrength,
            'byQueen' => $byQueen,
            'byDisease' => $byDisease,
            'byPest' => $byPest
        ];
    }

    /**
     * Get hives with health issues
     * 
     * @return array Hives with health issues
     */
    public function getHivesWithHealthIssues() {
        $query = "SELECT h.*, b.hiveNumber 
                  FROM {$this->table} h
                  JOIN beehive b ON h.hiveID = b.hiveID
                  WHERE h.diseaseSymptoms != 'None' 
                     OR h.pestProblems != 'None'
                     OR h.colonyStrength IN ('Weak', 'Critical')
                     OR h.queenPresent = 'No'
                  ORDER BY h.checkDate DESC";
                  
        return $this->executeQuery($query);
    }
}
?>
