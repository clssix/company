<?php
include_once('./config/init.php');
include_once('./check.php');

// // 加这个关键词的目的时为了分开请求
$action=isset($_POST['action']) ? $_POST['action'] : '';

if($action == "check"){
    $result=[
        'success'=>false,
        'msg'=>''
    ];

    $username=empty($_POST['username']) ? '' : $_POST['username'];

    // 根据用户名去判断这个人是否存在
    $sql = "SELECT * FROM {$pre_}admin WHERE username = '$username'";
    $admin=find($sql);

    // 如果管理员有重复说明已经添加过了
    if($admin){
        $result['success']=false;
        $result['msg']='用户已存在';
    }else{
        $result['success']=true;
        $result['msg']='用户不存在，可以注册';
    }

    // 延迟10s后再运行下面的程序
    echo json_encode($result);
    exit;
}

// 添加管理员 把管理员添加到数据库
// 表单提交 -> 先判断是否有提交数据 -> 先判断是否有重复输入 -> 获取 ->  添加 -> 判断是否添加成功 -> 返回列表页
if($_POST){
    $username=empty($_POST['username']) ? '' : $_POST['username'];

    // 判断管理员是否重复
    $sql = "SELECT * FROM {$pre_}admin WHERE username = '$username'";
    $admin=find($sql);

    // 如果管理员有重复说明已经添加过了
    if($admin){
        ShowMsg('该用户已存在，请重新填写');
        exit;
    }

    // 对密码进行加密
    $password=isset($_POST['password']) ? $_POST['password'] : '';
    
    if(empty($password)){
        ShowMsg("密码不能为空");
        exit;
    }

    // 生成密码盐
    $salt=randstr(10);

    // 组装数据
    $data=[
        "username"=>trim($_POST['username']),
        "password"=>trim(md5($password.$salt)),
        "salt"=>trim($salt),
    ];

    // 先判断是否有文件上传 (error=0 == 没错误  大小>0)
    if($_FILES['avatar']['error'] == 0 && $_FILES['avatar']['size'] > 0){
        $result=upload('avatar','assets/uploads/');
        // var_dump($result);
        // exit;
        if($result['success']){
            $data['avatar']=$result['msg'];
        }else{
            ShowMsg($data['msg']);
            exit;
        }
    }

    // 添加数据到数据库
    $insertid=add("admin",$data);

    if($insertid){
        ShowMsg('添加管理员成功','AdminList.php');
        exit;
    }else{
        ShowMsg('添加管理员失败');
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
                <h1 class="page-title">添加管理员</h1>
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
                                    <input type="text" name="username" placeholder="请输入用户名" class="input-xxlarge" required />
                                    <span id="notice" style="color: red;display:none;">用户名已注册</span>

                                    <label>密码</label>
                                    <input type="password" name="password" placeholder="请输入密码" class="input-xxlarge" required />

                                    <label>个人头像</label>
                                    <input type="file" name="avatar" class="input-xxlarge" />

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
<script>
    // change:获取状态
    $("input[name='username']").change(function(){
        // 用户名
        var username=$(this).val()

        var params = {
            action:'check',
            username:username
        }

        params=$.param(params)

        $.ajax({
            type:'post',
            url:'AdminAdd.php',
            dataType:'json',
            data:params,
            success:function(result){
                if(result.success){
                    // 可以注册
                    $("#notice").css({'display':'inline-block','color':'green'})
                    $("#notice").html('可以注册')
                    $("#add").prop('disabled',false)
                }else{
                    // 不可以注册
                    $("#notice").css({'display':'inline-block','color':'red'})
                    $("#notice").html('该用户名已注册')
                    $("#add").prop('disabled',true)
                }
            },
            error:function(err){
                alert('error')
            }
        })
        // JS版本的Ajax
        // 创建异步对象
        // var ajax=new XMLHttpRequest()

        // ajax.onreadystatechange= function(){
        //     // console.log(ajax.readyState)
        //     //http的状态码
        //     // console.log(ajax.status)
        //     // if(ajax.readyState == 4)
        //     if(ajax.status == 200){
        //         // 获取到的是一个json的字符串
        //         // console.log(ajax.responseText)

        //         // 将获取到的json变成js的数据类型
        //         var result=JSON.parse(ajax.responseText)

        //         // 根据结果判断
        //         if(result.success){
        //             // 可以注册
        //             $("#notice").css({'display':'inline-block','color':'green'})
        //             $("#notice").html('可以注册')
        //             $("#add").prop('disabled',false)
        //         }else{
        //             // 不可以注册
        //             $("#notice").css({'display':'inline-block','color':'red'})
        //             $("#notice").html('该用户名已注册')
        //             $("#add").prop('disabled',true)
        //         }
        //     }
        // }

        // // 发起请求 false：同步 true：异步
        // ajax.open('post','AdminAdd.php?action=check',true)

        // // post的请求一定要设置http头信息
        // ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded')

        // // 发送请求，并传递数据
        // ajax.send(`username=${username}`)
    })
</script>


