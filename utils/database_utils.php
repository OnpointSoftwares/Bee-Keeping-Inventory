<?php
/**
 * Database Utility Functions
 * 
 * This file contains utility functions for database operations
 * in the Beekeeping Inventory Management System
 */

// Include database configuration
require_once __DIR__ . '/../config/database.php';

/**
 * Get database connection
 * 
 * @return PDO Database connection
 */
function getDbConnection() {
    $db = new Database();
    return $db->connect();
}

/**
 * Execute a SELECT query
 * 
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return array Result rows
 */
function selectQuery($sql, $params = []) {
    $conn = getDbConnection();
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Execute a SELECT query and return a single row
 * 
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return array|false Result row or false if no rows
 */
function selectSingleRow($sql, $params = []) {
    $conn = getDbConnection();
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Insert data into a table
 * 
 * @param string $table Table name
 * @param array $data Associative array of column => value
 * @return int|false Last insert ID or false on failure
 */
function insertData($table, $data) {
    $conn = getDbConnection();
    $columns = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));
    
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $conn->prepare($sql);
    
    foreach ($data as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    
    if ($stmt->execute()) {
        return $conn->lastInsertId();
    }
    
    return false;
}

/**
 * Update data in a table
 * 
 * @param string $table Table name
 * @param array $data Associative array of column => value to update
 * @param string $condition WHERE condition (without the WHERE keyword)
 * @param array $params Parameters for the condition
 * @return bool Success or failure
 */
function updateData($table, $data, $condition, $params = []) {
    $conn = getDbConnection();
    $setClause = '';
    
    foreach ($data as $key => $value) {
        $setClause .= "$key = :set_$key, ";
    }
    $setClause = rtrim($setClause, ', ');
    
    $sql = "UPDATE $table SET $setClause WHERE $condition";
    $stmt = $conn->prepare($sql);
    
    // Bind set values with prefixed parameter names to avoid conflicts
    foreach ($data as $key => $value) {
        $stmt->bindValue(":set_$key", $value);
    }
    
    // Bind condition parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    
    return $stmt->execute();
}

/**
 * Delete data from a table
 * 
 * @param string $table Table name
 * @param string $condition WHERE condition (without the WHERE keyword)
 * @param array $params Parameters for the condition
 * @return bool Success or failure
 */
function deleteData($table, $condition, $params = []) {
    $conn = getDbConnection();
    $sql = "DELETE FROM $table WHERE $condition";
    $stmt = $conn->prepare($sql);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    
    return $stmt->execute();
}

/**
 * Count rows in a table
 * 
 * @param string $table Table name
 * @param string $condition Optional WHERE condition (without the WHERE keyword)
 * @param array $params Parameters for the condition
 * @return int Number of rows
 */
function countRows($table, $condition = '', $params = []) {
    $conn = getDbConnection();
    $sql = "SELECT COUNT(*) as count FROM $table";
    
    if (!empty($condition)) {
        $sql .= " WHERE $condition";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return (int)$result['count'];
}

/**
 * Execute a custom query
 * 
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return bool Success or failure
 */
function executeQuery($sql, $params = []) {
    $conn = getDbConnection();
    $stmt = $conn->prepare($sql);
    return $stmt->execute($params);
}

/**
 * Begin a transaction
 * 
 * @return bool Success or failure
 */
function beginTransaction() {
    $conn = getDbConnection();
    return $conn->beginTransaction();
}

/**
 * Commit a transaction
 * 
 * @return bool Success or failure
 */
function commitTransaction() {
    $conn = getDbConnection();
    return $conn->commit();
}

/**
 * Rollback a transaction
 * 
 * @return bool Success or failure
 */
function rollbackTransaction() {
    $conn = getDbConnection();
    return $conn->rollBack();
}
?>
