<?php
require('function.php');
debug('------------send------------');

if(!empty($_POST)){
    $email = $_POST['email'];
    nothing($email,'email');
    if(empty($err_msg)){
        email($email);
        maxLen($email,'email');
        if(empty($err_msg)){
            debug('バリデーションok');
            try{
                $dbh = db();
                $sql = 'SELECT count(*) FROM users WHERE email=? AND delete_flg=0';
                $data = array($email);
                $stmt = query($dbh,$sql,$data);
                $resu = $stmt->fetch(PDO::FETCH_ASSOC);
                if($stmt && !empty(array_shift($resu))){
                    $authkey = Randkey();
                    $from = 'info@tshop.com';
                    $to = $email;
                    $subject = 'パスワード再発行認証';
                    $comment = <<<EOT
                    本メールアドレス宛にパスワード再発行のご依頼がありました。
                    下記のURLにて認証キーをご入力頂くとパスワードが再発行されます。
          
                    パスワード再発行認証キー入力ページ：http://localhost:8888/tshop/remindrecieve.php
                    認証キー：{$authkey}
                    ※認証キーの有効期限は30分となります
          
                    認証キーを再発行されたい場合は下記ページより再度再発行をお願い致します。
                    http://localhost:8888/tshop/remindsend.php
                    EOT;
                    sendMail($to,$subject,$comment,$from);
                    $_SESSION['auth_key'] = $authkey;
                    $_SESSION['auth_email'] = $email;
                    $_SESSION['auth_limit'] = time() + (60*30);
                    header("Location:remindreceive.php");
                }else{
                    debug('クエリに失敗したかDBに登録のないemailが入力されました');
                    $err_msg['common'] = MSG03;
                }
            }catch (Exception $e){
                error_log('エラー発生:'.$e->getMessage());
                $err_msg['common'] = MSG08;
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
                        <h2>パスワードをお忘れの方</h2>
                    </div>
                    <div class="send-form-middle">
                        <form action="" method="post">
                            <div class="err-msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
                            <label>
                                <p>メールアドレス</p>
                                <input type="text" name="email" class="in-common text-common" placeholder="ご登録されたメールアドレス" value="<?php echo keep('email'); ?>">
                            </label>
                            <div class="err-msg"><?php echo err('email'); ?></div>
                            <span>ご登録されたメールアドレスにパスワード再設定のご案内が送信されます。</span>
                            
                            <input class="common-btn" type="submit" name="submit" value="送信する" style="font-size:16px;margin-top:">
                            
                        
                        </form>
                    </div>
                
                </div>
            </div>
        </div>
    </div>
<?php require('footer.php'); ?>