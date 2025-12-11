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
} elseif (isset($_POST['quenMK'])) {
    echo '<h1>Mời bạn nhập email đã đăng kí tài khoản tại shop</h1>
        <form action="sendEmailmk.php" method="post">
        <label for="email">Mời bạn nhập email của mình</label>
        <input type="text" name="email">
        <input type="submit" name="submit_email" value="Gửi">
        </form>
    ';
}
