<?php
// Direct script to edit production data in the database
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/production_errors.log');

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
        $productionID = $_POST['productionID'] ?? '';
        $hiveID = $_POST['hiveID'] ?? '';
        $harvestDate = $_POST['harvestDate'] ?? '';
        $quantity = $_POST['quantity'] ?? '';
        $type = $_POST['type'] ?? '';
        $quality = $_POST['quality'] ?? '';
        $notes = $_POST['notes'] ?? '';
        
        // Validate required fields
        $errors = [];
        if (empty($productionID)) $errors[] = "Production ID is required";
        if (empty($hiveID)) $errors[] = "Hive is required";
        if (empty($harvestDate)) $errors[] = "Harvest date is required";
        if (empty($quantity)) $errors[] = "Quantity is required";
        if (empty($type)) $errors[] = "Honey type is required";
        if (empty($quality)) $errors[] = "Quality is required";
        
        if (empty($errors)) {
            // Prepare data for update
            $data = [
                'hiveID' => $hiveID,
                'harvestDate' => $harvestDate,
                'quantity' => $quantity,
                'type' => $type,
                'quality' => $quality,
                'notes' => $notes
            ];
            
            // Update data in database
            $result = updateData('honey_production', $data, 'productionID = :productionID', ['productionID' => $productionID]);
            
            if ($result) {
                // Set success message
                $_SESSION['message'] = "Production record updated successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                // Set error message
                $_SESSION['message'] = "Failed to update production record. Please try again.";
                $_SESSION['message_type'] = "danger";
            }
        } else {
            // Set validation errors
            $_SESSION['message'] = "Please fix the following errors: " . implode(", ", $errors);
            $_SESSION['message_type'] = "danger";
        }
    } catch (Exception $e) {
        // Log error
        error_log("Error updating production record: " . $e->getMessage());
        
        // Set error message
        $_SESSION['message'] = "An error occurred. Please try again.";
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect back to production page
    header('Location: index.php?page=production');
    exit();
}

// If not a POST request, redirect to production page
header('Location: index.php?page=production');
exit();
?>
