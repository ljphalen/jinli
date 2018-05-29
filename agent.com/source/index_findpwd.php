<?php
include 'source/inc/check.php';//登陆检查
//var_dump($_POST);

if(post('sub')){
    
    $account = post('username');
    $mail = post('email');
    $db = db::factory(get_db_config());
    $user = new user($db);
    $ret = $user->checkMail($account, $mail);
    if(isset($ret[0][0]['ischeck'])&& $ret[0][0]['ischeck']>0){
        $pwd = $user->createPwd();
        //echo $pwd.'<br>';
        $ret1 = $user->changePwd($account, $pwd);
        $body = '您好:<br />';
        $body .= '您的新密码是：<b>'.$pwd.'</b><br />
                                请您登录后，尽快修改密码！';
        $body .= '感谢您的支持！<br />';	
        //echo '111';var_dump($ret1);exit;
        if(isset($ret1[0][0]['status']) && $ret1[0][0]['status']==0){
            $user->sendMail($body,$mail,'重置密码');
            jump_url('您的密码已经发送到邮箱，请注意查收','index.php?ac=login');
        }else{
            jump_url('提交失败');
        }
    }else{
        jump_url('邮箱错误');
    }
}


require 'plugin/smarty.php';

$smarty->display('index_'.$ac.'.html');