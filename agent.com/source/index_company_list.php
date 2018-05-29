<?php
include 'source/inc/check.php';//登陆检查
//更新渠道商缓存
$company_filename = SROOT.'/data/cache/company/modtime'.CACHE_FILE_EXT;
$modtime = file_get_contents($company_filename);
if(time()-$modtime < 86400){
	$url = get_hosturl().'/task.php?ac=update&forcecom=1';
	do_post_async($url);
}

//var_dump($_GET);
$channeltype = get('channeltype');
$page = get('page')?get('page'):1;
$start = ($page-1)*PAGESIZE;
$condition = get('condition');
//连接数据库
$db = db::factory(get_db_config());
$user = new user($db);
$company = new company($db);

if(isset($_SESSION['userinfo'])){
    $clientid = ($_SESSION['userinfo']['level']>200)?0:$_SESSION['userinfo']['clientid'];
    $clientids = ($_SESSION['userinfo']['level']>200)?-1:$_SESSION['userinfo']['clientids'];
	$channeltype = ($_SESSION['userinfo']['level']>=200)?$channeltype:$_SESSION['userinfo']['channeltype'];
}else{
    exit('session过期');
}

if($condition){
    $isnum = is_numeric($condition)?1:0;
    //echo $isnum;
    $tmpret = $company->searchCompanyList($condition,$isnum,$clientid,$clientids,$channeltype,$start,PAGESIZE);
}else{
    //echo 'xx';
    
    $tmpret = $company->getCompanyList($clientid,$channeltype,$start,PAGESIZE);
}

$totalsize = ceil($tmpret[1][0]['Total']/PAGESIZE);


// smarty
require 'plugin/smarty.php';
// 显示模板的内容
$smarty->assign('channeltypeArr',$config['level']);
$smarty->assign('list',$tmpret[0]);
$smarty->assign('curpage',$page);
$smarty->assign('pages',$totalsize);
$smarty->assign('channeltype',$channeltype);
$smarty->assign('condition',  $condition);
$smarty->assign('admin',$sess_userinfo);
$smarty->display('index_'.$ac.'.html');