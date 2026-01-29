<?php
require_once 'models/Product.php';
$model = new Product();

$id = isset($_GET['id']) ? $_GET['id'] : 0;
$page = isset($_GET['page']) ? $_GET['page'] : 1;

if ($id) {
    $model->delete($id);
    set_flash('success', 'Đã xóa thành công!');
}

header("Location: index.php?page=" . $page);
exit();
?>