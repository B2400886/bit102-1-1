<?php
include('user.php');

// 获取当前页数，默认为第1页
$page = isset($_GET['forum_page']) ? (int) $_GET['forum_page'] : 1;
$postsPerPage = 3; // 每页显示3条

// 计算社区列表分页的偏移量
$offset = ($page - 1) * $postsPerPage;

// 获取当前页的帖子及其统计信息
$stmt = $pdo->prepare("
    SELECT posts.*, users.username,
           (SELECT COUNT(*) FROM likes WHERE post_id = posts.id) AS like_count,
           (SELECT COUNT(*) FROM comments WHERE post_id = posts.id) AS comment_count,
           (SELECT COUNT(*) FROM bookmarks WHERE post_id = posts.id) AS bookmark_count
    FROM posts
    JOIN users ON posts.user_id = users.id
    ORDER BY posts.created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindParam(':limit', $postsPerPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

// 获取帖子总数
$stmt = $pdo->prepare("SELECT COUNT(*) FROM posts");
$stmt->execute();
$totalPosts = $stmt->fetchColumn();

// 计算总页数
$totalPages = ceil($totalPosts / $postsPerPage);

// 获取本周热门资讯（根据点赞数排序），并根据帖子 ID 获取统计信息
$popularPage = isset($_GET['popular_page']) ? (int) $_GET['popular_page'] : 1;
$postsPerPagePopular = 3; // 每页显示3条热门帖子
$offsetPopular = ($popularPage - 1) * $postsPerPagePopular;

// 获取本周热门资讯的帖子和统计信息
$stmt = $pdo->prepare("
    SELECT posts.*, users.username,
           (SELECT COUNT(*) FROM likes WHERE post_id = posts.id) AS like_count,
           (SELECT COUNT(*) FROM comments WHERE post_id = posts.id) AS comment_count,
           (SELECT COUNT(*) FROM bookmarks WHERE post_id = posts.id) AS bookmark_count
    FROM posts
    JOIN users ON posts.user_id = users.id
    WHERE YEARWEEK(posts.created_at, 1) = YEARWEEK(CURDATE(), 1)
    ORDER BY like_count DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindParam(':limit', $postsPerPagePopular, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offsetPopular, PDO::PARAM_INT);
$stmt->execute();
$popularPosts = $stmt->fetchAll();

// 获取本周热门资讯总数
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM posts 
    WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
");
$stmt->execute();
$totalPopularPosts = $stmt->fetchColumn();

// 计算总页数
$totalPopularPages = ceil($totalPopularPosts / $postsPerPagePopular);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Community - Community Interface</title>
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

    <!-- 主体内容 -->
    <div class="main-container">
        <!-- 中间内容区域 -->
        <main class="content">
            <div class="carousel">
                <div class="carousel-item active">
                    <img src="./images/post5.jpg" alt="Post 1">
                </div>
                <div class="carousel-item">
                    <img src="./images/post4.jpg" alt="Post 2">
                </div>
                <div class="carousel-item">
                    <img src="./images/post3.jpg" alt="Post 3">
                </div>
                <div class="carousel-dots">
                    <span class="dot active"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>
            </div>

            <!-- 帖子列表 -->
            <section class="post-list">
                <h2 style="border-bottom: 2px solid #ff5f5f; ">Community List</h2>

                <?php foreach ($posts as $post): ?>
                    <div class="post">
                        <?php if ($post['image']): ?>
                            <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="post-image">
                        <?php else: ?>
                            <img src="images/default.jpg" alt="Default Image" class="post-image">
                        <?php endif; ?>

                        <div class="post-details">
                            <h3>
                                <a
                                    href="post_detail.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a>
                            </h3>
                            <p>Author: <?php echo htmlspecialchars($post['username']); ?> | Time: <?php echo $post['created_at']; ?>
                            </p>
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

                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?forum_page=1">&laquo; First Page</a>
                        <a href="?forum_page=<?php echo $page - 1; ?>">Previous Page</a>
                    <?php endif; ?>

                    <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>

                    <?php if ($page < $totalPages): ?>
                        <a href="?forum_page=<?php echo $page + 1; ?>">Next Page</a>
                        <a href="?forum_page=<?php echo $totalPages; ?>">Last Page &raquo;</a>
                    <?php endif; ?>
                </div>
            </section>

            <!-- 本周热门资讯 -->
            <section class="categories">
            <h2 style="border-bottom: 2px solid #ff5f5f; ">This Week's Popular News</h2>

                <?php foreach ($popularPosts as $post): ?>
                    <div class="post">
                        <?php if ($post['image']): ?>
                            <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="post-image">
                        <?php else: ?>
                            <img src="images/default.jpg" alt="Default Image" class="post-image">
                        <?php endif; ?>

                        <div class="post-details">
                            <h3>
                                <a
                                    href="post_detail.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a>
                            </h3>
                            <p>Author: <?php echo htmlspecialchars($post['username']); ?> | Time: <?php echo $post['created_at']; ?>
                            </p>
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

                <div class="pagination">
                    <?php if ($popularPage > 1): ?>
                        <a href="?popular_page=1">&laquo; First Page</a>
                        <a href="?popular_page=<?php echo $popularPage - 1; ?>">Previous Page</a>
                    <?php endif; ?>

                    <span>Page <?php echo $popularPage; ?> of <?php echo $totalPopularPages; ?></span>

                    <?php if ($popularPage < $totalPopularPages): ?>
                        <a href="?popular_page=<?php echo $popularPage + 1; ?>">Next Page</a>
                        <a href="?popular_page=<?php echo $totalPopularPages; ?>">Last Page &raquo;</a>
                    <?php endif; ?>
                </div>
            </section>
        </main>

       <!-- 右侧侧边栏 -->
<aside class="sidebar-right">
    <h3>My Posts</h3>
    <ul class="my-posts-list">
        <?php
        // 判断用户是否登录
        if (!$isLoggedIn) {
            echo '<li>Please <a href="./login.php">login</a> to view your posts.</li>';
        } else {
            // 设置默认的每页显示帖子数和偏移量
            $postsPerPageMyPosts = 10; // 每页最多显示 10 条帖子
            $pageMyPosts = isset($_GET['myposts_page']) ? (int) $_GET['myposts_page'] : 1; // 获取当前页数，默认为第 1 页
            $offsetMyPosts = ($pageMyPosts - 1) * $postsPerPageMyPosts; // 计算偏移量
            
            // 获取当前登录用户发布的帖子
            $stmt = $pdo->prepare("
                SELECT * FROM posts 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset
            ");

            // 使用命名参数绑定变量
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':limit', $postsPerPageMyPosts, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offsetMyPosts, PDO::PARAM_INT);

            // 执行查询
            $stmt->execute();

            // 获取查询结果
            $myPosts = $stmt->fetchAll();

            // 如果用户有帖子，则显示
            if ($myPosts) {
                foreach ($myPosts as $post) {
                    echo '<li>';
                    echo '<h4><a href="post_detail.php?id=' . $post['id'] . '">' . htmlspecialchars($post['title']) . '</a></h4>';
                    echo '<p class="post-content">' . nl2br(htmlspecialchars($post['content'])) . '</p>';  // 显示内容
                    echo '<p>' . htmlspecialchars($post['created_at']) . '</p>'; // 显示时间
                    // 删除按钮
                    echo ' <form action="delete_post.php" method="POST" style="display:inline;">
                        <input type="hidden" name="post_id" value="' . $post['id'] . '">
                        <button type="submit" onclick="return confirm(\'Are you sure you want to delete this post?\');">Delete</button>
                    </form>';
                    echo '</li>';
                }
            } else {
                echo '<li>You have not posted any content yet.</li>';
            }

            // 获取帖子总数
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            $totalMyPosts = $stmt->fetchColumn();

            // 计算总页数
            $totalMyPostsPages = ceil($totalMyPosts / $postsPerPageMyPosts);
        }
        ?>
    </ul>

    <!-- 分页 -->
    <div class="pagination">
        <?php if (isset($totalMyPostsPages) && $pageMyPosts > 1): ?>
            <a href="?myposts_page=1">&laquo; First Page</a>
            <a href="?myposts_page=<?php echo $pageMyPosts - 1; ?>">Previous Page</a>
        <?php endif; ?>

        <?php if (isset($totalMyPostsPages)): ?>
            <span>Page <?php echo $pageMyPosts; ?> of <?php echo $totalMyPostsPages; ?></span>
        <?php endif; ?>

        <?php if (isset($totalMyPostsPages) && $pageMyPosts < $totalMyPostsPages): ?>
            <a href="?myposts_page=<?php echo $pageMyPosts + 1; ?>">Next Page</a>
            <a href="?myposts_page=<?php echo $totalMyPostsPages; ?>">Last Page &raquo;</a>
        <?php endif; ?>
    </div>
</aside>

    </div>

    <!-- 底部版权声明 -->
    <footer>
        <p>Copyright © 2025 Game Community. All rights reserved.</p>
    </footer>

    <script src="./js/forum_home.js"></script>
    <script>
        // 限制字符数并加上省略号
        document.addEventListener('DOMContentLoaded', function () {
            const posts = document.querySelectorAll('.post-content'); // 获取所有的帖子内容

            posts.forEach(post => {
                let text = post.textContent.trim(); // 获取帖子内容的文本
                if (text.length > 15) {
                    // 截取前15个字符并添加省略号
                    post.textContent = text.slice(0, 15) + '...';
                }
            });
        });
    </script>
</body>

</html>