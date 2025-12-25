<?php
session_start();
require_once('model/connect.php'); // Kết nối DB

// Bật chế độ lỗi để dễ debug
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Khởi tạo biến để lưu thông báo kết quả
$message = '';
$message_type = ''; // 'success' hoặc 'danger'

// --- 1. Kiểm tra giỏ hàng và User Login ---
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    // Nếu giỏ hàng trống và không có thông báo thành công từ lần POST trước, chuyển hướng
    if (!isset($_POST['is_posted'])) {
        header("Location: index.php");
        exit();
    }
}

if (!isset($_SESSION['id-user'])) {
    die("Bạn phải đăng nhập để đặt hàng.");
}
$userId = (int) $_SESSION['id-user'];


// --- 2. Lấy thông tin sản phẩm và tính tổng tiền (Cần thiết cho cả GET và POST) ---
$cart_details = [];
$calculatedTotal = 0;
$isCartEmpty = true;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $isCartEmpty = false;
    $ids = array_keys($_SESSION['cart']);
    $id_list = implode(",", array_map('intval', $ids));

    $sql = "SELECT id, name, price FROM products WHERE id IN ($id_list)";
    $stmt = $dbh->query($sql);
    $products_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$products_db) {
        // Trường hợp lỗi nghiêm trọng: sản phẩm trong giỏ nhưng không có trong DB
        $message = "Lỗi hệ thống: Không tìm thấy sản phẩm hợp lệ trong cơ sở dữ liệu.";
        $message_type = 'danger';
        $isCartEmpty = true;
    } else {
        foreach ($products_db as $p) {
            $pid = $p['id'];
            $qty = (int) $_SESSION['cart'][$pid];
            if ($qty <= 0) continue;

            $subtotal = $p['price'] * $qty;
            $calculatedTotal += $subtotal;

            $cart_details[] = [
                'id' => $pid,
                'name' => $p['name'],
                'price' => (float)$p['price'],
                'qty' => $qty,
                'subtotal' => $subtotal,
            ];
        }
    }
}


// --- 3. Lấy thông tin người dùng từ DB để điền vào form ---

try {
    $sqlUser = "SELECT fullname, phone, address FROM users WHERE id = $userId ";
    $stmtUser = $dbh->query($sqlUser);

    $userDB = $stmtUser->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Bỏ qua lỗi lấy user info
}


// =======================================================
// --- Xử lý ĐẶT HÀNG (Nếu form được gửi đi - POST Request) ---
// =======================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isCartEmpty) {

    // Đánh dấu đã POST để tránh chuyển hướng giỏ hàng trống ngay sau khi đặt hàng
    $_POST['is_posted'] = true;

    // Lấy thông tin từ Form (Dù không lưu, vẫn cần validate)
    $customerName = $_POST['name'] ?? '';
    $customerPhone = $_POST['phone'] ?? '';
    $customerAddress = $_POST['address'] ?? '';
    $paymentMethod = $_POST['payment_method'] ?? 'COD';

    if (empty($customerName) || empty($customerPhone) || empty($customerAddress)) {
        $message = "Vui lòng điền đầy đủ Họ và Tên, Số điện thoại và Địa chỉ giao hàng.";
        $message_type = 'danger';
    } else {
        // Bắt đầu Transaction
        $dbh->beginTransaction();

        try {
            // 1. LƯU ĐƠN HÀNG VÀO BẢNG `orders` (Chỉ dùng các cột yêu cầu: total, date_order, status, user_id)
            $sqlOrder = "
                INSERT INTO orders (total, date_order, status, user_id)
                VALUES (:total, NOW(), :status, :user_id)
            ";
            $stmtOrder = $dbh->prepare($sqlOrder);
            $status = 0; // Trạng thái mới (chờ xử lý)

            // Liên kết các tham số
            $stmtOrder->bindParam(':total', $calculatedTotal);
            $stmtOrder->bindParam(':status', $status, PDO::PARAM_INT);
            $stmtOrder->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmtOrder->execute();

            $order_id = $dbh->lastInsertId(); // Lấy id đơn vừa tạo

            // 2. LƯU CHI TIẾT ĐƠN HÀNG VÀO BẢNG `product_order`
            // Sử dụng các cột bạn yêu cầu: product_id, order_id, quantity
            $sqlDetail = "
                INSERT INTO product_order (order_id, product_id, quantity)
                VALUES (:order_id, :product_id, :quantity)
            ";
            $stmtDetail = $dbh->prepare($sqlDetail);

            foreach ($cart_details as $item) {
                $stmtDetail->bindParam(':order_id', $order_id, PDO::PARAM_INT);
                $stmtDetail->bindParam(':product_id', $item['id'], PDO::PARAM_INT);
                $stmtDetail->bindParam(':quantity', $item['qty'], PDO::PARAM_INT);
                $stmtDetail->execute();
            }

            // 3. Commit Transaction
            $dbh->commit();

            // 4. Xóa giỏ hàng
            unset($_SESSION['cart']);
            $isCartEmpty = true; // Cập nhật trạng thái giỏ hàng sau khi xóa

            // 5. Thông báo thành công
            $message = "Đặt hàng thành công. Mã đơn hàng: **#{$order_id}**. Tổng tiền: **" . number_format($calculatedTotal, 0, ',', '.') . " VNĐ**";
            $message_type = 'success';
        } catch (PDOException $e) {
            $dbh->rollBack();
            $message = "Lỗi khi lưu đơn hàng: " . htmlspecialchars($e->getMessage());
            $message_type = 'danger';
        }
    }
}
// =======================================================
// --- KẾT THÚC Xử lý ĐẶT HÀNG ---
// =======================================================


?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán Đơn Hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="checkout-container">
        <h2 class="mb-4 text-center text-primary"> Xác Nhận Thanh Toán</h2>

        <?php if ($message): ?>
            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <?php if ($message_type === 'success'): ?>
                    <p class="mt-3">Cảm ơn bạn đã đặt hàng!</p>
                    <a href='index.php' class="btn btn-sm btn-outline-success">Về Trang Chủ</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!$isCartEmpty): // Chỉ hiển thị form nếu giỏ hàng còn sản phẩm 
        ?>
            <div class="row">

                <div class="col-md-6">
                    <h4 class="mb-3 text-success">Thông Tin Giao Hàng</h4>

                    <form method="POST">

                        <div class="mb-3">
                            <label for="name" class="form-label">Họ và Tên (*)</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="<?= htmlspecialchars($_POST['name'] ?? $userDB['fullname']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số Điện Thoại (*)</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                value="<?= htmlspecialchars($_POST['phone'] ?? $userDB['phone']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa Chỉ Giao Hàng (*)</label>
                            <textarea class="form-control" id="address" name="address" rows="3"
                                required><?= htmlspecialchars($_POST['address'] ?? $userDB['address']) ?></textarea>
                        </div>

                        <h5 class="mt-4 text-info">Phương Thức Thanh Toán</h5>
                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="COD" checked>
                            <label class="form-check-label" for="cod">Thanh toán khi nhận hàng (COD)</label>
                        </div>
                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="bank" value="Bank">
                            <label class="form-check-label" for="bank">Chuyển khoản Ngân hàng</label>
                        </div>

                        <hr class="my-4">

                        <button class="w-100 btn btn-primary btn-lg" type="submit">
                            HOÀN TẤT ĐẶT HÀNG (<?= number_format($calculatedTotal, 0, ',', '.') ?> VNĐ)
                        </button>
                        <p class="text-center mt-2"><a href="viewcart.php">Quay lại Giỏ hàng</a></p>
                    </form>
                </div>

                <div class="col-md-6 order-md-2 mb-4">
                    <h4 class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary">Tóm Tắt Đơn Hàng</span>
                        <span class="badge bg-secondary rounded-pill"><?= count($cart_details) ?></span>
                    </h4>
                    <ul class="list-group mb-3">
                        <?php foreach ($cart_details as $item): ?>
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                    <h6 class="my-0"><?= htmlspecialchars($item['name']) ?></h6>
                                    <small class="text-muted">SL: <?= $item['qty'] ?> x
                                        <?= number_format($item['price'], 0, ',', '.') ?> VNĐ</small>
                                </div>
                                <span class="text-muted"><?= number_format($item['subtotal'], 0, ',', '.') ?> VNĐ</span>
                            </li>
                        <?php endforeach; ?>

                        <li class="list-group-item d-flex justify-content-between">
                            <span>Thành tiền (Tổng cộng)</span>
                            <strong><?= number_format($calculatedTotal, 0, ',', '.') ?> VNĐ</strong>
                        </li>
                    </ul>
                </div>
            </div>
        <?php elseif ($isCartEmpty && $message_type === 'danger'): ?>
            <p class="text-center mt-4">
                <a href='index.php' class="btn btn-primary btn-lg">Về Trang Chủ</a>
            </p>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>