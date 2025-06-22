<?php
session_start();

// Hardcoded admin credentials
$valid_username = "admin";
$valid_password = "admin";

// Get form data
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Validate credentials
if ($username === $valid_username && $password === $valid_password) {
    // Authentication successful
    $_SESSION['admin'] = [
        'id' => 1,
        'username' => $valid_username,
        'nama' => 'Administrator'
    ];
    header("Location: afterlogin.php");
    exit;
} else {
    // Authentication failed
    $_SESSION['login_error'] = "Username atau password salah!";
    header("Location: login.php");
    exit;
}
?>