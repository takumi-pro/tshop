<?php
require('function.php');
debug('-----------新規登録-------------');

if(!empty($_POST)){
    debug('POST送信あり');
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $pass_re = $_POST['re_password'];

    
    nothing($email,'email');
    nothing($pass,'password');
    nothing($pass_re,'re_password');

    if(empty($err_msg)){
        email($email);
        emaildup($email);
        maxLen($email,'email');

        maxLen($pass,'password');
        minLen($pass,'password');
        halfnum($pass,'password');

        /*maxLen($pass_re,'re_password');
        minLen($pass_re,'re_password');
        halfnum($pass_re,'re_password');*/

        if(empty($err_msg)){
            match($pass,$pass_re,'password');  
            if(empty($err_msg)){
                debug('バリデーションok');
                try{
                    $dbh = db();
                    $sql = 'INSERT INTO users SET name=?,email=?,password=?,create_date=?,login_time=?';
                    $data = array($name,$email,password_hash($pass,PASSWORD_DEFAULT),date('Y-m-d H:i:s'),date('Y-m-d H:i:s'));
                    $stmt = query($dbh,$sql,$data);
                    if($stmt){
                        $limit = 60*60;
                        $_SESSION['login_time'] = time();
                        $_SESSION['login_limit'] = $limit;
                        $_SESSION['user_id'] = $dbh->lastInsertID();
                        header("Location:mypage.php");
                    }else{
                        $err_msg['common'] = MSG03;
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
                        <h2>新規会員登録</h2>
                    </div>
                    <div class="form-middle">
                        <form action="" method="post">
                        <div class="err-msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
                            <label for="">
                                <p>ニックネーム<span class="free">任意</span></p>
                                <input type="text" name="name" class="in-common text-common" placeholder="例）フリマ太郎" value="<?php echo keep('name'); ?>">
                            </label>
                            <div class="err-msg"><?php echo err('name'); ?></div>
                            <label for="">
                                <p>メールアドレス<span class="imp">必須</span></p>
                                <input type="text" name="email" class="in-common text-common" placeholder="example@gmail.com" value="<?php echo keep('email'); ?>">
                                
                            </label>
                            <div class="err-msg"><?php echo err('email'); ?></div>
                            <label for="">
                                <p>パスワード<span class="imp">必須</span></p>
                                <input type="password" name="password" class="in-common text-common" placeholder="６文字以上の半角英数字" value="<?php echo keep('password'); ?>">
                                
                            </label>
                            <div class="err-msg"><?php echo err('password'); ?></div>
                            <label for="">
                                <p>パスワード（再入力）<span class="imp">必須</span></p>
                                <input type="password" name="re_password" class="in-common text-common" value="<?php echo keep('re_password'); ?>">
                                
                            </label>
                            <div class="err-msg"><?php echo err('re_password'); ?></div>
                            <input class="common-btn" type="submit" name="submit" value="登録">
                        </form>
                    </div>
                    <div class="form-bottom">
                        <p style="margin-bottom:8px;text-align:center;">登録済の方はこちら</p>
                        <a href="login.php">ログイン</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require('footer.php'); ?>