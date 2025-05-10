<?php
/**
 * Base Controller Class
 * 
 * This class serves as the foundation for all controllers in the Beekeeping Inventory Management System.
 * It provides common functionality and standardizes the controller structure.
 */

abstract class BaseController {
    /**
     * Handle incoming requests
     * 
     * @param string $action Action to perform
     * @param array $params Parameters for the action
     * @return array Response with success status and data/error message
     */
    abstract public function handleRequest($action, $params);
    
    /**
     * Format success response
     * 
     * @param mixed $data Response data
     * @param string $message Success message
     * @return array Formatted response
     */
    protected function success($data = null, $message = 'Operation completed successfully') {
        $response = ['success' => true];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        if ($message) {
            $response['message'] = $message;
        }
        
        return $response;
    }
    
    /**
     * Format error response
     * 
     * @param string $message Error message
     * @param int $code Error code
     * @return array Formatted response
     */
    protected function error($message = 'An error occurred', $code = 400) {
        return [
            'success' => false,
            'error' => $message,
            'code' => $code
        ];
    }
    
    /**
     * Validate required parameters
     * 
     * @param array $params Parameters to validate
     * @param array $required Required parameter keys
     * @return bool|string True if valid, error message if invalid
     */
    protected function validateParams($params, $required) {
        foreach ($required as $param) {
            if (!isset($params[$param]) || (is_string($params[$param]) && trim($params[$param]) === '')) {
                return "Missing required parameter: $param";
            }
        }
        
        return true;
    }
}
?>
