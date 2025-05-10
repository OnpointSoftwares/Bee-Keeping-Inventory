<?php
// Direct script to delete equipment data from the database
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/equipment_errors.log');

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
        
        // Get equipment ID
        $equipmentID = $_GET['id'];
        
        // Delete equipment from database
        $result = deleteData('equipment', 'equipmentID = :equipmentID', ['equipmentID' => $equipmentID]);
        
        if ($result) {
            // Set success message
            $_SESSION['message'] = "Equipment deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            // Set error message
            $_SESSION['message'] = "Failed to delete equipment. Please try again.";
            $_SESSION['message_type'] = "danger";
        }
    } catch (Exception $e) {
        // Log error
        error_log("Error deleting equipment: " . $e->getMessage());
        
        // Set error message
        $_SESSION['message'] = "An error occurred. Please try again.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    // Set error message
    $_SESSION['message'] = "Invalid equipment ID.";
    $_SESSION['message_type'] = "danger";
}

// Redirect back to equipment page
//header('Location: index.php?page=equipment');
exit();
?>
