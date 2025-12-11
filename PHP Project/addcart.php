<?php
session_start();

// Kiểm tra ID sản phẩm từ GET
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$product_id = (int) $_GET['id'];
$qty = isset($_GET['qty']) ? (int) $_GET['qty'] : 1;
if ($qty < 1) {
    $qty = 1;
}

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Nếu sản phẩm đã tồn tại trong giỏ → cộng dồn số lượng
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $qty;
} else {
    $_SESSION['cart'][$product_id] = $qty;
}

header("Location: viewcart.php");
exit();