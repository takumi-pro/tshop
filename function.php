<?php
ini_set('log_errors','on');
ini_set('error_log','php.log');

$debug = false;
function debug($str){
    global $debug;
    if($debug){
        error_log('デバック:'.$str);
    }
}

session_save_path("/var/tmp/");
ini_set('session.gc_maxlifetime',60*60*24*30);
ini_set('session.cookie_lifetime',60*60*24*30);
session_start();
session_regenerate_id();

define('MSG01','入力必須です');
define('MSG02','email形式で入力してください');
define('MSG03','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG04','emailが既に登録してあります');
define('MSG05','100文字以内で入力してください');
define('MSG06','6文字以上で入力してください');
define('MSG07','半角英数字のみご利用いただけます');
define('MSG08','パスワードがあっていません');
define('MSG09','emailまたはパスワードが違います');
define('MSG10','半角数字のみご利用いただけます');
define('MSG11','郵便番号形式で入力してください');
define('MSG12','電話番号形式で入力してください');
define('MSG13','古いパスワードが違います');
define('MSG14','古いパスワードと同じです');
define('MSG15','8文字で入力してください');
define('MSG16','認証キーが違います');
define('MSG17','有効期限切れです');
define('MSG18','字以内で入力してください');
define('MSG19','選択してください');
define('SUC01','商品が登録されました');
define('SUC02','商品を購入しました！');

$err_msg = array();

//db接続
function db(){
    $dsn = 'mysql:dbname=tshop;host=localhost;charset=utf8';
    $user = 'root';
    $pass = 'root';
    $option = array(
        // SQL実行失敗時にはエラーコードのみ設定
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
        // デフォルトフェッチモードを連想配列形式に設定
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
        // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
      );
    $dbh = new PDO($dsn, $user,$pass,$option);
    return $dbh;
}
//クエリ
function query($dbh,$sql,$data){
    $stmt = $dbh->prepare($sql);
    if(!$stmt->execute($data)){
        debug('クエリ失敗');
        $err_msg['common'] = MSG03;
        return false;
    }else{
        debug('クエリ成功');
        return $stmt;
    }
    return $stmt;
}
//未入力チェック
function nothing($str,$key){
    global $err_msg;
    if($str === ''){
        $err_msg[$key] = MSG01;
    }
}
//email形式
function email($str){
    global $err_msg;
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$str)){
        $err_msg['email'] = MSG02;
    }
}
//email重複チェック
function emaildup($str){
    global $err_msg;
    try{
        $dbh = db();
        $sql = 'SELECT count(*) FROM users WHERE email=? AND delete_flg=0';
        $data = array($str);
        $stmt = query($dbh,$sql,$data);
        $resu = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty(array_shift($resu))){
            $err_msg['email'] = MSG04;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
        $err_msg['common'] = MSG08;
    }
}
//最大文字数
function maxLen($str,$key,$len=100){
    global $err_msg;
    if(mb_strlen($str) > $len){
        $err_msg[$key] = MSG05;
    }
}
//最小文字数
function minLen($str,$key,$len=6){
    if(mb_strlen($str) < $len){
        global $err_msg;
        $err_msg[$key] = MSG06;
    }
}
//半角英数字チェック
function halfnum($str,$key){
    global $err_msg;
    if(!preg_match("/^[0-9a-zA-Z]*$/",$str)){
        $err_msg[$key] = MSG07;
    }
}
//半角数字チェック
function half($str,$key){
    global $err_msg;
    if(!preg_match("/^[0-9]+$/",$str)){
        $err_msg[$key] = MSG10;
    }
}
//郵便番号チェック
function zip($str){
    global $err_msg;
    if(!preg_match("/^(([0-9]{3}-[0-9]{4})|([0-9]{7}))$/",$str)){
        $err_msg['zip'] = MSG11;
    }
}
//電話番号チェック
function tel($str){
    global $err_msg;
    if(!preg_match("/^(0{1}\d{9,10})$/",$str)){
        $err_msg['tel'] = MSG12;
    }
}
//固定長チェック
function length($str,$key,$len=8){
    global $err_msg;
    if(mb_strlen($str) !== $len){
        $err_msg[$key] = MSG15;
    }
}
//パスワードマッチ
function match($str1,$str2,$key){
    global $err_msg;
    if($str1 !== $str2){
        $err_msg[$key] = MSG08;
    }
}
//エラー表示
function err($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        return $err_msg[$key];
    }
}
//表示キープ
function keep($key){
    if(!empty($_POST[$key])){
        return $_POST[$key];
    }
}
//DB情報取得
function getdb($str){
    try{
        $dbh = db();
        $sql = 'SELECT * FROM users WHERE id=? AND delete_flg=0';
        $data = array($str);
        $stmt = query($dbh,$sql,$data);
        if($stmt){
            $resu = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resu;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
//DB表示キープ
function dbkeep($str,$flg=false){
    global $err_msg;
    global $dbdata;
    if($flg){
        $method = $_GET;
    }else{
        $method = $_POST;
    }
    if(!empty($dbdata[$str])){
        if(!empty($err_msg[$str])){
            if(isset($method[$str])){
                return sanitize($method[$str]);
            }else{
                return sanitize($method[$str]);
            }
        }else{
            if(isset($method[$str]) && $method[$str] !== $dbdata[$str]){
                return sanitize($method[$str]);
            }else{
                return sanitize($dbdata[$str]);
            }
        }
    }else{
        if(isset($method[$str])){
            return sanitize($method[$str]);
        }
    }
}
//パスワード
function pass($str,$key){
    maxLen($str,$key);
    minLen($str,$key);
    halfnum($str,$key);
}
//認証キー発行
function Randkey($len=8){
    $char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for($i=0;$i<$len;$i++){
        $str .= $char[mt_rand(0,61)];
    }
    return $str;
}
//メール送信
function sendMail($to,$subject,$comment,$from){
    if(!empty($to) && !empty($subject) && !empty($comment)){
        //文字化けしないように
        mb_language('Japanese');
        mb_internal_encoding("UTF-8");

        $resu = mb_send_mail($to,$subject,$comment,"From:".$from);
        if($resu){
            debug('メール送信成功');
        }else{
            debug('メール送信失敗');
        }
    }
}
function getstatus(){
    try{
        $dbh = db();
        $sql = 'SELECT * FROM `status`';
        $data = array();
        $stmt = query($dbh,$sql,$data);
        if($stmt){
            $resu = $stmt->fetchAll();
            return $resu;
        }
        
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
function getcate(){
    try{
        $dbh = db();
        $sql = 'SELECT * FROM category';
        $data = array();
        $stmt = query($dbh,$sql,$data);
        if($stmt){
            $resu = $stmt->fetchAll();
            return $resu;
        }
        
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
//登録商品取得
function getpost($str,$key){
    try{
        $dbh = db();
        $sql = 'SELECT * FROM item WHERE user_id=? AND delete_flg=0 AND id=?';
        $data = array($str,$key);
        $stmt = query($dbh,$sql,$data);
        if($stmt){
            $resu = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resu;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
function itemnameLen($str,$key,$len=40){
    global $err_msg;
    if(mb_strlen($str) >= $len){
        $err_msg[$key] = $len.MSG18;
    }
}
function selectcheck($str,$key){
    global $err_msg;
    if(!preg_match("/^[1-9]+$/",$str)){
        $err_msg[$key] = MSG19;
    }
}
//price
function pricesele($price){
    $resu = array();
    if($price == 1){
        $resu[0] = '0';
        $resu[1] = '300';
        return $resu;
    }
    if($price == 2){
        $resu[0] = '300';
        $resu[1] = '1000';
        return $resu;
    }
    if($price == 3){
        $resu[0] = '1000';
        $resu[1] = '5000';
        return $resu;
    }
    if($price == 4){
        $resu[0] = '5000';
        $resu[1] = '10000';
        return $resu;
    }
    if($price == 5){
        $resu[0] = '10000';
        $resu[1] = '30000';
        return $resu;
    }
}
//商品状態
function statusj($c1,$c2,$c3,$c4,$c5,$c6){
    $sql = '';
    if(!empty($c1)){
        $sql .= ' status_id='.$c1.' OR';
    }
    if(!empty($c2)){
        $sql .= ' status_id='.$c2.' OR';
    }
    if(!empty($c3)){
        $sql .= ' status_id='.$c3.' OR';
    }
    if(!empty($c4)){
        $sql .= ' status_id='.$c4.' OR';
    }
    if(!empty($c5)){
        $sql .= ' status_id='.$c5.' OR';
    }
    if(!empty($c6)){
        $sql .= ' status_id='.$c6.' OR';
    }
    return $sql;
}
//検索用関数
function getpostlist($nowmin,$cate,$price,$keyword,$c1,$c2,$c3,$c4,$c5,$c6,$search,$sort,$span=20){
    $pricerenge = pricesele($price);
    try{
        $dbh = db();
        $sql = 'SELECT * FROM item';
        if(!empty($search)){
            $sql .= ' WHERE name LIKE "%'.$search.'%"';
        }
            if(!empty($keyword)){
                $sql .= ' WHERE name LIKE "%'.$keyword.'%"';
                if(!empty($cate)){
                    $sql .= ' AND category_id='.$cate;
                }
                if(!empty($price)){
                    $sql .= ' AND price BETWEEN '.$pricerenge[0].' AND '.$pricerenge[1];
                }
                if(!empty($c1) || !empty($c2) || !empty($c3) || !empty($c4) || !empty($c5) || !empty($c6)){
                    $sqlprapre = statusj($c1,$c2,$c3,$c4,$c5,$c6);
                    $sqlexce = mb_substr($sqlprapre,0,-3,"UTF-8");
                    $sql .= ' AND'.$sqlexce;
                }
                if(!empty($sort)){
                    switch($sort){
                       
                    case 1:
                        $sql .= ' ORDER BY create_date DESC';
                        break;
                    case 2:
                        $sql .= ' ORDER BY create_date ASC';
                        break;
                    }
                }  
            }else{
                if(!empty($cate) && empty($price)){
                    $sql .= ' WHERE category_id='.$cate;
                }elseif(!empty($price) && empty($cate)){
                    $sql .= ' WHERE price BETWEEN '.$pricerenge[0].' AND '.$pricerenge[1];
                }elseif(!empty($price) && !empty($cate)){
                    $sql .= ' WHERE category_id='.$cate.' price BETWEEN '.$pricerenge[0].' AND '.$pricerenge[1];
                }
                if(!empty($c1) || !empty($c2) || !empty($c3) || !empty($c4) || !empty($c5) || !empty($c6)){
                    $sqlprapre = statusj($c1,$c2,$c3,$c4,$c5,$c6);
                    $sqlexce = mb_substr($sqlprapre,0,-3,"UTF-8");
                    $sql .= ' AND'.$sqlexce;
                }
                if(!empty($sort)){
                    switch($sort){
                       
                    case 1:
                        $sql .= ' ORDER BY create_date DESC';
                        break;
                    case 2:
                        $sql .= ' ORDER BY create_date ASC';
                        break;
                    }
                } 
            }
        
        $data = array();
        debug('SQL：'.$sql);
        $stmt = query($dbh,$sql,$data);
        $rst['total'] = $stmt->rowCount(); //総レコード数
        $rst['total_page'] = ceil($rst['total']/$span); //総ページ数
        if(!$stmt){
            return false;
        }

        //ページング用
        $sql = 'SELECT * FROM item';
        if(!empty($search)){
            $sql .= ' WHERE name LIKE "%'.$search.'%"';
        }
        if(!empty($keyword)){
            $sql .= ' WHERE name LIKE "%'.$keyword.'%"';
            if(!empty($cate)){
                $sql .= ' AND category_id='.$cate;
            }
            if(!empty($price)){
                $sql .= ' AND price BETWEEN '.$pricerenge[0].' AND '.$pricerenge[1];
            }
            if(!empty($c1) || !empty($c2) || !empty($c3) || !empty($c4) || !empty($c5) || !empty($c6)){
                $sqlprapre = statusj($c1,$c2,$c3,$c4,$c5,$c6);
                $sqlexce = mb_substr($sqlprapre,0,-3,"UTF-8");
                $sql .= ' AND'.$sqlexce;
            }
            if(!empty($sort)){
                switch($sort){
                   
                  case 1:
                    $sql .= ' ORDER BY create_date DESC';
                    break;
                  case 2:
                    $sql .= ' ORDER BY create_date ASC';
                    break;
                }
            }
        }else{
            if(!empty($cate) && empty($price)){
                $sql .= ' WHERE category_id='.$cate;
            }elseif(!empty($price) && empty($cate)){
                $sql .= ' WHERE price BETWEEN '.$pricerenge[0].' AND '.$pricerenge[1];
            }elseif(!empty($price) && !empty($cate)){
                $sql .= ' WHERE category_id='.$cate.' price BETWEEN '.$pricerenge[0].' AND '.$pricerenge[1];
            }
            if(!empty($c1) || !empty($c2) || !empty($c3) || !empty($c4) || !empty($c5) || !empty($c6)){
                $sqlprapre = statusj($c1,$c2,$c3,$c4,$c5,$c6);
                $sqlexce = mb_substr($sqlprapre,0,-3,"UTF-8");
                $sql .= ' AND'.$sqlexce;
            }
            if(!empty($sort)){
                switch($sort){
                case 1:
                    $sql .= ' ORDER BY create_date DESC';
                    break;
                case 2:
                    $sql .= ' ORDER BY create_date ASC';
                    break;
                }
            } 
        }
    
            $sql .= ' LIMIT '.$span.' OFFSET '.$nowmin; 
            $data = array();
                debug('SQL：'.$sql);
                // クエリ実行
                $stmt = query($dbh,$sql,$data);
            
                if($stmt){
                    // クエリ結果のデータを全レコードを格納
                    $rst['data'] = $stmt->fetchAll(); 
                    return $rst;
                }else{
                    return false;
                }
        
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
//画像アップロード
function uploadimg($file,$key){
    debug('FILE情報:'.print_r($_FILES,true));
    if(isset($file['error']) && is_int($file['error'])){
        try{
            switch ($file['error']) {
                case UPLOAD_ERR_OK:
            break;
            case UPLOAD_ERR_NO_FILE:
                throw new Runtimeexception('ファイルが選択されていません');
            case UPLOAD_INI_SIZE:
            case UPLOAD_FORM_SIZE:
                throw new Runtimeexception('ファイルサイズが大きすぎます');
                default:
                throw new Runtimeexception('その他のエラーが発生しました');
            }
            $type = @exif_imagetype($file['tmp_name']);
            if(!in_array($type,[IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG],true)){
                throw new Runtimeexception('画像形式が未対応です');
            }
            $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
            if(!move_uploaded_file($file['tmp_name'],$path)){
                throw new Runtimeexception('ファイル保存時にエラーが発生しました');
            }
            chmod($path,0644);
            debug('ファイルは正常にアップロードされました');
            debug('ファイルパス:'.$path);
            return $path;
        }catch (Runtimeexception $e){
            debug($e->getMessage());
            global $err_msg;
            $err_msg[$key] = $e->getMessage();
        }
    }
}
//サニタイズ
function sanitize($str){
    return htmlspecialchars($str,ENT_QUOTES);
}
//セッションを一回取得
function flash($key){
    if(!empty($_SESSION[$key])){
        $data = $_SESSION[$key];
        $_SESSION[$key] = '';
        return $data;
    }
}
//ページネーション
function pagenation($nowpage,$totalPageNum,$link='',$pageColNum=5){
    if ($nowpage == $totalPageNum && $totalPageNum >= $pageColNum) {
        $minPageNum = $nowpage - 4;
        $maxPageNum = $nowpage;
    } elseif ($nowpage == ($totalPageNum - 1) && $totalPageNum >= $pageColNum) {
        $minPageNum = $nowpage - 3;
        $maxPageNum = $nowpage + 1;
    } elseif ($nowpage == 2 && $totalPageNum >= $pageColNum) {
        $minPageNum = $nowpage - 1;
        $maxPageNum = $nowpage + 3;
    } elseif ($nowpage == 1 && $totalPageNum >= $pageColNum) {
        $minPageNum = $nowpage;
        $maxPageNum = $nowpage + 4;
    } elseif ($totalPageNum < $pageColNum) {
        $minPageNum = 1;
        $maxPageNum = $totalPageNum;
    } else {
        $minPageNum = $nowpage - 2;
        $maxPageNum = $nowpage + 2;
    }
    echo '<div class="page-nation">';
    echo '<ul class="flex">';
    if($nowpage != 1){
        echo '<li><a href="?p=1">&lt;</a></li>';
    }
    for($i=$minPageNum;$i<=$maxPageNum;$i++){
        echo '<li><a class="';
        if($nowpage == $i) echo 'active';
        echo '" href="postlist.php?p='.$i.'">'.$i.'</a></li>';
    }
    if($nowpage != $maxPageNum){
        echo '<li><a href="?p='.$maxPageNum.'">&gt;</a></li>';
    }
    echo '</ul>';
    echo '</div>';
}
//商品詳細用
function getpostone($p_id){
    debug('商品ID:'.$p_id);
    try{
        $dbh = db();
        $sql = 'SELECT i.id,i.name,i.price,i.des,i.pic1,i.pic2,i.pic3,i.create_date,i.user_id,i.update_date,i.sold_item,c.name AS category,s.name AS status FROM item AS i INNER JOIN status AS s ON i.status_id=s.id LEFT JOIN category AS c ON i.category_id=c.id WHERE i.id=? AND i.delete_flg=0 AND c.delete_flg=0 AND s.delete_flg=0';
        $data = array($p_id);
        $stmt = query($dbh,$sql,$data);
        if($stmt){
            $resu = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resu;
        }else{
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
//画像表示
function showimg($path){
    if(empty($path)){
        return 'img/sample-img.png';
    }else{
        return $path;
    }
}
//コメントを取得
function getcmt($p_id){
    try{
        $dbh = db();
        $sql = 'SELECT m.send_date,m.msg,m.to_user,m.from_user,u.name AS username,u.pic FROM message AS m LEFT JOIN users AS u ON m.from_user=u.id WHERE m.item_id=? AND m.bord_id=0 AND m.delete_flg=0 AND u.delete_flg=0';
        $data = array($p_id);
        $stmt = query($dbh,$sql,$data);
        if($stmt){
            $resu = $stmt->fetchAll();
            return $resu;
        }else{
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
//GETパラメータ付与
function append($reject_key=array()){
    if(!empty($_GET)){
        $str = '?';
        foreach($_GET as $key => $val){
            if(!in_array($key,$reject_key,true)){
                $str .= $key.'='.$val.'&';
            }
        }
        $str = mb_substr($str,0,-1,'UTF-8');
        return $str;
    }
}
function getMandB($id){
    debug('msg情報を取得');
    debug('掲示板ID:'.$id);
    try{
        $dbh = db();
        $sql = 'SELECT m.id AS m_id,send_date,from_user,to_user,msg,sell_user,buy_user,m.item_id AS m_item,b.item_id AS b_item,b.create_date FROM message AS m RIGHT JOIN bord AS b ON b.id=m.bord_id WHERE b.id=? ORDER BY send_date ASC';
        $data = array($id);
        $stmt = query($dbh,$sql,$data);
        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
function sold($p_id){
    try{
        $dbh = db();
        $sql = 'UPDATE item SET sold_item=1 WHERE id=? AND delete_flg=0';
        $data = array($p_id);
        $stmt = query($dbh,$sql,$data);
        if($stmt){
            debug('売り切れ');
            return true;
        }else{
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
//お気に入り
function isFavo($u_id,$p_id){
    debug('ユーザーID:'.$u_id);
    debug('商品ID:'.$p_id);
    try{
        $dbh = db();
        $sql = 'SELECT * FROM favo WHERE item_id=? AND user_id=? AND delete_flg=0';
        $data = array($p_id,$u_id);
        $stmt = query($dbh,$sql,$data);
        if($stmt->rowCount()){
            debug('お気に入り');
            return true;
        }else{
            debug('気に入ってない');
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
//ログイン確認
function isLogin(){
    if(!empty($_SESSION['login_time'])){
        debug('ログイン済');
        if(($_SESSION['login_time']+$_SESSION['login_limit']) > time()){
            debug('有効期限内');
            return true;
        }else{
            return false;
        }
    }else{
        debug('未ログイン');
        return false;
    }
}
//出品商品取得
function getpostitem($u_id){
    try{
        $dbh = db();
        $sql = 'SELECT * FROM item WHERE user_id=? AND delete_flg=0';
        $data = array($u_id);
        $stmt = query($dbh,$sql,$data);
        if($stmt){
            $resu = $stmt->fetchAll();
            return $resu;
        }else{
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
//自分のメッセージ情報取得
function getMyMandB($u_id){
    try{
        $dbh = db();
        $sql = 'SELECT * FROM bord WHERE sell_user=? OR buy_user=? AND delete_flg=0';
        $data = array($u_id,$u_id);
        $stmt = query($dbh,$sql,$data);
        $resu = $stmt->fetchAll();
        if(!empty($resu)){
            foreach($resu as $key => $val){
                $sql = 'SELECT * FROM message WHERE bord_id=? AND delete_flg=0 ORDER BY send_date DESC';
                $data = array($val['id']);
                $stmt = query($dbh,$sql,$data);
                $resu[$key]['msg'] = $stmt->fetchAll();
            }
        }
        if($stmt){
            return $resu;
        }else{
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
//いいね情報
function getdbFavo($u_id){
    try{
        $dbh = db();
        $sql = 'SELECT * FROM favo WHERE user_id=? AND delete_flg=0';
        $data = array($u_id);
        $stmt = query($dbh,$sql,$data);
        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}
?>