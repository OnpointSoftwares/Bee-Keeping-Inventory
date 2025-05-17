<?php
/**
 * Base Model Class
 * 
 * This class serves as the foundation for all models in the Beekeeping Inventory Management System.
 * It provides common database operations and standardizes the model structure.
 */

require_once __DIR__ . '/../config/database.php';

abstract class BaseModel {
    protected $conn;
    protected $table;
    protected $primaryKey = 'id';

    /**
     * Constructor - establishes database connection
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Create a new record
     * 
     * @param array $data Associative array of column => value
     * @return int|bool Last insert ID or false on failure
     */
    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Error creating record: " . $e->getMessage());
        }
    }

    /**
     * Read a single record by ID
     * 
     * @param int $id Record ID
     * @return array|false Record data or false if not found
     */
    public function read($id) {
        $query = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error reading record: " . $e->getMessage());
        }
    }

    /**
     * Read all records
     * 
     * @param string $orderBy Column to order by
     * @param string $direction Sort direction (ASC or DESC)
     * @return array Records
     */
    public function readAll($orderBy = null, $direction = 'DESC') {
        $query = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $query .= " ORDER BY $orderBy $direction";
        }
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error reading records: " . $e->getMessage());
        }
    }

    /**
     * Update a record
     * 
     * @param int $id Record ID
     * @param array $data Associative array of column => value
     * @return bool Success or failure
     */
    public function update($id, $data) {
        $setClause = '';
        
        foreach ($data as $key => $value) {
            $setClause .= "$key = :$key, ";
        }
        $setClause = rtrim($setClause, ', ');
        
        $query = "UPDATE {$this->table} SET $setClause WHERE {$this->primaryKey} = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->bindValue(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error updating record: " . $e->getMessage());
        }
    }

    /**
     * Delete a record
     * 
     * @param int $id Record ID
     * @return bool Success or failure
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error deleting record: " . $e->getMessage());
        }
    }

    /**
     * Find records by condition
     * 
     * @param string $condition WHERE condition (without the WHERE keyword)
     * @param array $params Parameters for the condition
     * @param string $orderBy Column to order by
     * @param string $direction Sort direction (ASC or DESC)
     * @return array Records
     */
    public function findBy($condition, $params = [], $orderBy = null, $direction = 'DESC') {
        $query = "SELECT * FROM {$this->table} WHERE $condition";
        
        if ($orderBy) {
            $query .= " ORDER BY $orderBy $direction";
        }
        
        try {
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error finding records: " . $e->getMessage());
        }
    }

    /**
     * Count records
     * 
     * @param string $condition Optional WHERE condition (without the WHERE keyword)
     * @param array $params Parameters for the condition
     * @return int Number of records
     */
    public function count($condition = '', $params = []) {
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if (!empty($condition)) {
            $query .= " WHERE $condition";
        }
        
        try {
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return (int)$result['count'];
        } catch (PDOException $e) {
            throw new Exception("Error counting records: " . $e->getMessage());
        }
    }

    /**
     * Begin a transaction
     * 
     * @return bool Success or failure
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    /**
     * Commit a transaction
     * 
     * @return bool Success or failure
     */
    public function commitTransaction() {
        return $this->conn->commit();
    }

    /**
     * Rollback a transaction
     * 
     * @return bool Success or failure
     */
    public function rollbackTransaction() {
        return $this->conn->rollBack();
    }

    /**
     * Execute a custom query
     * 
     * @param string $query SQL query
     * @param array $params Parameters for prepared statement
     * @param bool $fetchAll Whether to fetch all results or just one row
     * @return mixed Query results or boolean success status
     */
    public function executeQuery($query, $params = [], $fetchAll = true) {
        try {
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->execute();
            
            if (stripos($query, 'SELECT') === 0) {
                return $fetchAll ? $stmt->fetchAll(PDO::FETCH_ASSOC) : $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error executing query: " . $e->getMessage());
        }
    }
}
?>
