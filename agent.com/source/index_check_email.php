<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
//var_dump($_SESSION);

$userid = get('userid');
$email = get('mail');
$code = get('code');
if(md5($userid.$email.$code.ENCRYPTKEY) == get('sign')){
    //echo $code.'<br>';
    $db = db::factory(get_db_config());
    $user = new user($db);
    $ret = $user->validationRead($userid,$code);
    if(isset($ret[0][0]['dateline'])){
        if(($ret[0][0]['dateline']+60*60*24*7) > time()){
            $ret = $user->validationDel($userid,$code);
            if(isset($ret[0][0]['status']) && $ret[0][0]['status'] == 0){
                //成功删除
                $ret = $user->changeMail($userid, $email);
                $_SESSION['userinfo']['email'] = $email;
                if(isset($ret[0]['status']) && $ret[0]['status'] == 0){
                    echo '邮箱修改成功';
                }else{
                    echo '邮箱修改失败';
                }
            }else{
                echo '验证失败';
            }
            //绑定成功
        }else{
            //验证过期
            echo '验证过期';
        }
    }else{
        //找不到对应的记录
        echo '找不到对应的记录';
    }
    
}else{
    echo '参数传输中被串改';
}
?>
