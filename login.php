
<?php

session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $sql = "SELECT * FROM users WHERE email = :email";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ":email" => $email
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {

        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];

        header("Location: dashboard.php");
        exit();

    } else {

        echo "Invalid email or password.";
        echo "<br><a href='index.php'>Try Again</a>";

    }

} else {

    header("Location: index.php");
    exit();

}

?>
