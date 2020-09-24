<?php
require('function.php');
debug('------------receive------------');
debug('セッション変数:'.print_r($_SESSION,true));
if(empty($_SESSION['auth_key'])){
    debug('認証キーがありません');
    header("Location:remindsend.php");
}
if(!empty($_POST)){
    $auth = $_POST['auth_key'];
    nothing($auth,'auth_key');
    if(empty($err_msg)){
        length($auth,'auth_key');
        halfnum($auth,'auth_key');
        if(empty($err_msg)){
            debug('バリデーションok');
            if($auth !== $_SESSION['auth_key']){
                $err_msg['auth_key'] = MSG16;
            }
            if(time() > $_SESSION['auth_limit']){
                $err_msg['auth_key'] = MSG17;
            }
            if(empty($err_msg)){
                debug('認証ok');
                $password = Randkey();
                try{
                    $dbh = db();
                    $sql = 'UPDATE users SET password=? WHERE email=? AND delete_flg=0';
                    $data = array(password_hash($password,PASSWORD_DEFAULT),$_SESSION['auth_email']);
                    $stmt = query($dbh,$sql,$data);
                    if($stmt){
                        $from = 'takumidiary.0927@gmail.com';
                        $to = $_SESSION['auth_email'];
                        $subject = 'パスワード再発行認証';
                        $comment = <<<EOT
                        本メールアドレス宛にパスワードの再発行を致しました。
                        下記のURLにて再発行パスワードをご入力頂き、ログインください。
            
                        ログインページ：http://takumidiary.com/tshop/login.php
                        再発行パスワード：{$password}
                        ※ログイン後、パスワードのご変更をお願い致します
                        EOT;
                        sendMail($to,$subject,$comment,$from);
                        session_unset();
                        debug('ログインページへ');
                        debug($password);
                        header("Location:login.php");
                    }else{
                        debug('クエリに失敗しました');
                        $err_msg['common'] = MSG08;
                    }
                }catch (Exception $e){
                    error_log('エラー発生:'.$e->getMessage());
                }
            }
        }
    }
}
?>

<?php require('head.php'); ?>
<body id="signup">
    <?php require('signup-header.php'); ?>
    <div class="wrapper">
        <div class="main-wrapper">
            <div class="form-wrap">
                <div class="form-inner">
                    <div class="form-top">
                        <h2>認証キーの確認</h2>
                    </div>
                    <div class="form-middle">
                        <form action="" method="post">
                            <div class="err-msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
                            <label>
                                <p>認証キー</p>
                                <input type="text" name="auth_key" class="in-common text-common" placeholder="認証キー" value="<?php echo keep('auth_key'); ?>">
                            </label>
                            <div class="err-msg"><?php echo err('auth_key'); ?></div>
                            <span>ご登録されたメールアドレスにお送りした「認証キー」をご入力ください。</span>
                            
                            <input class="common-btn" type="submit" name="submit" value="再発行する" style="font-size:16px;margin-top:">
                            
                        
                        </form>
                    </div>
                
                </div>
            </div>
        </div>
    </div>
<?php require('footer.php'); ?>