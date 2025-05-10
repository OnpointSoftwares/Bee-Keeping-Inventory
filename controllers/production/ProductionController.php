<?php
require_once __DIR__ . '/../BaseController.php';
require_once __DIR__ . '/../../model/production/production.php';

class ProductionController extends BaseController {
    private $productionModel;

    public function __construct() {
        $this->productionModel = new Production();
    }

    public function handleRequest($action, $params) {
        // Log the incoming request for debugging
        error_log("ProductionController: Action received: '$action', Params: " . json_encode($params));
        
        // Check if action is empty and try to get it from params
        if (empty($action) && isset($params['action'])) {
            $action = $params['action'];
            error_log("ProductionController: Action was empty, using action from params: '$action'");
        }
        
        switch ($action) {
            case 'add':
                error_log("ProductionController: Calling addProduction");
                return $this->addProduction($params);
            case 'update':
                error_log("ProductionController: Calling updateProduction");
                return $this->updateProduction($params);
            case 'delete':
                error_log("ProductionController: Calling deleteProduction");
                return $this->deleteProduction($params);
            case 'getAllProduction':
                error_log("ProductionController: Calling getAllProduction");
                return $this->getAllProduction();
            case 'getReport':
                error_log("ProductionController: Calling getProductionReport");
                return $this->getProductionReport();
            default:
                error_log("ProductionController: Invalid action: '$action'");
                return $this->error('Invalid action: ' . $action);
        }
    }

    private function addProduction($params) {
        // Log the parameters for debugging
        error_log("Adding production with params: " . json_encode($params));
        
        $validation = $this->validateParams($params, ['hiveID', 'type', 'quantity', 'quality', 'harvestDate']);
        if ($validation !== true) {
            error_log("Validation failed: " . $validation);
            return $this->error($validation);
        }

        try {
            // Ensure numeric values are properly formatted
            $params['quantity'] = floatval($params['quantity']);
            $params['hiveID'] = intval($params['hiveID']);
            
            $result = $this->productionModel->addProduction($params);
            if (!$result) {
                error_log("Failed to add production record");
                return $this->error("Failed to add production record");
            }
            
            error_log("Production added successfully with ID: $result");
            return $this->success(['id' => $result], 'Production record added successfully');
        } catch (Exception $e) {
            error_log("Exception in addProduction: " . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    private function updateProduction($params) {
        // Log the parameters for debugging
        error_log("Updating production with params: " . json_encode($params));
        
        $validation = $this->validateParams($params, ['productionID', 'hiveID', 'type', 'quantity', 'quality', 'harvestDate']);
        if ($validation !== true) {
            error_log("Validation failed: " . $validation);
            return $this->error($validation);
        }

        try {
            // Ensure numeric values are properly formatted
            $params['productionID'] = intval($params['productionID']);
            $params['quantity'] = floatval($params['quantity']);
            $params['hiveID'] = intval($params['hiveID']);
            
            $result = $this->productionModel->updateProduction($params);
            if (!$result) {
                error_log("Failed to update production record");
                return $this->error("Failed to update production record");
            }
            
            error_log("Production updated successfully");
            return $this->success(null, 'Production record updated successfully');
        } catch (Exception $e) {
            error_log("Exception in updateProduction: " . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    private function deleteProduction($params) {
        // Log the parameters for debugging
        error_log("Deleting production with params: " . json_encode($params));
        
        $validation = $this->validateParams($params, ['productionID']);
        if ($validation !== true) {
            error_log("Validation failed: " . $validation);
            return $this->error($validation);
        }

        try {
            $productionID = intval($params['productionID']);
            $result = $this->productionModel->deleteProduction($productionID);
            if (!$result) {
                error_log("Failed to delete production record");
                return $this->error("Failed to delete production record");
            }
            
            error_log("Production deleted successfully");
            return $this->success(null, 'Production record deleted successfully');
        } catch (Exception $e) {
            error_log("Exception in deleteProduction: " . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    private function getAllProduction() {
        try {
            $data = $this->productionModel->getAllProduction();
            return $this->success($data);
        } catch (Exception $e) {
            error_log("Exception in getAllProduction: " . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }

    private function getProductionReport() {
        try {
            $data = $this->productionModel->getProductionReport();
            return $this->success($data);
        } catch (Exception $e) {
            error_log("Exception in getProductionReport: " . $e->getMessage());
            return $this->error($e->getMessage());
        }
    }
}