<?php
require_once 'models/Product.php';
$model = new Product();

// --- 1. KHỞI TẠO GIÁ TRỊ MẶC ĐỊNH ---
// Biến $data và $is_edit phải luôn tồn tại để HTML bên dưới sử dụng
$data = ['name' => '', 'image' => 'default.png'];
$is_edit = false;

// --- 2. LOGIC LẤY DỮ LIỆU ĐỂ SỬA ---
$id = isset($_GET['id']) ? $_GET['id'] : null;
$page = isset($_GET['page']) ? $_GET['page'] : 1;

if ($id) {
    $product_db = $model->getById($id);
    if ($product_db) {
        $data = $product_db;
        $is_edit = true;
    }
}

// --- 3. XỬ LÝ KHI BẤM LƯU (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $current_image = $_POST['current_image'];
    $final_image = $current_image;

    // Xử lý upload ảnh
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_result = upload_image($_FILES['image'], ($is_edit ? $current_image : null));
        
        if ($upload_result === false) {
            // Nếu lỗi upload, redirect lại form
            header("Location: form.php" . ($id ? "?id=$id&page=$page" : "?page=$page"));
            exit();
        }
        $final_image = $upload_result;
    }

    // Lưu vào Database
    if ($is_edit) {
        $model->update($id, $name, $final_image);
        set_flash('success', 'Đã cập nhật thành công!');
    } else {
        $model->add($name, $final_image);
        set_flash('success', 'Đã thêm mới thành công!');
    }

    // Redirect về trang danh sách
    header("Location: index.php?page=" . $page);
    exit();
}

// --- 4. BẮT ĐẦU GIAO DIỆN (PHẦN BẠN ĐANG BỊ THIẾU) ---
require_once 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><?= $is_edit ? 'Cập Nhật Sản Phẩm' : 'Thêm Mới Sản Phẩm' ?></h6>
            </div>
            <div class="card-body">
                
                <?php if ($err = get_flash('error')): ?>
                    <div class="alert alert-danger"><?= $err ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="current_image" value="<?= $data['image'] ?>">
                    
                    <div class="form-group">
                        <label>Tên sản phẩm</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($data['name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Hình ảnh</label>
                        <div class="mb-2">
                            <img src="public/uploads/<?= $data['image'] ?>" class="img-thumbnail" style="width: 150px; height: auto;">
                        </div>
                        <input type="file" name="image" class="form-control-file">
                        <small class="text-muted">Định dạng: jpg, png, webp (Tối đa 2MB)</small>
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-success btn-icon-split">
                        <span class="icon text-white-50"><i class="fas fa-save"></i></span>
                        <span class="text">Lưu dữ liệu</span>
                    </button>
                    
                    <a href="index.php?page=<?= $page ?>" class="btn btn-secondary btn-icon-split">
                        <span class="text">Quay lại</span>
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
// Đóng giao diện chân trang
require_once 'includes/footer.php'; 
?>