<?php
error_reporting(E_ALL);
ini_set("display_errors",1);
require_once(__DIR__ . '/../../model/production/honey.php');
class ProductionController {
    public $productionModel;
    public $db;
    
    public function __construct($db) {
        $this->db = $db;
        $this->productionModel = new HoneyProduction($db); // Initialize the production model
    }

    public function handleRequest($params = []) {
        try {
            $action = isset($params['action']) ? $params['action'] : '';
            
            switch($action) {
                case 'add':
                    return $this->addProduction($params);
                    
                case 'getHiveProduction':
                    return $this->getHiveProduction($params['hiveID']);
                    
                case 'getReport':
                    return $this->getReport($params);
                    
                case 'getTypeReport':
                    return $this->getTypeReport($params);
                    
                case 'getAllProduction':
                    return $this->getAllProduction();
                    
                default:
                    return ['success' => false, 'error' => 'Invalid action specified'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function addProduction($input) {
        error_log('Adding production (controller): ' . json_encode($input));
    
        // Validate required fields
        $requiredFields = ['hiveID', 'type', 'quantity'];
        $validation = $this->validateRequiredFields($input, $requiredFields);
        if (!$validation['success']) {
            return $validation;
        }
    
        try {
            // Build production data
            $productionData = [
                'hiveID'       => $input['hiveID'],
                'harvestDate'  => $input['harvestDate'] ?? null,
                'quantity'     => $input['quantity'],
                'type'         => $input['type'],
                'quality'      => $input['quality'] ?? null,
                'notes'        => $input['notes'] ?? null
            ];
    
            error_log('Production data (controller-prepared): ' . json_encode($productionData));
    
            // Call model
            $result = $this->productionModel->addProduction($productionData);
            error_log('Result from addProduction (model): ' . json_encode($result));
            return $result;
    
        } catch (Exception $e) {
            error_log('Controller exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    public function validateRequiredFields($data, $requiredFields) {
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return ['success' => false, 'error' => "Missing required field: $field"];
            }
        }
        return ['success' => true];
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

    public function getAllProduction() {
        try {
            return $this->productionModel->getAllProduction();
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}