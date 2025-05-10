<?php
// This script creates a test user for the Beekeeping Inventory Management System

// Include database configuration
require_once 'config/database.php';

// Create database connection
$database = new Database();
$conn = $database->connect();

// Check if the users table exists, if not create it
$checkTableSql = "SHOW TABLES LIKE 'users'";
$stmt = $conn->prepare($checkTableSql);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    // Create the users table
    $createTableSql = "
    CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_username (username)
    )";
    
    $conn->exec($createTableSql);
    echo "Users table created successfully.<br>";
}

// Check if test user already exists
$checkUserSql = "SELECT * FROM users WHERE username = 'admin@example.com'";
$stmt = $conn->prepare($checkUserSql);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    echo "Test user already exists.<br>";
} else {
    // Create test user
    $username = 'admin@example.com';
    $password = password_hash('password', PASSWORD_DEFAULT);
    $fullName = 'Admin User';
    
    $insertUserSql = "
    INSERT INTO users (username, password, full_name)
    VALUES (:username, :password, :full_name)
    ";
    
    $stmt = $conn->prepare($insertUserSql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':full_name', $fullName);
    
    if ($stmt->execute()) {
        echo "Test user created successfully.<br>";
        echo "Username: admin@example.com<br>";
        echo "Password: password<br>";
    } else {
        echo "Error creating test user.<br>";
    }
}

echo "<br>You can now <a href='login.php'>login</a> with the test user credentials.";
?>
