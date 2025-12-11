<?php
session_start();
error_reporting(E_ALL ^ E_DEPRECATED);
require_once('../model/connect.php');

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $res = $dbh->query($sql);

    $rows = $res->rowCount();
    if ($rows > 0) {
        $_SESSION['username'] = $username; // Initializing Session,Khởi tạo Session cho username
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['id-user'] = $row['id'];
        }
        header("location:../index.php?ls=success");
        exit();
    } else {
        $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không hợp lệ!';

        header("location:../user/login.php?error=wrong");
        exit();
    }
} else {
    //    echo 'lala';
}