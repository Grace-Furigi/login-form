
<?php
require_once "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strlen($password) < 8) {

        $message = "Please enter valid details. Password must be at least 8 characters.";

    } else {
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        try {
            $stmt = $conn->prepare(
                "INSERT INTO users (username, email, password)
                 VALUES (:username, :email, :password)"
            );

            $stmt->execute([
                ":username" => $username,
                ":email" => $email,
                ":password" => $hashedPassword
            ]);

            $message = "Registration successful! You can now log in.";

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "This email is already registered.";
            } else {
                $message = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <form method="POST" action="register.php">
        <h2>Create Account</h2>

        <p><?= htmlspecialchars($message) ?></p>

        <input type="text" name="username"
               placeholder="Username" required>

        <input type="email" name="email"
               placeholder="Email Address" required>

        <input type="password" name="password"
               placeholder="Password (minimum 8 characters)"
               minlength="8" required>

        <button type="submit">Register</button>

        <p>Already have an account?
            <a href="index.php">Login here</a>
        </p>
    </form>
</div>

</body>
</html>

