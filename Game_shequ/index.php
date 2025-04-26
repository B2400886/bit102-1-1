<?php
session_start(); // Start session to check user login status
require_once 'db.php'; // Include database connection file
// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
if ($isLoggedIn) {
    // Get the logged-in user's profile
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Community Homepage</title>
    <link rel="stylesheet" href="./css/forum_home.css">
</head>

<body>
    <div class="navbar">
        <div class="logo">
            <a href="#">Game Community</a>
        </div>
        <div class="menu">
            <a href="./index.php">Home</a>
            <a href="./home.php">Game Community</a>
            <a href="./create_post.php">Post New Thread</a>
            <a href="./about.php">About Us</a>
            <?php if ($isLoggedIn): ?>
                <a href="profile.php">Profile</a>
            <?php endif; ?>
        </div>
        <div class="search">
            <div class="auth-buttons">
                <?php if ($isLoggedIn): ?>
                    <!-- 显示已登录用户的用户名和按钮 -->
                    <span class="username">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <button id="btn-logout" onclick="location.href='logout.php'">Logout</button>
                <?php else: ?>
                    <!-- 未登录时显示登录和注册按钮 -->
                    <button id="btn-login" onclick="location.href='login.php'">Login</button>
                    <button id="btn-register" onclick="location.href='register.php'">Register</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="banner">
        <img src="./images/background.jpg" alt="Banner Image">
    </div>
    <!-- 添加的底部 -->
    <footer>
        <p>Copyright © 2025 Game Community. All rights reserved.</p>
    </footer>
</body>

</html>