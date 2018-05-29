<?php
include 'common.php';
/**
 * 从BI拉取 ---  获取临时消息消息明细
 */

//get bi pushmsg
$biMsg = Common::getDao("Client_Dao_BIPushMsgDetail");
$result = $biMsg->getsBy(array('day_id'=>date('Ymd', strtotime("-2 day"))));


//insert local pushmsglog
$lcMsg = Common::getDao("Client_Dao_PushMsgLog");
$read = array();


function enImeiAES($imei){
	$cryptAES = new Util_CryptAES();
	$enImei = $cryptAES->encrypt($imei);
	return  strtoupper($enImei); 
}

if($result){
	//把今天的数据全部加入表
	foreach($result as $key=>$value){
		$imei = enImeiAES($value['imei']);
		$msgInfo = Client_Service_PushMsg::getBy(array('id'=>$value['push_id']));
		$msgLog = Client_Service_PushMsgLog::getBy(array('imei'=>$imei,'msgid'=>$value['push_id']));
		$usrInfo = Account_Service_User::getUserInfo(array('uname'=>$value['user_id']));
		if(!$msgLog){
			$read[] = array(
					'id'        => '',
					'type'        => $msgInfo['type'],
					'msgid'       => $msgInfo['id'],
					'uuid'        => $usrInfo ? $usrInfo['uuid'] : '',
					'imei'        => $imei,
					'create_time' =>strtotime($value['op_time'])
			);
		}
	}
	
	print_r($read);
	
	if($read){
		$ret = Client_Service_PushMsgLog::mutiInsert($read);
	}
}
echo CRON_SUCCESS;