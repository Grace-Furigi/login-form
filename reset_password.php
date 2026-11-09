
<?php
require_once "db.php";

$message = "";
$validToken = false;

$token = $_GET["token"] ?? $_POST["token"] ?? "";

if (is_string($token) && preg_match('/\A[a-f0-9]{64}\z/', $token)) {

    $tokenHash = hash("sha256", $token);

    $stmt = $conn->prepare(
        "SELECT email FROM password_resets
         WHERE token_hash = :token
         AND expires_at > NOW()"
    );

    $stmt->execute([":token" => $tokenHash]);

    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    $validToken = (bool) $reset;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $validToken) {

    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if (strlen($password) < 8) {

        $message = "Password must contain at least 8 characters.";

    } elseif ($password !== $confirmPassword) {

        $message = "Passwords do not match.";

    } else {

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $conn->beginTransaction();

        try {

            $update = $conn->prepare(
                "UPDATE users
                 SET password = :password
                 WHERE email = :email"
            );

            $update->execute([
                ":password" => $hashedPassword,
                ":email" => $reset["email"]
            ]);

            $delete = $conn->prepare(
                "DELETE FROM password_resets
                 WHERE email = :email"
            );

            $delete->execute([
                ":email" => $reset["email"]
            ]);

            $conn->commit();

            header("Location: index.php?reset=success");
            exit;

        } catch (Exception $e) {

            $conn->rollBack();
            $message = "Unable to reset password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <div class="logo">
        <img src="images/logo.png" alt="Wester University Logo">
    </div>

    <h2>Reset Password</h2>

    <?php if ($validToken): ?>

        <p>Enter your new password below.</p>

        <form method="POST" action="reset_password.php">

            <input type="hidden" name="token"
                   value="<?= htmlspecialchars($token) ?>">

            <input type="password"
                   name="password"
                   placeholder="New Password"
                   minlength="8"
                   required>

            <input type="password"
                   name="confirm_password"
                   placeholder="Confirm New Password"
                   minlength="8"
                   required>

            <button type="submit">Reset Password</button>

        </form>

    <?php else: ?>

        <p>This password reset link is invalid or expired.</p>

        <a href="forgot_password.php">Request another reset link</a>

    <?php endif; ?>

    <p><?= htmlspecialchars($message) ?></p>

    <a href="index.php">Back to Login</a>

</div>

</body>
</html>
