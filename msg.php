<?php
require('function.php');
debug('-------------msg-------------');
//ログイン認証
require('auth.php');
$opponentid = '';
$opponentinfo = '';
$myinfo = '';
$iteminfo = '';
//GETパラメータ取得
$mb_id = (!empty($_GET['mb_id'])) ? $_GET['mb_id'] : '';
//DBから掲示板とメッセージデータ取得
$viewData = getMandB($mb_id);
debug('取得したDBデータ:'.print_r($viewData,true));
//商品情報を取得
$iteminfo = getpostone($viewData[0]['b_item']);
debug('取得した商品:'.print_r($iteminfo,true));
//viewDataから相手のユーザーIDを取り出す
$dealid[] = $viewData[0]['buy_user'];
$dealid[] = $viewData[0]['sell_user'];
if(($key = array_search($_SESSION['user_id'],$dealid)) !== false){
    unset($dealid[$key]);
}
$opponentid = array_shift($dealid);
debug('相手のID:'.$opponentid);
$opponentinfo = getdb($opponentid);
debug('相手情報:'.print_r($opponentinfo,true));

//DBから自分の情報を取得
$myinfo = getdb($_SESSION['user_id']);
debug('自分のID:'.print_r($myinfo,true));


if(empty($viewData) || empty($iteminfo) || empty($opponentid) || empty($myinfo)){
    error_log('エラー発生:情報が取得できませんでした');
    header("Location:mypage.php");
}else{
    sold($viewData[0]['b_item']);
}


if(!empty($_POST)){
    $msg = (isset($_POST['msg'])) ? $_POST['msg'] : '';
    maxLen($msg,'msg');
    nothing($msg,'msg');
    if(empty($err_msg)){
        debug('バリデーションok');
        try{
            $dbh = db();
            $sql = 'INSERT INTO message SET to_user=?,from_user=?,msg=?,create_date=?,bord_id=?,send_date=?,item_id=?';
            $data = array($opponentid,$_SESSION['user_id'],$msg,date('Y-m-d H:i:s'),$mb_id,date('Y-m-d H:i:s'),$viewData[0]['b_item']);
            $stmt = query($dbh,$sql,$data);
            if($stmt){
                $_POST = array();
                header("Location:".$_SERVER['PHP_SELF'].'?mb_id='.$mb_id);
            }
        }catch (Exception $e){
            error_log('エラー発生:'.$e->getMessage());
        }
    }
}


?>

<?php require('head.php'); ?>
<body id="msg">
    <?php require('header.php'); ?>
    <p id="js-show-msg" class="msg-slide" style="display:none;"><?php echo flash('msg_success'); ?></p>
        <a class="regibtn" href="post.php">
            <div>出品</div>
            <i class="fas fa-camera fa-3x"></i>
        </a>
    <div class="wrapper">
        <div class="main-wrapper">
            <div class="content-wrap">
            
                <div class="content-inner">

                <div class="dealbar-wrap">
                    <div class="sidebar-inner">
                        <p class="deal">取引情報</p>
                        <ul class="deallist">
                            <li>
                                <dl class="flex">
                                    <dt>商品</dt>
                                    <div class="deal-item">
                                        <dd><img src="<?php echo showimg(sanitize($iteminfo['pic1'])); ?>" alt=""></dd>
                                        <dd><?php echo sanitize($iteminfo['name']); ?></dd>
                                    </div>
                                    
                                </dl>
                            </li>
                            <li>
                                <dl class="flex" style="background-color:#f7f7f7;">
                                    <dt>購入日</dt>
                                    <dd><?php echo sanitize($viewData[0]['create_date']); ?></dd>
                                </dl>
                            </li>
                            <li>
                                <dl class="flex">
                                    <dt>お届け先</dt>
                                    <dd><?php echo sanitize($myinfo['address']); ?></dd>
                                </dl>
                            </li>
                        </ul>
                    </div>
                    <div class="poster-wrap">
                        <p class="poster">出品者</p>
                        <div class="poster-link">
                            <a href="">
                                <p class="poster-img"><img style="border-radius:50%;" src="img/sample-img.png" alt=""></p>
                                <p class="poster-name">ダニエル</p>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div>
                    <div class="dealmsg-wrap">
                        <div class="dealmsg-inner">
                            <h2>取引画面</h2>
                            <div class="deal-cons">
                            <?php
                            if(!empty($viewData[0]['msg'])){
                                foreach($viewData as $key => $val){
                                    if(!empty($val['from_user']) && $val['from_user'] == $opponentid){
                            ?>
                                <div class="opp-cons cons">
                                    <ul>
                                        <li>
                                            <dl class="flex">
                                                <dt><img src="<?php echo sanitize(showimg($opponentinfo['pic'])); ?>" alt=""></dt>
                                                <div class="cons-word">
                                                    <dd><?php echo sanitize($val['msg']); ?></dd>
                                                    <dd><?php echo sanitize($val['send_date']); ?></dd>
                                                </div>
                                            </dl>
                                        </li>
                                    </ul>
                                </div>
                                <?php
                                    }else{
                                ?>
                                <div class="my-cons cons">
                                    <ul>
                                        <li>
                                            <dl class="flex">
                                                <dt><img src="<?php echo sanitize(showimg($myinfo['pic'])); ?>" alt=""></dt>
                                                <div class="cons-word">
                                                    <dd><?php echo sanitize($val['msg']); ?></dd>
                                                    <dd style="text-align:right;"><?php echo sanitize($val['send_date']); ?></dd>
                                                </div>  
                                            </dl>
                                        </li>
                                    </ul>
                                </div>
                                <?php
                                    }
                                }
                            }else{?>
                            <div class="no-cons">
                                <p>取引を開始しましょう！</p>
                            </div>
                                <?php
                            }
                                ?>
                                <form action="" method="post">
                                    <textarea name="msg" id="" cols="30" rows="10" placeholder="気になることがあったら質問してみましょう" class="comment"></textarea>
                                    <input type="submit" name="submit_comment" value="取引メッセージを送る" class="comment-btn">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
            </div>
</div>
<?php require('footer.php'); ?>