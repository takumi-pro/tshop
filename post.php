<?php
require('function.php');
debug('-------------post-----------');
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
debug('GET:'.print_r($_GET,true));
$dbdata = (!empty($p_id)) ? getpost($_SESSION['user_id'],$p_id) : '';
$edit_flg = (empty($dbdata)) ? false : true;
$dbstatus = getstatus();
$dbcate = getcate();
if(!empty($_POST)){
    debug('POST情報:'.print_r($_POST,true));
    $name = $_POST['name'];
    $des = $_POST['des'];
    $cate = $_POST['category_id'];
    $status = $_POST['status_id'];
    $price = $_POST['price'];
    $pic1 = (!empty($_FILES['pic1']['name'])) ? uploadimg($_FILES['pic1'],'pic1') : '';
    $pic1 = (empty($pic1) && !empty($dbdata['pic1'])) ? $dbdata['pic1'] : $pic1;
    $pic2 = (!empty($_FILES['pic2']['name'])) ? uploadimg($_FILES['pic2'],'pic2') : '';
    $pic2 = (empty($pic2) && !empty($dbdata['pic2'])) ? $dbdata['pic2'] : $pic2;
    $pic3 = (!empty($_FILES['pic3']['name'])) ? uploadimg($_FILES['pic3'],'pic3') : '';
    $pic3 = (empty($pic3) && !empty($dbdata['pic3'])) ? $dbdata['pic3'] : $pic3;

    nothing($name,'name');
    nothing($des,'des');
    nothing($cate,'category_id');
    nothing($status,'status_id');
    nothing($price,'price');
    if(empty($dbdata)){
        //新規の場合
        itemnameLen($name,'name');
        maxLen($des,'des');
        selectcheck($cate,'category_id');
        selectcheck($status,'status_id');
        halfnum($price,'price');
    }else{
        //編集の場合
        if($dbData['name'] !== $name){
            itemnameLen($name,'name');
        }
        if($dbData['des'] !== $des){
            maxLen($des,'des');
        }
        if($dbData['categpry_id'] !== $cate){
            selectcheck($cate,'category_id');
        }
        if($dbData['status_id'] !== $status){
            selectcheck($status,'status_id');
        }
    }
    if(empty($err_msg)){
        debug('バリデーションok');
        try{
           $dbh = db();
            if($edit_flg){
                debug('編集');
                $sql = 'UPDATE item SET name=?,category_id=?,des=?,status_id=?,price=?,pic1=?,pic2=?,pic3=? WHERE id=? AND user_id=?';
                $data = array($name,$cate,$des,$status,$price,$pic1,$pic2,$pic3,$p_id,$_SESSION['user_id']);
                $stmt = query($dbh,$sql,$data);
            }else{
                debug('登録');
                $sql = 'INSERT INTO item SET name=?,des=?,category_id=?,status_id=?,user_id=?,create_date=?,price=?,pic1=?,pic2=?,pic3=?';
                $data = array($name,$des,$cate,$status,$_SESSION['user_id'],date('Y-m-d H:i:s'),$price,$pic1,$pic2,$pic3);
                $stmt = query($dbh,$sql,$data);
            }
            if($stmt){
                debug('マイページへ');
                $_SESSION['msg_success'] = SUC01;
                header("Location:mypage.php");
            } 
        }catch (Exception $e){
            error_log('エラー発生:'.$e->getMessage());
        }
    }
}
?>

<?php require('head.php'); ?>
<body id="post">
    <?php require('post-header.php'); ?>
    <div class="wrapper">
        <div class="main-wrapper">
            <div class="post-form-wrap">
                <div class="post-form-inner">
                    <form action="" method="post" enctype="multipart/form-data">
                    <div class="post-img-wrap">
                            <p>出品写真<span class="imp">必須</span></p>
                            <ul class="flex">
                                <li class="post-image">
                                    <label class="raf input-file" for="" method="post" style="">
                                        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                        <input type="file" name="pic1" class="file">
                                        <img src="<?php echo dbkeep('pic1'); ?>" alt="" class="prev-img" style="<?php if(empty(dbkeep('pic1'))) echo 'display:none;'; ?>">
                                        drag & drop
                                    </label>
                                    <div class="err-msg"><?php echo err('pic1'); ?></div>
                                </li>
                                <li class="post-image">
                                    <label class="raf input-file" for="" method="post" style="">
                                        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                        <input type="file" name="pic2" class="file">
                                        <img src="<?php echo dbkeep('pic2'); ?>" alt="" class="prev-img" style="<?php if(empty(dbkeep('pic2'))) echo 'display:none;'; ?>">
                                        drag & drop
                                    </label>
                                    <div class="err-msg"><?php echo err('pic2'); ?></div>
                                </li>
                                <li class="post-image">
                                    <label class="raf input-file" for="" method="post" style="">
                                        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                        <input type="file" name="pic3" class="file">
                                        <img src="<?php echo dbkeep('pic3'); ?>" alt="" class="prev-img" style="<?php if(empty(dbkeep('pic3'))) echo 'display:none;'; ?>">
                                        drag & drop
                                    </label>
                                    <div class="err-msg"><?php echo err('pic3'); ?></div>
                                </li>
                            </ul>
                        </div>

                    <div class="post-description">
                        <label>
                            <p>商品名<span class="imp">必須</span></p>
                            <input type="text" name="name" class="in-common text-common" placeholder="40字まで" value="<?php echo dbkeep('name'); ?>">
                        </label>
                        <div class="err-msg"><?php echo err('name'); ?></div>
                        <label>
                            <p>商品の説明<span class="imp">必須</span></p>
                            <textarea name="des" class="in-common teatarea" cols="30" rows="10" placeholder="商品の説明"><?php echo dbkeep('des'); ?></textarea>
                        </label>
                        <div class="err-msg"><?php echo err('des'); ?></div>
                    </div>
                    <div class="post-detail">
                        <p>カテゴリー<span class="imp">必須</span></p>
                        <select name="category_id" id="" class="select">
                            <option value="0">選択してください</option>
                            <?php foreach($dbcate as $key => $val): ?>
                            <option value="<?php echo $val['id']; ?>" <?php if(dbkeep('category_id') === $val['id']) echo 'selected'; ?>><?php echo $val['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="err-msg"><?php echo err('category_id'); ?></div>
                        <p>商品の状態<span class="imp">必須</span></p>
                        <select name="status_id" id="" class="select">
                            <option value="0">選択してください</option>
                            <?php foreach($dbstatus as $key => $val): ?>
                            <option value="<?php echo $val['id']; ?>" <?php if(dbkeep('status_id') === $val['id']) echo 'selected';?>><?php echo $val['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="err-msg"><?php echo err('status_id'); ?></div>
                    </div>
                    <div class="post-price flex">
                        <p>販売価格<span class="imp">必須</span></p>
                        <div style="width:50%;">
                            <div style="width:100%;" class="flex">
                                <span class="doll" style="margin-right:8px;">¥</span><input type="text" name="price" class="in-common text-common" value="<?php echo dbkeep('price'); ?>">
                            </div>
                            <div class="err-msg"><?php echo err('price'); ?></div>
                        </div>
                        
                    </div>
                    <div class="post-btn">
                        <?php if($edit_flg){ ?>
                        <input type="submit" name="submit" class="common-btn" value="更新する">
                        <input type="submit" name="delete" class="delete-btn" value="削除する">
                        <?php }else{ ?>
                        <input type="submit" name="submit" class="common-btn" value="出品する">
                        <?php } ?>
                        <div class="back" style="text-align:center;"><a href="">もどる</a></div>
                    </div>
                    
                    </form>
                </div>
            </div>
            </div>
            </div>
<?php require('footer.php'); ?>