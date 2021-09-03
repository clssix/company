<?php
// 引入数据库链接
include_once('./config/init.php');

$action=isset($_GET['acton']) ? $_GET['action'] : '';

if($action=='logout'){
    //说明要退出，退出就要销毁session会话
    session_destroy();
    ShowMsg('退出成功','login.php');
    exit;
}

// 获取session存放的用户信息
$LoginId=isset($_SESSION['id']) ? $_SESSION['id'] : 0;
$LoginUsername=isset($_SESSION['username']) ? $_SESSION['username'] : '';

// 查询这个人是否存在
$LoginSQL="SELECT * FROM {$pre_}admin WHERE id=$LoginId AND username='$LoginUsername'";
$LoginUser=find($LoginSQL);

// 说明没有找到用户
if($LoginUser){
    ShowMsg('您已经登录，无须重复登录','index.php');
    exit;
}

// 判断表单是否提交

if(!empty($_POST)){
    // 获取用户名
    $username=isset($_POST['username']) ? trim($_POST['username']) : '';
    $password=isset($_POST['password']) ? trim($_POST['password']) : '';
    $imgcode=isset($_POST['imgcode']) ? trim($_POST['imgcode']) : '';

    // 验证码是否正确
    if(strtolower($imgcode) != $_SESSION['imgcode'])
    {
        ShowMsg('验证码输入错误');
        exit;
    }

    // 查询语句
    $sql="SELECT * FROM {$pre_}admin WHERE username='$username'";
    $admin=find($sql);

    if(!$admin){
        ShowMsg("用户不存在");
        exit;
    }

    // 获取查询到的密码盐
    $salt=$admin['salt'];

    // 加密后的密码
    $password=md5($password.$salt);

    // 判断密码是否正确
    if($password==$admin['password']){
        $_SESSION['id']=$admin['id'];
        $_SESSION['username']=$admin['username'];

        header("Location:index.php");
        exit;
    }else{
        // 密码错误
        ShowMsg('密码输入错误，请重新输入');
        exit;
    }

}




?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include_once("meta.php");?>
  </head>
  <body> 

    <div class="row-fluid">
        <div class="dialog">
            <div class="block">
                <p class="block-heading">登录</p>
                <div class="block-body">
                    <form method="post">
                        <label>用户名</label>
                        <input type="text" name="username" placeholder="请输入用户名" required class="span12" />
                        
                        <label>密码</label>
                        <input type="password" name="password" placeholder="请输入密码" class="span12" required />

                        <label>验证码</label>
                        <input type="text" name="imgcode" placeholder="请输入验证码" required class="span12" />

                        <img src="imgcode.php" onclick="this.src='imgcode.php'" />

                        <button type="submit" class="btn btn-primary pull-right">登录</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
  </body>
</html>
<?php include_once("script.php");?>

