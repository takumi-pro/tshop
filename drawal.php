<?php
require('function.php');
debug('-----------drawal------------');

require('auth.php');
if(!empty($_POST)){
    try{
        $dbh = db();
        $sql1 = 'UPDATE users SET delete_flg=1 WHERE id=?';
        $sql2 = 'UPDATE item SET delete_flg=1 WHERE user_id=?';
        $sql3 = 'UPDATE favo SET delete_flg=1 WHERE user_id=?';
        $data = array($_SESSION['user_id']);
        $stmt = query($dbh,$sql1,$data);
        if($stmt){
            session_destroy();
            header("Location:top.php");
            debug('トップへ遷移');
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
        
    }
}
?>

<?php require('head.php'); ?>
<body id="drawal">
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
                            <input type="submit" name="submit" value="退会する" class="common-btn">
                        </form>
                    </div>

                </div>
            </div>
            </div>
</div>
<?php require('footer.php'); ?>