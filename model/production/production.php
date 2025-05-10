<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../BaseModel.php';

class Production extends BaseModel {
    protected $table = 'honey_production';
    protected $primaryKey = 'productionID';

    public function __construct() {
        parent::__construct();
    }

    public function addProduction($params) {
        return $this->create([
            'hiveID' => $params['hiveID'],
            'type' => $params['type'],
            'quantity' => $params['quantity'],
            'quality' => $params['quality'],
            'harvestDate' => $params['harvestDate'],
            'notes' => isset($params['notes']) ? $params['notes'] : ''
        ]);
    }

    public function updateProduction($params) {
        $data = [
            'hiveID' => $params['hiveID'],
            'type' => $params['type'],
            'quantity' => $params['quantity'],
            'quality' => $params['quality'],
            'harvestDate' => $params['harvestDate'],
            'notes' => isset($params['notes']) ? $params['notes'] : ''
        ];
        
        return $this->update($params['productionID'], $data);
    }

    public function deleteProduction($productionID) {
        return $this->delete($productionID);
    }

    public function getAllProduction() {
        $query = "SELECT p.*, h.hiveNumber 
                  FROM {$this->table} p 
                  JOIN beehive h ON p.hiveID = h.hiveID 
                  ORDER BY p.harvestDate DESC";

        return $this->executeQuery($query);
    }

    public function getProductionReport() {
        try {
            // Get production by hive
            $byHiveQuery = "SELECT 
                              h.hiveNumber,
                              h.hiveID,
                              SUM(p.quantity) as totalQuantity,
                              AVG(CASE 
                                  WHEN p.quality = 'Premium' THEN 3
                                  WHEN p.quality = 'Standard' THEN 2
                                  WHEN p.quality = 'Low' THEN 1
                                  ELSE 0 
                              END) as averageQualityScore,
                              CASE 
                                  WHEN AVG(CASE 
                                      WHEN p.quality = 'Premium' THEN 3
                                      WHEN p.quality = 'Standard' THEN 2
                                      WHEN p.quality = 'Low' THEN 1
                                      ELSE 0 
                                  END) >= 2.5 THEN 'Premium'
                                  WHEN AVG(CASE 
                                      WHEN p.quality = 'Premium' THEN 3
                                      WHEN p.quality = 'Standard' THEN 2
                                      WHEN p.quality = 'Low' THEN 1
                                      ELSE 0 
                                  END) >= 1.5 THEN 'Standard'
                                  ELSE 'Low'
                              END as averageQuality
                          FROM {$this->table} p
                          JOIN beehive h ON p.hiveID = h.hiveID
                          GROUP BY h.hiveID, h.hiveNumber
                          ORDER BY totalQuantity DESC";

            $byHive = $this->executeQuery($byHiveQuery);

            // Get production by type
            $byTypeQuery = "SELECT 
                              p.type,
                              SUM(p.quantity) as totalQuantity,
                              AVG(CASE 
                                  WHEN p.quality = 'Premium' THEN 3
                                  WHEN p.quality = 'Standard' THEN 2
                                  WHEN p.quality = 'Low' THEN 1
                                  ELSE 0 
                              END) as averageQualityScore,
                              CASE 
                                  WHEN AVG(CASE 
                                      WHEN p.quality = 'Premium' THEN 3
                                      WHEN p.quality = 'Standard' THEN 2
                                      WHEN p.quality = 'Low' THEN 1
                                      ELSE 0 
                                  END) >= 2.5 THEN 'Premium'
                                  WHEN AVG(CASE 
                                      WHEN p.quality = 'Premium' THEN 3
                                      WHEN p.quality = 'Standard' THEN 2
                                      WHEN p.quality = 'Low' THEN 1
                                      ELSE 0 
                                  END) >= 1.5 THEN 'Standard'
                                  ELSE 'Low'
                              END as averageQuality
                          FROM {$this->table} p
                          GROUP BY p.type
                          ORDER BY totalQuantity DESC";

            $byType = $this->executeQuery($byTypeQuery);

            return [
                'byHive' => $byHive,
                'byType' => $byType
            ];
        } catch (PDOException $e) {
            throw new Exception("Error generating production report: " . $e->getMessage());
        }
    }
}
