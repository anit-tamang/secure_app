<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../classes/User.php';

$error = ""; // Initialize error message variable
$message = ""; // Initialize success message variable

// Get the token from the URL
$token = isset($_GET['token']) ? sanitize_input($_GET['token']) : '';

// Only process the form if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve token from the hidden input field
    $token = sanitize_input($_POST['token']);
    $newPassword = sanitize_input($_POST['new_password']);
    $confirmPassword = sanitize_input($_POST['confirm_password']);

    $user = new User($pdo);
    $storedUser = $user->getUserByToken($token);

    if ($storedUser) {
        if (strtotime($storedUser['token_expiry']) > time()) {
            // Password confirmation check
            if ($newPassword !== $confirmPassword) {
                $error = "Passwords do not match.";
            }
            // Check if the new password is the same as the old password
            elseif (password_verify($newPassword, $storedUser['password'])) {
                $error = "New password cannot be the same as the old password.";
            } 
            // Password complexity check
            elseif (!preg_match("/[A-Z]/", $newPassword) || // Check for uppercase letter
                    !preg_match("/[a-z]/", $newPassword) || // Check for lowercase letter
                    !preg_match("/[0-9]/", $newPassword) || // Check for digit
                    !preg_match("/[\W_]/", $newPassword) || // Check for special character
                    strlen($newPassword) < 8) { // Minimum length
                $error = "Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.";
            } else {
                // Update the user's password
                $user->updatePassword($storedUser['id'], $newPassword);
                $message = "Your password has been updated successfully.";

                // Redirect after 2 seconds
                header("Refresh: 2; url=login.php");
            }
        } else {
            $error = "The reset link has expired.";
        }
    } else {
        $error = "Invalid reset token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('../images/reset.jpeg'); 
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5); 
            z-index: -1; 
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        .success-message {
            color: green;
        }

        .error-message {
            color: red;
        }

        .form-group label {
            color: #555;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: none;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>
    <div class="login-container">
        <h2>Reset Password</h2>
        <?php if (!empty($message)) echo "<p class='success-message'>$message</p>"; ?>
        <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>
        
        <!-- Show the form only if there's no success message -->
        <?php if (empty($message)) : ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="form-group">
                    <label for="new_password">Enter new password:</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm new password:</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Update Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
