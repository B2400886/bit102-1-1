<?php
session_start();
require_once 'db.php';
include('user.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the logged-in user's profile
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        // Get image information
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Validate image format
        if (in_array(strtolower($imageExt), $allowedExtensions)) {
            $imagePath = './uploads/' . uniqid('post_', true) . '.' . $imageExt;
            if (move_uploaded_file($imageTmpName, $imagePath)) {
                $image = $imagePath; // Image uploaded successfully, save image path
                $successMessage = "Image uploaded successfully!";
            } else {
                $errorMessage = "Image upload failed!";
            }
        } else {
            $errorMessage = "Unsupported image format!";
        }
    }

    // Insert post data into the database
    if (empty($errorMessage)) {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, image) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$_SESSION['user_id'], $title, $content, $image])) {
            header("Location: home.php"); // Redirect to homepage after success
            exit;
        } else {
            $errorMessage = "Failed to post!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <link rel="stylesheet" href="./css/forum_home.css">
    <link rel="stylesheet" href="./css/post.css">
</head>

<body>
    <!-- Top navigation bar -->
    <div class="navbar">
        <div class="logo">
            <a href="./index.php">Game Community</a>
        </div>
        <div class="menu">
            <a href="./index.php">Home</a>
            <a href="./home.php">Game Community</a>
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

    <!-- Main content -->
    <div class="main-container">
        <main class="content">
            <div class="post-form">
                <h2>Create New Post</h2>

                <!-- Display success or error message -->
                <?php if ($successMessage): ?>
                    <div class="success-message"><?php echo htmlspecialchars($successMessage); ?></div>
                <?php elseif ($errorMessage): ?>
                    <div class="error-message"><?php echo htmlspecialchars($errorMessage); ?></div>
                <?php endif; ?>

                <form action="create_post.php" method="POST" enctype="multipart/form-data">
                    <label for="title">Post Title</label>
                    <input type="text" id="title" name="title" required>

                    <label for="content">Post Content</label>
                    <textarea id="content" name="content" rows="5" required></textarea>

                    <label for="image">Upload Image (Optional)</label>
                    <input type="file" id="image" name="image" accept="image/*">

                    <button type="submit">Post</button>
                </form>

                <!-- Display uploaded image -->
                <?php if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK && $image): ?>
                    <h3>Uploaded Image:</h3>
                    <img src="<?php echo './uploads/' . basename($image); ?>" alt="Uploaded Image"
                        style="max-width: 100%; height: auto;">
                <?php endif; ?>

            </div>
        </main>
    </div>


</body>

</html>