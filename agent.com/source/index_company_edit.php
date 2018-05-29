<?php
include 'source/inc/check.php';//登陆检查
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
// smarty
require 'plugin/smarty.php';
// 显示模板的内容

$db = db::factory(get_db_config());
$company = new company($db);

if(post('smt')){

    $id = post('id');
    $channeltype = post('channeltype');
    $name = post('name');
    
    $cominfo = $company->findOneCompany($id);
    if($_SESSION['userinfo']['level'] == 30){
    	/* $ret = $company->findOneCompany($id);
    	 if($ret[0]['clientid'] == $_SESSION['userinfo']['clientid'] || $ret[0]['clientids'] == 0){
    	jump_url('您无编辑权限');exit;
    	} */
    	 
    	if($cominfo[0]['clientid'] != $_SESSION['userinfo']['clientid']){
    		jump_url('您无编辑权限');exit;
    	}
    }elseif($_SESSION['userinfo']['level'] == 10){
    	if(!($cominfo[0]['clientid'] == $_SESSION['userinfo']['clientid']&&$cominfo[0]['clientids'] == $_SESSION['userinfo']['clientids'])){
    		jump_url('您无编辑权限');exit;
    	}
    }
    
    if($_SESSION['userinfo']['level'] < 200){
    	$channeltype = $_SESSION['userinfo']['channeltype'];
    }
    
    //print_r($cominfo);exit;
    if($name!=$cominfo[0]['name']){
    	$ret = $company->checkcompanyNameIsExists($name);
    	if($ret[0]['Status'] != 1){
    		jump_url('公司名已存在');
    		exit;
    	}
    }
    
    $clientid =post('clientid');
    $clientids = post('clientids');
    $intoratio = post('intoratio');
    $phone = post('phone');
    $mobile = post('mobile');
    $linkman = post('linkman');
    $address = post('address');
    $postcode = post('postcode');
    $describe = post('describe');
    $opname = $_SESSION['userinfo']['nickname'];
    //var_dump($_SESSION);
    //var_dump($_POST);//exit;
    $method = post('method');
    switch($method){
        case 'edit':
            $ret = $company->editCompany($id,$name,$phone,$mobile,$linkman,$address,$postcode,$intoratio,$clientid,$clientids,$describe,$opname,$channeltype);
            if(isset($ret[0]['status']) && $ret[0]['status'] == 0){
            	$task = new task($db);
            	$task->update_company_time();
                jump_url('修改成功','/index.php?ac='.$ac.'&method=edit&cid='.$id.'&channel='.$channeltype);
            }else{
                jump_url('修改失败');
            }
            break;
        case 'del':
            break;
    }
    
}elseif(get('method')){
    $method = get('method');
    $channeltype = get('channel');
    $cid = get('cid');
    $admin['levels'] = $_SESSION['userinfo']['level'];
    $admin['userid'] = $_SESSION['userinfo']['userid'];
    $info = array();
    switch($method){
        case 'edit':{
            $ret = $company->findOneCompany($cid);
            $ret2 = $company->getCompanyOperator($cid);
           // var_dump($_SESSION);
            $info = $ret[0];
            $userlist = $ret2[0];
            $info['channeltype'] = $channeltype;
            $info['id'] = $cid;
            //var_dump($info);
            }
            break;
        case 'del':
             $cominfo = $company->findOneCompany($cid);
            if($_SESSION['userinfo']['level'] == 30){
                if($cominfo[0]['clientid'] != $_SESSION['userinfo']['clientid']){
                        jump_url('您无编辑权限');exit;
                }
            }elseif($_SESSION['userinfo']['level'] == 10){
                if(!($cominfo[0]['clientid'] == $_SESSION['userinfo']['clientid']&&$cominfo[0]['clientids'] == $_SESSION['userinfo']['clientids'])){
                        jump_url('您无编辑权限');exit;
                }
            }
            $ret = $company->delCompany($cid);
            //$ret[0]['status'] == 0;
            if($ret[0]['status'] == 0){
            	$task = new task($db);
            	$task->update_company_time();
                jump_url('删除成功');
            }else{
                jump_url('删除失败');
            }
            break;
    }
}


//var_dump($userlist);

$smarty->assign('userlist',$userlist);
$smarty->assign('method',$method);
$smarty->assign('channeltype',$channeltype);
$smarty->assign('actionText','渠道商编辑');
$smarty->assign('info',$info);
$smarty->assign('channel_types',array(1=>'自主渠道',2=>'移动渠道'));
$smarty->assign('admin',$admin);
$smarty->assign('action','update');
$smarty->assign('role',$config['level']);
$smarty->display('index_'.$ac.'.html');
