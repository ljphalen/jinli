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
$user = new user($db);

if(post('smt')){
    $username = post('username ');
    $userpass = post('userpass');
    $repass = post('repass');
    /*
    if(!$repass || !$userpass){
        //jump_url($config['error'][18]);
       // var_dump($_SESSION);exit;
        $repass = $_SESSION['userinfo']['passwd'];
        $userpass = $_SESSION['userinfo']['passwd'];
    }
     * 
     */
    if($userpass != $repass){
        jump_url($config['error'][9]);
    }
    
    $nickname =post('nickname');
    $email = post('email');
    $uid = post('userid');
    $method = post('method');
    /*
    if($_SESSION['userinfo']['level'] < 200){
        if($uid == $_SESSION['userinfo']['userid']){
            jump_url('不能对自己编辑');exit;
        }
    }*/
    $rets = $user->getOneOperator($uid);
    $cominfo = $rets[0];
    if($_SESSION['userinfo']['level'] == 30){
        if($cominfo[0]['clientid'] != $_SESSION['userinfo']['clientid']){
                jump_url('您无编辑权限');exit;
        }
    }elseif($_SESSION['userinfo']['level'] == 10){
        if(!($cominfo[0]['clientid'] == $_SESSION['userinfo']['clientid']&&$cominfo[0]['clientids'] == $_SESSION['userinfo']['clientids'])){
                jump_url('您无编辑权限');exit;
        }
    }
    
    if($repass==''){
    	$repass = $cominfo[0]['userpass'];
    }else{
    	$repass = md5($repass.ENCRYPTKEY);
    }
    
    switch($method){
        case 'edit':
            $ret = $user->editUser($uid, $repass, $nickname, $email);
            if($ret[0]['status'] == 0){
                jump_url('修改成功');
            }else{
                jump_url('修改失败');
            }
            break;
        case 'del':
            break;
    }
    
}elseif(get('method')){
    $method = get('method');
    $channeltype = get('channeltype');
    $uid = get('uid');
    switch($method){
        case 'edit':{
            $ret = $user->getOneOperator($uid);
            $userinfo['name'] = $ret[0][0]['name'];
            $userinfo['clientids'] = $ret[0][0]['clientid'];
            $userinfo['intoratio'] = $ret[0][0]['intoratio'];
            $userinfo['describe'] = $ret[0][0]['describe'];
            $userinfo['username'] = $ret[0][0]['username'];
            $userinfo['nickname'] = $ret[0][0]['nickname'];
            $userinfo['email'] = $ret[0][0]['email'];
            $userinfo['userid'] = $uid;
            }
            break;
        case 'del':
            $rrts = $user->getOneOperator($uid);
            $cominfo = $rrts[0];

            //权限检查
            if($cominfo[0]['levels']>$_SESSION['userinfo']['level']||$cominfo[0]['levels']>200){
            	jump_url('您无编辑权限');exit;
            }
            
            if($_SESSION['userinfo']['level'] == 30){
                if($cominfo[0]['clientid'] != $_SESSION['userinfo']['clientid']){
                        jump_url('您无编辑权限');exit;
                }
            }elseif($_SESSION['userinfo']['level'] == 10){
                if(!($cominfo[0]['clientid'] == $_SESSION['userinfo']['clientid']&&$cominfo[0]['clientids'] == $_SESSION['userinfo']['clientids'])){
                        jump_url('您无编辑权限');exit;
                }
            }
            
            
            $ret = $user->delUser($uid);
            if($ret[0]['status'] == 0){
                jump_url('删除成功');
            }else{
                jump_url('删除失败');
            }
            break;
    }
}
/*
$userinfo['name'] = $_SESSION['userinfo']['name'];
$userinfo['clientids'] = $_SESSION['userinfo']['clientid'];
$userinfo['intoratio'] = $_SESSION['userinfo']['intoratio'];
$userinfo['describe'] = $_SESSION['userinfo']['describe'];
$userinfo['username'] = $_SESSION['userinfo']['username'];
$userinfo['nickname'] = $_SESSION['userinfo']['nickname'];
$userinfo['email'] = $_SESSION['userinfo']['email'];
$userinfo['userid'] = $uid;
*/


$smarty->assign('url','/index.php?ac=user_edit');
//$smarty->assign('userid',$uid);
$smarty->assign('actionText','编辑用户');
$smarty->assign('method',$method);
$smarty->assign('info',$userinfo);
$smarty->assign('action','update');
$smarty->display('index_user_add.html');