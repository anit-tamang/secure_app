<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../classes/User.php';

if (isset($_GET['token'])) {
    $token = sanitize_input($_GET['token']);
    $user = new User($pdo);

    if ($user->verify($token)) {
        $_SESSION['message'] = "Your email has been successfully verified. You can now log in.";
    } else {
        $_SESSION['error'] = "Invalid or expired verification link.";
    }

    header('Location: login.php');
    exit();
} else {
    $_SESSION['error'] = "No verification token provided.";
    header('Location: login.php');
    exit();
}
?>
