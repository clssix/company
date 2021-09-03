<div class="navbar">
    <div class="navbar-inner">
        <ul class="nav pull-right">
            <li>
                <a href="./index.php" role="button">
                    <i class="icon-user"></i>
                    <?php echo @$_SESSION['username']; ?>
                </a>
            </li>
            <li>
                <a href="./login.php?action=logout" class="hidden-phone visible-tablet visible-desktop" role="button">Logout</a>
            </li>
        </ul>
        <a class="brand" href="./index.php">
            <span class="second">公司通讯录</span>
        </a>
    </div>
</div>