<?php
/**
 * Beehive Model
 * 
 * This class handles all database operations related to beehives
 */
require_once __DIR__ . '/../BaseModel.php';

class Beehive extends BaseModel {
    protected $table = 'beehive';
    protected $primaryKey = 'hiveID';

    /**
     * Add a new beehive
     * 
     * @param array $params Beehive data
     * @return int|bool ID of new beehive or false on failure
     */
    public function addBeehive($params) {
        return $this->create([
            'hiveNumber' => $params['hiveNumber'],
            'location' => $params['location'],
            'dateEstablished' => $params['dateEstablished'],
            'queenAge' => $params['queenAge'] ?? null,
            'notes' => $params['notes'] ?? '',
            'status' => $params['status'] ?? 'Active'
        ]);
    }

    /**
     * Update an existing beehive
     * 
     * @param array $params Beehive data
     * @return bool Success or failure
     */
    public function updateBeehive($params) {
        $data = [
            'hiveNumber' => $params['hiveNumber'],
            'location' => $params['location'],
            'dateEstablished' => $params['dateEstablished'],
            'queenAge' => $params['queenAge'] ?? null,
            'notes' => $params['notes'] ?? '',
            'status' => $params['status'] ?? 'Active'
        ];
        
        return $this->update($params['hiveID'], $data);
    }

    /**
     * Delete a beehive
     * 
     * @param int $hiveID Beehive ID
     * @return bool Success or failure
     */
    public function deleteBeehive($hiveID) {
        return $this->delete($hiveID);
    }

    /**
     * Get all beehives
     * 
     * @return array Beehives
     */
    public function getAllBeehives() {
        return $this->readAll('hiveNumber', 'ASC');
    }

    /**
     * Get a beehive by ID
     * 
     * @param int $hiveID Beehive ID
     * @return array|bool Beehive data or false if not found
     */
    public function getBeehiveById($hiveID) {
        return $this->read($hiveID);
    }

    /**
     * Get beehives by status
     * 
     * @param string $status Beehive status
     * @return array Beehives
     */
    public function getBeehivesByStatus($status) {
        return $this->findBy('status = :status', ['status' => $status], 'hiveNumber', 'ASC');
    }

    /**
     * Get beehive health history
     * 
     * @param int $hiveID Beehive ID
     * @return array Health records
     */
    public function getHealthHistory($hiveID) {
        $query = "SELECT h.* 
                  FROM hive_health h
                  WHERE h.hiveID = :hiveID
                  ORDER BY h.checkDate DESC";
                  
        return $this->executeQuery($query, ['hiveID' => $hiveID]);
    }

    /**
     * Get beehive production history
     * 
     * @param int $hiveID Beehive ID
     * @return array Production records
     */
    public function getProductionHistory($hiveID) {
        $query = "SELECT p.* 
                  FROM honey_production p
                  WHERE p.hiveID = :hiveID
                  ORDER BY p.harvestDate DESC";
                  
        return $this->executeQuery($query, ['hiveID' => $hiveID]);
    }

    /**
     * Get beehive summary statistics
     * 
     * @param int $hiveID Beehive ID
     * @return array Summary statistics
     */
    public function getBeehiveSummary($hiveID) {
        // Get total production
        $productionQuery = "SELECT 
                              SUM(p.quantity) as totalProduction,
                              COUNT(*) as harvestCount,
                              MAX(p.harvestDate) as lastHarvestDate
                           FROM honey_production p
                           WHERE p.hiveID = :hiveID";
        
        $production = $this->executeQuery($productionQuery, ['hiveID' => $hiveID], false);
        
        // Get latest health check
        $healthQuery = "SELECT h.*
                        FROM hive_health h
                        WHERE h.hiveID = :hiveID
                        ORDER BY h.checkDate DESC
                        LIMIT 1";
        
        $health = $this->executeQuery($healthQuery, ['hiveID' => $hiveID], false);
        
        // Get beehive details
        $hive = $this->read($hiveID);
        
        return [
            'hive' => $hive,
            'production' => $production,
            'health' => $health
        ];
    }
}
?>
