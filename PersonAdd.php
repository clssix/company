<?php
include_once('./config/init.php');
include_once('./check.php');

$action=isset($_POST['action']) ? $_POST['action'] : '';

if($action == "check"){
    $result=[
        'success'=>false,
        'msg'=>''
    ];

    $mobile=empty($_POST['mobile']) ? '' : $_POST['mobile'];

    $sql="SELECT * FROM {$pre_}person WHERE mobile = '$mobile'";
    $person=find($sql);

    if($person){
        $result['success']=false;
        $result['msg']='手机号已存在';
    }else{
        $result['success']=true;
        $result['msg']='手机号不存在，可以注册';
    }

    echo json_encode($result);
    exit;
}

// 添加员工 把员工添加到数据库
// 表单提交 -> 先判断是否有提交数据 -> 先判断是否有重复输入 -> 获取 -> 判断是否有上传图片 -> 添加 -> 判断是否添加成功 -> 返回列表页
if($_POST){
    $mobile=isset($_POST['mobile']) ? $_POST['mobile'] : '';

    // 判断手机号码是否重复
    $sql = "SELECT * FROM {$pre_}person WHERE mobile = '$mobile'";
    $row=find($sql);
    // 如果手机号码有重复说明已经添加过了
    if($row){
        ShowMsg('员工手机号码已存在，请重新填写');
        exit;
    }

    // 组装数据
    $data=[
        "name"=>trim($_POST['name']),
        "sex"=>trim($_POST['sex']),
        "mobile"=>trim($_POST['mobile']),
        "email"=>trim($_POST['email']),
        "address"=>trim($_POST['address']),
        "createtime"=>strtotime(trim($_POST['createtime'])),//strtotime()把年月日转化为时间戳
        "depid"=>trim($_POST['depid']),
        "jobid"=>trim($_POST['jobid']),
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
    $insertid=add("person",$data);

    if($insertid){
        ShowMsg('添加员工成功','PersonList.php');
        exit;
    }else{
        ShowMsg('添加员工失败');
        exit;
    }
}

// 查询部门
$sql="SELECT * FROM {$pre_}department";
$deplist=all($sql);

// 查询职位
$sql="SELECT * FROM {$pre_}job";
$joblist=all($sql);

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
                <h1 class="page-title">添加员工</h1>
            </div>
            <ul class="breadcrumb">
                <li><a href="index.php">Home</a> <span class="divider">/</span></li>
                <li class="active">Index</li>
            </ul>

            <div class="container-fluid">
                <div class="row-fluid">
                        
                    <div class="btn-toolbar">
                        <button class="btn btn-primary" onClick="location='PersonList.php'">
                            <i class="icon-list"></i> 返回员工列表
                        </button>
                        <div class="btn-group">
                        </div>
                    </div>

                    <div class="well">
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane active in" id="home">
                                <form method="post" enctype="multipart/form-data">
                                    <label>员工名称</label>
                                    <input type="text" name="name" placeholder="请输入员工名称" class="input-xxlarge" required />
                                    
                                    <label>性别</label>
                                    <select name="sex" class="input-xlarge" required>
                                        <option value="1">男</option>
                                        <option value="0">女</option>
                                    </select>

                                    <label>手机号码</label>
                                    <input type="text" name="mobile" placeholder="请输入手机号码" class="input-xxlarge" required />
                                    <span id="notice" style="color:red;display:none;">手机号码已存在</span>
                                    
                                    <label>邮箱</label>
                                    <input type="email" name="email" placeholder="请输入邮箱地址" class="input-xxlarge" required />
                                    
                                    <label>居住地址</label>
                                    <input type="text" name="address" placeholder="请输入居住地址" class="input-xxlarge" required />
                                    
                                    <label>入职时间</label>
                                    <input type="date" name="createtime" placeholder="请输入入职时间" class="input-xxlarge" required value="<?php echo date("Y-m-d",time());?>" />
                                    
                                    <label>所属部门</label>
                                    <select name="depid" class="input-xlarge" required>
                                        <?php foreach($deplist as $item){ ?>
                                        <option value="<?php echo $item['id'];?>"><?php echo $item['name'];?></option>
                                        <?php } ?>
                                    </select>

                                    <label>所属职位</label>
                                    <select name="jobid" class="input-xlarge" required>
                                        <?php foreach($joblist as $item){ ?>
                                        <option value="<?php echo $item['id'];?>"><?php echo $item['name'];?></option>
                                        <?php } ?>
                                    </select>

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
    $("input[name='mobile']").change(function(){
        var mobile=$(this).val()

        var params={
            action:'check',
            mobile:mobile
        }
        params=$.param(params)

        $.ajax({
            type:'post',
            url:'PersonAdd.php',
            dataType:'json',
            data:params,
            success:function(result){
                if(result.success){
                    // 手机号不相同
                    $("#notice").css({'display':'inline-block','color':'green'})
                    $("#notice").html('手机号不存在，可以注册')
                    $("#add").prop('disabled',false)
                }else{
                    // 手机号相同
                    $("#notice").css({'display':'inline-block','color':'red'})
                    $("#notice").html('手机号码已存在')
                    $("#add").prop('disabled',true)
                }
            },
            error:function(err){
                alert('error')
            }
        })
    })
</script>

