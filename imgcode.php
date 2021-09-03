<?php
// 1,先创建一张指定宽度和高度的一张验证码图片 imagecreatetruecolor()
// 2，给验证码图片添加背景颜色 和文字颜色 imagecolorallocate()
// 3, 在指定图片上面，画一个矩形 imagefilledrectangle()
// 4, 获取随机数  (定义一个函数) get_rand_str()
//   1，定义一个字符串
//   2，将上面的字符串 打乱  str_shuffle()
//   3，并且从这个打乱的字符串当中去截取一部分内容 substr()
//   4，把截取出来的字符串转化成小写字母 strtolower()
//   5, 将这个字符串 return
// 5,将随机数写入到这个图片里面去 imagestring()
// 6,防止别人去恶意刷我们的验证码 可以在这个图片上面加上一些点 imagesetpixel()
// 7,开启session会话  将我们的验证码 存储到session当中与我们表单当中输入的验证码进行匹配
// 8,输入图片的 头信息 和 图片资源 删除 header("Content-Type:image/png"); imagepng($img); imagedestroy($img);
// 9,在登录界面 获取表单输入的验证码  和 我们session当中的验证码进行对比 如果正确就跳转登录界面 否则重新输入

// 开启会话
session_start();

// 创建随机数的方法
function get_rand_str($length=4){
    $chars='1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    // 随机打乱
    $str=str_shuffle($chars);

    // 获取随机结果
    $str=substr($str,0,$length);

    // 转化为小写
    $str=strtolower($str);
    return $str;
}

// 验证码图片的宽和高
$width=90;
$heigth=40;

// 新建一张彩色的图片
$img=imagecreatetruecolor($width,$heigth);

// 背景颜色
$backcolor=imagecolorallocate($img,0,0,0);

// 文字颜色
$textcolor=imagecolorallocate($img,255,255,255);

// 画一个矩形
imagefilledrectangle($img,0,0,$width,$heigth,$backcolor);

// 获取随机数的方法
$str=get_rand_str();

// 存放进去
$_SESSION['imgcode']=$str;

// 在图片当中画一些点，防止有人破解验证码
for($i=0;$i<500;$i++){
    // 生成x,y
    $x=mt_rand(0,$width);
    $y=mt_rand(0,$heigth);

    // 生成随机颜色
    $color=imagecolorallocate($img,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));

    // 写像素点上去
    imagesetpixel($img,$x,$y,$color);
}


// 将随机字符串写到图片上
// imagestring($img,3,10,3,$str,$textcolor);
imagettftext($img,25,0,6,30,$textcolor,'D:/wamp/www/company_cls/assets/fonts/OpenSans-Light.ttf',$str);

// 声明为图片的头信息
header("Content-Type:image/png");

// 输出图片
imagepng($img);

// 输出后销毁图片
imagedestroy($img);
?>