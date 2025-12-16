<?php
session_start();
error_reporting(E_ALL ^ E_DEPRECATED);
require_once '../model/connect.php';


if (isset($_POST['submit'])) {
    if (isset($_POST['fullname'])) {
        $fullname = $_POST['fullname'];
    }

    if (isset($_POST['username'])) {
        $username = $_POST['username'];
    }

    if (isset($_POST['password'])) {
        $password = $_POST['password'];
    }

    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $kq = $dbh->query($sql); # trả về dạng bảng dữ liệu
        $user = $kq->fetch(PDO::FETCH_ASSOC); # chuyển từ bảng sang mảng
        if ($email = $user['email']) {
            echo "<h5>Hiện tại đã có tài khoản sử dụng mail của bạn<br>Hãy sử dụng mail khác</h5>";
        }
    }

    if (isset($_POST['address'])) {
        $address = $_POST['address'];
    }

    if (isset($_POST['phone'])) {
        $phone = $_POST['phone'];
    }

    $sql = "INSERT INTO users (fullname, username, password, email, phone, address, role)
                VALUES ('$fullname', '$username', '$password', '$email', $phone, '$address', 1)";
    $res = $dbh->query($sql);
    if ($res) {
        if (isset($email)) {
            // Biến $email đã được gán giá trị ở phần trên
            include 'sendEmail.php';
        }
        header("location:login.php?rs=success");
        exit();
    } else {
        header("location:login.php?rf=fail");
        exit();
    }
}
