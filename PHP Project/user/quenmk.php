<?php

echo '
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Khôi phục Mật khẩu</title>
    <style>
        /* CSS Chung cho trang */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Định dạng cho Form */
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        /* Tiêu đề */
        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* Form */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px; /* Khoảng cách giữa các phần tử form */
            align-items: flex-start; /* Căn chỉnh nội dung form sang trái */
        }

        /* Nhãn (Label) */
        label {
            font-weight: bold;
            color: #555;
            width: 100%; /* Đảm bảo label chiếm đủ chiều rộng */
            text-align: left;
        }

        /* Trường nhập (Input Text) */
        input[type="text"] {
            width: 95%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Tính cả padding và border vào width */
            font-size: 16px;
        }

        /* Nút Gửi (Input Submit) */
        input[type="submit"] {
            background-color: #007bff; /* Màu xanh dương */
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            width: 100%;
            margin-top: 10px; /* Khoảng cách với trường nhập liệu */
        }

        input[type="submit"]:hover {
            background-color: #0056b3; /* Màu xanh đậm hơn khi rê chuột */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Mời bạn nhập email đã đăng kí tài khoản tại shop</h1>
        <form action="sendEmailmk.php" method="post">
            <label for="email">Mời bạn nhập email của mình</label>
            <input type="text" name="email" id="email" placeholder="">  
            <input type="submit" name="submit_email" value="Gửi">
        </form>
    </div>
</body>
</html>
';

// Lưu ý: Nếu bạn muốn tách HTML và PHP ra file riêng, chỉ cần giữ lại phần <html>...</html>
// trong file HTML (ví dụ: `reset_password.html`) và điều chỉnh PHP để gọi form.