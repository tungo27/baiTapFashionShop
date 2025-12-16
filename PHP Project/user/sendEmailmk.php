<?php

session_start();
error_reporting(E_ALL ^ E_DEPRECATED);
require_once('../model/connect.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require  '../sending mail/PHPMailer-master/src/Exception.php';
require  '../sending mail/PHPMailer-master/src/PHPMailer.php';
require  '../sending mail/PHPMailer-master/src/SMTP.php';
//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);
if (isset($_POST['email'])) {

    $email = $_POST['email'];
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $kq = $dbh->query($sql);
    $user = $kq->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        // print_r($user);
        // Tìm thấy người dùng
        $email = $user['email'];
        $password = $user['password'];
        // echo "Đã tìm thấy tài khoản cho email: " . $email;
    } else {
        // Không tìm thấy người dùng
        echo "Email chưa được đăng ký trong hệ thống.";
    }
}
try {
    //Server settings
    $mail->isSMTP();                                            // để quy định rằng gửi email bằng smtp
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'minhtu080906@gmail.com';                     //SMTP username
    $mail->Password   = 'usrw tclz olvu hcsb';                         //SMTP password
    // Cấu hình thay thế (STARTTLS qua cổng 587)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Đổi sang STARTTLS
    $mail->Port = 587; // Đổi sang cổng 587
    //Recipients
    $mail->setFrom('minhtu080906@gmail.com', 'DragonThread Shop');
    $mail->addAddress($email, 'User');     //Add a recipient
    // $mail->addAddress('ellen@example.com');               //Name is optional
    $mail->addReplyTo('minhtu080906@gmail.com', 'Information');


    //Content
    $mail->isHTML(true);       //Set email format to HTML
    $mail->Subject = 'DragonThread Shop';
    $mail->Body    = "Chào mừng bạn đến với shop của chúng tôi!
    <br>Đây là mật khẩu của bạn: '$password'
        <br>Ở đây chúng tôi có rất nhiều món hàng mà bạn đang tìm kiếm. <br>Tận hưởng niềm vui mua hàng đi nhé<3";


    $mail->send();
    echo 'Message has been sent';
    header("Location:../index.php");
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
