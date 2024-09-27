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

$username = htmlspecialchars($_SESSION['username']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --background-color: #ecf0f1;
            --card-color: #ffffff;
            --text-color: #34495e;
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
        .dashboard-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 30px;
        }
        .header {
            background-color: var(--card-color);
            border-radius: 10px;
            padding: 20px 30px;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        h1 {
            color: var(--secondary-color);
            font-weight: 600;
            margin: 0;
            font-size: 24px;
        }
        .user-info {
            display: flex;
            align-items: center;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 600;
            margin-right: 15px;
        }
        .username {
            font-size: 16px;
            font-weight: 600;
            color: var(--secondary-color);
        }
        .dashboard-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        .dashboard-card {
            background-color: var(--card-color);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .dashboard-card i {
            font-size: 48px;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        .dashboard-card h2 {
            margin: 0 0 15px;
            color: var(--secondary-color);
            font-size: 20px;
            font-weight: 600;
        }
        .dashboard-card p {
            margin-bottom: 20px;
            color: #7f8c8d;
        }
        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
            display: inline-block;
        }
        .btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        .logout-container {
            text-align: center;
            margin-top: 40px;
        }
        .btn-logout {
            background-color: #e74c3c;
        }
        .btn-logout:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header class="header">
            <h1>Welcome, <?php echo $username; ?></h1>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo substr($username, 0, 1); ?>
                </div>
                <span class="username"><?php echo $username; ?></span>
            </div>
        </header>

        <main class="dashboard-content">
            <div class="dashboard-card">
                <i class="fas fa-user-circle"></i>
                <h2>View Profile</h2>
                <p>Review your personal information and account details.</p>
                <a href="view_profile.php" class="btn">View Profile</a>
            </div>
            <div class="dashboard-card">
                <i class="fas fa-user-edit"></i>
                <h2>Edit Profile</h2>
                <p>Update your account information and preferences.</p>
                <a href="edit_profile.php" class="btn">Edit Profile</a>
            </div>
        </main>

        <div class="logout-container">
            <form action="logout.php" method="post">
                <button type="submit" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('mouseenter', () => {
                button.style.transform = 'translateY(-2px)';
            });
            button.addEventListener('mouseleave', () => {
                button.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>
