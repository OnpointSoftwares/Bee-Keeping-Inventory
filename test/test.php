<?php
// Prevent PHP from outputting errors as HTML
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log','api_errors.log');

// Set JSON content type
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Start session for authentication
session_start();

// Handle fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Fatal error: ' . $error['message']
        ]);
        exit();
    }
});

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get database connection
try {
    $db = require_once(__DIR__ . '/../inc/config/db.php');
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit();
}

require_once(__DIR__ . '/controllers/auth.php');
require_once(__DIR__ . '/../controllers/hive/HiveController.php');
require_once(__DIR__ . '/../controllers/production/ProductionController.php');
require_once(__DIR__ . '/../controllers/equipment/EquipmentController.php');

// Get the request path
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim(str_replace('/inventory-management-system/api/', '', $path), '/');

// Get input for POST requests
$input = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = file_get_contents('php://input');
    // Log incoming input for debugging
    error_log('Incoming request: ' . $rawInput);
    $input = json_decode($rawInput, true) ?? [];
    if (json_last_error() !== JSON_ERROR_NONE) {
        $input = $_POST;
    }
} else {
    $input = $_GET;
}

try {
    // Route request to appropriate controller
    $result = null;

    switch($path) {
        case 'auth':
            $authController = new AuthController($db);
            switch($input['action'] ?? '') {
                case 'login':
                    $result = $authController->login($input);
                    break;
                case 'register':
                    $result = $authController->register($input);
                    break;
                case 'resetPassword':
                    $result = $authController->resetPassword($input);
                    break;
                default:
                    throw new Exception('Invalid auth action: ' . ($input['action'] ?? ''));
            }
            break;

        case 'hive':
            // Check authentication for protected routes
            if (!isset($_SESSION['loggedIn'])) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'error' => 'Authentication required'
                ]);
                exit();
            }
            $controller = new HiveController($db);
            $result = $controller->handleRequest($input);
            break;
            
        case 'production':
    /*if (!isset($_SESSION['loggedIn'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'Authentication required'
        ]);
        exit();
    }*/
    $controller = new ProductionController($db);
    if (isset($input['action'])) {
        switch ($input['action']) {
            case 'add':
                $result = $controller->addProduction($input);
error_log('Result from controller: ' . json_encode($result));
                break;
            case 'getReport':
                $result = $controller->getReport($input); // Pass $input to the method
                break;
            case 'getHiveProduction':
                $result = $controller->getHiveProduction($input['hiveID']);
                break;
            case 'getTotalProduction':
                $result = $controller->getTotalProduction($input['startDate'], $input['endDate']);
                break;
            case 'getProductionByType':
                $result = $controller->getProductionByType($input['startDate'], $input['endDate']);
                break;
            case 'getAllProduction': // Ensure this case is present
                $result = $controller->getAllProduction();
                break;
            default:
                throw new Exception('Invalid production action: ' . ($input['action'] ?? ''));
        }
    }
    break;
        case 'equipment':
            if (!isset($_SESSION['loggedIn'])) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'error' => 'Authentication required'
                ]);
                exit();
            }
            $controller = new EquipmentController($db);
            
            // Log the incoming request for debugging
            error_log('Equipment request: ' . json_encode($input));

            // Handle the request based on the action
            if (isset($input['action'])) {
                switch ($input['action']) {
                    case 'getAll':
                        $result = $controller->getAllEquipment(); // Ensure this method exists
                        break;
                    case 'getInventoryReport':
                        $result = $controller->getInventoryReport(); // Ensure this method exists
                        break;
                    case 'add':
                        $result = $controller->addEquipment($input); // Ensure this method exists
                        break;
                    case 'update':
                        $result = $controller->updateEquipment($input); // Ensure this method exists
                        break;
                    case 'delete':
                        $result = $controller->deleteEquipment($input['equipmentID']); // Ensure this method exists
                        break;
                    default:
                        http_response_code(400);
                        echo json_encode([
                            'success' => false,
                            'error' => 'Invalid action: ' . $input['action']
                        ]);
                        exit();
                }
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'No action specified'
                ]);
                exit();
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Invalid endpoint: ' . $path
            ]);
            exit();
    }

    // Ensure we have a valid response
    if (!is_array($result)) {
        throw new Exception('Invalid response from controller');
    }

    echo json_encode($result);

} catch(PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    error_log('Server error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage()
    ]);
}