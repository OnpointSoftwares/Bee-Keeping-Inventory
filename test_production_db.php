<?php
// Test script to verify database connection and table structure
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

try {
    // Connect to database
    $database = new Database();
    $conn = $database->connect();
    echo "Database connection successful!<br>";
    
    // Check if honey_production table exists
    $query = "SHOW TABLES LIKE 'honey_production'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "Table 'honey_production' exists.<br>";
        
        // Get table structure
        $query = "DESCRIBE honey_production";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Table Structure:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        foreach ($columns as $column) {
            echo "<tr>";
            foreach ($column as $key => $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Get existing records
        $query = "SELECT * FROM honey_production";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Existing Records (" . count($records) . "):</h3>";
        
        if (count($records) > 0) {
            echo "<table border='1'>";
            echo "<tr>";
            foreach (array_keys($records[0]) as $header) {
                echo "<th>" . htmlspecialchars($header) . "</th>";
            }
            echo "</tr>";
            
            foreach ($records as $record) {
                echo "<tr>";
                foreach ($record as $value) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "No records found in the table.";
        }
        
        // Test insert functionality
        echo "<h3>Testing Insert Functionality:</h3>";
        
        $query = "INSERT INTO honey_production (hiveID, harvestDate, quantity, type, quality, notes) 
                 VALUES (1, CURDATE(), 5.5, 'Test Honey', 'Premium', 'Test record from diagnostic script')";
        
        try {
            $stmt = $conn->prepare($query);
            $result = $stmt->execute();
            
            if ($result) {
                $lastId = $conn->lastInsertId();
                echo "Test record inserted successfully with ID: $lastId<br>";
                
                // Clean up test record
                $query = "DELETE FROM honey_production WHERE productionID = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$lastId]);
                echo "Test record cleaned up.<br>";
            } else {
                echo "Failed to insert test record.<br>";
            }
        } catch (PDOException $e) {
            echo "Error inserting test record: " . $e->getMessage() . "<br>";
        }
        
    } else {
        echo "Table 'honey_production' does not exist!<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
