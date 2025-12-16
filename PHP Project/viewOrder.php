<?php
session_start();
require_once('model/connect.php'); // Kết nối DB

// Bật chế độ lỗi
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// --- Kiểm tra người dùng đã đăng nhập chưa ---
if (!isset($_SESSION['id-user'])) {
    die("Bạn phải đăng nhập để xem lịch sử đơn hàng.");
}
$userId = (int) $_SESSION['id-user'];


$sql = "
    SELECT 
        idOrder, fullname, phone, address, 
        idProduct, nameProduct, price, quantity, dateOrder, status
    FROM 
        view_order_list 
    WHERE 
        idUser = :user_id
    ORDER BY 
        dateOrder DESC, idOrder DESC
";
// Note: Nếu bạn không có view_order_list, bạn cần thay thế bằng câu lệnh JOIN 
// giữa orders, product_order và products.

$stmt = $dbh->prepare($sql);
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Xử lý nhóm các mục đơn hàng lại ---
$orders = [];
foreach ($order_items as $item) {
    $order_id = $item['idOrder'];

    // Tạo cấu trúc đơn hàng nếu chưa có
    if (!isset($orders[$order_id])) {
        $orders[$order_id] = [
            'id' => $order_id,
            'date' => $item['dateOrder'],
            'status' => $item['status'],
            'total' => 0, // Sẽ tính sau
            'items' => [],
            // Lấy thông tin người nhận (giả định là thông tin cố định trong view/bảng này)
            'recipient' => [
                'name' => $item['fullname'],
                'phone' => $item['phone'],
                'address' => $item['address'],
            ]
        ];
    }

    // Thêm sản phẩm vào đơn hàng đó
    $subtotal = (float)$item['price'] * (int)$item['quantity'];
    $orders[$order_id]['items'][] = [
        'name' => $item['nameProduct'],
        'price' => (float)$item['price'],
        'qty' => (int)$item['quantity'],
        'subtotal' => $subtotal
    ];
    $orders[$order_id]['total'] += $subtotal;
}

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Sử Đơn Hàng Của Tôi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .order-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .order-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .order-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
    </style>
</head>

<body>

    <div class="order-container">
        <h2 class="mb-5 text-center text-primary"> Lịch Sử Đơn Hàng Của Tôi</h2>

        <?php if (empty($orders)): ?>
            <div class="alert alert-info text-center">
                Bạn chưa có đơn hàng nào. <a href="index.php">Tiếp tục mua sắm</a>
            </div>
        <?php else: ?>

            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Mã Đơn: #<?= $order['id'] ?></strong> |
                            Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['date'])) ?>
                        </div>
                        <div>
                            <?php
                            $status_text = $order['status'] == 1 ? 'Đã hoàn thiện' : 'Đang trên đường giao';
                            $status_class = $order['status'] == 1 ? 'bg-success' : 'bg-warning text-dark';
                            ?>
                            Trạng thái: <span class="badge <?= $status_class ?>"><?= $status_text ?></span>
                        </div>
                    </div>

                    <div class="p-3 bg-light border-bottom">
                        <p class="mb-1"><strong>Người nhận:</strong>
                            <?= htmlspecialchars($order['recipient']['fullname'] ?? $order['recipient']['name']) ?> -
                            <?= htmlspecialchars($order['recipient']['phone']) ?></p>
                        <p class="mb-1"><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['recipient']['address']) ?></p>
                        <h5 class="mt-2 text-danger text-end">TỔNG TIỀN: <?= number_format($order['total'], 0, ',', '.') ?> VNĐ
                        </h5>
                    </div>

                    <div class="p-3">
                        <h6>Chi tiết sản phẩm:</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th style="width: 50%;">Tên Sản Phẩm</th>
                                        <th class="text-end" style="width: 20%;">Đơn Giá</th>
                                        <th class="text-center" style="width: 15%;">Số Lượng</th>
                                        <th class="text-end" style="width: 15%;">Thành Tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['name']) ?></td>
                                            <td class="text-end"><?= number_format($item['price'], 0, ',', '.') ?> VNĐ</td>
                                            <td class="text-center"><?= $item['qty'] ?></td>
                                            <td class="text-end"><?= number_format($item['subtotal'], 0, ',', '.') ?> VNĐ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>
    <p class="text-center mt-4">
        <a href='index.php' class="btn btn-primary btn-lg">Về Trang Chủ</a>
    </p>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>