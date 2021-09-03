<?php
include_once('./config/init.php');

// 判断是否登录
include_once('check.php');

?>


<!DOCTYPE html>
<html lang="en">
  <head>
      <?php include_once("meta.php");?>
  </head>
  <body> 
    <!-- 头部 -->
    <?php include_once("header.php");?>
    
    <!-- 菜单 -->
    <?php include_once("menu.php");?>

    <div class="content">
        <div class="header">
            <h1 class="page-title">后台首页</h1>
        </div>
        <ul class="breadcrumb">
            <li><a href="./index.php">Home</a> <span class="divider">/</span></li>
            <li class="active">Index</li>
        </ul>

        <div class="container-fluid">
            <div class="row-fluid">
                <footer>
                    <hr>
                    <p>&copy; 2017 <a href="#" target="_blank">copyright</a></p>
                </footer>
            </div>
        </div>
    </div>
  </body>
</html>
<?php include_once("script.php");?>


