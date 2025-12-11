<?php
session_start();
require_once('model/connect.php');
echo "Hiện tại chưa làm chỉ tạo file";
// Nếu chưa có đơn hàng
// if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
//     echo "<p>Giỏ hàng trống.</p>";
//     echo '<p><a href="index.php">Tiếp tục mua hàng</a></p>';
//     exit();
// }