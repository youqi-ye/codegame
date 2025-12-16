<?php
session_start();

// 檢查是否有 'remember_user' cookie
if (isset($_COOKIE['remember_user']) && !isset($_SESSION['username'])) {
    // 假設 cookie 保存的是用戶名
    $_SESSION['username'] = $_COOKIE['remember_user'];
}

// -------------------------
// 登出邏輯：當 URL 參數 ?logout=true 時銷毀 Session 並重導回首頁
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_destroy();
    header("Location: index.php");
    exit();
}

// -------------------------
// 登入邏輯：如果有表單送出帳號與密碼，則進行驗證
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"])) {
    // 請根據實際情況調整下列資料庫連線設定
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'taiwan_travel';
    
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($mysqli->connect_errno) {
        die("資料庫連線失敗：" . $mysqli->connect_error);
    }
    
    // 假設使用 users 資料表，欄位為 username 與 password（密碼使用 password_hash() 加密）
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    
    $stmt = $mysqli->prepare("SELECT username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_username, $db_password);
        $stmt->fetch();
        if (password_verify($password, $db_password)) {
            // 驗證成功，設定 Session
            $_SESSION["username"] = $db_username;
            header("Location: index.php");
            exit();
        } else {
            $error = "密碼錯誤，請重新輸入！";
        }
    } else {
        $error = "帳號不存在，請確認輸入的帳號！";
    }
    $stmt->close();
    $mysqli->close();
}

// -------------------------
// 建立資料庫連線（影片與廣告使用相同連線）
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'taiwan_travel';

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_errno) {
    die("資料庫連線失敗：" . $mysqli->connect_error);
}

// 撈取上架狀態的影片資料
$videos = [];
$status = 1;
$stmt = $mysqli->prepare("SELECT id, title, video_id, status FROM videos WHERE status = ?");
$stmt->bind_param("i", $status);
$stmt->execute();
$result = $stmt->get_result();
$videos = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 撈取形象廣告資料：只取狀態為上架且在有效期內的廣告
$ads = [];
$sql = "SELECT img_url, target_url, start_date, duration FROM ads 
        WHERE status = 1 
          AND NOW() < DATE_ADD(start_date, INTERVAL duration DAY)
        ORDER BY start_date DESC";
if ($result = $mysqli->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $ads[] = $row;
    }
    $result->free();
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>台灣到處走</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        .banner {
            width: 100%;
            height: 200px;
            background: url('banner.jpg') center/cover no-repeat;
            margin-bottom: -10px;
            padding: 0;
            display: block;
        }
        #travelCarousel {
            margin: 0;
            padding: 0;
        }
        /* 設定圖片置中並加上滑動過渡效果 */
        #travelCarousel img {
            display: block;
            margin: 0 auto;
        }
        .carousel-item {
            transition: transform 0.75s ease;
        }
        .video-card {
            text-align: center;
            margin-bottom: 30px;
        }
        .video-card iframe {
            width: 100%;
            height: 200px;
        }
        .qrcode {
            margin-top: 10px;
        }
        .custom-btn {
            background-color: #87CEEB;
            color: white;
            font-weight: bold;
            border-radius: 15px;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
        .custom-btn:hover {
            background-color: #6cbce6;
        }
    </style>
</head>
<body>
    <!-- Navbar 固定區塊 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#" id="home-link">臺灣到處走</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#video-section">臺灣旅遊景點攻略介紹</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="submit_testimonial.php">客戶使用口碑</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">聯絡我們</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="favorite_actions.php">我的收藏</a></li>
                    <?php if (isset($_SESSION['username'])): ?>
                        <!-- 登入後顯示會員名稱與登出 -->
                        <li class="nav-item d-flex justify-content-center">
                            <span class="navbar-text">歡迎 <strong class="text-primary"><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="member_logout.php">登出</a>
                        </li>
                    <?php else: ?>
                        <!-- 尚未登入時顯示登入按鈕，觸發 Modal -->
                        <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#memberLoginModal">會員登入</a></li>
                        <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">後台管理</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- 形象廣告輪播 -->
    <div id="travelCarousel" class="carousel slide container-fluid" data-bs-ride="carousel" data-bs-interval="3000" data-bs-touch="true">
        <div class="carousel-inner">
            <?php if(count($ads) > 0): ?>
                <?php foreach($ads as $index => $ad): ?>
                    <div class="carousel-item <?php echo ($index == 0) ? 'active' : ''; ?>">
                        <img src="<?php echo $ad['img_url']; ?>" class="d-block w-100" alt="">
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- 若無廣告資料，顯示預設圖片 -->
                <div class="carousel-item active">
                    <img src="https://wallpapercave.com/wp/wp7508057.jpg" class="d-block w-100" alt="">
                </div>
            <?php endif; ?>
        </div>
        <!-- 左右切換按鈕 -->
        <button class="carousel-control-prev" type="button" data-bs-target="#travelCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">上一張</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#travelCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">下一張</span>
        </button>
    </div>

    <!-- 影片展示區 -->
    <div id="video-section" class="container mt-4">
        <div class="row">
            <?php if(count($videos) > 0): ?>
                <?php foreach($videos as $video): ?>
                    <div class="col-md-4 video-card" id="video-<?php echo $video['id']; ?>">
                        <iframe src="https://www.youtube.com/embed/<?php echo $video['video_id']; ?>" frameborder="0" allowfullscreen></iframe>
                        <h5><?php echo $video['title']; ?></h5>
                        <p>瀏覽次數: <span id="view-count-<?php echo $video['id']; ?>">載入中...</span></p>
                        <button class="custom-btn" onclick="showQRCode('https://www.youtube.com/watch?v=<?php echo $video['video_id']; ?>')">瀏覽 QR Code</button>
                        <form action="favorite_actions.php" method="POST">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="video_id" value="<?php echo $video['video_id']; ?>">
                            <button type="submit">加入收藏</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>目前沒有影片上架。</p>
                </div>
            <?php endif; ?>
        </div>
        <!-- 按鈕只出現在影片區塊底部 -->
        <div class="row mt-3">
            <div class="col-12 text-center">
                <button class="btn btn-secondary" onclick="scrollToTop()">返回上方首頁</button>
            </div>
        </div>
    </div>

    <!-- QR Code Modal -->
    <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content p-4 shadow-sm">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="qrCodeImage" src="" alt="QR Code" width="200" height="200">
                </div>
            </div>
        </div>
    </div>
    
    <!-- 會員登入 Modal (原 memberLoginModal)，action 指向 index.php 進行登入驗證 -->
    <div class="modal fade" id="memberLoginModal" tabindex="-1" aria-labelledby="memberLoginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content p-4 shadow-sm">
                <div class="modal-header">
                    <h4 class="modal-title" id="memberLoginModalLabel">會員登入</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if(isset($error)): ?>
                        <script>
                            alert('<?php echo $error; ?>');
                            window.location.href = 'index.php';
                        </script>
                    <?php endif; ?>
                    <form method="POST" action="member_login.php">
                        <input type="text" name="username" class="form-control mt-2" placeholder="帳號" required>
                        <input type="password" name="password" class="form-control mt-2" placeholder="密碼" required>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary w-100">登入</button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <p>還沒有帳號嗎？ <a href="register.php">立即註冊</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 登入 Modal (授權碼登入，此 Modal 保留不變) -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content p-4 shadow-sm">
                <div class="modal-header">
                    <h4 class="modal-title" id="loginModalLabel">請輸入授權碼登入</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="login.php">
                        <input type="password" name="authCode" class="form-control mt-2" placeholder="輸入授權碼">
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary w-100">登入</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 錯誤訊息 Modal (保留原版) -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content p-4 shadow-sm">
                <div class="modal-body text-center">
                    <i class="bi bi-emoji-dizzy text-danger" style="font-size: 50px;"></i>
                    <p class="text-danger mt-3"><strong>錯誤：</strong> 授權碼錯誤，請重新輸入。</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 頁面載入後初始化滾動與視窗效果
        $(document).ready(function() {
            if (window.location.search.indexOf('error=1') !== -1) {
                $('#errorModal').modal('show');
            }
            $('a[href="#video-section"]').on('click', function(e) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $('#video-section').offset().top
                }, 500);
            });
            <?php foreach($videos as $video): ?>
                fetchViewCount('<?php echo $video['video_id']; ?>', 'view-count-<?php echo $video['id']; ?>');
            <?php endforeach; ?>
        });

        $(document).ready(function() {
            $('#home-link').on('click', function(e) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: 0
                }, 500);
            });
            if (window.location.search.indexOf('error=1') !== -1) {
                $('#errorModal').modal('show');
            }
        });

        function scrollToTop() {
            $('html, body').animate({
                scrollTop: 0
            }, 500);
        }

        function showQRCode(url) {
            var qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(url);
            document.getElementById('qrCodeImage').src = qrCodeUrl;
            $('#qrCodeModal').modal('show');
        }

        function fetchViewCount(videoId, elementId) {
            $.get('fetch_view_count.php', { videoId: videoId }, function(data) {
                document.getElementById(elementId).innerText = data;
            });
        }
    </script>
</body>
</html>
