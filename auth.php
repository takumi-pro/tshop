<?php
if(!empty($_SESSION['login_time'])){
    debug('ログイン済');
    if(($_SESSION['login_time']+$_SESSION['login_limit']) > time()){
        debug('有効期限内');
        $_SESSION['login_time'] = time();
        if(basename($_SERVER['PHP_SELF']) === "login.php"){
            header("Location:mypage.php");
        }
        
    }else{
        debug('有効期限切れ');
        session_destroy();
        header("Location:login.php");
    }
}else{
    debug('未ログイン');
    if(basename($_SERVER['PHP_SELF']) !== "login.php"){
        header("Location:login.php");
    }
}
?>