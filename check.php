<?php
include_once('./config/init.php');

$LoginId=isset($_SESSION['id']) ? $_SESSION['id'] : 0;
$LoginUsername=isset($_SESSION['username']) ? $_SESSION['username'] : '';

// 查询用户是否存在
$LoginSQL="SELECT * FROM {$pre_}admin WHERE id=$LoginId AND username = '$LoginUsername'";

$LoginUser=find($LoginSQL);

//说明没有找到用户
if(!$LoginUser){
    //说明session被人伪造了，伪造就说明非法，非法就要删除
    // 删除所有的session数据
    session_destroy();

    ShowMsg('非法登录','login.php');
    exit;
}


?>