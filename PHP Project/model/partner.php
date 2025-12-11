<div class="container">
    <div class="banner wow lightSpeedIn">
        <div class="row">
            <?php
            echo "<h3 class='title text-center'>PARTNER - PNV 27</h3>";
            include("connect.php");
            $sql = "SELECT image FROM slides WHERE status=3";
            $result = $dbh->query($sql);
            while ($kq = $result->fetch(PDO::FETCH_ASSOC)) {

            ?>
            <div class="col-md-2 col-sm-1">
                <div class="thumbnail">
                    <div class="banner">
                        <img src="<?php echo "./" . $kq['image']; ?>" slide alt="Generic placeholder thumbnail"
                            width="100%" height="160">
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>