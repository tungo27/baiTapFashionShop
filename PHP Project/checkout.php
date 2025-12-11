<?php
session_start();
require_once('model/connect.php');

// Bật chế độ lỗi để dễ debug
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ——— Kiểm tra giỏ hàng ———
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// ——— Kiểm tra user đã login chưa ———
if (!isset($_SESSION['id-user'])) {
    die("Bạn phải đăng nhập để đặt hàng.");
}
$userId = (int) $_SESSION['id-user'];

// ——— Lấy thông tin sản phẩm từ giỏ hàng ———
$ids = array_keys($_SESSION['cart']);
$id_list = implode(",", array_map('intval', $ids));

$sql = "SELECT id, price FROM products WHERE id IN ($id_list)";
$stmt = $dbh->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$products) {
    die("Có lỗi: không tìm thấy sản phẩm trong DB.");
}

// ——— Tính tổng tiền đơn hàng ———
$total = 0;
foreach ($products as $p) {
    $pid = $p['id'];
    $qty = (int) $_SESSION['cart'][$pid];
    $total += $p['price'] * $qty;
}

// ——— Lưu đơn hàng với user_id ———
try {
    $sqlOrder = "
      INSERT INTO orders (total, date_order, status, user_id)
      VALUES (:total, NOW(), :status, :user_id)
    ";
    $stmtOrder = $dbh->prepare($sqlOrder);
    $status = 0;  // trạng thái mới  
    $stmtOrder->bindParam(':total', $total);
    $stmtOrder->bindParam(':status', $status, PDO::PARAM_INT);
    $stmtOrder->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmtOrder->execute();

    $order_id = $dbh->lastInsertId();  // lấy id đơn vừa tạo

    // ——— Xóa giỏ hàng ———
    unset($_SESSION['cart']);

    // ——— Thông báo thành công ———
    echo "Đặt hàng thành công. Mã đơn hàng: " . htmlspecialchars($order_id);
    echo "<a href='index.php'><button>Về nhà ăn tết</button></a>";
} catch (PDOException $e) {
    echo "Lỗi khi lưu đơn hàng: " . htmlspecialchars($e->getMessage());
    exit();
}