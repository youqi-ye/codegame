<?php
session_start();
require 'db_connect.php'; // 連線資料庫

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // 更新該會員最新一筆尚未登出的紀錄
    $update_stmt = $conn->prepare("
        UPDATE member_logs 
        SET logout_time = NOW() 
        WHERE username = ? AND logout_time IS NULL 
        ORDER BY login_time DESC 
        LIMIT 1
    ");
    $update_stmt->bind_param("s", $username);
    $update_stmt->execute();
    $update_stmt->close();
}

$conn->close();

// 清除 session
session_unset();
session_destroy();

// 清除 cookie
setcookie('remember_user', '', time() - 3600, "/");

// 跳轉回登入頁
header("Location: index.php");
exit;
?>
