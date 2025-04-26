<?php
// 引入数据库连接文件
include('db.php'); 
include('user.php');

// 获取搜索关键词
$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// 设置每页显示帖子数和偏移量
$postsPerPage = 3; // 每页显示 3 条帖子
$page = isset($_GET['forum_page']) ? (int) $_GET['forum_page'] : 1;
$offset = ($page - 1) * $postsPerPage;

// 构建 SQL 查询条件
$sql = "SELECT posts.*, users.username,
           (SELECT COUNT(*) FROM likes WHERE post_id = posts.id) AS like_count,
           (SELECT COUNT(*) FROM comments WHERE post_id = posts.id) AS comment_count,
           (SELECT COUNT(*) FROM bookmarks WHERE post_id = posts.id) AS bookmark_count
        FROM posts
        JOIN users ON posts.user_id = users.id";

// 如果有搜索关键词，添加搜索条件
if ($searchKeyword) {
    $sql .= " WHERE posts.title LIKE :search OR posts.content LIKE :search";
}

$sql .= " ORDER BY posts.created_at DESC LIMIT :limit OFFSET :offset";

// 准备并执行查询
$stmt = $pdo->prepare($sql);

// 绑定参数
if ($searchKeyword) {
    $searchTerm = '%' . $searchKeyword . '%'; // 使用通配符进行模糊匹配
    $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
}

$stmt->bindParam(':limit', $postsPerPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

// 获取帖子数据
$posts = $stmt->fetchAll();

// 获取总帖数（包括搜索条件）
if ($searchKeyword) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE title LIKE :search OR content LIKE :search");
    $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts");
    $stmt->execute();
}

$totalPosts = $stmt->fetchColumn();

// 计算总页数
$totalPages = ceil($totalPosts / $postsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="./css/forum_home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            <?php if ($isLoggedIn): ?>
                <a href="profile.php">Profile</a> 
                <?php endif; ?>
        </div>
        <div class="search">
            <!-- 搜索框 -->
            <div class="search">
                <form action="search_results.php" method="get">
                    <label>
                        <input type="text" name="search" placeholder="Enter search content..."
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit">Search</button>
                    </label>
                </form>
            </div>

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

    <h2>Search Results: <?php echo htmlspecialchars($searchKeyword); ?></h2>

    <!-- 帖子列表 -->
    <section class="post-list">
        <?php if (empty($posts)): ?>
            <p>No posts found matching the criteria.</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <?php if ($post['image']): ?>
                        <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="post-image">
                    <?php else: ?>
                        <img src="images/default.jpg" alt="Default Image" class="post-image">
                    <?php endif; ?>

                    <div class="post-details">
                        <h3>
                            <a href="post_detail.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a>
                        </h3>
                        <p>Author: <?php echo htmlspecialchars($post['username']); ?> | Time: <?php echo $post['created_at']; ?></p>
                        <p class="post-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                    </div>

                    <div class="post-stats">
                        <span class="like-count">
                            <i class="fas fa-thumbs-up"></i> <?php echo $post['like_count']; ?>
                        </span>
                        <span class="comment-count">
                            <i class="fas fa-comment"></i> <?php echo $post['comment_count']; ?>
                        </span>
                        <span class="bookmark-count">
                            <i class="fas fa-bookmark"></i> <?php echo $post['bookmark_count']; ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- 分页 -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?search=<?php echo urlencode($searchKeyword); ?>&forum_page=1">&laquo; First Page</a>
                <a href="?search=<?php echo urlencode($searchKeyword); ?>&forum_page=<?php echo $page - 1; ?>">Previous Page</a>
            <?php endif; ?>

            <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>

            <?php if ($page < $totalPages): ?>
                <a href="?search=<?php echo urlencode($searchKeyword); ?>&forum_page=<?php echo $page + 1; ?>">Next Page</a>
                <a href="?search=<?php echo urlencode($searchKeyword); ?>&forum_page=<?php echo $totalPages; ?>">Last Page &raquo;</a>
            <?php endif; ?>
        </div>
    </section>

</body>
</html>
