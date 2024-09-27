<?php
// SMTP settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'anittamang47@gmail.com');
define('SMTP_PASS', 'rtoilxdfppjnauba'); 
define('SMTP_PORT', 587);

// reCAPTCHA settings
define('SITE_KEY', '6LehgSwqAAAAAC6OGBeUeQPj08AH9ssc_vGH6NBO'); 
define('SECRET_KEY', '6LehgSwqAAAAAH6TtQ3YnxZp_EpJMIcBkMjfOZDz'); 

// Database connection
$host = 'localhost'; //database host
$dbname = 'secure_app'; //database name
$dbuser = 'root'; //database username
$dbpass = ''; //database password


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
