<?php 
session_start();  // 啟用 session

// 資料庫連線設定（請依照實際環境修改參數）
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'taiwan_travel';

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_errno) {
    die("資料庫連線失敗：" . $mysqli->connect_error);
}

// 登出處理：點選登出時結束會話並重新導向首頁
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// 根據請求判斷處理影片或形象廣告
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    /*=================== 影片管理區 ===================*/
    // 新增影片
    if (isset($_POST['new_video_title']) && isset($_POST['new_video_id'])) {
        $new_video_title = $mysqli->real_escape_string($_POST['new_video_title']);
        $new_video_id = $mysqli->real_escape_string($_POST['new_video_id']);
        // 預設上架狀態為 1
        $sql = "INSERT INTO videos (title, video_id, status) VALUES ('$new_video_title', '$new_video_id', 1)";
        if ($mysqli->query($sql)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
        }
        exit;
    }

    // 更新影片狀態
    if (isset($_POST['video_id']) && isset($_POST['status'])) {
        $video_id = (int) $_POST['video_id'];
        $status = (int) $_POST['status'];  // 預期傳入 0 或 1
        $sql = "UPDATE videos SET status = $status WHERE id = $video_id";
        if ($mysqli->query($sql)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
        }
        exit;
    }

    // 刪除影片
    if (isset($_POST['delete_video_id'])) {
        $delete_video_id = (int) $_POST['delete_video_id'];
        $sql = "DELETE FROM videos WHERE id = $delete_video_id";
        if ($mysqli->query($sql)) {
            echo json_encode(['status' => 'success', 'id' => $delete_video_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
        }
        exit;
    }

    // 修改影片資料
    if (isset($_POST['edit_video_id']) && isset($_POST['edit_video_title']) && isset($_POST['edit_video_id_value'])) {
        $edit_video_id = (int) $_POST['edit_video_id'];
        $edit_video_title = $mysqli->real_escape_string($_POST['edit_video_title']);
        $edit_video_id_value = $mysqli->real_escape_string($_POST['edit_video_id_value']);
        $sql = "UPDATE videos SET title = '$edit_video_title', video_id = '$edit_video_id_value' WHERE id = $edit_video_id";
        if ($mysqli->query($sql)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
        }
        exit;
    }

    /*=================== 形象廣告管理區 ===================*/
    // 新增形象廣告（僅包含圖片連結與上架天數）
    if (isset($_POST['new_ad_img_url']) && isset($_POST['new_ad_duration'])) {
        $ad_img_url  = $mysqli->real_escape_string($_POST['new_ad_img_url']);
        $ad_duration = (int) $_POST['new_ad_duration'];
        // 插入時設定 start_date 為 NOW()，狀態預設上架(1)
        $sql = "INSERT INTO ads (img_url, duration, start_date, status) VALUES ('$ad_img_url', $ad_duration, NOW(), 1)";
        if ($mysqli->query($sql)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
        }
        exit;
    }

    // 更新廣告狀態（上架/下架）
    if (isset($_POST['ad_id']) && isset($_POST['ad_status'])) {
        $ad_id     = (int) $_POST['ad_id'];
        $ad_status = (int) $_POST['ad_status'];
        $sql = "UPDATE ads SET status = $ad_status WHERE id = $ad_id";
        if ($mysqli->query($sql)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
        }
        exit;
    }

    // 刪除廣告
    if (isset($_POST['delete_ad_id'])) {
        $delete_ad_id = (int) $_POST['delete_ad_id'];
        $sql = "DELETE FROM ads WHERE id = $delete_ad_id";
        if ($mysqli->query($sql)) {
            echo json_encode(['status' => 'success', 'id' => $delete_ad_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
        }
        exit;
    }

    // 修改廣告資料（僅包含圖片連結與上架天數）
    if (isset($_POST['edit_ad_id']) && isset($_POST['edit_ad_img_url']) && isset($_POST['edit_ad_duration'])) {
        $edit_ad_id      = (int) $_POST['edit_ad_id'];
        $edit_ad_img_url = $mysqli->real_escape_string($_POST['edit_ad_img_url']);
        $edit_ad_duration = (int) $_POST['edit_ad_duration'];
        $sql = "UPDATE ads SET img_url = '$edit_ad_img_url', duration = $edit_ad_duration WHERE id = $edit_ad_id";
        if ($mysqli->query($sql)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
        }
        exit;
    }
}

// 撈取所有影片資料
$videos = [];
$sql = "SELECT id, title, video_id, status FROM videos";
if ($result = $mysqli->query($sql)) {
    while($row = $result->fetch_assoc()){
        $videos[] = $row;
    }
    $result->free();
}

// 撈取所有形象廣告資料（包含所有狀態）
$ads = [];
$sql = "SELECT id, img_url, duration, start_date, status FROM ads";
if ($result = $mysqli->query($sql)) {
    while($row = $result->fetch_assoc()){
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
  <title>後台管理 - 台灣到處走</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body { font-family: Arial, sans-serif; }
    .navbar-brand { font-weight: bold; }
    .btn-logout { background-color: #FF5733; border-color: #FF5733; }
    .btn-logout:hover { background-color: #FF4500; border-color: #FF4500; }
    .btn { border-radius: 15px; border: none; }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">臺灣到處走</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link btn btn-logout text-white" href="logout.php">登出</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-3">
    <!-- 影片管理區 -->
    <h4 class="mt-4">影片管理</h4>
    <!-- 新增影片表單 -->
    <form id="add-video-form" class="mb-4">
      <div class="mb-3">
        <label for="new_video_title" class="form-label">影片標題</label>
        <input type="text" class="form-control" id="new_video_title" name="new_video_title" required>
      </div>
      <div class="mb-3">
        <label for="new_video_id" class="form-label">影片ID (YouTube ID)</label>
        <input type="text" class="form-control" id="new_video_id" name="new_video_id" required>
      </div>
      <button type="submit" class="btn btn-primary">新增影片</button>
    </form>

    <!-- 影片列表 -->
    <table class="table">
      <thead>
        <tr>
          <th>影片標題</th>
          <th>影片ID</th>
          <th>狀態</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody id="video-list">
        <?php foreach ($videos as $video): ?>
          <tr data-id="<?php echo $video['id']; ?>">
            <td><?php echo $video['title']; ?></td>
            <td><?php echo $video['video_id']; ?></td>
            <td>
              <select class="form-select status-select" data-id="<?php echo $video['id']; ?>">
                <option value="1" <?php echo ($video['status'] == 1) ? 'selected' : ''; ?>>上架</option>
                <option value="0" <?php echo ($video['status'] == 0) ? 'selected' : ''; ?>>下架</option>
              </select>
            </td>
            <td>
              <button class="btn btn-warning edit-video" data-id="<?php echo $video['id']; ?>" data-title="<?php echo $video['title']; ?>" data-video_id="<?php echo $video['video_id']; ?>">修改</button>
              <button class="btn btn-danger delete-video" data-id="<?php echo $video['id']; ?>">刪除</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- 修改影片 Modal -->
    <div class="modal fade" id="editVideoModal" tabindex="-1" aria-labelledby="editVideoModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editVideoModalLabel">修改影片</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="edit-video-form">
              <div class="mb-3">
                <label for="edit_video_title" class="form-label">影片標題</label>
                <input type="text" class="form-control" id="edit_video_title" name="edit_video_title" required>
              </div>
              <div class="mb-3">
                <label for="edit_video_id_value" class="form-label">影片ID (YouTube ID)</label>
                <input type="text" class="form-control" id="edit_video_id_value" name="edit_video_id_value" required>
              </div>
              <input type="hidden" id="edit_video_id" name="edit_video_id">
              <button type="submit" class="btn btn-primary">保存</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- 形象廣告管理區 -->
    <h4 class="mt-5">形象廣告管理</h4>
    <!-- 新增廣告表單 (僅包含圖片連結與上架天數) -->
    <form id="add-ad-form" class="mb-4">
      <div class="mb-3">
        <label for="new_ad_img_url" class="form-label">圖片連結 (Image URL)</label>
        <input type="text" class="form-control" id="new_ad_img_url" name="new_ad_img_url" required>
      </div>
      <div class="mb-3">
        <label for="new_ad_duration" class="form-label">上架天數</label>
        <input type="number" class="form-control" id="new_ad_duration" name="new_ad_duration" required>
      </div>
      <button type="submit" class="btn btn-primary">新增形象廣告</button>
    </form>

    <!-- 廣告列表 -->
    <table class="table">
      <thead>
        <tr>
          <th>圖片連結</th>
          <th>上架天數</th>
          <th>上架日期</th>
          <th>狀態</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody id="ad-list">
        <?php foreach ($ads as $ad): ?>
          <tr data-id="<?php echo $ad['id']; ?>">
            <td><?php echo $ad['img_url']; ?></td>
            <td><?php echo $ad['duration']; ?></td>
            <td><?php echo $ad['start_date']; ?></td>
            <td>
              <select class="form-select ad-status-select" data-id="<?php echo $ad['id']; ?>">
                <option value="1" <?php echo ($ad['status'] == 1) ? 'selected' : ''; ?>>上架</option>
                <option value="0" <?php echo ($ad['status'] == 0) ? 'selected' : ''; ?>>下架</option>
              </select>
            </td>
            <td>
              <button class="btn btn-danger delete-ad" data-id="<?php echo $ad['id']; ?>">刪除</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- JavaScript AJAX 程式 -->
  <script>
    $(document).ready(function() {
      /*===== 影片相關操作 =====*/
      // 更新影片狀態
      $('.status-select').change(function() {
        var video_id = $(this).data('id');
        var status = $(this).val();
        $.ajax({
          url: 'admin.php',
          type: 'POST',
          data: { video_id: video_id, status: status },
          success: function(response) {
            var data = JSON.parse(response);
            if (data.status === 'success') {
              $('#message').text('影片狀態已更新').show().fadeOut(3000);
            }
          }
        });
      });

      // 新增影片
      $('#add-video-form').submit(function(e) {
        e.preventDefault();
        var title = $('#new_video_title').val();
        var video_id = $('#new_video_id').val();
        $.ajax({
          url: 'admin.php',
          type: 'POST',
          data: { new_video_title: title, new_video_id: video_id },
          success: function(response) {
            var data = JSON.parse(response);
            if (data.status === 'success') {
              location.reload();
            }
          }
        });
      });

      // 顯示修改影片 Modal
      $(document).on('click', '.edit-video', function() {
        var video_id = $(this).data('id');
        var title = $(this).data('title');
        var video_id_value = $(this).data('video_id');
        $('#edit_video_id').val(video_id);
        $('#edit_video_title').val(title);
        $('#edit_video_id_value').val(video_id_value);
        $('#editVideoModal').modal('show');
      });

      // 保存影片修改
      $('#edit-video-form').submit(function(e) {
        e.preventDefault();
        var video_id = $('#edit_video_id').val();
        var title = $('#edit_video_title').val();
        var video_id_value = $('#edit_video_id_value').val();
        $.ajax({
          url: 'admin.php',
          type: 'POST',
          data: { edit_video_id: video_id, edit_video_title: title, edit_video_id_value: video_id_value },
          success: function(response) {
            var data = JSON.parse(response);
            if (data.status === 'success') {
              location.reload();
            }
          }
        });
      });

      // 刪除影片
      $(document).on('click', '.delete-video', function() {
        if (!confirm('確定要刪除這部影片嗎？')) return;
        var video_id = $(this).data('id');
        $.ajax({
          url: 'admin.php',
          type: 'POST',
          data: { delete_video_id: video_id },
          success: function(response) {
            var data = JSON.parse(response);
            if (data.status === 'success') {
              location.reload();
            }
          }
        });
      });

      /*===== 形象廣告相關操作 =====*/
      // 新增廣告
      $('#add-ad-form').submit(function(e) {
        e.preventDefault();
        var img_url = $('#new_ad_img_url').val();
        var duration = $('#new_ad_duration').val();
        $.ajax({
          url: 'admin.php',
          type: 'POST',
          data: {
            new_ad_img_url: img_url,
            new_ad_duration: duration
          },
          success: function(response) {
            var data = JSON.parse(response);
            if (data.status === 'success') {
              location.reload();
            }
          }
        });
      });

      // 更新廣告狀態
      $('.ad-status-select').change(function() {
        var ad_id = $(this).data('id');
        var ad_status = $(this).val();
        $.ajax({
          url: 'admin.php',
          type: 'POST',
          data: { ad_id: ad_id, ad_status: ad_status },
          success: function(response) {
            var data = JSON.parse(response);
            if (data.status === 'success') {
              console.log('廣告狀態已更新');
            }
          }
        });
      });

      // 刪除廣告
      $('.delete-ad').click(function() {
        if (!confirm('確定要刪除此筆廣告嗎？')) return;
        var ad_id = $(this).data('id');
        $.ajax({
          url: 'admin.php',
          type: 'POST',
          data: { delete_ad_id: ad_id },
          success: function(response) {
            var data = JSON.parse(response);
            if (data.status === 'success') {
              location.reload();
            }
          }
        });
      });
    });
  </script>
</body>
</html>
