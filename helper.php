<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Kết nối Database
function getDB() {
    $host = 'localhost'; $db = 'demo_php'; $user = 'root'; $pass = ''; // Sửa pass nếu có
    try {
        $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Lỗi kết nối: " . $e->getMessage());
    }
}

// 2. Flash Message
function set_flash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

function get_flash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

// 3. Upload Ảnh An Toàn
function upload_image($file, $old_image = null) {
    $target_dir = __DIR__ . "/../public/uploads/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
    
    if ($file['error'] !== UPLOAD_ERR_OK) return false;

    // Validate size (2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        set_flash('error', 'File quá lớn (Max 2MB).');
        return false;
    }

    // Validate ext & mime
    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowed_mime = ['image/jpeg', 'image/png', 'image/webp'];

    if (!in_array($ext, $allowed_ext) || !in_array($mime, $allowed_mime)) {
        set_flash('error', 'Chỉ chấp nhận file ảnh (jpg, png, webp).');
        return false;
    }

    // Upload & Xóa ảnh cũ
    $new_name = uniqid('img_') . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $target_dir . $new_name)) {
        if ($old_image && $old_image !== 'default.png') {
            $old_path = $target_dir . $old_image;
            if (file_exists($old_path)) unlink($old_path);
        }
        return $new_name;
    }
    return false;
}
?>