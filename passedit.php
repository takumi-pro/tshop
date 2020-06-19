<?php
require('function.php');
debug('--------------passedit------------');
require('auth.php');

$dbdata = getdb($_SESSION['user_id']);

if(!empty($_POST)){
    $old_pass = $_POST['old'];
    $new_pass = $_POST['new'];
    $renew_pass = $_POST['re-new'];
    nothing($old_pass,'old');
    nothing($new_pass,'new');
    nothing($renew_pass,'re-new');
    if(empty($err_msg)){
        pass($old_pass,'old');
        pass($new_pass,'new');
        if(!password_verify($old_pass,$dbdata['password'])){
            $err_msg['old'] = MSG13;
        }
        if($old_pass === $new_pass){
            $err_msg['new'] = MSG14;
        }
        match($new_pass,$renew_pass,'new');
        if(empty($err_msg)){
            debug('バリデーションok');
            try{
                $dbh = db();
                $sql = 'UPDATE users SET password=? WHERE id=? AND delete_flg=0';
                $data = array(password_hash($new_pass,PASSWORD_DEFAULT),$_SESSION['user_id']);
                $stmt = query($dbh,$sql,$data);
                if($stmt){
                    debug('パスワード変更');
                    header("Location:mypage.php");
                }
            }catch (Exception $e){
                error_log('エラー発生:'.$e->getMessage());
            }
        }
    }
}
?>

<?php require('head.php'); ?>
<body id="passedit">
    <?php require('header.php'); ?>
    <div class="wrapper">
        <div class="main-wrapper">
            <div class="content-wrap">
                <div class="content-inner">
                    <?php require('side.php'); ?>
                    <div class="main-page-wrap view-wrap">
                        <div class="pass-inner view-inner">
                            <h2>パスワード変更</h2>
                            <div class="pass-form">
                                <form action="" method="post">
                                    <label>
                                        <p>古いパスワード</p>
                                        <input type="text" name="old" class="in-common text-common" value="<?php echo keep('old'); ?>">
                                    </label>
                                    <div class="err-msg"><?php echo err('old'); ?></div>
                                    <label>
                                        <p>新しいパスワード</p>
                                        <input type="text" name="new" class="in-common text-common" placeholder="6文字以上の半角英数字" value="<?php echo keep('new'); ?>">
                                    </label>
                                    <div class="err-msg"><?php echo err('new'); ?></div>
                                    <label>
                                        <p>新しいパスワード（再入力）</p>
                                        <input type="text" name="re-new" class="in-common text-common" placeholder=""  value="<?php echo keep('re_new'); ?>">
                                    </label>
                                    <div class="err-msg"><?php echo err('re-new'); ?></div>
                                    <input class="common-btn" type="submit" name="submit" value="変更する">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require('footer.php'); ?>