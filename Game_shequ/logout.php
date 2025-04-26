<?php
// logout.php - 退出登录
session_start();
session_unset();
session_destroy();
header("Location: home.php"); // 退出后跳转到登录页面
exit;
?>
