<?php
session_start();
error_reporting(E_ALL);
require_once('../model/connect.php');

if (isset($_POST['submit'])) {
    // Tốt hơn nên sử dụng prepare statement để ngăn chặn SQL Injection
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. Thực hiện truy vấn để lấy toàn bộ thông tin người dùng
    $sql = "SELECT id, username, role FROM users WHERE username = :username AND password = :password";

    // Sử dụng Prepared Statements
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    $rows = $stmt->rowCount();

    if ($rows > 0) {
        // Đăng nhập thành công
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Khởi tạo Session
        $_SESSION['username'] = $row['username'];
        $_SESSION['id-user'] = $row['id'];
        $_SESSION['role'] = $row['role']; // Lấy role để sử dụng sau này

        // 2. Xét điều kiện dựa trên ROLES
        $user_role = $row['role'];

        if ($user_role == 1) {
            // Role 1: Quản trị viên (Admin)
            header("location:../admin/index.php?ls=success");
            exit();
        } elseif ($user_role == 0) {
            // Role 0: Người dùng thường (User)
            header("location:../index.php?ls=success");
            exit();
        } else {
            // Các role khác không được định nghĩa (Tùy chọn)
            $_SESSION['error'] = 'Quyền truy cập không hợp lệ!';
            header("location:../user/login.php?error=invalid_role");
            exit();
        }
    } else {
        // Đăng nhập thất bại
        $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không hợp lệ!';
        header("location:../user/login.php?error=wrong");
        exit();
    }
}