<?php
// login.php - User Login
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 查找用户名
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // 验证密码
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: home.php"); // Redirect to profile page after successful login
    } else {
        echo "Incorrect username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Game Community</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/forum_home.css">
</head>
<body>
<!-- 顶部导航栏 -->
<div class="navbar">
        <div class="logo">
            <a href="#">Game Community</a>
        </div>
        <div class="menu">
            <a href="./index.php">Home</a>
            <a href="./home.php">Game Community</a>
            <a href="./create_post.php">Create New Post</a>
            <a href="./about.php">About Us</a>
        </div>
    </div>

<div class="container">
    <div class="auth-form">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </div>
</div>
</body>
</html>

