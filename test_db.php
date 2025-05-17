<?php
// Simple script to test database connection and table structure
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

try {
    // Connect to database
    $database = new Database();
    $conn = $database->connect();
    echo "<h2>Database Connection</h2>";
    echo "Database connection successful!<br>";
    
    // Check tables
    $tables = ['honey_production', 'beehive', 'equipment', 'hive_health', 'users'];
    echo "<h2>Tables Check</h2>";
    
    foreach ($tables as $table) {
        $query = "SHOW TABLES LIKE '$table'";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo "Table '$table' exists.<br>";
            
            // Get table structure
            $query = "DESCRIBE $table";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h3>Structure of '$table':</h3>";
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
            
            // Count records
            $query = "SELECT COUNT(*) as count FROM $table";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "Number of records in '$table': $count<br>";
        } else {
            echo "Table '$table' does not exist!<br>";
        }
        
        echo "<hr>";
    }
    
} catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "Error: " . $e->getMessage();
}
?>
