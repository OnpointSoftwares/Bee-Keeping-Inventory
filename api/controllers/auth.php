<?php
require_once(__DIR__ . '/../models/user.php');

class AuthController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function login($input) {
        // Log the incoming login request for debugging
        error_log('Login attempt: ' . json_encode($input));
        
        // Validate input
        if (!isset($input['username']) || !isset($input['password'])) {
            error_log('Login failed: Missing username or password');
            return ['success' => false, 'error' => 'Missing username or password'];
        }

        $username = trim($input['username']);
        $password = $input['password'];

        // Attempt login
        $user = $this->userModel->getUserByUsername($username);
        
        if (!$user) {
            error_log('Login failed: User not found - ' . $username);
            return ['success' => false, 'error' => 'Invalid username or password'];
        }

        if (!password_verify($password, $user['password'])) {
            error_log('Login failed: Invalid password for user - ' . $username);
            return ['success' => false, 'error' => 'Invalid username or password'];
        }

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['loggedIn'] = true;

        error_log('Login successful: ' . $username);
        return ['success' => true, 'message' => 'Login successful'];
    }

    public function register($input) {
        // Log incoming registration data
        error_log('Incoming registration data: ' . json_encode($input));

        // Validate input
        if (!isset($input['username']) || !isset($input['password']) || !isset($input['fullName'])) {
            error_log('Registration error: Missing required fields');
            return ['success' => false, 'error' => 'Missing required fields'];
        }

        $username = trim($input['username']);
        $password = $input['password'];
        $fullName = trim($input['fullName']);

        // Validate email format for username
        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
            error_log('Registration error: Invalid email format');
            return ['success' => false, 'error' => 'Invalid email format'];
        }

        // Check if username already exists
        if ($this->userModel->getUserByUsername($username)) {
            error_log('Registration error: Username already exists');
            return ['success' => false, 'error' => 'Username already exists'];
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Create user
        $result = $this->userModel->createUser([
            'username' => $username,
            'password' => $hashedPassword,
            'full_name' => $fullName
        ]);

        if (!$result) {
            error_log('Registration error: Failed to create user');
            return ['success' => false, 'error' => 'Failed to create user'];
        }

        error_log('Registration successful: ' . $username);
        return ['success' => true, 'message' => 'Registration successful'];
    }

    public function resetPassword($input) {
        // Log the reset password request
        error_log('Reset password attempt: ' . json_encode($input));
        
        // Validate input
        if (!isset($input['username']) || !isset($input['password'])) {
            error_log('Reset password error: Missing required fields');
            return ['success' => false, 'error' => 'Missing required fields'];
        }

        $username = trim($input['username']);
        $password = $input['password'];

        // Check if user exists
        if (!$this->userModel->getUserByUsername($username)) {
            error_log('Reset password error: User not found - ' . $username);
            return ['success' => false, 'error' => 'User not found'];
        }

        // Hash new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Update password
        $result = $this->userModel->updatePassword($username, $hashedPassword);

        if (!$result) {
            error_log('Reset password error: Failed to update password for user - ' . $username);
            return ['success' => false, 'error' => 'Failed to update password'];
        }

        error_log('Reset password successful: ' . $username);
        return ['success' => true, 'message' => 'Password reset successfully'];
    }
}
