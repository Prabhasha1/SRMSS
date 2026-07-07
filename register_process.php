<?php
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if any fields are empty
    if (empty($username) || empty($role) || empty($password) || empty($confirm_password)) {
        header("Location: register.php?error=All fields are required");
        exit;
    }

    // Verify password match
    if ($password !== $confirm_password) {
        header("Location: register.php?error=Passwords do not match");
        exit;
    }

    try {
        // Business Logic: Check if username already exists in database
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = :username");
        $checkStmt->execute(['username' => $username]);
        
        if ($checkStmt->rowCount() > 0) {
            header("Location: register.php?error=Username is already taken");
            exit;
        }

        // Securely Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Data Layer Interaction: Insert user record
        $insertStmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
        $insertStmt->execute([
            'username' => $username,
            'password' => $hashed_password,
            'role' => $role
        ]);

        header("Location: register.php?success=Account registered successfully! You can now log in.");
        exit;

    } catch (PDOException $e) {
        header("Location: register.php?error=System error occurred: " . $e->getMessage());
        exit;
    }
} else {
    header("Location: register.php");
    exit;
}
?>