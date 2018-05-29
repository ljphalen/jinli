<?php
include 'source/inc/check.php';//登陆检查
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * 
 * 9 => 
    array
      'userid' => string '23572' (length=5)
      'username' => string 'asdasd' (length=6)
      'nickname' => string 'asdasd' (length=6)
      'email' => string 'xxxxxx@qq.com' (length=13)
      'levels' => string '10' (length=2)
      'dateline' => string '1365507329' (length=10)
      'logonip' => string '222.223.224.220' (length=15)
 * 
 * 
 * array
  'name' => string '名游网络' (length=12)
  'phone' => string '' (length=0)
  'mobile' => string '' (length=0)
  'linkman' => string 'admin' (length=5)
  'address' => string '深圳南山' (length=12)
  'postcode' => string '' (length=0)
  'intoratio' => string '0.00' (length=4)
  'clientid' => string '0' (length=1)
  'clientids' => string '0' (length=1)
  'describe' => string '名游网络' (length=12)
  'channeltype' => string '1' (length=1)
  'id' => string '1' (length=1)
 * 
 * 
 * array
  'levels' => string '255' (length=3)
  'userid' => string '1' (length=1)
 */
// smarty
require 'plugin/smarty.php';
$admin['levels'] = $_SESSION['userinfo']['level'];
$admin['userid'] = $_SESSION['userinfo']['userid'];
$sessionInfo = $_SESSION['userinfo'];

$userlist[0]['userid'] = $sessionInfo['userid'];
$userlist[0]['username'] = $sessionInfo['username'];
$userlist[0]['nickname'] = $sessionInfo['nickname'];
$userlist[0]['email'] = $sessionInfo['email'];
$userlist[0]['levels'] = $sessionInfo['level'];
$userlist[0]['dateline'] = $sessionInfo['dateline'];
$userlist[0]['logonip'] = $sessionInfo['logonip'];
$db = db::factory(get_db_config());
$company = new company($db);
$ret = $company->findOneCompany($sessionInfo['agentid']);
$info = $ret[0];
$info['channeltype'] = $sessionInfo['channeltype'];
$info['id'] = $sessionInfo['agentid'];
$channeltype = $sessionInfo['channeltype'];

//var_dump($userlist);
//var_dump($info);
$method = 'edit';
$smarty->assign('userlist',$userlist);
$smarty->assign('method',$method);
$smarty->assign('channeltype',$channeltype);
$smarty->assign('actionText','我的渠道');
$smarty->assign('info',$info);
$smarty->assign('channel_types',array(1=>'自主渠道',2=>'移动渠道'));
$smarty->assign('admin',$admin);
$smarty->assign('action','update');
$smarty->assign('role',$config['level']);
$smarty->display('index_company_edit.html');