<?php
require('function.php');
debug('-------------ajax------------');

if(isset($_POST['itemId']) && isset($_SESSION['user_id']) && isLogin()){
    $p_id = $_POST['itemId'];
    debug('商品ID:'.$p_id);
    debug('POST中身:'.print_r($_POST,true));
    try{
        $dbh = db();
        $sql = 'SELECT * FROM favo WHERE user_id=? AND item_id=?';
        $data = array($_SESSION['user_id'],$p_id);
        $stmt = query($dbh,$sql,$data);
        $resu = $stmt->rowCount();
        if(!empty($resu)){
            $sql = 'DELETE FROM favo WHERE user_id=? AND item_id=?';
            $data = array($_SESSION['user_id'],$p_id);
            $stmt = query($dbh,$sql,$data);
        }else{
            $sql = 'INSERT INTO favo SET user_id=?,item_id=?,create_date=?';
            $data = array($_SESSION['user_id'],$p_id,date('Y-m-d H:i:s'));
            $stmt = query($dbh,$sql,$data);
        }
    }catch (Exception $e){
        error_log('エラー発生:'.$e->getMessage());
    }
}


?>