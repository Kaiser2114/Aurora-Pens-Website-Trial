<?php
// api/register.php

// Force block any background errors from breaking the JSON string format
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and trim inputs cleanly
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'All structural fields are required.']);
        exit;
    }

    // Secure encryption matching NFR Security settings
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Double-check connection availability
        if (!$pdo) {
            echo json_encode(['status' => 'error', 'message' => 'Database connection context missing.']);
            exit;
        }

        // Execute query matching the exact reset schema attributes
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role, profile_pic) VALUES (?, ?, ?, 'customer', 'default.png')");
        $success = $stmt->execute([$username, $email, $password_hash]);

        if ($success) {
            echo json_encode(['status' => 'success', 'message' => 'Registration successful! You can now log in.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'The system failed to write the account record safely.']);
        }
    } catch (\PDOException $e) {
        // Clean handling of duplicate keys
        if ($e->getCode() == 23000 || strpos($e->getMessage(), '1062') !== false) {
            echo json_encode(['status' => 'error', 'message' => 'Username or Email address is already taken.']);
        } else {
            // Even if it completely breaks, return a safe JSON layout so JavaScript never drops line 55 again
            echo json_encode(['status' => 'error', 'message' => 'Database reject protocol: ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid structural request method.']);
}
exit;
?>