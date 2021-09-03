<?php 
include_once('./config/init.php');

// 判断是否有登录
include_once('./check.php');

// 删除操作
if($_POST){
  $delpid=empty($_POST['delpid']) ? 0 : trim($_POST['delpid'],",");

  // 先查询要删除的头像字段
  $sql="SELECT avatar FROM {$pre_}person WHERE id IN($delpid)";
  $AvatarList=all($sql);

  // 提取二维数组中某个字段内容到一个一维数组中
  $AvatarList=array_column($AvatarList,"avatar");

  // 去除空元素
  $AvatarList=array_filter($AvatarList);

  // 重置索引
  // $AvatarList=array_values($AvatarList);

  // 先执行删除数据，再执行删除图片
  $affect=del("person","id IN($delpid)");

  if($affect){
    if($AvatarList){
      foreach($AvatarList as $item){
        // 先判断图片是否真实存在  &&  删除图片 （利用逻辑与的逻辑短路进行删除，如果第一个为真，就执行第二个，否则就都不执行）
        @is_file($item) && @unlink($item);
      }
    }

    ShowMsg('删除员元成功','PersonList.php');
    exit;
    }else{
      ShowMsg('删除员工失败','PersonList.php');
      exit;
  }
}

// 获取当前的页码值
$page=isset($_GET['page']) ? $_GET['page'] : 1;

// 查询当前的总页数
$sql="SELECT count(*) AS c FROM {$pre_}person";
$count=find($sql);

// 每页显示多少条数据
$limit=6;

// 中间显示多少个分页的页码数
$size=5;

// 调用分页函数
$PageStr=page($page,$count['c'],$limit,$size,"digg");

// 查询数据
// 设置偏移量
$start=($page-1)*$limit;

// 链表查询,共链接三张表
$sql = "SELECT person.*,dep.name AS depName,job.name AS jobName FROM {$pre_}person AS person LEFT JOIN {$pre_}department AS dep ON person.depid = dep.id LEFT JOIN {$pre_}job AS job ON person.jobid = job.id ORDER BY createtime ASC LIMIT $start,$limit";
$person_list = all($sql);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include_once("meta.php");?>

    <!-- 分页样式 -->
    <link rel="stylesheet" href="./assets/page/css.css" />
  </head>
  <body>    
    <!-- 头部 -->
    <?php include_once("header.php");?>
    
    <!-- 菜单 -->
    <?php include_once("menu.php");?>

    <div class="content">
        <div class="header">
            <h1 class="page-title">员工管理</h1>
        </div>
        <ul class="breadcrumb">
            <li><a href="./index.php">Home</a> <span class="divider">/</span></li>
            <li class="active">Index</li>
        </ul>

        <div class="container-fluid">
            <div class="row-fluid">
                <div class="btn-toolbar">
                    <button class="btn btn-primary" onClick="location='PersonAdd.php'">
                      <i class="icon-plus"></i>添加员工
                    </button>
                    <a href="#myModal" class="btn btn-info" onclick="delAll()" role="button" data-toggle="modal">
                      <i class="icon-remove"></i>批量删除员工
                    </a>
                </div>
                <div class="well">
                    <table class="table">
                      <thead>
                        <tr>
                          <th><input type="checkbox" name="all" id="all" />全选</th>
                          <th>姓名</th>
                          <th>性别</th>
                          <th>手机号码</th>
                          <th>邮箱</th>
                          <th>所属职位</th>
                          <th>所属部门</th>
                          <th>入职时间</th>
                          <th style="width: 50px;">操作</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach($person_list as $item){ ?>
                          <tr>
                            <td><input type="checkbox" name="pid" value="<?php echo $item['id'];?>" /></td>
                            <td><?php echo $item['name'];?></td>
                            <td><?php echo $item['sex'] ? "男" : "女";?></td>
                            <td><?php echo $item['mobile'];?></td>
                            <td><?php echo $item['email'];?></td>
                            <td><?php echo $item['depName'];?></td>
                            <td><?php echo $item['jobName'];?></td>
                            <td><?php echo date("Y-m-d",$item['createtime']);?></td>
                            <td>
                                <!-- 编辑按钮 -->
                                <a href="PersonEdit.php?pid=<?php echo $item['id']; ?>"><i class="icon-pencil"></i></a>
                                <!-- 删除按钮 -->
                                <a onclick="del(<?php echo $item['id'];?>)" href="#myModal" role="button" data-toggle="modal"><i class="icon-remove"></i></a>
                            </td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <?php echo $PageStr;?>
                </div>
                
                <!-- 弹出框 -->
                <form method="post">
                  <!-- 设置一个隐藏域 装的是value 需要删除的ID -->
                  <input type="hidden" name="delpid" value="" />

                  <div class="modal small hide fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                          <h3 id="myModalLabel">确认框</h3>
                      </div>
                      <div class="modal-body">
                          <p class="error-text">
                            <i class="icon-warning-sign modal-icon"></i>是否确认删除员工？
                          </p>
                      </div>
                      <div class="modal-footer">
                          <button class="btn" data-dismiss="modal" aria-hidden="true">取消</button>
                          <button class="btn btn-danger" type="submit">确认</button>
                      </div>
                  </div>
                </form>

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
  // 单条删除
  function del(pid){
    // 将点击删除的ID赋值给隐藏域，让表单进行提交，提交给PHP，让PHP拿到ID再进行删除
    $("input[name='delpid']").val(pid)
  }

  // 多条删除
  function delAll(){
    var str=""

    // 获取到指定的元素进行循环
    $("input[name='pid']:checked").each(function(){
      str+=$(this).val()+","
    })
    
    // 将拼接好的ID给到隐藏域
    $("input[name='delpid']").val(str)
  }

  // 全选和反选
  $("#all").click(function(){
    $("input[name='pid']").prop("checked",$(this).prop("checked"))
  })
</script>
