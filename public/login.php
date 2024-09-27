<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$block_duration = 180; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];
    $captcha = $_POST['g-recaptcha-response'];

    // Verify reCAPTCHA response
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . SECRET_KEY . "&response=" . $captcha);
    $responseKeys = json_decode($response, true);

    if (intval($responseKeys["success"]) !== 1) {
        $error = "Please verify that you are not a robot.";
    } else {
        $user = new User($pdo);
        $storedUser = $user->getUserByUsername($username);

        if ($storedUser) {
            // Check if the user is blocked due to too many failed attempts
            if ($storedUser['failed_login_attempts'] >= 3) {
                $last_failed_login = strtotime($storedUser['last_failed_login']);
                $current_time = time();
                $time_diff = $current_time - $last_failed_login;

                if ($time_diff < $block_duration) {
                    $time_remaining = ceil(($block_duration - $time_diff) / 60); 
                    $error = "Your account is temporarily locked due to multiple failed login attempts. Please try again in $time_remaining seconds.";
                } else {
                    // Reset failed attempts after the block period ends
                    $user->resetFailedLoginAttempts($storedUser['id']);
                }
            }

            if (!isset($error)) {
                if ($storedUser['verified']) {
                    if (password_verify($password, $storedUser['password'])) {
                        // Successful login, reset failed attempts
                        $user->resetFailedLoginAttempts($storedUser['id']);
                        
                        if ($storedUser['two_factor_enabled']) {
                            // Generate OTP and send via email
                            $otp = mt_rand(100000, 999999);
                            $_SESSION['otp'] = $otp;
                            $_SESSION['user_id'] = $storedUser['id'];
                            $_SESSION['username'] = $storedUser['username'];
                            $_SESSION['awaiting_2fa'] = true;

                            // Send OTP via email
                            $mail = new PHPMailer(true);
                            try {
                                // Server settings
                                $mail->isSMTP();
                                $mail->Host = 'smtp.gmail.com';
                                $mail->SMTPAuth = true;
                                $mail->Username = 'anittamang47@gmail.com';
                                $mail->Password = 'rtoilxdfppjnauba';
                                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                                $mail->Port = 587;

                                // Recipients
                                $mail->setFrom('anittamang47@gmail.com', 'Secure App');
                                $mail->addAddress($storedUser['email']);

                                // Content
                                $mail->isHTML(true);
                                $mail->Subject = 'Your OTP for Secure Login';
                                $mail->Body = "Your OTP is: $otp";
                                $mail->AltBody = "Your OTP is: $otp";

                                $mail->send();
                                header('Location: otp_verification.php');
                                exit();
                            } catch (Exception $e) {
                                $error = "Failed to send OTP email. Mailer Error: {$mail->ErrorInfo}";
                            }
                        } else {
                            // 2FA not enabled, proceed to dashboard
                            $_SESSION['user_id'] = $storedUser['id'];
                            $_SESSION['username'] = $storedUser['username'];
                            $_SESSION['role'] = $storedUser['role'];
                            header('Location: dashboard.php');
                            exit();
                        }
                    } else {
                        // Password is incorrect, increment failed login attempts
                        $user->incrementFailedLoginAttempts($storedUser['id']);
                        $error = "Invalid username or password.";
                    }
                } else {
                    $error = "Please verify your email before logging in.";
                }
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Login - Your Cyber Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

        :root {
            --primary-color: #3a0ca3;
            --secondary-color: #4361ee;
            --accent-color: #4cc9f0;
            --background-color: #f8f9fa;
            --text-color: #2b2d42;
            --error-color: #ef233c;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .container {
            display: flex;
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
            overflow: hidden;
        }

        .row {
            min-height: 600px;
        }

        .login-container {
            padding: 3rem;
            width: 50%;
            text-align: center;
        }

        .image-container {
            width: 50%;
            background-image: url('/secure_app/images/login.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        h2 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.075);
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(76, 201, 240, 0.25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .g-recaptcha {
            margin-bottom: 1.5rem;
        }

        .login-footer {
            font-size: 0.9rem;
            color: #666;
            margin-top: 1.5rem;
        }

        .login-footer a {
            color: var(--secondary-color);
            text-decoration: underline;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-footer a:hover {
            color: var(--primary-color);
            text-decoration: none;
        }

        .error-message {
            color: var(--error-color);
            font-weight: 500;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background-color: rgba(239, 35, 60, 0.1);
            border-radius: 5px;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .login-container,
            .image-container {
                width: 100%;
            }

            .image-container {
                min-height: 200px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="image-container"></div>
        <div class="login-container">
            <h2><i class="fas fa-shield-alt"></i> Secure Login</h2>
            <?php if (isset($error)) echo "<p class='error-message'><i class='fas fa-exclamation-circle'></i> $error</p>"; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="g-recaptcha" data-sitekey="<?php echo SITE_KEY; ?>"></div>
                <button type="submit" class="btn btn-primary">Login</button>
                <div class="login-footer">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                    <p><a href="forgot_password.php">Forgot your password?</a></p>
                    <p><a href="index.php">Back to Home</a></p>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
