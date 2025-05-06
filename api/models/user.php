<?php
class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getUserByUsername($username) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting user: " . $e->getMessage());
            return false;
        }
    }

    public function createUser($userData) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO users (username, password, full_name, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            return $stmt->execute([
                $userData['username'],
                $userData['password'],
                $userData['full_name']
            ]);
        } catch (PDOException $e) {
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }

    public function updatePassword($username, $hashedPassword) {
        try {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET password = ?, updated_at = NOW()
                WHERE username = ?
            ");
            return $stmt->execute([$hashedPassword, $username]);
        } catch (PDOException $e) {
            error_log("Error updating password: " . $e->getMessage());
            return false;
        }
    }

    public function updateUser($userId, $userData) {
        try {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET full_name = ?, updated_at = NOW()
                WHERE id = ?
            ");
            return $stmt->execute([$userData['full_name'], $userId]);
        } catch (PDOException $e) {
            error_log("Error updating user: " . $e->getMessage());
            return false;
        }
    }
}
