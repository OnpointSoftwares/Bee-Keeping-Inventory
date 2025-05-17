<?php
// Direct script to delete hive data from the database
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/hive_errors.log');

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
        
        // Get hive ID
        $hiveID = $_GET['id'];
        
        // Check if there are related records
        $relatedProduction = countRows('honey_production', 'hiveID = :hiveID', ['hiveID' => $hiveID]);
        $relatedHealth = countRows('hive_health', 'hiveID = :hiveID', ['hiveID' => $hiveID]);
        
        if ($relatedProduction > 0 || $relatedHealth > 0) {
            // Set error message
            $_SESSION['message'] = "Cannot delete hive because it has related production or health records. Consider marking it as inactive instead.";
            $_SESSION['message_type'] = "warning";
        } else {
            // Delete hive from database
            $result = deleteData('beehive', 'hiveID = :hiveID', ['hiveID' => $hiveID]);
            
            if ($result) {
                // Set success message
                $_SESSION['message'] = "Hive deleted successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                // Set error message
                $_SESSION['message'] = "Failed to delete hive. Please try again.";
                $_SESSION['message_type'] = "danger";
            }
        }
    } catch (Exception $e) {
        // Log error
        error_log("Error deleting hive: " . $e->getMessage());
        
        // Set error message
        $_SESSION['message'] = "An error occurred. Please try again.";
        $_SESSION['message_type'] = "danger";
    }
} else {
    // Set error message
    $_SESSION['message'] = "Invalid hive ID.";
    $_SESSION['message_type'] = "danger";
}

// Redirect back to hives page
header('Location: index.php?page=hives');
exit();
?>
