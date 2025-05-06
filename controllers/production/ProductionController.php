<?php
require_once(__DIR__ . '/../../model/production/honey.php');
class ProductionController {
    private $productionModel;
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
        $this->productionModel = new HoneyProduction($db); // Instantiate the HoneyProduction model
    }

    public function handleRequest($params = []) {
        try {
            $action = isset($params['action']) ? $params['action'] : '';
            
            switch($action) {
                case 'add':
                    return $this->productionModel->addProduction($params);
                    
                case 'getHiveProduction':
                    return $this->getHiveProduction($params['hiveID']);
                    
                case 'getReport':
                    return $this->getReport($params);
                    
                case 'getTypeReport':
                    return $this->getTypeReport($params);
                    
                default:
                    return ['success' => false, 'error' => 'Invalid action specified'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getHiveProduction($hiveID) {
        if (!isset($hiveID)) {
            return ['success' => false, 'error' => 'Missing hive ID'];
        }
        return $this->productionModel->getHiveProduction($hiveID);
    }

    public function getReport($params) {
        $startDate = $params['startDate'] ?? date('Y-m-d', strtotime('-1 year'));
        $endDate = $params['endDate'] ?? date('Y-m-d');
        
        $report = $this->productionModel->getTotalProduction($startDate, $endDate);
        
        if ($report['success']) {
            $total = array_reduce($report['data'], function($carry, $item) {
                return $carry + $item['totalQuantity'];
            }, 0);
            
            return [
                'success' => true,
                'data' => [
                    'report' => $report['data'],
                    'totalProduction' => $total,
                    'period' => [
                        'start' => $startDate,
                        'end' => $endDate
                    ]
                ]
            ];
        }
        return $report;
    }
    
    public function getTypeReport($params) {
        $startDate = $params['startDate'] ?? date('Y-m-d', strtotime('-1 year'));
        $endDate = $params['endDate'] ?? date('Y-m-d');
        
        $report = $this->productionModel->getProductionByType($startDate, $endDate);
        
        if ($report['success']) {
            return [
                'success' => true,
                'data' => [
                    'report' => $report['data'],
                    'period' => [
                        'start' => $startDate,
                        'end' => $endDate
                    ]
                ]
            ];
        }
        return $report;
    }
}