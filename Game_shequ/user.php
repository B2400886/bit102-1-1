<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php'; // 引入数据库连接文件

// 检查用户是否已登录
$isLoggedIn = isset($_SESSION['user_id']);
if ($isLoggedIn) {
    // 获取已登录用户的资料
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}
?>