<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $captcha = $_POST['g-recaptcha-response'];

    // Verify reCAPTCHA response
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . SECRET_KEY . "&response=" . $captcha);
    $responseKeys = json_decode($response, true);

    // Backend password criteria check
    $password_errors = [];
    if (!preg_match('/[a-z]/', $password)) {
        $password_errors[] = "Password must include at least one lowercase letter.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $password_errors[] = "Password must include at least one uppercase letter.";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $password_errors[] = "Password must include at least one number.";
    }
    if (!preg_match('/[!@#$%^&*]/', $password)) {
        $password_errors[] = "Password must include at least one special character (!@#$%^&*).";
    }
    if (strlen($password) < 8) {
        $password_errors[] = "Password must be at least 8 characters long.";
    }

    // Backend validations
    if (intval($responseKeys["success"]) !== 1) {
        $error = "Please verify that you are not a robot.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!empty($password_errors)) {
        $error = implode(" ", $password_errors); // Combine all password criteria errors
    } else {
        $user = new User($pdo);

        // Check for existing username or email
        if ($user->usernameExists($username)) {
            $error = "Username already exists. Please choose a different username.";
        } elseif ($user->emailExists($email)) {
            $error = "Email already exists. Please use a different email address.";
        } else {
            try {
                // Generate a confirmation token
                $token = bin2hex(random_bytes(32));

                if ($user->register($username, $password, $email, $token)) {
                    // Send confirmation email
                    $mail = new PHPMailer(true);
                    try {
                        // Server settings
                        $mail->isSMTP();
                        $mail->Host = SMTP_HOST;
                        $mail->SMTPAuth = true;
                        $mail->Username = SMTP_USER;
                        $mail->Password = SMTP_PASS;
                        $mail->SMTPSecure = 'tls';
                        $mail->Port = SMTP_PORT;

                        // Recipients
                        $mail->setFrom(SMTP_USER, 'Secure App');
                        $mail->addAddress($email);

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Email Verification';
                        $mail->Body    = "Hi $username, <br>Please verify your email by clicking on the link below: <br><a href='http://localhost/secure_app/public/verify.php?token=$token'>Verify Email</a>";

                        $mail->send();
                        $_SESSION['message'] = "Registration successful. Please check your email to verify your account.";
                        header('Location: login.php');
                        exit();
                    } catch (Exception $e) {
                        $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }
                }
            } catch (Exception $e) {
                $error = $e->getMessage(); // Display the exception message (e.g., username already exists)
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Secure App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body {
            background-image: url('/secure_app/images/register.jpeg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .form-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        h2 {
            font-weight: 700;
            color: #333;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-group label {
            font-weight: 600;
            color: #555;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0.75rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .password-hints {
            font-size: 0.875rem;
            margin-top: 0.5rem;
            color: #666;
        }

        .valid {
            color: #28a745;
        }

        .invalid {
            color: #dc3545;
        }

        .password-strength {
            margin-top: 0.5rem;
            font-weight: bold;
        }

        .weak { color: #dc3545; }
        .medium { color: #ffc107; }
        .strong { color: #28a745; }

        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
        }

        .g-recaptcha {
            margin-bottom: 1rem;
        }

        .text-muted {
            font-size: 0.875rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container fade-in">
            <h2>Register</h2>
            <?php if (isset($error)) echo "<p class='text-danger text-center'>$error</p>"; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div id="password-hints" class="password-hints">
                            <small id="lowercase" class="invalid">Lowercase letter, </small>
                            <small id="uppercase" class="invalid">Uppercase letter, </small>
                            <small id="number" class="invalid">Number, </small>
                            <small id="special" class="invalid">Special character(!@#$%^&*), </small>
                            <small id="length" class="invalid">At least 8 characters</small>
                        </div>
                        <div id="password-strength" class="password-strength">Password Strength: </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <div class="g-recaptcha" data-sitekey="6LehgSwqAAAAAC6OGBeUeQPj08AH9ssc_vGH6NBO"></div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            <p class="text-muted text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
            <p><a href="index.php">Back to Home</a></p>
        </div>
    </div>

    <script>
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const passwordHints = document.getElementById('password-hints');
        const passwordStrength = document.getElementById('password-strength');
        const lowercase = document.getElementById('lowercase');
        const uppercase = document.getElementById('uppercase');
        const number = document.getElementById('number');
        const special = document.getElementById('special');
        const length = document.getElementById('length');

        password.addEventListener('input', function () {
            let passValue = password.value;
            let strength = 0;

            // Check for lowercase
            if (/[a-z]/.test(passValue)) {
                lowercase.classList.remove('invalid');
                lowercase.classList.add('valid');
                strength++;
            } else {
                lowercase.classList.remove('valid');
                lowercase.classList.add('invalid');
            }

            // Check for uppercase
            if (/[A-Z]/.test(passValue)) {
                uppercase.classList.remove('invalid');
                uppercase.classList.add('valid');
                strength++;
            } else {
                uppercase.classList.remove('valid');
                uppercase.classList.add('invalid');
            }

            // Check for numbers
            if (/[0-9]/.test(passValue)) {
                number.classList.remove('invalid');
                number.classList.add('valid');
                strength++;
            } else {
                number.classList.remove('valid');
                number.classList.add('invalid');
            }

            // Check for special characters
            if (/[!@#$%^&*]/.test(passValue)) {
                special.classList.remove('invalid');
                special.classList.add('valid');
                strength++;
            } else {
                special.classList.remove('valid');
                special.classList.add('invalid');
            }

            // Check for length
            if (passValue.length >= 8) {
                length.classList.remove('invalid');
                length.classList.add('valid');
                strength++;
            } else {
                length.classList.remove('valid');
                length.classList.add('invalid');
            }

            // Display password strength
            if (strength < 3) {
                passwordStrength.textContent = 'Password Strength: Weak';
                passwordStrength.className = 'password-strength weak';
            } else if (strength < 5) {
                passwordStrength.textContent = 'Password Strength: Medium';
                passwordStrength.className = 'password-strength medium';
            } else {
                passwordStrength.textContent = 'Password Strength: Strong';
                passwordStrength.className = 'password-strength strong';
            }
        });

    </script>
</body>
</html>
