<?php
require('function.php');
require('auth.php');
debug('-----------マイページ----------');

//自分情報を取得
$myinfo = getdb($_SESSION['user_id']);
debug('自分情報:'.print_r($myinfo,true));
//出品商品情報取得
$postitem = getpostitem($_SESSION['user_id']);
debug('出品商品情報:'.print_r($postitem,true));
//メッセージ情報取得
$mymsg = getMyMandB($_SESSION['user_id']);
debug('メッセージ情報:'.print_r($mymsg,true));
//取引商品情報
$dealitem = getpostone($mymsg['item_id']);
?>

<?php require('head.php'); ?>
<body id="mypage">
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

                    <?php require('side.php'); ?>

                    <div class="main-page-wrap">
                        <div class="main-page-inner">
                            <div class="main-page-img">
                                <div class="prof-top">
                                    <div class="prof-img"><img src="<?php echo showimg(sanitize($myinfo['pic'])); ?>" alt=""></div>
                                    <p class="myname"><?php echo sanitize($myinfo['name']); ?></p>
                                </div>
                                <div class="prof-bottom">
                                    <div class="prof-bottom-inner">
                                        <h2>出品した商品</h2>
                                        <?php if(!empty($postitem)){ ?>
                                            <ul class="flex">
                                                <?php foreach($postitem as $key => $val){ ?>
                                                    <li><a class="prof-item-link" href="post.php?p_id=<?php echo $val['id']; ?>">
                                                        <dl class="flex">
                                                            <dt><img src="<?php echo showimg(sanitize($val['pic1'])); ?>" alt=""></dt>
                                                            <dd><?php echo sanitize($val['name']); ?></dd>
                                                        </dl>
                                                    </a></li>
                                                <?php } ?>
                                            </ul>
                                            <?php }else{ ?>
                                            <p class="no-item">出品した商品はありません</p>
                                        <?php } ?>
                                    </div>

                                    <div class="prof-bottom-inner">
                                        <h2>取引中の商品</h2>
                                        <?php if(!empty($mymsg)){ ?>
                                        <ul class="flex">
                                        <?php foreach($mymsg as $key => $val){
                                            if(!empty($val['msg'])){
                                                $msg = array_shift($val['msg']);
                                                $dealitem = getpostone($val['item_id']); ?>
                                            <li><a class="prof-item-link" href="msg.php?mb_id=<?php echo $val['id']; ?>">
                                                <dl class="flex">
                                                    <dt><img src="<?php echo showimg(sanitize($dealitem['pic1'])); ?>" alt=""></dt>
                                                    <div>
                                                        <dd><?php echo sanitize($dealitem['name']); ?></dd>
                                                        <dd class="bordmsg"><?php echo sanitize(mb_substr($msg['msg'],0,25,"UTF-8").'...'); ?></dd>
                                                    </div>
                                                </dl>
                                            </a></li>
                                            <?php }else{ 
                                                $dealitem = getpostone($val['item_id']); ?>
                                                <li><a class="prof-item-link" href="msg.php?mb_id=<?php echo $val['id']; ?>">
                                                <dl class="flex">
                                                    <dt><img src="<?php echo showimg(sanitize($dealitem['pic1'])); ?>" alt=""></dt>
                                                    <div>
                                                        <dd><?php echo sanitize($dealitem['name']); ?></dd>
                                                        <dd class="bordmsg">取引メッセージはありません</dd>
                                                    </div>
                                                </dl>
                                            </a></li>
                                            <?php }
                                            } ?>
                                        </ul>
                                        <?php }else{ ?>
                                        <div><p class="no-item">取引中の商品はありません</p></div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            </div>
</div>
<?php require('footer.php'); ?>