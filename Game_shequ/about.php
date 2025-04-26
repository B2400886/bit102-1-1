<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
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
    <title>About Us - Game Community</title>
    <link rel="stylesheet" href="./css/forum_home.css">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <!-- Top Navigation Bar -->
    <div class="navbar">
        <div class="logo">
            <a href="#">Game Forum</a>
        </div>
        <div class="menu">
            <a href="./index.php">Home</a>
            <a href="./home.php">Game Forum</a>
            <a href="./create_post.php">Create New Post</a>
            <a href="./about.php">About Us</a>
            <?php if ($isLoggedIn): ?>
                <a href="profile.php">Profile</a>
            <?php endif; ?>
        </div>
        <div class="search">
            <div class="auth-buttons">
                <span class="username">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <button id="btn-logout" onclick="location.href='logout.php'">Logout</button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-container">
        <main class="content about-content">
            <h1>About Game Community</h1>
            <p>Welcome to the Game Community, a vibrant platform where gamers from all over the world come together to share their passion for gaming. Our community is dedicated to providing a space where members can connect, share insights, and discuss the latest trends in the gaming world.</p>
            <p>Whether you're a casual gamer or a hardcore enthusiast, you'll find a place here. Our forums are filled with discussions on a wide range of topics, from game strategies and tips to the latest news and updates in the gaming industry.</p>
            <p>Join us today and become a part of a growing community that celebrates the love of gaming. We look forward to seeing you in the forums!</p>
        </main>
    </div>


</body>
</html> 