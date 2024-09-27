<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../classes/User.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch user details
$query = $pdo->prepare("SELECT password, two_factor_enabled FROM users WHERE id = ?");
$query->execute([$userId]);
$user = $query->fetch(PDO::FETCH_ASSOC);
$hashedPassword = $user['password'];
$is2FAEnabled = $user['two_factor_enabled'];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // For changing the password
    if (!empty($_POST['old_password']) && !empty($_POST['password']) && !empty($_POST['confirm_password'])) {
        $oldPassword = $_POST['old_password'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        // Verify the old password
        if (password_verify($oldPassword, $hashedPassword)) {
            // Check if the new password matches the confirmation password
            if ($password === $confirmPassword) {
                // Check if new password is different from the old one
                if (!password_verify($password, $hashedPassword)) {
                    // Check password complexity
                    if (preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[\W_]).{8,}$/', $password)) {
                        // Update password in the database
                        $newHashedPassword = password_hash($password, PASSWORD_BCRYPT);
                        $query = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                        $query->execute([$newHashedPassword, $userId]);
                        $success = "Password updated successfully.";
                    } else {
                        $error = "Password must be at least 8 characters long, and include an uppercase letter, a lowercase letter, a number, and a special character.";
                    }
                } else {
                    $error = "New password cannot be the same as the old password.";
                }
            } else {
                $error = "Passwords do not match.";
            }
        } else {
            $error = "Old password is incorrect.";
        }
    }

    // Handle 2FA enabling/disabling
    if (isset($_POST['enable_2fa'])) {
        $query = $pdo->prepare("UPDATE users SET two_factor_enabled = 1 WHERE id = ?");
        $query->execute([$userId]);
        $success .= " Two-factor authentication enabled.";
    } elseif (isset($_POST['disable_2fa'])) {
        $query = $pdo->prepare("UPDATE users SET two_factor_enabled = 0 WHERE id = ?");
        $query->execute([$userId]);
        $success .= " Two-factor authentication disabled.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --background-color: #ecf0f1;
            --card-color: #ffffff;
            --text-color: #34495e;
            --error-color: #e74c3c;
            --success-color: #2ecc71;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background-color);
            margin: 0;
            padding: 0;
            color: var(--text-color);
            line-height: 1.6;
        }
        .edit-profile-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 40px;
            background-color: var(--card-color);
            border-radius: 10px;
            box-shadow: var(--shadow);
        }
        h1 {
            color: var(--secondary-color);
            font-weight: 600;
            margin: 0 0 30px;
            font-size: 28px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--secondary-color);
        }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        input[type="password"]:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        .checkbox-group {
            display: flex;
            align-items: center;
        }
        input[type="checkbox"] {
            margin-right: 10px;
        }
        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
            display: inline-block;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background-color: #95a5a6;
        }
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        .error-message, .success-message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .error-message {
            background-color: #fadbd8;
            color: var(--error-color);
        }
        .success-message {
            background-color: #d4efdf;
            color: var(--success-color);
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="edit-profile-container">
        <h1>Edit Profile</h1>

        <?php if ($success): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Password update form -->
        <form action="edit_profile.php" method="post">
            <div class="form-group">
                <label for="old_password">
                    <i class="fas fa-lock"></i> Old Password
                </label>
                <input type="password" name="old_password" id="old_password" required>
            </div>
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> New Password
                </label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">
                    <i class="fas fa-lock"></i> Confirm Password
                </label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <div class="button-group">
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i> Update Password
                </button>
            </div>
        </form>

        <!-- 2FA toggle form -->
        <form action="edit_profile.php" method="post">
            <div class="form-group checkbox-group">
                <?php if ($is2FAEnabled): ?>
                    <input type="checkbox" name="disable_2fa" id="disable_2fa" checked>
                    <label for="disable_2fa">
                        <i class="fas fa-shield-alt"></i> Disable Two-Factor Authentication
                    </label>
                <?php else: ?>
                    <input type="checkbox" name="enable_2fa" id="enable_2fa">
                    <label for="enable_2fa">
                        <i class="fas fa-shield-alt"></i> Enable Two-Factor Authentication
                    </label>
                <?php endif; ?>
            </div>
            <div class="button-group">
                <button type="submit" class="btn">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </form>
    </div>
</body>
</html>
