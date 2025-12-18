<?php
session_start();
error_reporting(E_ALL ^ E_DEPRECATED);
require_once '../model/connect.php';

if (isset($_POST['submit'])) {
    // Thu thập dữ liệu từ form
    $fullname = $_POST['fullname'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email    = $_POST['email'] ?? '';
    $address  = $_POST['address'] ?? '';
    $phone    = $_POST['phone'] ?? '';

    // 1. Logic kiểm tra trùng Email
    if (!empty($email)) {
        $sql_check = "SELECT * FROM users WHERE email = '$email'";
        $kq = $dbh->query($sql_check);

        // Kiểm tra xem có dòng dữ liệu nào trả về không
        if ($kq->rowCount() > 0) {
            // Nếu trùng, chuyển hướng về trang đăng ký với thông báo lỗi
            header("location:register.php?error=email_exists");
            // Hoặc dùng session để lưu thông báo nếu bạn muốn hiển thị tại chỗ
            $_SESSION['error_msg'] = "Email này đã được sử dụng. Vui lòng chọn email khác!";
            exit(); // Quan trọng: Dừng ngay lập tức không cho chạy xuống phần INSERT
        }
    }

    // 2. Nếu không trùng, thực hiện Insert tài khoản mới (Role mặc định là 0)
    $sql_insert = "INSERT INTO users (fullname, username, password, email, phone, address, role) 
                   VALUES ('$fullname', '$username', '$password', '$email', '$phone', '$address', 0)";

    $res = $dbh->query($sql_insert);

    if ($res) {
        // Gửi email xác nhận nếu cần
        if (isset($email)) {
            include 'sendEmail.php';
        }
        header("location:login.php?rs=success");
        exit();
    } else {
        header("location:login.php?rf=fail");
        exit();
    }
}