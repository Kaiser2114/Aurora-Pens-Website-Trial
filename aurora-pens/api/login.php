<?php
// api/login.php
session_start(); // Start the session to manage login state
header('Content-Type: application/json');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Basic Validation
    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Email and password are required.']);
        exit;
    }

    try {
        // Find the user by email
        $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verify user exists AND the provided password matches the stored hash
        if ($user && password_verify($password, $user['password_hash'])) {
            
            // Password is correct; set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            echo json_encode(['status' => 'success', 'message' => 'Login successful!']);
        } else {
            // Generic error message for both wrong email and wrong password to prevent email enumeration
            echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
        }
    } catch (\PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database error.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>