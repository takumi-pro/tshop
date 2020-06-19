<?php
require('function.php');
debug('---------ログイン----------');
require('auth.php');
if(!empty($_POST)){
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $save = (!empty($_POST['save'])) ? true : false;

    nothing($email,'email');
    nothing($pass,'password');
    if(empty($err_msg)){
        email($email);
        maxLen($email,'email');
        maxLen($pass,'password');
        minLen($pass,'password');
        halfnum($pass,'password');
        if(empty($err_msg)){
            debug('バリデーションok');
            try{
                $dbh = db();
                $sql = 'SELECT password,id FROM users WHERE email=? AND delete_flg=0';
                $data = array($email);
                $stmt = query($dbh,$sql,$data);
                $resu = $stmt->fetch(PDO::FETCH_ASSOC);
                if(!empty($resu) && password_verify($pass,array_shift($resu))){
                    debug('パスワードがマッチしました');
                    $limit = 60*60;
                    $_SESSION['login_time'] = time();
                    $_SESSION['user_id'] = $resu['id'];
                    if($save){
                        $_SESSION['login_limit'] = $limit*24*30;
                        debug('ログイン保持がありました');
                    }else{
                        $_SESSION['login_limit'] = $limit;
                        debug('ログイン保持なし');
                    }
                    debug('セッション変数中身:'.print_r($_SESSION,true));
                    header("Location:mypage.php");
                }else{
                    debug('パスワードがマッチしませんでした');
                    $err_msg['common'] = MSG09;
                }
            }catch (Exception $e){
                error_log('エラー発生:'.$e->getMessage());
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
                        <h2>ログイン</h2>
                    </div>
                    <div class="form-middle">
                        <form action="" method="post">
                        <div class="err-msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
                            <label>
                                <p>メールアドレス</p>
                                <input type="text" name="email" class="in-common text-common" placeholder="example@gmail.com" value="<?php echo keep('email'); ?>">
                            </label>
                            <div class="err-msg"><?php echo err('email'); ?></div>
                            <label>
                                <p>パスワード</p>
                                <input type="password" name="password" class="in-common text-common" placeholder="６文字以上の半角英数字" value="<?php echo keep('password'); ?>">                               
                            </label>
                            <div class="err-msg"><?php echo err('password'); ?></div>

                            <label><input type="checkbox" name="save" style="margin-right:10px;">ログインを保持する</label>
                            
                            <input class="common-btn" type="submit" name="submit" value="ログイン" style="font-size:16px;margin-top:">
                            
                            <a href="remindsend.php" style="color: rgb(85, 144, 255);">パスワードをお忘れの方</a>
                        </form>
                    </div>
                    <div class="form-bottom">
                        <p style="margin-bottom:8px;text-align:center;">アカウントをお持ちでない方はこちら</p>
                        <a href="signup.php">新規会員登録</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require('footer.php'); ?>