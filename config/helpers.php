<?php

header("Content-Type:text/html;charset=utf-8");

// 查询单条数据
function find($sql=null){
    // 引入全局变量
    global $conn;
    $res=mysqli_query($conn,$sql);
    if(!$res){
        echo "SQL语句执行失败：".$sql;
        exit;
    }

    return mysqli_fetch_assoc($res);
}

// 查询多条数据
function all($sql=null){
    // 引入全局变量
    global $conn;
    $res=mysqli_query($conn,$sql);
    if(!$res){
        echo "SQL语句执行失败：".$sql;
        exit;
    }

    //  多条数据要循环
    $list=[];
    while($data=mysqli_fetch_assoc($res)){
        $list[]=$data;
    }
    
    // 返回数据
    return $list;
}

// 添加数据
function add($table,$data){
    global $conn;
    global $pre_;

    // 给表名拼接上表前缀
    $table=$pre_.$table;

    // 将数组里面的索引全部提取出来变成一个新数组
    $keys=array_keys($data);

    $fields="`".implode("`,`",$keys)."`";

    $values="'".implode("','",$data)."'";

    $sql="INSERT INTO $table($fields)VALUES($values)";

    // 执行语句
    $res=mysqli_query($conn,$sql);

    if(!$res){
        echo "SQL语句执行失败".$sql;
        exit;
    }

    // 返回插入的id
    return mysqli_insert_id($conn);
}

// 更新数据
function update($table,$data,$where=1){
    global $conn;
    global $pre_;

    $table=$pre_.$table;

    // 直接组装
    $str="";

    foreach($data as $key=>$item){
        $str .= "`$key` = '$item',";
    }

    // 去除两边的逗号
    $str=trim($str,",");

    $sql="UPDATE $table SET $str WHERE $where";

    $res=mysqli_query($conn,$sql);

    if(!$res){
        echo "SQL语句执行失败".$sql;
        exit;
    }

    // 返回影响的行数
    return mysqli_affected_rows($conn);
}

// 删除数据
function del($table,$where=1){
    global $conn;
    global $pre_;

    $table=$pre_.$table;

    $sql="DELETE FROM $table WHERE $where";

    $res=mysqli_query($conn,$sql);

    if(!$res){
        echo "SQL语句执行失败".$sql;
        exit;
    }

    // 返回影响的行数
    return mysqli_affected_rows($conn);
}


/**
 * 提醒消息的方法
 * @msg 提醒消息内容
 * #url 跳转的地址
 */

function ShowMsg($msg='',$url=''){
    $msg=empty($msg) ? '未知消息' : trim($msg);

    @header("Content-Type:text/html;charset=utf-8");

    // 输出的内容
    $output="";

    // 跳转地址为空 返回上一个界面
    if(empty($url)){
        $output="
            <script>
                alert('$msg');
                history.go(-1);
            </script>
        ";
    }else{
        // 地址不为空时，就跳转到指定的界面
        $output="
            <script>
                alert('$msg');
                location.href='$url';
            </script>
        ";
    }

    echo $output;
    return;
}

// 得到当前网址
function get_url(){
    $str=$_SESSION['PHP_SELF'].'?';
    if($_GET){
        foreach($_GET as $k=>$v){ //$_GET['page']
            if($k!='page'){
                $str.=$k.'='.$v.'&';
            }
        }
    }
    return $str;
}

//分页函数
/**
 *@pargam $current	当前页
 *@pargam $count	记录总数
 *@pargam $limit	每页显示多少条
 *@pargam $size		中间显示多少条
 *@pargam $class	样式
*/

function page($current,$count,$limit,$size,$class='sabrosus'){
    $str='';
    if($count>$limit){
        $pages=ceil($count/$limit);//算出总页数
        $url=get_url();//获取当前页面的URL地址（包含参数）

        $str.='<div class="'.$class.'">';

        // 开始
        if($current==1){
            $str.='<span class="disabled">首&nbsp;&nbsp;页</span>';
            $str.='<span class="disabled"> &lt;上一页</span>';
        }else{
            $str.='<a href="'.$url.'page=1">首&nbsp;&nbsp;页</a>';
            $str.='<a href="'.$url.'page='.($current-1).'"> &lt;上一页</a>';
        }

        // 中间
        // 判断得出start与end
        if($current<=floor($size/2)){
            $start=1;
            $end=$pages > $size ? $size : $pages;//哪个最小，就取哪个
        }else if($current>=$pages-floor($size/2)){
            $start=$pages-$size+1<=0 ? 1 : $pages-$size+1;//避免出现负数
            $end=$pages;
        }else{
            $d=floor($size/2);
            $start=$current-$d;
            $end=$current+$d;
        }

        for($i=$start;$i<=$end;$i++){
            if($i==$current){
                $str.='<span class="current">'.$i.'</span>';
            }else{
                $str.='<a href="'.$url.'page='.$i.'">'.$i.'</a>';
            }
        }

        // 最后
        if($pages==$current){
            $str.='<span class="disabled"> 下一页&lt; </span>';
            $str.='<span class="disabled"> 尾&nbsp;&nbsp;页</span>';
        }else{
            $str.='<a href="'.$url.'page='.($current+1).'"> 下一页&lt; </a>';
            $str.='<a href="'.$url.'page='.$pages.'"> 尾&nbsp;&nbsp;页</a>';
        }
        $str.='</div>';
    }
    return $str;
}


/**
 * 文件上传函数
 * $name input的名字
 * $type 上传的文件类型
 * $size 限制的文件大小
 * $path 文件上传存放的路径
 */

function upload($name='image',$path='./uploads',$type=['jpg','png','gif','jpeg','bmp','tif','pcx','tga','exif','fpx','svg','psd','cdr','pcd','dxf','ufo','eps','ai','raw','WMF','webp','avif'],$size=12345678){
    // 返回的上传信息
    $result=[
        'success'=>false,//是否上传成功
        'msg'=>'',//上传成功或者失败的信息
    ];

    $input = $_FILES[$name];

    //说明图片上传报错
    if($input['error'] > 0){
        $result['success']=false;
        $result['msg']='文件上传失败(错误码)：'.$input['error'];
        return $result;
    }

    // 判断文件上传的类型对不对
    $ext=pathinfo($input['name'],PATHINFO_EXTENSION);

    // 判断类型是否属于这个里面
    // in_array(val1,val2)判断val1是否在val2的里面的方法
    if(!in_array($ext,$type)){
        $result['success']=false;
        $result['msg']='上传文件的格式错误';
        return $result;
    }

    // 判断大小不能超过指定限制的大小
    if($input['size'] > $size){
        $result['success']=false;
        $result['msg']='上传文件已超过最大限制';
        return $result;
    }

    // 先要判断一下文件存放的目录是否存在
    if(!is_dir($path)){
        $result['success']=false;
        $result['msg']='文件上传存放的目录不存在';
        return $result;
    }

    // 原文件名
    $filename=pathinfo($input['name'],PATHINFO_FILENAME);

    // 新文件名
    $path=rtrim($path,"/");//清除右边的“/”
    $newname=$path."/".$filename.time().".".$ext;

    // 判断是否通过http post上传的
    if(!is_uploaded_file($input['tmp_name'])){
        $result['success']=false;
        $result['msg']='文件上传途径有误';
        return $result;
    }

    // 最后的步骤将临时文件移动到指定的目录里面
    if(move_uploaded_file($input['tmp_name'],$newname)){
        $result['success']=true;
        $result['msg']=$newname;
        return $result;
    }else{
        $result['success']=false;
        $result['msg']='文件上传失败';
        return $result;
    }
}

//当函数不存在的时候才去创建
if (!function_exists('randstr')) {
    /**
     * 获得随机字符串
     * @param $len             需要的长度
     * @param $special        是否需要特殊符号
     * @return string       返回随机字符串
     */
    function randstr($len = 8, $special=false)
    {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9"
        );
    
        if($special){
            $chars = array_merge($chars, array(
                "!", "@", "#", "$", "?", "|", "{", "/", ":", ";",
                "%", "^", "&", "*", "(", ")", "-", "_", "[", "]",
                "}", "<", ">", "~", "+", "=", ",", "."
            ));
        }
    
        $charsLen = count($chars) - 1;
        shuffle($chars);                            //打乱数组顺序
        $str = '';
        for($i=0; $i<$len; $i++)
        {
            $str .= $chars[mt_rand(0, $charsLen)];    //随机取出一位
        }
        return $str;
    }
}

?>


