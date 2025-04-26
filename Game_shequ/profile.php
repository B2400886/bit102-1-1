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

// Get the logged-in user's profile
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'] ?? null;
    $confirm_password = $_POST['confirm_password'] ?? null;

    // If the user entered a password, check if the password and confirm password match
    if ($password && $password !== $confirm_password) {
        $error = "Password and confirm password do not match";
    } else {
        // If the password has changed, update the password
        if ($password) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT); // Password encryption
            $stmt = $pdo->prepare("UPDATE users SET phone = ?, address = ?, password = ? WHERE id = ?");
            $stmt->execute([$phone, $address, $passwordHash, $_SESSION['user_id']]);
        } else {
            // Otherwise, only update the phone number and address
            $stmt = $pdo->prepare("UPDATE users SET phone = ?, address = ? WHERE id = ?");
            $stmt->execute([$phone, $address, $_SESSION['user_id']]);
        }

        // Redirect to the profile page after a successful update
        header("Location: home.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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

    <div class="container">
        <div class="profile-form">
            <h2>Edit Profile</h2>

            <!-- Display error if exists -->
            <?php if (isset($error)) { ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php } ?>

            <form action="profile.php" method="POST">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                    value="<?php echo htmlspecialchars($user['username']); ?>" required disabled>

                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

                <label for="address">Address</label>
                <input type="text" id="address" name="address"
                    value="<?php echo htmlspecialchars($user['address']); ?>">

                <!-- Add password and confirm password fields -->
                <label for="password">New Password</label>
                <input type="password" id="password" name="password">

                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password">

                <button type="submit">Update Profile</button>
            </form>
        </div>
    </div>
</body>

</html>