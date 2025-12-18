<?php
session_start();
require_once('model/connect.php');

// Xử lý khi user submit update hoặc xóa
if (isset($_POST['update_cart'])) {
    // POST['qty'] là mảng: key = product id, value = số lượng mới
    foreach ($_POST['qty'] as $pid => $qty) {
        $pid = (int)$pid;
        $qty = (int)$qty;
        if ($qty <= 0) {
            // nếu số lượng ≤ 0 -> xóa khỏi giỏ
            unset($_SESSION['cart'][$pid]);
        } else {
            // cập nhật số lượng
            $_SESSION['cart'][$pid] = $qty;
        }
    }
    // sau cập nhật — reload lại trang để hiển thị mới
    header("Location: viewcart.php");
    exit();
}

if (isset($_GET['remove'])) {
    // remove theo product id truyền qua GET
    $pid = (int) $_GET['remove'];
    if (isset($_SESSION['cart'][$pid])) {
        unset($_SESSION['cart'][$pid]);
    }
    header("Location: viewcart.php");
    exit();
}

// Nếu giỏ trống
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Giỏ hàng trống.</p>";
    echo '<p><a href="index.php">Tiếp tục mua hàng</a></p>';
    exit();
}

// Lấy dữ liệu sản phẩm
$ids = array_keys($_SESSION['cart']);
$id_list = implode(",", array_map('intval', $ids));
$sql = "SELECT id, name, price, image FROM products WHERE id IN ($id_list)";
$stmt = $dbh->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Giỏ hàng</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container my-5">
        <h1 class="text-center mb-4">Giỏ hàng của bạn</h1>

        <form method="post" action="viewcart.php">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Ảnh</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $prod):
                            $pid = $prod['id'];
                            $qty = $_SESSION['cart'][$pid];
                            $subtotal = $prod['price'] * $qty;
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($prod['name']); ?></td>
                            <td>
                                <img src="<?php echo htmlspecialchars($prod['image']); ?>" class="img-fluid"
                                    style="max-width: 80px;">
                            </td>
                            <td><?php echo number_format($prod['price'], 0, ',', '.'); ?>₫</td>
                            <td style="width: 120px;">
                                <input type="number" class="form-control text-center" name="qty[<?php echo $pid; ?>]"
                                    value="<?php echo $qty; ?>" min="0">
                            </td>
                            <td><?php echo number_format($subtotal, 0, ',', '.'); ?>₫</td>
                            <td>
                                <a href="viewcart.php?remove=<?php echo $pid; ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Bạn có chắc muốn xóa mặt hàng này?');">
                                    Xóa
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <!-- Tổng tiền -->
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Tổng:</td>
                            <td colspan="2" class="fw-bold">
                                <?php echo number_format($total, 0, ',', '.'); ?>₫
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <button type="submit" name="update_cart" class="btn btn-primary">
                    Cập nhật giỏ hàng
                </button>

                <div>
                    <a href="index.php" class="btn btn-outline-secondary">Tiếp tục mua hàng</a>
                    <a href="checkout.php" class="btn btn-success">Thanh toán</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>