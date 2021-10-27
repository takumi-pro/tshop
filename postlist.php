<?php 
require('function.php');
debug('--------------posilist---------');

$nowpage = (!empty($_GET['p'])) ? $_GET['p'] : 1;
$w_id = (!empty($_GET['search'])) ? $_GET['search'] : '';
debug($w_id);
$search = (!empty($_GET['search'])) ? $_GET['search'] : '';
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';
$keyword = (!empty($_GET['keyword'])) ? $_GET['keyword'] : '';
$cate = (!empty($_GET['category'])) ? $_GET['category'] : '';
$price = (!empty($_GET['price'])) ? $_GET['price'] : '';
$cate = (!empty($_GET['category'])) ? $_GET['category'] : '';
$c1 = (!empty($_GET['c1'])) ? $_GET['c1'] : '';
$c2 = (!empty($_GET['c2'])) ? $_GET['c2'] : '';
$c3 = (!empty($_GET['c3'])) ? $_GET['c3'] : '';
$c4 = (!empty($_GET['c4'])) ? $_GET['c4'] : '';
$c5 = (!empty($_GET['c5'])) ? $_GET['c5'] : '';
$c6 = (!empty($_GET['c6'])) ? $_GET['c6'] : '';
$dbstatus = getstatus();
$dbcate = getcate();
//表示件数
$list = 20;
//現在の表示レコード先頭を算出
$nowmin = (($nowpage-1)*$list);
//DBから投稿情報を取得
$dbpostdata = getpostlist($nowmin,$cate,$price,$keyword,$c1,$c2,$c3,$c4,$c5,$c6,$search,$sort);
if(!is_int((int)$nowpage)){
    header("Location:mypage.php");
    error_log('エラー発生:指定ページに不正な値が入りました');
}
debug('現在ページ:'.$nowpage);
?>

<?php require('head.php'); ?>
<body id="postlist">
    <?php require('header.php'); ?>
    <a class="regibtn" href="post.php">
            <div>出品</div>
            <i class="fas fa-camera fa-3x"></i>
    </a>
    <div class="wrapper">
        <div class="main-wrapper">
            <div class="content-wrap">
                <div class="content-inner flex">
                    <div class="search-detail">
                        <form action="" method="get">
                            <h2>詳細検索</h2>
                            <label>
                                <p>キーワード</p>
                                <input type="text" name="keyword" class="in-common text-common" placeholder="" value="<?php if(!empty($search)){echo $search;}elseif(empty($search)){echo dbkeep('keyword',true);} ?>">
                            </label>
                            <p>カテゴリーを選択する</p>
                            <select name="category" id="" class="select">
                                <option value="0" <?php if(dbkeep('category',true) == 0) echo 'selected'; ?>>すべて</option>
                                <?php foreach($dbcate as $key => $val): ?>
                                <option value="<?php echo $val['id']; ?>" <?php if(dbkeep('category',true) == $val['id']) echo 'selected'; ?>><?php echo $val['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p>価格帯</p>
                            <select name="price" id="" class="select">
                                <option value="0" <?php if(dbkeep('price',true) == 0) echo 'selected'; ?>>選択してください</option>
                                <option value="1" <?php if(dbkeep('price',true) == 1) echo 'selected'; ?>>~300</option>
                                <option value="2" <?php if(dbkeep('price',true) == 2) echo 'selected'; ?>>300~1000</option>
                                <option value="3" <?php if(dbkeep('price',true) == 3) echo 'selected'; ?>>1000~5000</option>
                                <option value="4" <?php if(dbkeep('price',true) == 4) echo 'selected'; ?>>5000~10000</option>
                                <option value="5" <?php if(dbkeep('price',true) == 5) echo 'selected'; ?>>10000~30000</option>

                            </select>
                            <p>表示順</p>
                            <select name="sort" id="" class="select">
                                <option value="0" <?php if(dbkeep('sort',true) == 0) echo 'selected'; ?>>選択してください</option>
                                <option value="1" <?php if(dbkeep('sort',true) == 1) echo 'selected'; ?>>新しい順</option>
                                <option value="2" <?php if(dbkeep('sort',true) == 2) echo 'selected'; ?>>古い順</option>
                            </select>
                            <p>商品状態</p>
                        
                            <ul class="flex check-list">
                                <li>
                                    <label>
                                        <input class="check" type="checkbox" name="c1" value="1" <?php if(dbkeep('c1',true) == 1) echo 'checked="checked"'; ?>>
                                        <span>新品、未使用</span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input class="check" type="checkbox" name="c2" value="2" <?php if(dbkeep('c2',true) == 2) echo 'checked="checked"'; ?>>
                                        <span>未使用に近い</span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input class="check" type="checkbox" name="c3" value="3" <?php if(dbkeep('c3',true) == 3) echo 'checked="checked"'; ?>>
                                        <span>目立った傷や汚れなし</span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input class="check" type="checkbox" name="c4" value="4" <?php if(dbkeep('c4',true) == 4) echo 'checked="checked"'; ?>>
                                        <span>やや傷や汚れあり</span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input class="check" type="checkbox" name="c5" value="5" <?php if(dbkeep('c5',true) == 5) echo 'checked="checked"'; ?>>
                                        <span>傷や汚れあり</span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input class="check" type="checkbox" name="c6" value="6" <?php if(dbkeep('c6',true) == 6) echo 'checked="checked"'; ?>>
                                        <span>全体的に状態が悪い</span>
                                    </label>
                                </li>
                            </ul>

                            <input class="common-btn" type="submit" name="submit" value="完了する">
                            
                            
                        </form>
                    </div>
                    <div class="postlist-wrap">
                        <div class="postlist-inner">
                            <div class="search-word">
                            <?php if(!empty($search)){ ?>
                                <h2 class="">'<?php echo $search; ?>'の検索結果</h2>
                            <?php }elseif(!empty($keyword)){ ?>
                                <h2 class="">'<?php echo $keyword; ?>'の検索結果</h2>
                            <?php }else{ ?>
                                <h2 class="">検索結果</h2>
                            <?php } ?>
                                <p><span><?php echo (!empty($dbpostdata['data'])) ? $nowmin+1 : 0; ?></span> - <span><?php echo $nowmin+count($dbpostdata['data']); ?></span>件 / <span><?php echo $dbpostdata['total']; ?></span>件中</p>
                            </div>
                            <?php if($dbpostdata['total'] != 0){ ?>
                            <div class="search-items">
                                <ul class="flex">
                                    <li>
                                <?php foreach($dbpostdata['data'] as $key => $val): 
                                    $sold = getpostone($val['id']); ?>
                                        <a href="postdetail.php<?php echo (!empty(append())) ? append().'&p_id='.$val['id'] : '?p_id='.$val['id']; ?>">
                                            <dl style="position:relative;">
                                                <dt><img src="<?php echo sanitize($val['pic1']); ?>" alt=""></dt>
                                                <dd><?php echo sanitize($val['name']); ?></dd>
                                                <?php if($sold['sold_item'] == 1){ ?>
                                                <dd class="sold-item"></dd>
                                                <dd class="sold-word">SOLD</dd>
                                                <?php } ?>
                                                <dd>¥<?php echo sanitize($val['price']); ?></dd>
                                        
                                            </dl>
                                        </a>   
                                    </li>
                                <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php }else{ ?>
                            <div class="search_noitem">
                                <p class="noitem-m" style="text-align:center;">検索結果はありません</p>
                            </div>
                            <?php } ?>
                            <?php pagenation($nowpage,$dbpostdata['total_page']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require('footer.php'); ?>