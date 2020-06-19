<?php
require('function.php');
debug('-------------postdetail-----------');

$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
$m_id = (!empty($_GET['m_id'])) ? $_GET['m_id'] : '';

$viewdata = getpostone($p_id);
if(empty($viewdata)){
    debug('エラー発生:指定ページに不正な値が入りました');
    header("Location:postlist.php");
}
$postuser = getdb($viewdata['user_id']);
debug('出品者情報:'.print_r($postuser,true));

if(!empty($_POST['submit'])){
    debug('購入');
    if(!empty($_SESSION['user_id'])){
        try{
            $dbh = db();
            $sql = 'INSERT INTO bord SET sell_user=?,buy_user=?,item_id=?,create_date=?';
            $data = array($viewdata['user_id'],$_SESSION['user_id'],$p_id,date('Y-m-d H:i:s'));
            $stmt = query($dbh,$sql,$data);
            if($stmt){
                $_SESSION['msg_success'] = SUC02;
                header("Location:msg.php?mb_id=".$dbh->lastInsertID());
            }
        }catch (Exception $e){
            error_log('エラー発生:'.$e->getMessage());
        }
    }elseif(basename($_SERVER['PHP_SELF']) === 'postdetail.php'){
        header("Location:signup.php");
    }
}

if(!empty($_POST['submit_comment'])){
    
    $comment = $_POST['comment'];
    maxLen($comment,'comment');
    debug('コメント:'.$comment);
    if(empty($err_msg)){
        try{
            $dbh = db();
            $sql = 'INSERT INTO message SET msg=?,from_user=?,to_user=?,create_date=?,send_date=?,item_id=?';
            $data = array($comment,$_SESSION['user_id'],$postuser['id'],date('Y-m-d H:i:s'),date('Y-m-d H:i:s'),$p_id);
            $stmt = query($dbh,$sql,$data);
            if($stmt){
                $resu = $stmt->fetch(PDO::FETCH_ASSOC);
                header("Location:".$_SERVER['PHP_SELF'].'?p_id='.$p_id.'&m_id='.$dbh->lastInsertID());
            }
        }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
    }
}
$cmt = getcmt($p_id);
debug('コメント:'.print_r($cmt,true));
?>

<?php require('head.php'); ?>
<body id="mypage">
    <?php require('header.php'); ?>
    <div class="wrapper">
        <div class="main-wrapper">
            <div class="postdetail-wrap">
                <div class="postdetail-inner-top">
                    <h1 class="item-name" style="text-align:center;"><?php echo sanitize($viewdata['name']); ?></h1>
                    <div class="main-detail flex">
                        <div class="item-img-wrap">
                            <div class="item-main-img">
                                <img src="<?php echo sanitize(showimg($viewdata['pic1'])); ?>" alt="">
                                <?php if($viewdata['sold_item'] == 1){ ?>
                                <dd class="sold-item"></dd>
                                <dd class="sold-word">SOLD</dd>
                                <?php } ?>
                            </div>
                            <div class="item-sub-wrap flex">
                                <div class="sub-img"><img src="<?php echo sanitize(showimg($viewdata['pic1'])); ?>" alt=""></div>
                                <div class="sub-img"><img src="<?php echo sanitize(showimg($viewdata['pic2'])); ?>" alt=""></div>
                                <div class="sub-img"><img src="<?php echo sanitize(showimg($viewdata['pic3'])); ?>" alt=""></div>
                            </div>
                        </div>
                        <table class="item-table">
                            <tbody>
                                <tr>
                                    <th>出品者</th>
                                    <td><a href=""><?php echo sanitize($postuser['name']); ?></a></td>
                                </tr>
                                <tr>
                                    <th>カテゴリー</th>
                                    <td><a href=""><?php echo sanitize($viewdata['category']); ?></a></td>
                                </tr>
                                <tr>
                                    <th>商品の状態</th>
                                    <td><?php echo sanitize($viewdata['status']); ?></td>
                                </tr>
                            </tbody>
                            
                        </table>
                        
                    </div>
                    <div class="price">¥ <span><?php echo sanitize($viewdata['price']); ?></span></div>
                    <div class="buy">
                    <form action="" method="post">
                        <?php if($viewdata['sold_item'] == 1){ ?>
                        <div class="sold-out">売り切れました</div>
                        <?php }else{ ?>
                        <input type="submit" name="submit" class="buy-btn" value="購入する">
                        <?php } ?>
                    </form>
                        <!--<a href="" class="buy-btn">購入する</a>-->
                    </div>
                    <div class="item-description">
                        <span>商品の説明</span>
                        <p><?php echo sanitize($viewdata['des']); ?></p>
                        
                    </div>
                    <div class="favo">
                        <span data-itemid="<?php echo sanitize($viewdata['id']); ?>" class="js-click-favo"><i class="fas fa-heart <?php if(isFavo($_SESSION['user_id'],$viewdata['id'])) echo 'favo-active'; ?>" style="margin-right:5px; "></i>いいね！</span>
                    </div>
                </div>
                <div class="postdetail-inner-bottom">
                <?php foreach($cmt as $key => $val): ?>
                <div class="comment-view">
                    <div class="cmt-img"><img src="<?php echo showimg($val['pic']); ?>" alt=""></div>
                    <div class="cmt-content">
                        <div class="cmt-name"><span><?php echo $val['username']; ?></span></div>
                        <div class="cmt-word">
                            <p class="cmt"><?php echo $val['msg']; ?></p>
                            <span class="date"><?php echo $val['send_date']; ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                    <form action="" method="post">
                        <textarea name="comment" id="" cols="30" rows="10" placeholder="気になることがあったら質問してみましょう" class="comment"></textarea>
                        <input type="submit" name="submit_comment" value="コメントする" class="comment-btn">
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php require('footer.php'); ?>