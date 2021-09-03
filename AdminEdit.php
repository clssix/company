<?php
include_once('./config/init.php');
include_once('./check.php');

// 接收管理员ID
$aid=isset($_GET['aid']) ? $_GET['aid'] : 0;
$sql="SELECT * FROM {$pre_}admin WHERE id=$aid";
$admin=find($sql);

// 管理员不存在的时候
if(!$admin){
    ShowMsg('管理员不存在','AdminList.php');
    exit;
}

if($_POST){
    // 生成密码盐
    $salt=randstr(10);

    // 组装数据
    $data=[
        "password"=>trim($_POST['password']),
        "salt"=>trim($salt),
    ];
    $data['password']=empty($data['password']) ? trim(md5($admin['password'].$data['salt'])) : trim(md5($data['password'].$data['salt']));

    // 先判断是否有文件上传 (error=0 == 没错误  大小>0)
    if($_FILES['avatar']['error'] == 0 && $_FILES['avatar']['size'] > 0){
        $result=upload('avatar','assets/uploads/');
        // var_dump($result);
        // exit;
        if($result['success']){
            $data['avatar']=$result['msg'];
        }else{
            ShowMsg($result['msg']);
            exit;
        }
    }

    // 编辑数据到数据库
    $affect=update("admin",$data,"id = $aid");

    if($affect){
        // 判断是否有上传新头像，如果有就删除旧的头像
        if(isset($data['avatar'])){
            // 删除旧的图片，先判断旧的图片是否真的存在，如果存在再去删除
            @is_file($admin['avatar']) && @unlink($admin['avatar']);
        }

        ShowMsg('编辑管理员信息成功','AdminList.php');
        exit;
    }else{
        ShowMsg('编辑管理员信息失败');
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
        <!-- 头部 -->
        <?php include_once("header.php");?>
        
        <!-- 菜单 -->
        <?php include_once("menu.php");?>

        <div class="content">
            <div class="header">
                <h1 class="page-title">编辑管理员信息</h1>
            </div>
            <ul class="breadcrumb">
                <li><a href="index.php">Home</a> <span class="divider">/</span></li>
                <li class="active">Index</li>
            </ul>

            <div class="container-fluid">
                <div class="row-fluid">
                        
                    <div class="btn-toolbar">
                        <button class="btn btn-primary" onClick="location='AdminList.php'">
                            <i class="icon-list"></i> 返回管理员列表
                        </button>
                        <div class="btn-group">
                        </div>
                    </div>

                    <div class="well">
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane active in" id="home">
                                <form method="post" enctype="multipart/form-data">
                                    <label>用户名</label>
                                    <input type="text" name="username" placeholder="请输入用户名" class="input-xxlarge" value="<?php echo $admin['username']; ?>" disabled />
                                    <span id="notice" style="color: red;display:none;">用户名已注册</span>

                                    <label>密码</label>
                                    <input type="password" name="password" placeholder="为空不修改密码" class="input-xxlarge" />

                                    <label>个人头像</label>
                                    <input type="file" name="avatar" class="input-xxlarge" />

                                    <?php if(!empty($admin['avatar'])){?>
                                        <label>
                                            <a href="<?php echo $admin['avatar'];?>" target="_blank">
                                                <img src="<?php echo $admin['avatar'];?>" style="width: 100px;height:100px;" />
                                            </a>
                                        </label>
                                    <?php }?>

                                    <label></label>
                                    <br />
                                    <input id="add" class="btn btn-primary" type="submit" value="提交" />
                                </form>
                            </div>
                        </div>
                    </div>

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


