<?php
require('function.php');
$newiteminfo = newitem();
debug('新着情報：'.print_r($newiteminfo,true));
?>

<?php require('head.php'); ?>
<body id="top">
    <?php require('header.php'); ?>
    <div class="wrapper">
        <div class="main-img">
        </div>
        <div class="main-wrapper">
            <div class="main-des-wrap">
                <div class="main-des-inner">
                    <p>身の回りに、いらなくなったものはありませんか？<br>
                    それは誰かが欲しがっているかも...今すぐtshopで出品しよう！ <br>
                </p>
                <p class="try-wrap"><a href="signup.php" class="try-btn">出品してみる</a></p>
                </div>
            </div>
            <div class="newitem-wrap items-wrap">
                <div class="newitem-inner items-inner">
                    <div class="cate-title">
                        <h2>新着商品</h2>
                    </div>
                    <div class="newitem items">
                        <?php if(!empty($newiteminfo[0]['name'])): ?>
                        <ul class="flex">
                            <?php foreach($newiteminfo as $key => $val): ?>
                            <li>
                                <a href="postdetail.php<?php echo '?p_id='.$val['id']; ?>">
                                    <dl style="position: relative;">
                                    <dt><img src="<?php echo $val['pic1']; ?>" alt=""></dt>
                                    <dd>¥<?php echo $val['price']; ?></dd>
                                    <dd><?php echo $val['name']; ?></dd>
                                </dl>
                                </a>
                            </li>
                            <?php endforeach; ?>
                            <?php else: ?>
                                <p class="newitem-nothing">新着商品はありません</p>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
</div>
</div>
<?php require('footer.php'); ?>