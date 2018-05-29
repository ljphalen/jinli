<?php
include 'source/inc/check.php';//登陆检查
require 'plugin/smarty.php';

//var_dump($_POST);

//连接数据库
$db = db::factory(get_db_config());
$user = new user($db);
$company = new company($db);
//$tmpret = $company->getClientList(1);
//var_dump($_POST);exit;
/*
$str = '[';
foreach ($tmpret as $key => $var){
    //$str .='{'.$var['clientid'].':'.$var['clientid'].'-'.$var['name'].'},';
    $str .= '{title:"'.$var['name'].'",clientid:"'.$var['clientid'].'"},';
}   
$str = rtrim($str,',');
$str .= ']';
 * */

//echo $str;exit;
$channeltype = get('channeltype')?get('channeltype'):1;

if (post('smt')){
   //var_dump($_POST);
	$status = 0;
	do{
		$username = post('username');
		$userpass = post('userpass');
		$repass = post('repass');
		$channeltype = post('channeltype')?post('channeltype'):get('channeltype');
		$level = post('levels');
		$nickname = post('nickname');
		$email = post('email');
		$name = post('name');
		$clientid = post('clientid');
		$clientids = post('clientids');
		$intoratio = post('intoratio');
		$phone = post('phone');
		$mobile = post('mobile');
		$linkman = post('linkman');
		$address = post('address');
		$postcode = post('postcode');
		$describe = post('describe');
                $agentid = post('agentid');
		
		$channeltype = ($_SESSION['userinfo']['level']>=200)?$channeltype:$_SESSION['userinfo']['channeltype'];
		$clientid = ($_SESSION['userinfo']['level']>=200)?$clientid:$_SESSION['userinfo']['clientid'];

		if (empty($level)){
			$status = 10;
			break;
		}
		
		if($_SESSION['userinfo']['level']<200 && $level>$_SESSION['userinfo']['level']){
			$status = 21;
			break;
		}
		
		
		if ($userpass != $repass){
			$status = 9;
			break;
		}
		switch ($level){
			case 200:{
				if (empty($username)){
						$status = 17;
						break;
					}
					if (empty($userpass)){
						$status = 18;
						break;
					}
					if (empty($nickname)){
						$status = 19;
						break;
					}
				}
				break;
			case 10:{
                            if(!$agentid ){
				if (empty($clientids)){
					$status = 15;
					break;
					}
                            }
                        }
			case 30:{
                            if(!$agentid ){
                                if($level == 30){
                                    $clientid = $clientids;
                                }
				if (empty($name)){
					$status = 13;
					break;
				}
				if (empty($clientid)){
					$status = 14;
					break;
				}
				if(empty($intoratio)){
					$status = 16;
					break;
				}
                            }
				if (empty($username)){
					$status = 17;
					break;
				}
				if (empty($userpass)){
					$status = 18;
					break;
				}
				if (empty($nickname)){
					$status = 19;
					break;
				}
			}break;
			
			default:
				jump_url('非法操作！');
				break;
		}
		
		
		
		
		
		


		
		
		
		
		
		$op = new op();
		if($agentid >0){
                    $ret = $agentid;
                }else{
                    switch ($level){
                            case 10:
                                    $ret = $op->addSecondThirdCompany($company, $name, $phone, $mobile, $linkman, $address, $postcode, $intoratio, $clientid, $clientids, $describe, $channeltype);
                                    break;
                            case 30:
                                    $ret = $op->addSecondThirdCompany($company, $name, $phone, $mobile, $linkman, $address, $postcode, $intoratio, $clientid, 0, $describe, $channeltype);
                                break;
                            default:
                                    $ret = $sess_userinfo['agentid'];
                                    break;

                    }
                }
                if (!$ret){
                        jump_url('添加公司失败');
                }
                
                $task = new task($db);
                $task->update_company_time();
                //var_dump($ret);
		$ret2 = $op->addUser($username, $repass, $nickname, $email, $level, $ret,true,$user);
		$status = $ret2;
		 //writeLog('debug', var_export($_POST,true), '$status'.$status);
                 jump_url($config['error'][$ret2]);
	}while(0);
	
	jump_url($config['error'][$status]);
}

//var_dump($_SESSION);
//var_dump($_SESSION);
//echo $_SESSION['userinfo']['level'];
//$level = get('cid')?get('cid'):$_SESSION['userinfo']['level'];
if(get('cid')){
    $cid = get('cid');
    $ret = $company->findOneCompany($cid);
   // var_dump($ret);
    if($ret[0]['clientid'] !=0){
        if($ret[0]['clientids'] ==0){
            $level = 30;
        }else{
            $level = 10;
        }
    }else{
        $level = 200;
    }
    //var_dump($ret);
    //$level = $_SESSION['userinfo']['level'];
    $admin['level'] = $level;
    $admin['agentid'] = $cid;
    $admin['clientid'] = $ret[0]['clientid'];

    //$userinfo['name'] = $ret[0]['name'];
    //$userinfo['clientids'] = $ret[0]['clientid'];
    //$userinfo['intoratio'] = $ret[0]['intoratio'];
    //$userinfo['describe'] = $ret[0]['describe'];
    
    
}else{
    $level = $_SESSION['userinfo']['level'];
    $admin['level'] = $_SESSION['userinfo']['level'];
    $admin['agentid'] = $_SESSION['userinfo']['agentid'];
    $admin['clientid'] = $_SESSION['userinfo']['clientid'];

    $userinfo['name'] = $_SESSION['userinfo']['name'];
    $userinfo['clientids'] = $_SESSION['userinfo']['clientid'];
    $userinfo['intoratio'] = $_SESSION['userinfo']['intoratio'];
    $userinfo['describe'] = $_SESSION['userinfo']['describe'];
}

unset($config['level'][222]);
$smarty->assign('levels',$level);
//$smarty->assign('str',$str);
$smarty->assign('channeltype',$channeltype);
$smarty->assign('channel_types',array(1=>'自主渠道',2=>'移动渠道'));
$smarty->assign('role',$config['level']);
$smarty->assign('info',$userinfo);
$smarty->assign('admin',$admin);
$smarty->assign('actionText','添加新用户');
$smarty->assign('action','add');
$smarty->assign('agentid',get('cid'));
$smarty->display('index_'.$ac.'.html');

