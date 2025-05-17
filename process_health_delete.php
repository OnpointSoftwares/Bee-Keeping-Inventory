<?php
// Direct script to delete health check data from the database
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/health_errors.log');

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedIn'])) {
    header('Location: login.php');
    exit();
}

// Check if ID was provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Include database connection
    require_once 'config/database.php';
    require_once 'utils/database_utils.php';
    
    try {
        // Get database connection
        $db = getDbConnection();
        
        // Get health ID
        $healthID = $_GET['id'];
        
        // Delete health check record from database
        $result = deleteData('hive_health', 'healthID = :healthID', ['healthID' => $healthID]);
        
        if ($result) {
            // Set success message
            $_SESSION['message'] = "Health check record deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            // Set error message
            $_SESSION['message'] = "Failed to delete health check record. Please try again.";
            $_SESSION['message_type'] = "danger";
        }
    } catch (Exception $e) {
        // Log error
        error_log("Error deleting health check record: " . $e->getMessage());
        
        // Set error message
        $_SESSION['message'] = "An error occurred. Please try again.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    // Set error message
    $_SESSION['message'] = "Invalid health check ID.";
    $_SESSION['message_type'] = "danger";
}

// Redirect back to health page
header('Location: index.php?page=health');
exit();
?>
