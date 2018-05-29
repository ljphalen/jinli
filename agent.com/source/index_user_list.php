<?php
include 'source/inc/check.php';//登陆检查

//var_dump($_GET);
$channeltype = get('channeltype')?get('channeltype'):1;
$page = get('page')?get('page'):1;
$condition = get('condition');
$start = ($page-1)*PAGESIZE;


//连接数据库
$db = db::factory(get_db_config());
$user = new user($db);
if(isset($_SESSION['userinfo'])){
    $clientid = ($_SESSION['userinfo']['level']>=200)?-1:$_SESSION['userinfo']['clientid'];
    $clientids = ($_SESSION['userinfo']['level']>=200)?-1:$_SESSION['userinfo']['clientids'];
    $channeltype = ($_SESSION['userinfo']['level']>=200)?$channeltype:$_SESSION['userinfo']['channeltype'];
}else{
    exit('session过期');
}
if($condition){
    $isnum = is_numeric($condition)?1:0;
    $tmpret = $user->searchOperatorList($condition,$isnum,$clientid,$clientids,$channeltype,$start,PAGESIZE);
}else{
    $tmpret = $user->getOperatorList($clientid,$channeltype,$start,PAGESIZE);
}
$totalsize = ceil($tmpret[1][0]['Total']/PAGESIZE);



// smarty
require 'plugin/smarty.php';
//echo CACHE_USER_LIST.$channeltype.$page.'.html';
//$smarty->assign('paht',CACHE_USER_LIST.$channeltype.$page.'.html');

$smarty->assign('curpage',$page);
$smarty->assign('pages',$totalsize);
$smarty->assign('userList',$tmpret[0]);
$smarty->assign('condition',  $condition);
$smarty->assign('channeltype',$channeltype);
$smarty->assign('role',$config['level']);
$smarty->assign('admin',$sess_userinfo);
// 显示模板的内容
$smarty->display('index_'.$ac.'.html');