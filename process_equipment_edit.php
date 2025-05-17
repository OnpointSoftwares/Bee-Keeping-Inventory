<?php
// Direct script to edit equipment data in the database
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

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include database connection
    require_once 'config/database.php';
    require_once 'utils/database_utils.php';
    
    try {
        // Get database connection
        $db = getDbConnection();
        
        // Get form data
        $equipmentID = $_POST['equipmentID'] ?? '';
        $name = $_POST['name'] ?? '';
        $type = $_POST['type'] ?? '';
        $quantity = $_POST['quantity'] ?? '';
        $condition = $_POST['condition_status'] ?? '';
        $purchaseDate = $_POST['purchaseDate'] ?? '';
        $notes = $_POST['notes'] ?? '';
        $status = $_POST['status'] ?? 'Active';
        
        // Validate required fields
        $errors = [];
        if (empty($equipmentID)) $errors[] = "Equipment ID is required";
        if (empty($name)) $errors[] = "Equipment name is required";
        if (empty($type)) $errors[] = "Equipment type is required";
        if (empty($quantity)) $errors[] = "Quantity is required";
        if (empty($condition)) $errors[] = "Condition is required";
        
        if (empty($errors)) {
            // Prepare data for update
            $data = [
                'name' => $name,
                'type' => $type,
                'quantity' => $quantity,
                'condition_status' => $condition,
                'purchaseDate' => $purchaseDate,
                'notes' => $notes,
                'status' => $status
            ];
            
            // Update data in database
            $result = updateData('equipment', $data, 'equipmentID = :equipmentID', ['equipmentID' => $equipmentID]);
            
            if ($result) {
                // Set success message
                $_SESSION['message'] = "Equipment updated successfully!";
                $_SESSION['message_type'] = "success";
            } else {
                // Set error message
                $_SESSION['message'] = "Failed to update equipment. Please try again.";
                $_SESSION['message_type'] = "danger";
            }
        } else {
            // Set validation errors
            $_SESSION['message'] = "Please fix the following errors: " . implode(", ", $errors);
            $_SESSION['message_type'] = "danger";
        }
    } catch (Exception $e) {
        // Log error
        error_log("Error updating equipment: " . $e->getMessage());
        
        // Set error message
        $_SESSION['message'] = "An error occurred. Please try again.";
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect back to equipment page
    header('Location: index.php?page=equipment');
    exit();
}

// If not a POST request, redirect to equipment page
header('Location: index.php?page=equipment');
exit();
?>
