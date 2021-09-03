<?php 
include_once('./config/init.php');

// 判断是否有登录
include_once('./check.php');

// 删除操作
if($_POST){
  $deldid=empty($_POST['deldid']) ? 0 : trim($_POST['deldid'],",");

  $affect=del("department","id IN($deldid)");

  if($affect){
    ShowMsg('删除部门成功','DepList.php');
    exit;
  }else{
    ShowMsg('删除部门失败');
    exit;
  }
}

// 获取当前的页码值
$page=isset($_GET['page']) ? $_GET['page'] : 1;

// 查询当前的总页数
$sql="SELECT count(*) AS c FROM {$pre_}department";
$count=find($sql);

// 每页显示多少条数据
$limit=6;

// 中间显示多少个分页的页码数
$size=5;

// 调用分页函数
$PageStr=page($page,$count['c'],$limit,$size,"digg");
// var_dump($PageStr);
// exit;

// 查询数据
// 设置偏移量
$start=($page-1)*$limit;

// 链表查询
$sql="SELECT * FROM {$pre_}department LIMIT $start,$limit";
$dep_list=all($sql);

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
            <h1 class="page-title">部门管理</h1>
        </div>
        <ul class="breadcrumb">
            <li><a href="./index.php">Home</a> <span class="divider">/</span></li>
            <li class="active">Index</li>
        </ul>

        <div class="container-fluid">
            <div class="row-fluid">
                <div class="btn-toolbar">
                    <button class="btn btn-primary" onClick="location='DepAdd.php'">
                      <i class="icon-plus"></i>添加部门
                    </button>

                    <a href="#myModal" class="btn btn-info" onclick="delAll()" role="button" data-toggle="modal">
                      <i class="icon-remove"></i>批量删除部门
                    </a>
                </div>
                <div class="well">
                    <table class="table">
                      <thead>
                        <tr>
                          <th><input type="checkbox" name="id" id="all" />全选</th>
                          <th>ID</th>
                          <th>部门名称</th>
                          <th style="width: 50px;">操作</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach($dep_list as $item){ ?>
                          <tr>
                            <td><input type="checkbox" name="did" value="<?php echo $item['id'];?>" /></td>
                            <td><?php echo $item['id'];?></td>
                            <td><?php echo $item['name'];?></td>
                            <td>
                                <a href="DepEdit.php?pid=<?php echo $item['id']; ?>">
                                  <i class="icon-pencil"></i>
                                </a>

                                <a onclick="del(<?php echo $item['id'];?>)" href="#myModal" role="button" data-toggle="modal">
                                  <i class="icon-remove"></i>
                                </a>
                            </td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <?php echo $PageStr;?>
                </div>

                <form method="post" enctype="multipart/form-data">
                  <input type="hidden" name="deldid" value="" />

                  <div class="modal small hide fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                          <h3 id="myModalLabel">确认框</h3>
                      </div>
                      <div class="modal-body">
                          <p class="error-text">
                            <i class="icon-warning-sign modal-icon"></i>是否确认删除该部门?
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
  function del(did){
    $("input[name='deldid']").val(did)
  }

  function delAll(){
    var str=""
    $("input[name='did']:checked").each(function(){
      str+=$(this).val()+","
    })
    $("input[name='deldid']").val(str)
  }

  $("#all").click(function(){
    $("input[name='did']").prop("checked",$(this).prop("checked"))
  })
</script>
