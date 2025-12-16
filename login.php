<?php
session_start();
date_default_timezone_set('Asia/Taipei'); // 設定時區為台灣

// 確認授權碼
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $authCode = $_POST['authCode'] ?? ''; // 使用者輸入的授權碼

    // 取得目前時間（格式：1435）
    $currentTime = date("Hi"); // H=時(24小時), i=分
    $expectedCode = 'nfu' . $currentTime; // 正確授權碼格式

    if ($authCode === $expectedCode) {
        // 登入成功
        $_SESSION['authenticated'] = true;
        header("Location: admin.php");
        exit;
    } else {
        // 授權碼錯誤
        header("Location: index.php?error=1");
        exit;
    }
}
?>
