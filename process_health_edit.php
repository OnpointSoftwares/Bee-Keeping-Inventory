<?php
// Direct script to edit health check data in the database
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

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include database connection
    require_once 'config/database.php';
    require_once 'utils/database_utils.php';
    
    try {
        // Get database connection
        $db = getDbConnection();
        
        // Get form data
        $healthID = $_POST['healthID'] ?? '';
        $hiveID = $_POST['hiveID'] ?? '';
        $checkDate = $_POST['checkDate'] ?? '';
        $queenPresent = isset($_POST['queenPresent']) ? 1 : 0;
        $colonyStrength = $_POST['colonyStrength'] ?? '';
        $diseaseSymptoms = $_POST['diseaseSymptoms'] ?? 'None';
        $pestProblems = $_POST['pestProblems'] ?? 'None';
        $notes = $_POST['notes'] ?? '';
        
        // Validate required fields
        $errors = [];
        if (empty($healthID)) $errors[] = "Health ID is required";
        if (empty($hiveID)) $errors[] = "Hive is required";
        if (empty($checkDate)) $errors[] = "Check date is required";
        if (empty($colonyStrength)) $errors[] = "Colony strength is required";
        
        if (empty($errors)) {
            // Prepare data for update
            $data = [
                'hiveID' => $hiveID,
                'checkDate' => $checkDate,
                'queenPresent' => $queenPresent,
                'colonyStrength' => $colonyStrength,
                'diseaseSymptoms' => $diseaseSymptoms,
                'pestProblems' => $pestProblems,
                'notes' => $notes
            ];
            
            // Update data in database
            $result = updateData('hive_health', $data, 'healthID = :healthID', ['healthID' => $healthID]);
            
            if ($result) {
                // Set success message
                $_SESSION['message'] = "Health check record updated successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                // Set error message
                $_SESSION['message'] = "Failed to update health check record. Please try again.";
                $_SESSION['message_type'] = "danger";
            }
        } else {
            // Set validation errors
            $_SESSION['message'] = "Please fix the following errors: " . implode(", ", $errors);
            $_SESSION['message_type'] = "danger";
        }
    } catch (Exception $e) {
        // Log error
        error_log("Error updating health check record: " . $e->getMessage());
        
        // Set error message
        $_SESSION['message'] = "An error occurred. Please try again.";
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect back to health page
    header('Location: index.php?page=health');
    exit();
}

// If not a POST request, redirect to health page
header('Location: index.php?page=health');
exit();
?>
