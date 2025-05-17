<?php
// Direct script to edit hive data in the database
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

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include database connection
    require_once 'config/database.php';
    require_once 'utils/database_utils.php';
    
    try {
        // Get database connection
        $db = getDbConnection();
        
        // Get form data
        $hiveID = $_POST['hiveID'] ?? '';
        $hiveNumber = $_POST['hiveNumber'] ?? '';
        $location = $_POST['location'] ?? '';
        $dateEstablished = $_POST['dateEstablished'] ?? '';
        $queenAge = $_POST['queenAge'] ?? '';
        $notes = $_POST['notes'] ?? '';
        $status = $_POST['status'] ?? 'Active';
        
        // Validate required fields
        $errors = [];
        if (empty($hiveID)) $errors[] = "Hive ID is required";
        if (empty($hiveNumber)) $errors[] = "Hive number is required";
        if (empty($location)) $errors[] = "Location is required";
        if (empty($dateEstablished)) $errors[] = "Date established is required";
        
        if (empty($errors)) {
            // Prepare data for update
            $data = [
                'hiveNumber' => $hiveNumber,
                'location' => $location,
                'dateEstablished' => $dateEstablished,
                'queenAge' => $queenAge,
                'notes' => $notes,
                'status' => $status
            ];
            
            // Update data in database
            $result = updateData('beehive', $data, 'hiveID = :hiveID', ['hiveID' => $hiveID]);
            
            if ($result) {
                // Set success message
                $_SESSION['message'] = "Hive updated successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                // Set error message
                $_SESSION['message'] = "Failed to update hive. Please try again.";
                $_SESSION['message_type'] = "danger";
            }
        } else {
            // Set validation errors
            $_SESSION['message'] = "Please fix the following errors: " . implode(", ", $errors);
            $_SESSION['message_type'] = "danger";
        }
    } catch (Exception $e) {
        // Log error
        error_log("Error updating hive: " . $e->getMessage());
        
        // Set error message
        $_SESSION['message'] = "An error occurred. Please try again.";
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect back to hives page
    header('Location: index.php?page=hives');
    exit();
}

// If not a POST request, redirect to hives page
header('Location: index.php?page=hives');
exit();
?>
