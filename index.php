
<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <form action="login.php" method="POST"
          id="loginForm">

<div class="logo">
    <img src="WPU_logo.png" alt="WPU Logo">
</div>
          
        <h2>Welcome User</h2>
        <p>Please login to your account</p>

        <input type="email"
               name="email"
               placeholder="Email Address"
               required>

        <input type="password"
               name="password"
               id="password"
               placeholder="Password"
               required>

        <label class="show-password">
            <input type="checkbox" id="showPassword">
            Show Password
        </label>

        <button type="submit">Login</button>

    <p>Don't have an account?
        <a href="register.php">Register here</a>
           </p>

    <p class="forgot-link">
    <a href="forgot_password.php">Forgot Password?</a>
</p>

    </form>

</div>

<script src="script.js"></script>

</body>
</html>
