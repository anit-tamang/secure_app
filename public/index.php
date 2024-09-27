<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure App - Advanced Digital Security</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto:wght@300;400;700&display=swap');

        :root {
            --primary-color: #00ff9d;
            --secondary-color: #0084ff;
            --bg-color: #0a192f;
            --text-color: #e6f1ff;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .cyber-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(to right, rgba(0,255,157,0.1) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(0,255,157,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: -1;
            transform: perspective(500px) rotateX(60deg);
            animation: grid-move 20s linear infinite;
        }

        @keyframes grid-move {
            0% {
                background-position: 0 0;
            }
            100% {
                background-position: 50px 50px;
            }
        }

        .container {
            background: rgba(10, 25, 47, 0.8);
            border: 2px solid var(--primary-color);
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 255, 157, 0.3);
        }

        .container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(
                transparent,
                rgba(0, 255, 157, 0.1),
                transparent 30%
            );
            animation: rotate 4s linear infinite;
        }

        @keyframes rotate {
            100% {
                transform: rotate(360deg);
            }
        }

        h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 3px;
            position: relative;
        }

        .logo {
            width: 120px;
            margin-bottom: 30px;
            filter: drop-shadow(0 0 10px var(--primary-color));
        }

        .btn {
            font-family: 'Orbitron', sans-serif;
            padding: 12px 30px;
            margin: 10px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            z-index: -1;
            transition: transform 0.3s ease;
        }

        .btn-primary::before {
            transform: scaleX(0);
            transform-origin: right;
        }

        .btn-secondary::before {
            transform: scaleY(0);
            transform-origin: bottom;
        }

        .btn:hover::before {
            transform: scale(1);
        }

        .btn-primary {
            background-color: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-secondary {
            background-color: transparent;
            border: 2px solid var(--secondary-color);
            color: var(--secondary-color);
        }

        .btn:hover {
            color: var(--bg-color);
        }

        .cyber-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background-color: var(--primary-color);
            width: 2px;
            height: 2px;
            border-radius: 50%;
            animation: float 10s infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
            }
            50% {
                transform: translateY(-100px) translateX(100px);
            }
        }
    </style>
</head>
<body>
    <div class="cyber-grid"></div>
    <div class="cyber-particles">
        <?php for ($i = 0; $i < 50; $i++): ?>
            <div class="particle" style="
                left: <?= rand(0, 100) ?>%;
                top: <?= rand(0, 100) ?>%;
                animation-delay: -<?= rand(0, 10) ?>s;
            "></div>
        <?php endfor; ?>
    </div>
    <div class="container">
        <h1>Secure App</h1>
        <p class="lead mb-4">Advanced Digital Security Solutions</p>
        <div>
            <a href="login.php" class="btn btn-primary">
                <i class="fas fa-shield-alt"></i> Login
            </a>
            <a href="register.php" class="btn btn-secondary">
                <i class="fas fa-user-lock"></i> Register Account
            </a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>