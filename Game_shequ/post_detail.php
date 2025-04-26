<?php
require_once 'user.php';

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
if ($isLoggedIn) {
    // Get the logged-in user's profile
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}

// Get the post ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid post ID");
}

$postId = (int)$_GET['id'];

// Get post details
$stmt = $pdo->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?");
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    die("Post does not exist");
}

// Get the number of comments for the post
$stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
$stmt->execute([$postId]);
$commentCount = $stmt->fetchColumn();

// Get comments for the post
$stmt = $pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? ORDER BY comments.created_at DESC");
$stmt->execute([$postId]);
$comments = $stmt->fetchAll();

// Get the number of likes
$stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
$stmt->execute([$postId]);
$likeCount = $stmt->fetchColumn();

// Get the number of bookmarks
$stmt = $pdo->prepare("SELECT COUNT(*) FROM bookmarks WHERE post_id = ?");
$stmt->execute([$postId]);
$bookmarkCount = $stmt->fetchColumn();

// Check if the post is liked
$isLiked = false;
if ($isLoggedIn) {
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$_SESSION['user_id'], $postId]);
    $isLiked = $stmt->rowCount() > 0;
}

// Check if the post is bookmarked
$isBookmarked = false;
if ($isLoggedIn) {
    $stmt = $pdo->prepare("SELECT * FROM bookmarks WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$_SESSION['user_id'], $postId]);
    $isBookmarked = $stmt->rowCount() > 0;
}

// Handle comments
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $comment = $_POST['comment'];
    if (!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$postId, $_SESSION['user_id'], $comment]);
        header("Location: post_detail.php?id=" . $postId); // Refresh the page to show new comments
        exit;
    }
}

// Handle likes
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['like'])) {
    if ($isLoggedIn && !$isLiked) {
        $stmt = $pdo->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
        $stmt->execute([$postId, $_SESSION['user_id']]);
        header("Location: post_detail.php?id=" . $postId); // Refresh the page to show likes
        exit;
    }
}

// Handle bookmarks
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bookmark'])) {
    if ($isLoggedIn && !$isBookmarked) {
        $stmt = $pdo->prepare("INSERT INTO bookmarks (post_id, user_id) VALUES (?, ?)");
        $stmt->execute([$postId, $_SESSION['user_id']]);
        header("Location: post_detail.php?id=" . $postId); // Refresh the page to show bookmarks
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Details</title>
    <link rel="stylesheet" href="./css/forum_home.css">
    <link rel="stylesheet" href="./css/post_detail.css">
    <!-- 引入 Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<!-- 顶部导航栏 -->
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

<!-- 主体内容 -->
<div class="main-container">
    <main class="content">
        <section class="post-detail">
            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
            <p>Author: <?php echo htmlspecialchars($post['username']); ?> | Time: <?php echo $post['created_at']; ?></p>
            <div class="post-content">
                <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            </div>

            <!-- 点赞和收藏 -->
            <div class="button-group">
                <form method="POST" action="">
                    <?php if ($isLoggedIn): ?>
                        <button type="submit" name="like" <?php echo $isLiked ? 'disabled' : ''; ?>>
                            <i class="fas fa-thumbs-up"></i>
                            <?php echo $isLiked ? 'Liked' : 'Like'; ?>
                        </button>
                    <?php endif; ?>
                </form>
                <form method="POST" action="">
                    <?php if ($isLoggedIn): ?>
                        <button type="submit" name="bookmark" <?php echo $isBookmarked ? 'disabled' : ''; ?>>
                            <i class="fas fa-bookmark"></i>
                            <?php echo $isBookmarked ? 'Bookmarked' : 'Bookmark'; ?>
                        </button>
                    <?php endif; ?>
                </form>
            </div>

            <!-- 点赞和收藏数 -->
            <div class="button-counts">
                <p><i class="fas fa-thumbs-up"></i> Likes: <?php echo $likeCount; ?></p>
                <p><i class="fas fa-bookmark"></i> Bookmarks: <?php echo $bookmarkCount; ?></p>
                <p><i class="fas fa-comments"></i> Comments: <?php echo $commentCount; ?></p>
            </div>

            <!-- 评论区 -->
            <h3>Comments</h3>
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <p><strong><?php echo htmlspecialchars($comment['username']); ?>:</strong> <?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                    <p class="comment-time"><?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?></p>
                </div>
            <?php endforeach; ?>

            <!-- 评论表单 -->
            <?php if ($isLoggedIn): ?>
                <form method="POST" action="" class="comment-form">
                    <textarea name="comment" rows="4" placeholder="Write your comment..." required></textarea>
                    <button type="submit">Post Comment</button>
                </form>
            <?php else: ?>
                <p>Please log in to post a comment.</p>
            <?php endif; ?>
        </section>
    </main>
</div>

</body>
</html>
