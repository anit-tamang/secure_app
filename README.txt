Secure User Login System
Description:
This project implements a secure user management system featuring user registration, login, password reset with email verification via PHPMailer, profile management, and CAPTCHA validation using Google reCAPTCHA. It ensures secure password management with real-time strength evaluation and supports email-based account verification and password resets.
System Requirements 
1. Software Requirements:
•	Operating System:
o	Windows 10 or higher, macOS, or any Linux distribution.
•	Web Server:
o	Apache (with XAMPP).
•	Database:
o	MySQL
•	PHP Version:
o	PHP 7.4 or higher.
•	Composer:
o	Required for managing PHP dependencies.
•	Browser:
o	Google Chrome, Firefox, or any modern web browser.
2. Dependencies:
•	PHPMailer:
o	For sending email verifications and password reset emails.
•	Google reCAPTCHA:
o	For CAPTCHA verification in the registration form.
•	PDO Extension:
o	Required for database interaction.
3. Hardware Requirements:
•	Processor:
o	Dual-core processor or higher.
•	Memory (RAM):
o	Minimum 2 GB RAM.
•	Disk Space:
o	At least 200 MB of free disk space for the project files and dependencies.
•	Internet:
o	Active internet connection for email services and reCAPTCHA integration.
________________________________________
Features:
1.	User Registration: New users can register with real-time password strength feedback and CAPTCHA protection.
2.	Email Verification: Users must verify their email after registration before logging in.
3.	Login: Users can log in after verifying their email.
4.	Password Reset: Password reset functionality with email verification.
5.	Profile Management: Allows users to view and update their profiles, including password changes.
6.	CAPTCHA Validation: Google reCAPTCHA ensures that only real users can sign up.

________________________________________
Installation Instructions:
1. Clone or Download the Project Files:
•	Unzip the project files and place them in your web root directory (htdocs for XAMPP, www for WAMP, etc.).
2. Database Setup:
•	Create a MySQL database.
•	Import the database.sql file (if provided) to set up the required tables.
•	Update your database credentials in includes/config.php:

php
Copy code
define('DB_HOST', 'your_db_host');
define('DB_NAME', 'your_db_name');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

3. Install Dependencies:
•	Ensure Composer is installed on your system.
•	Run composer install in the project root to install PHPMailer and other dependencies.
4. Google reCAPTCHA Setup:
•	Obtain a Google reCAPTCHA v2 API key from Google reCAPTCHA.
•	Add the site and secret keys in includes/config.php:
php
Copy code
define('RECAPTCHA_SITE_KEY', 'your_site_key');
define('RECAPTCHA_SECRET_KEY', 'your_secret_key');
5. Configure PHPMailer:
•	In includes/config.php, configure the SMTP settings for PHPMailer:
php
Copy code
define('SMTP_HOST', 'your_smtp_host');
define('SMTP_USER', 'your_email_address');
define('SMTP_PASS', 'your_email_password');
define('SMTP_PORT', 587);  // Or 465 for SSL
6. Run the Application:
•	Start your local server (e.g., XAMPP).
•	Access the application through your browser at http://localhost/your_project_directory/public/index.php.
________________________________________
Usage:
1.	User Registration:
o	Navigate to register.php to create a new account with real-time password strength indicators and CAPTCHA protection.
2.	Email Verification:
o	Upon registration, a verification email will be sent. Users need to verify their email before logging in.
3.	Login:
o	Go to login.php to log in with your registered email and password.
4.	Forgot Password:
o	Go to forgot_password.php to request a password reset email. After receiving the reset link, visit reset_password.php to change your password.
5.	Profile Management:
o	Users can update their details by navigating to edit_profile.php. They must provide their old password to change the new one.
________________________________________
File Structure:
Classes:
•	classes/User.php: Contains user-related functionality, such as registration, login, password reset, email verification, etc.
Images:
•	images/: Contains images used in the application (e.g., background or profile pictures).
Includes:
•	config.php: Configuration file for database connection, SMTP, and CAPTCHA settings.
•	db.php: Database connection helper.
•	functions.php: Helper functions used across the application.
Public Directory:
•	dashboard.php: Displays the user's dashboard after login.
•	edit_profile.php: Allows users to update their profile, including password changes.
•	forgot_password.php: Page for users to request password reset via email.
•	index.php: The main landing page.
•	login.php: Page for user login.
•	logout.php: Handles user logout.
•	otp_verification.php: (Optional) Handles OTP verification.
•	register.php: User registration page with CAPTCHA and password strength evaluation.
•	reset_password.php: Page for resetting a password using a link sent to the user’s email.
•	verify.php: Verifies user email via token after registration.
•	view_profile.php: Displays the user’s profile.
Vendor:
•	Contains third-party libraries installed via Composer (such as PHPMailer).
________________________________________
Supplementary Files:
•	composer.json: Defines project dependencies.
•	composer.lock: Locks the specific versions of dependencies.
•	vendor/: Contains third-party packages (e.g., PHPMailer).
________________________________________
Troubleshooting:
1.	Database Errors:
o	Verify the database connection details in includes/config.php.
o	Ensure the database is correctly set up with the database.sql file.
2.	Email Not Sending:
o	Confirm the SMTP details in includes/config.php.
o	Check if your SMTP server allows outgoing emails.
3.	CAPTCHA Not Working:
o	Double-check the Google reCAPTCHA site and secret keys in includes/config.php.
4.	Password Reset Issues:
o	Ensure proper synchronization between the password reset token generation and expiration.
________________________________________
