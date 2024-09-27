<?php
class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Method to get user by username
    public function getUserByUsername($username)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Method to check if username exists
    public function usernameExists($username)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetchColumn() > 0; // Returns true if username exists
    }

    // Method to check if email exists
    public function emailExists($email)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn() > 0; // Returns true if email exists
    }

    // Method to get user by email
    public function getUserByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Method to get user by reset token
    public function getUserByToken($token)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE reset_token = :token AND token_expiry > NOW()");
        $stmt->execute(['token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Method to register a new user
    public function register($username, $password, $email, $token)
    {
        // Check if username already exists
        if ($this->usernameExists($username)) {
            throw new Exception("Username already exists.");
        }

        // Check if email already exists
        if ($this->emailExists($email)) {
            throw new Exception("Email already exists.");
        }

        // Insert new user into the database
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password, email, verification_token) VALUES (:username, :password, :email, :token)");
        $stmt->execute([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'email' => $email,
            'token' => $token
        ]);
        return $stmt->rowCount() > 0;
    }

    // Reset failed login attempts
    public function resetFailedLoginAttempts($userId) {
        $sql = "UPDATE users SET failed_login_attempts = 0, last_failed_login = NULL WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
    }

    // Increment failed login attempts
    public function incrementFailedLoginAttempts($userId) {
        $sql = "UPDATE users SET failed_login_attempts = failed_login_attempts + 1, last_failed_login = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
    }

    // Method to verify user by token
    public function verify($token)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET verified = 1 WHERE verification_token = :token");
        $stmt->execute(['token' => $token]);
        return $stmt->rowCount() > 0;
    }

    // Method to store the reset token and expiry
    public function storeResetToken($userId, $token, $expiry)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET reset_token = :token, token_expiry = :expiry WHERE id = :id");
        $stmt->execute([
            'token' => $token,
            'expiry' => $expiry,
            'id' => $userId
        ]);
    }

    // Method to update the user's password
    public function updatePassword($userId, $password)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET password = :password, reset_token = NULL, token_expiry = NULL WHERE id = :id");
        $stmt->execute([
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'id' => $userId
        ]);
    }

    // Optional: Method to check if a user is verified
    public function isUserVerified($userId)
    {
        $stmt = $this->pdo->prepare("SELECT verified FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchColumn() == 1; // Returns true if verified
    }
}
?>
