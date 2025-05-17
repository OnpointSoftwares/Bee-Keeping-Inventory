<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'beekeeping';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            // Use socket path for XAMPP on Linux
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';unix_socket=/opt/lampp/var/mysql/mysql.sock',
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw new Exception("Connection error: " . $e->getMessage());
        }

        return $this->conn;
    }
}
