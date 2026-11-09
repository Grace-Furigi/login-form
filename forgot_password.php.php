
<?php
require_once "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {

        $stmt = $conn->prepare(
            "SELECT id FROM users WHERE email = :email"
        );

        $stmt->execute([":email" => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            // Generate a secure random token
            $token = bin2hex(random_bytes(32));

            // Store only the hashed token
            $tokenHash = hash("sha256", $token);

            $expiry = date("Y-m-d H:i:s", time() + 1800);

            // Remove previous reset requests
            $delete = $conn->prepare(
                "DELETE FROM password_resets WHERE email = :email"
            );

            $delete->execute([":email" => $email]);

            // Save new reset token
            $insert = $conn->prepare(
                "INSERT INTO password_resets
                (email, token_hash, expires_at)
                VALUES (:email, :token, :expiry)"
            );

            $insert->execute([
                ":email" => $email,
                ":token" => $tokenHash,
                ":expiry" => $expiry
            ]);

            // Change localhost to your actual domain when deployed
            $resetLink =
                "http://localhost/login/reset_password.php?token="
                . urlencode($token);

            $subject = "Wester University Password Reset";

            $body = "Hello,\n\n";
            $body .= "Click the link below to reset your password:\n";
            $body .= $resetLink . "\n\n";
            $body .= "This link expires in 30 minutes.\n";
            $body .= "If you did not request this, ignore this email.";

            $headers = "From: no-reply@yourdomain.com";

            // Send the reset email
            if (!mail($email, $subject, $body, $headers)) {
                error_log("Password reset email could not be sent.");
            }
        }

        // Do not reveal whether an email is registered
        $message = "If that email address is registered, "
                 . "a password reset link will be sent.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <div class="logo">
        <img src="images/logo.png" alt="Wester University Logo">
    </div>

    <h2>Forgot Password?</h2>

    <p>Enter your registered email address to reset your password.</p>

    <form method="POST" action="forgot_password.php">

        <input type="email"
               name="email"
               placeholder="Enter your email address"
               required>

        <button type="submit">Send Reset Link</button>

    </form>

    <p><?= htmlspecialchars($message) ?></p>

    <p>
        <a href="index.php">Back to Login</a>
    </p>

</div>

</body>
</html>
