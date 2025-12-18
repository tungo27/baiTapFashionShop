<?php

require_once('../model/connect.php'); //
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Sản Phẩm - Admin</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/animate.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="dist/css/sb-admin-2.css">
    <link rel="stylesheet" href="dist/css/timeline.css">
    <script src="dist/js/sb-admin-2.js"></script>

    <style>
    /* Tùy chỉnh thêm một chút cho đẹp hơn */
    .page-header {
        margin-top: 0;
        padding-bottom: 9px;
        border-bottom: 1px solid #eee;
    }

    .table img {
        max-width: 80px;
        /* Giảm kích thước ảnh hiển thị trong bảng */
        height: auto;
        border-radius: 4px;
    }
    </style>
</head>

<body>
    <?php
    require_once('../model/connect.php');
    ?>

    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"> Danh sách sản phẩm </h1>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    Quản lý chi tiết các sản phẩm hiện có
                </div>

                <span><i class="fa fa-sign-out"></i><a href="../user/logout.php"> Đăng xuất </a></span>

                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr align="center">
                                    <th colspan="8" class="text-left">
                                        <a href="product-add.php" class="btn btn-success">
                                            <i class="fa fa-plus-circle"></i> Thêm Sản Phẩm Mới
                                        </a>
                                    </th>
                                </tr>
                                <tr align="center" class="info">
                                    <th> STT </th>
                                    <th> Tên sản phẩm </th>
                                    <th> Mã danh mục </th>
                                    <th> Hình ảnh </th>
                                    <th> Giá </th>
                                    <th> Giảm giá </th>
                                    <th> Chỉnh sửa </th>
                                    <th> Xóa </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM products ORDER BY id DESC";
                                $result = $dbh->query($sql);

                                if ($result) {
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        $thumbImage = ($row['image'] == null || $row['image'] == '')
                                            ? ""
                                            : '../' . $row['image'];
                                ?>
                                <tr class="odd gradeX" align="center">
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['name']; ?></td>
                                    <td><?php echo $row['category_id']; ?></td>
                                    <td>
                                        <img src="<?php echo $thumbImage; ?>" alt="<?php echo $row['name']; ?>">
                                    </td>
                                    <td><?php echo number_format($row['price'], 0, ',', '.'); ?> VNĐ</td>
                                    <td><?php echo number_format($row['saleprice'], 0, ',', '.'); ?> VNĐ</td>

                                    <td class="center">
                                        <a href="product-edit.php?idProduct=<?php echo $row['id']; ?>"
                                            class="btn btn-sm btn-primary" title="Chỉnh sửa">
                                            <i class="fa fa-pencil"></i> Sửa
                                        </a>
                                    </td>

                                    <td class="center">
                                        <a href="product-delete.php?idProducts=<?php echo $row['id']; ?>"
                                            class="btn btn-sm btn-danger" title="Xóa"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">
                                            <i class="fa fa-trash-o"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>

</html>