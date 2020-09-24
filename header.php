<header id="main-header">
        <div class="header-inner">
            <div class="head-up flex">
                <div class="logo">
                    <h1 class="main-logo" style="height:40px;position:relative;"><a class="en" href="index.php" style="font-size:28px;color:#111;position:absolute;top:0;">Tshop</a></h1>
                </div>
                <form class="search" action="postlist.php" method="get">
                    <input class="search-bar" type="search" name="search" placeholder="何をお探しですか？" value="">
                    <input class="s-btn" type="submit" name="submit" value="">
                </form>
            </div>
            <div class="head-down">
                <nav>
                    <ul class="flex navi">
                        <?php
                        if(!empty($_SESSION['login_time'])){
                        ?>
                        <li><a class="login-nav" href="mypage.php"><i class="fas fa-user-circle fa-lg"></i><span>マイページ</span></a></li>
                        <li><a class="login-nav" href="profedit.php"><i class="fas fa-edit"></i><span>プロフィール編集</span></a></li>
                        <li><a class="login-nav" href="passedit.php"><i class="fas fa-unlock-alt"></i><span>パスワード変更</span></a></li>
                        <?php }else{ ?>
                        <li><a class="new" href="signup.php">新規登録</a></li>
                        <li><a class="login" href="login.php">ログイン</a></li>
                        <?php } ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>