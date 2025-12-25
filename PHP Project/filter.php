<?php
require_once('model/connect.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Kết quả lọc - Fashion MyLiShop</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Fashion MyLiShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="images/logohong.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="css/bootstrap.min.css"> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <!-- <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script> -->
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css'>
    <script src='js/wow.js'></script>
    <script type="text/javascript" src="js/mylishop.js"></script>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
    <?php include("model/header.php"); ?>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h class="section-title">Sản phẩm được lọc theo giá</h 3>

                <div class="row">
                    <?php
                    // 1. Lấy giá trị sắp xếp từ URL
                    $sortType = isset($_GET['sort']) ? $_GET['sort'] : '';

                    // 2. Xây dựng câu SQL gốc
                    $sql = "SELECT id, image, name, price FROM products ";

                    // 3. Logic kiểm tra để thêm ORDER BY
                    if ($sortType == 'asc') {
                        $sql .= " ORDER BY price ASC";
                    } elseif ($sortType == 'desc') {
                        $sql .= " ORDER BY price DESC";
                    }

                    // 4. Thực thi truy vấn
                    $result = $dbh->query($sql);

                    // 5. Hiển thị kết quả
                    if ($result->rowCount() > 0) {
                        while ($kq = $result->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                            <div class="col-md-3 col-sm-6 text-center">
                                <div class="thumbnail">
                                    <div class="hoverimage1">
                                        <img src="<?php echo $kq['image']; ?>" width="100%" height="300">
                                    </div>
                                    <div class="name-product"><?php echo $kq['name']; ?></div>
                                    <div class="price">Giá: <?php echo number_format($kq['price'], 0, ',', '.'); ?> đ</div>
                                    <div class="product-info">
                                        <a href="addcart.php?id=<?php echo $kq['id']; ?>" class="btn btn-primary">Mua hàng</a>
                                        <a href="detail.php?id=<?php echo $kq['id']; ?>" class="btn btn-default">Chi Tiết</a>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo "<p>Không tìm thấy sản phẩm nào.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php include("model/footer.php"); ?>
</body>

</html>