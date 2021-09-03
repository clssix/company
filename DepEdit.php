<?php
include_once('./config/init.php');
include_once('./check.php');

$action=isset($_POST['action']) ? $_POST['action'] : '';

if($action == "check"){
    $result=[
        'success'=>false,
        'msg'=>''
    ];

    $name=empty($_POST['name']) ? '' : $_POST['name'];

    $pid = isset($_GET['pid']) ? $_GET['pid'] : 0;
    $sql="SELECT * FROM {$pre_}department WHERE name = '$name' AND id != $pid";
    $dep=find($sql);

    if($dep){
        $result['success']=false;
        $result['msg']='该部门已存在';
    }else{
        $result['success']=true;
        $result['msg']='该部门不存在，可以添加';
    }

    echo json_encode($result);
    exit;
}

// 获取当前部门的ID，根据部门ID查找这个人是否存在
$pid = isset($_GET['pid']) ? $_GET['pid'] : 0;
$sql = "SELECT * FROM {$pre_}department WHERE id = $pid";
$dep= find($sql);
if(!$dep){
    ShowMsg('该部门不存在','DepList.php');
    exit;
}

// 添加部门 把部门添加到数据库
// 表单提交 -> 先判断是否有提交数据 -> 先判断是否有重复输入 -> 获取 -> 判断是否有上传图片 -> 添加 -> 判断是否添加成功 -> 返回列表页
if($_POST){
    $mobile=isset($_POST['name']) ? $_POST['name'] : '';

    // 判断部门是否重复且不能把自己找出来
    $sql = "SELECT * FROM {$pre_}department WHERE name = '$name' AND id != $pid";
    $row=find($sql);

    // 如果部门有重复说明已经添加过了
    if($row){
        ShowMsg('部门已存在，请重新填写');
        exit;
    }

    // 组装数据
    $data=[
        "name"=>trim($_POST['name']),
    ];

    // 编辑数据到数据库
    $affect=update("department",$data,"id = $pid");

    if($affect){
        ShowMsg('编辑部门信息成功','DepList.php');
        exit;
    }else{
        ShowMsg('编辑部门信息失败');
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
                <h1 class="page-title">编辑部门信息</h1>
            </div>
            <ul class="breadcrumb">
                <li><a href="index.php">Home</a> <span class="divider">/</span></li>
                <li class="active">Index</li>
            </ul>

            <div class="container-fluid">
                <div class="row-fluid">
                        
                    <div class="btn-toolbar">
                        <button class="btn btn-primary" onClick="location='DepList.php'">
                            <i class="icon-list"></i> 返回部门列表
                        </button>
                        <div class="btn-group">
                        </div>
                    </div>

                    <div class="well">
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane active in" id="home">
                                <form method="post" enctype="multipart/form-data">
                                    <label>部门名称</label>
                                    <input type="text" name="name" placeholder="请输入部门名称" class="input-xxlarge" required value="<?php echo $dep['name']; ?>" />
                                    <span id="notice" style="color:red;display:none;">该职位已存在</span>

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
    $("input[name='name']").change(function(){
        var name=$(this).val()

        var params={
            action:'check',
            name:name
        }
        params=$.param(params)

        $.ajax({
            type:'post',
            url:'DepEdit.php',
            dataType:'json',
            data:params,
            success:function(result){
                if(result.success){
                    // 部门不相同
                    $("#notice").css({'display':'inline-block','color':'green'})
                    $("#notice").html('该部门不存在，可以添加')
                    $("#add").prop('disabled',false)
                }else{
                    // 部门相同
                    $("#notice").css({'display':'inline-block','color':'red'})
                    $("#notice").html('该部门已存在')
                    $("#add").prop('disabled',true)
                }
            },
            error:function(err){
                alert('error')
            }
        })
    })
</script>

