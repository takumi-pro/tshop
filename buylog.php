<?php
require('function.php');
require('auth.php');

$mymsg = getMyMandB($_SESSION['user_id']);
?>

<?php require('head.php'); ?>
<body id="buylog">
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
                    
                    <div>
                        <div class="view-wrap">
                            <div class="view-inner">
                                <h2>購入した商品</h2>
                                <ul class="">
                                    <?php if(!empty($mymsg)){
                                        foreach($mymsg as $key => $val){
                                            $buyitem = getpostone($val['item_id']);
                                            $msg = array_shift($val['msg']); ?>
                                    <li><a class="prof-item-link" href="msg.php?mb_id=<?php echo $val['id']; ?>">
                                        <dl class="flex">
                                            <dt><img src="<?php echo showimg(sanitize($buyitem['pic1'])); ?>" alt=""></dt>
                                            <dd><?php echo sanitize($buyitem['name']); ?></dd>
                                            <?php if(!empty($msg['msg'])){ ?>
                                            <span class="dealing">取引中</span>
                                            <?php } ?>
                                        </dl>
                                    </a></li>
                                        <?php } ?>
                                </ul>
                                        <?php }else{ ?>
                                            <div><p class="no-item">購入した商品はありません</p></div>
                                        <?php } ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            </div>
</div>
<?php require('footer.php'); ?>