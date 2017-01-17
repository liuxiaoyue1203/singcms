<?php
// Common模块是一个特殊的模块，是应用的公共模块，访问所有的模块之前都会首先加载公共模块下面
// 的配置文件（ Conf/config.php ）和公共函数文件（ Common/function.php ）。但Common模块本身
// 不能通过URL直接访问，公共模块的其他文件则可以被其他模块继承或者调用

//用于在controller中向common.js里的jquery事件响应时，输出json数据
function show($status,$message,$data=array()){
      $result=array(
          'status' => $status,
          'message' => $message,
          'data' => $data,  
      );
      exit(json_encode($result));
}

// 对(密码+'MD5_PRE')进行md5加密
function getMd5Password($password){
    // C方法 读取配置
    return md5($password.C('MD5_PRE'));
}
// 显示菜单类型
function getMenuType($type){
		return $type==1 ? '后台菜单':'前端导航';
}
// 显示菜单的状态
function getMenuStatus($status){
		if($status==0) return '关闭';
		elseif($status==1) return '正常';
		elseif($status==-1) return '删除';
		return '异常';
}
//生成左侧导航栏的url
function getAdminMenuUrl($nav){
    $url='/admin.php?c='.$nav['c'].'&a='.$nav['f'];
    if($nav['f']=='index'){
        $url='/admin.php?c='.$nav['c'];
    }
    return $url;
}
//判断左侧导航栏的高亮选项
function getActiove($navc){
    $c=strtolower(CONTROLLER_NAME);
    if(strtolower($navc)==$c){
        return 'class="active"';
    }
    return '';
}

function showKind($status,$data){
    header('Content-type:application/json;charset=UTF-8');
    if($status==0){
        exit(json_encode(array('error'=>0,'url'=>$data)));
    }
    exit(json_encode(array('error'=>1,'message'=>'上传失败')));
}

//获取登录用户的登录名
function getLoginUsername(){
    return $_SESSION['adminUser']['username']? $_SESSION['adminUser']['username']:'';
}

function getCatName($navs,$id){
    foreach($navs as $nav){
        $navList[$nav['menu_id']]=$nav['name'];
    }
    return isset($navList[$id]) ? $navList[$id]:'';
}
// 得到文章的来源
function getCopyFromById($id){
    $copyFrom = C("COPY_FROM");
    return $copyFrom[$id]?$copyFrom[$id]:'';
}
// 判断是否有缩略图
function isThumb($thumb){
    if($thumb){
        return '<span style="color:red">有</span>';
    }
    return '无';
}