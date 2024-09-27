<?php
session_start();
if (!isset($_SESSION['awaiting_2fa']) || !$_SESSION['awaiting_2fa']) {
    header('Location: login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    if ($otp == $_SESSION['otp']) {
        unset($_SESSION['otp'], $_SESSION['awaiting_2fa']);
        $_SESSION['role'] = $storedUser['role']; 
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - Secure App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .otp-container {
            background-color: #ffffff;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .otp-container:hover {
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
            text-align: center;
            letter-spacing: 0.5em;
            font-size: 1.2em;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 8px;
            background-color: #007bff;
            border-color: #007bff;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            transform: translateY(-2px);
        }

        .error-message {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 1rem;
            margin-top: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
            text-align: center;
        }

        .otp-icon {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 1rem;
            text-align: center;
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
        <div class="otp-container fade-in">
            <div class="otp-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h2>OTP Verification</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="otp">Enter the OTP sent to your device:</label>
                    <input type="text" class="form-control" id="otp" name="otp" required maxlength="6" autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-circle mr-2"></i>Verify OTP
                </button>
            </form>
            <?php if (isset($error)) echo "<div class='error-message fade-in'><i class='fas fa-exclamation-circle mr-2'></i>$error</div>"; ?>
        </div>
    </div>

    <script>
        // Auto-focus on the OTP input field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('otp').focus();
        });

        // Only allow numbers in the OTP input
        document.getElementById('otp').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>