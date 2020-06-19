<?php
require('function.php');
require('auth.php');

//いいね情報取得
$myfavo = getdbFavo($_SESSION['user_id']);
debug('いいね情報:'.print_r($myfavo,true));
?>

<?php require('head.php'); ?>
<body id="favolist">
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
                        <div class="favolist-wrap view-wrap">
                            <div class="favolist-inner view-inner">
                                <h2>いいね！一覧</h2>
                                <?php if(!empty($myfavo)){ ?>
                                <ul class="">
                                    <?php foreach($myfavo as $key => $val){ 
                                        $favoitem = getpostone($val['item_id']); ?>
                                    <li><a class="prof-item-link" href="postdetail.php?p_id=<?php echo $val['item_id']; ?>">
                                        <dl class="flex">
                                            <dt><img src="<?php echo showimg(sanitize($favoitem['pic1'])); ?>" alt=""></dt>
                                            <dd><?php echo sanitize($favoitem['name']); ?></dd>
                                        </dl>
                                    </a></li>
                                    <?php } ?>
                                </ul>
                                <?php }else{ ?>
                                    <div><p class="no-item">いいねした商品はありません</p></div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            </div>
</div>
<?php require('footer.php'); ?>