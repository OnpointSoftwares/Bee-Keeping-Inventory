<?php
/**
 * Equipment Model
 * 
 * This class handles all database operations related to beekeeping equipment
 */
require_once __DIR__ . '/../BaseModel.php';

class Equipment extends BaseModel {
    protected $table = 'equipment';
    protected $primaryKey = 'equipmentID';

    /**
     * Add new equipment
     * 
     * @param array $params Equipment data
     * @return int|bool ID of new equipment or false on failure
     */
    public function addEquipment($params) {
        return $this->create([
            'name' => $params['name'],
            'type' => $params['type'],
            'quantity' => $params['quantity'],
            'condition_status' => $params['condition_status'] ?? 'Good',
            'purchaseDate' => $params['purchaseDate'] ?? null,
            'notes' => $params['notes'] ?? '',
            'status' => $params['status'] ?? 'Active'
        ]);
    }

    /**
     * Update existing equipment
     * 
     * @param array $params Equipment data
     * @return bool Success or failure
     */
    public function updateEquipment($params) {
        $data = [
            'name' => $params['name'],
            'type' => $params['type'],
            'quantity' => $params['quantity'],
            'condition_status' => $params['condition_status'] ?? 'Good',
            'purchaseDate' => $params['purchaseDate'] ?? null,
            'notes' => $params['notes'] ?? '',
            'status' => $params['status'] ?? 'Active'
        ];
        
        return $this->update($params['equipmentID'], $data);
    }

    /**
     * Delete equipment
     * 
     * @param int $equipmentID Equipment ID
     * @return bool Success or failure
     */
    public function deleteEquipment($equipmentID) {
        return $this->delete($equipmentID);
    }

    /**
     * Get all equipment
     * 
     * @return array Equipment
     */
    public function getAllEquipment() {
        return $this->readAll('name', 'ASC');
    }

    /**
     * Get equipment by ID
     * 
     * @param int $equipmentID Equipment ID
     * @return array|bool Equipment data or false if not found
     */
    public function getEquipmentById($equipmentID) {
        return $this->read($equipmentID);
    }

    /**
     * Get equipment by type
     * 
     * @param string $type Equipment type
     * @return array Equipment
     */
    public function getEquipmentByType($type) {
        return $this->findBy('type = :type', ['type' => $type], 'name', 'ASC');
    }

    /**
     * Get equipment by condition status
     * 
     * @param string $condition Condition status
     * @return array Equipment
     */
    public function getEquipmentByCondition($condition) {
        return $this->findBy('condition_status = :condition', ['condition' => $condition], 'name', 'ASC');
    }

    /**
     * Get equipment inventory summary
     * 
     * @return array Summary statistics
     */
    public function getInventorySummary() {
        // Get counts by type
        $byTypeQuery = "SELECT 
                          type, 
                          COUNT(*) as itemCount,
                          SUM(quantity) as totalQuantity
                        FROM {$this->table}
                        GROUP BY type
                        ORDER BY totalQuantity DESC";
        
        $byType = $this->executeQuery($byTypeQuery);
        
        // Get counts by condition
        $byConditionQuery = "SELECT 
                              condition_status, 
                              COUNT(*) as itemCount,
                              SUM(quantity) as totalQuantity
                            FROM {$this->table}
                            GROUP BY condition_status
                            ORDER BY totalQuantity DESC";
        
        $byCondition = $this->executeQuery($byConditionQuery);
        
        // Get total inventory value
        $totalQuery = "SELECT 
                         COUNT(*) as totalItems,
                         SUM(quantity) as totalQuantity
                       FROM {$this->table}";
        
        $totals = $this->executeQuery($totalQuery, [], false);
        
        return [
            'byType' => $byType,
            'byCondition' => $byCondition,
            'totals' => $totals
        ];
    }
}
?>
