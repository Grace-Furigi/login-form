
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Welcome!</h2>

    <h3>
        Hello,
        <?= htmlspecialchars($_SESSION["username"]) ?>
    </h3>

    <p>You have successfully logged in.</p>

    <a href="logout.php">
        <button>Logout</button>
    </a>
</div>

</body>
</html>
