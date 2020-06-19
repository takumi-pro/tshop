<?php
require('function.php');
debug('-----------------profedit-----------------');
require('auth.php');

$dbdata = getdb($_SESSION['user_id']);
debug('DB情報:'.print_r($dbdata,true));

if(!empty($_POST)){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $zip = (!empty($_POST['zip'])) ? $_POST['zip'] : 0;
    $tel = $_POST['tel'];
    $intro = $_POST['intro'];
    $pic = (!empty($_FILES['pic']['name'])) ? uploadimg($_FILES['pic'],'pic') : '';
    $pic = (empty($pic) && !empty($dbdata['pic'])) ? $dbdata['pic'] : $pic;

    if($dbdata['name'] !== $name){
        maxLen($name,'name');
    }
    if($dbdata['email'] !== $email){
        maxLen($email,'email');
        if(empty($err_msg['email'])){
            emaildup($email);
        }
        nothing($email,'email');
        email($email,'email');
    }
    if($dbdata['address'] !== $address){
        maxLen($address,'address');
    }
    if($dbdata['intro'] !== $intro){
        maxLen($intro,'intro');
    }
    if((int)$dbdata['zip'] !== $zip){
        zip($zip);
    }
    if($dbdata['tel'] !== $tel){
        tel($tel);
    }
    if(empty($err_msg)){
        debug('バリデーションok');
        try{
            $dbh = db();
            $sql = 'UPDATE users SET name=?,email=?,address=?,intro=?,zip=?,tel=?,pic=? WHERE id=? AND delete_flg=0';
            $data = array($name,$email,$address,$intro,$zip,$tel,$pic,$_SESSION['user_id']);
            $stmt = query($dbh,$sql,$data);
            if($stmt){
                debug('変更完了');
                header("Location:mypage.php");
            }
        }catch (Exception $e){
            error_log('エラー発生:'.$e->getMessage());
        }
    }

}
?>

<?php require('head.php'); ?>
<body id="mypage">
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
                    <div class="prof-page-wrap">
                        <div class="prof-img-inner">
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="prof-top ">
                                    <div class="prof-top-in flex">
                                        <label class="prof-file-wrap" for="" method="post" style="">
                                            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                            <input type="file" name="pic" class="file">
                                            <img src="<?php echo showimg(sanitize($dbdata['pic'])); ?>" alt="" class="prev-img" style="<?php if(empty($dbdata['pic'])) echo 'display:none;'; ?>border-radius:50%;">
                                            画像
                                        </label>
                                        <input type="text" name="name" class="name-text" placeholder="ニックネーム" value="<?php echo dbkeep('name'); ?>">
                                    </div>
                                </div>
                                <div class="profedit-wrap">
                                    <label>
                                        <p>メールアドレス</p>
                                        <input type="text" name="email" class="in-common text-common" placeholder="" value="<?php echo dbkeep('email'); ?>">
                                    </label>
                                    <div class="err-msg"><?php echo err('email'); ?></div>
                                    <label>
                                        <p>住所</p>
                                        <input type="text" name="address" class="in-common text-common" placeholder="" value="<?php echo dbkeep('address'); ?>">
                                    </label>
                                    <div class="err-msg"></div>
                                    <div class="tel-zip flex">
                                        <div class="zip-in">
                                            <div class="zip" style="color:#111;">
                                                <p>郵便番号<span style="font-size:12px;color: rgb(172, 172, 172);margin-left:8px;">※ハイフンなし</span></p>
                                                <div style="width:100%;" class="flex">
                                                    <input type="text" name="zip" class="in-common text-common" value="<?php echo dbkeep('zip'); ?>">
                                                </div>
                                            </div>
                                            <div class="err-msg"><?php echo err('zip'); ?></div>
                                        </div>
                                        <div class="tel-in">
                                            <div class="tel">
                                                <p>電話番号<span style="font-size:12px;color: rgb(172, 172, 172);margin-left:8px;">※ハイフンなし</span></p>
                                                <div style="width:100%;" class="flex">
                                                    <input type="text" name="tel" class="in-common text-common" value="<?php echo dbkeep('tel'); ?>">
                                                </div>
                                            </div>
                                            <div class="err-msg"><?php echo err('tel'); ?></div>
                                        </div>
                                    </div>
                                    <label>
                                        <p>自己紹介文</p>
                                        <textarea name="intro" class="in-common teatarea" cols="30" rows="10" placeholder="自分をアピールして商品を買ってもらおう！"><?php echo dbkeep('intro'); ?></textarea>
                                    </label>
                                    <div class="err-msg"><?php echo err('intro'); ?></div>
                                    <div class="prof-btn">
                                        <input type="submit" name="submit" class="common-btn" value="変更する">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require('footer.php'); ?>