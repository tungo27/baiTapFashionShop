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
            // nếu số lượng ≤ 0 → xóa khỏi giỏ
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
</head>

<body>
    <h1>Giỏ hàng của bạn</h1>

    <form method="post" action="viewcart.php">
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Sản phẩm</th>
                <th>Ảnh</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
                <th>Xóa</th>
            </tr>

            <?php foreach ($products as $prod):
                $pid = $prod['id'];
                $qty = $_SESSION['cart'][$pid];
                $subtotal = $prod['price'] * $qty;
                $total += $subtotal;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($prod['name']); ?></td>
                <td><img src="<?php echo htmlspecialchars($prod['image']); ?>" width="80"></td>
                <td><?php echo number_format($prod['price'], 0, ',', '.'); ?></td>
                <td>
                    <input type="number" name="qty[<?php echo $pid; ?>]" value="<?php echo $qty; ?>" min="0"
                        style="width: 50px;">
                </td>
                <td><?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                <td>
                    <a href="viewcart.php?remove=<?php echo $pid; ?>"
                        onclick="return confirm('Bạn có chắc muốn xóa mặt hàng này?');">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>

            <tr>
                <td colspan="4" style="text-align: right;"><strong>Tổng:</strong></td>
                <td colspan="2"><strong><?php echo number_format($total, 0, ',', '.'); ?></strong></td>
            </tr>
        </table>

        <p>
            <button type="submit" name="update_cart">Cập nhật giỏ hàng</button>
        </p>
    </form>

    <p>
        <a href="index.php">Tiếp tục mua hàng</a> |
        <a href="checkout.php">Thanh toán / Checkout</a>
    </p>
</body>

</html>