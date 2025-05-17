<?php
// Test script to diagnose API issues
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/test_api_errors.log');

// Set JSON content type
header('Content-Type: application/json');

// Get the raw POST data
$rawData = file_get_contents('php://input');
file_put_contents('test_api_raw_data.txt', $rawData);

// Try to decode as JSON
$data = json_decode($rawData, true);

// Log what we received
error_log("Raw data received: " . $rawData);
error_log("Decoded data: " . print_r($data, true));

// Check if we have a controller and action
$controller = isset($data['controller']) ? $data['controller'] : '';
$action = isset($data['action']) ? $data['action'] : '';

error_log("Controller: $controller, Action: $action");

// Simulate a direct call to the ProductionController
require_once __DIR__ . '/controllers/production/ProductionController.php';
$productionController = new ProductionController();

try {
    // Try to call handleRequest with the action
    $result = $productionController->handleRequest('add', $data);
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Exception: ' . $e->getMessage()
    ]);
}
?>
