<?php
// db.php - 数据库连接
$host = 'localhost';       // 数据库主机
$dbname = 'forum_db';      // 数据库名称
$username = 'root';        // 数据库用户名
$password = '';    // 数据库密码
$port = 3307;              // 数据库端口号

// 创建数据库连接
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // 设置错误模式
} catch (PDOException $e) {
    echo "数据库连接失败: " . $e->getMessage();
    die();
}
?>
