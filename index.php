<?php
require_once 'models/Product.php';
$model = new Product();

// --- XỬ LÝ PHÂN TRANG ---
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total_records = $model->countAll();
$total_pages = ceil($total_records / $limit);

if ($page < 1) $page = 1;
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

$offset = ($page - 1) * $limit;
$products = $model->getPage($limit, $offset);

// --- VIEW ---
require_once 'includes/header.php';
?>

<h1 class="h3 mb-2 text-gray-800">Quản lý Sản phẩm</h1>

<?php if ($msg = get_flash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= $msg ?> <button class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách</h6>
        <a href="form.php?page=<?= $page ?>" class="btn btn-primary btn-sm btn-icon-split">
            <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
            <span class="text">Thêm mới</span>
        </a>
    </div>
    <div class="card-body">
        <p>Trang <?= $page ?>/<?= $total_pages ?: 1 ?> - Tổng <?= $total_records ?> dòng</p>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr><th>ID</th><th>Ảnh</th><th>Tên</th><th>Thao tác</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $row): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td>
                            <img src="public/uploads/<?= htmlspecialchars($row['image']) ?>" 
                                 class="img-thumbnail" style="width:60px; height:60px; object-fit:cover">
                        </td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>
                            <a href="form.php?id=<?= $row['id'] ?>&page=<?= $page ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?id=<?= $row['id'] ?>&page=<?= $page ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Trước</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Sau</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>