<?php
require('function.php');
debug('-----------logout------------');

require('auth.php');
if(!empty($_POST)){
    session_destroy();
    header("Location:top.php");
}
?>

<?php require('head.php'); ?>
<body id="logout">
    <?php require('header.php'); ?>
        <a class="regibtn" href="post.php">
            <div>出品</div>
            <i class="fas fa-camera fa-3x"></i>
        </a>
    <div class="wrapper">
        <div class="main-wrapper">
            <div class="content-wrap">
                <div class="content-inner">

                    <?php require('side.php'); ?>

                    <div class="single-btn">
                        <form action="" method="post">
                            <input type="submit" name="submit" value="ログアウト" class="common-btn">
                        </form>
                    </div>

                </div>
            </div>
            </div>
</div>
<?php require('footer.php'); ?>