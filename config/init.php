<?php
// 开启session缓存
session_start();

// 设置时区
date_default_timezone_set("PRC");

header("Content-Type:text/html;charset=utf-8");

// 链接数据库
$conn=@mysqli_connect('localhost','root','root');

// 链接数据库失败判断
if(!$conn){
    echo '链接数据库失败';
    exit;
}

// 选择数据库
$select=mysqli_select_db($conn,'company');

if(!$select){
    echo "选择数据库失败";
    exit;
}

// 设置编码
mysqli_query($conn,'SET NAMES UTF8');

// 表前缀
$pre_='pre_';

// 引入辅助函数
include_once("helpers.php");

?>