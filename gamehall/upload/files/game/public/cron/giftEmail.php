<?php
include 'common.php';

//获取队列的key值
while( $mailInfo =  json_decode(Common::getQueue()->pop('game_gift'),TRUE)){
	if($mailInfo){
	    $emailContent = getEmailContent($mailInfo, $mailInfo['conditionCode']);
	    if($mailInfo['sendtype'] == 'yunying'){
	        $emailSubject = $emailContent['emailSubject'][0];
	        $body = $emailContent['body'][0];
	    } else {
	        $emailSubject = $emailContent['emailSubject'][1];
	        $body = $emailContent['body'][1];
	    }
	    echo "<pre>";
	    print_r($mailInfo);
	    echo $emailSubject."<br>";
	    echo $body."<br>";
		$ret  = Common::sendEmail($emailSubject,  $body,  $mailInfo['email']);
	}
}


function getEmailContent($mailInfo, $conditionCode){
    $yunying_emailSubject = $yunying_body = $dev_emailSubject = $dev_body ='';
    if($conditionCode == Client_Service_Gift::GIFT_REMAIN_ZERO){
        $yunying_emailSubject ='剩余礼包比例预警';
        $yunying_body = '礼包ID：'.$mailInfo['gift_id'].'，游戏ID：'.$mailInfo['game_id'].'，游戏名称：'.$mailInfo['game_name'].'，礼包名称：'.$mailInfo['gift_name'].'，剩余的数量：'.$mailInfo['remain_gifts'].'个，请及时添加。';
    
        $dev_emailSubject = '【金立游戏开发者平台】剩余礼包零预警：礼包名称 '.$mailInfo['gift_name'];
        $dev_body= '<div><font style="font-family:\"Microsoft YaHei\";font-weight:bold;">游戏名称：'.$mailInfo['game_name'].'，包名：'.$mailInfo['package'].'，礼包名称：'.$mailInfo['gift_name'].'，剩余礼包兑换码数量：0个。</font></div><br><br>';
        $dev_body.= '<div>请及时与运营确认是否需要补充兑换码数量或者添加新的礼包种类。</div><br><br>';
        $dev_body.= '<div><a href="http://dev.game.gionee.com">点击进入</a>（若点击无效，请复制此地址到Chrome浏览器，http://dev.game.gionee.com）</td><br><br>';
        $dev_body.= '<div>From：金立游戏，'.date("Y-m-d",Common::getTime()).'</div>';
    
        $emailSubject['emailSubject'] = array($yunying_emailSubject, $dev_emailSubject,);
        $emailSubject['body'] = array($yunying_body, $dev_body);
    
    } else if($conditionCode == Client_Service_Gift::GIFT_PERCENT){
        $giftNum = Game_Service_Config::getValue('game_gift_num');
        $yunying_emailSubject ='剩余礼包比例预警';
        $yunying_body = '礼包ID：'.$mailInfo['gift_id'].'，游戏ID：'.$mailInfo['game_id'].'，游戏名称：'.$mailInfo['game_name'].'，礼包名称：'.$mailInfo['gift_name'].'，剩余的数量：'.$mailInfo['remain_gifts'].'个，请及时添加。';
    
        $dev_emailSubject = '【金立游戏开发者平台】剩余礼包兑换码不足'.$giftNum.'%：礼包名称'.$mailInfo['name'];
        $dev_body= '<div>游戏名称：'.$mailInfo['game_name'].'，包名：'.$mailInfo['package'].'，礼包名称：'.$mailInfo['gift_name'].'，剩余礼包兑换码数量：'.$mailInfo['remain_gifts'].'个，不足'.$giftNum.'%。</div><br><br>';
        $dev_body.= '<div>请及时与运营确认是否需要补充兑换码数量或者添加新的礼包种类。</div><br><br>';
        $dev_body.= '<div><a href="http://dev.game.gionee.com">点击进入</a>（若点击无效，请复制此地址到Chrome浏览器，http://dev.game.gionee.com）</td><br><br>';
        $dev_body.= '<div>From：金立游戏，'.date("Y-m-d",Common::getTime()).'</div>';
    
        $emailSubject['emailSubject'] = array($yunying_emailSubject, $dev_emailSubject,);
        $emailSubject['body'] = array($yunying_body, $dev_body);
         
    } else if($conditionCode == Client_Service_Gift::GIFT_YUNYING){
        $yunying_body = '礼包ID：'.$mailInfo['gift_id'].'，游戏ID：'.$mailInfo['game_id'].'，游戏名称：'.$mailInfo['game_name'].'，礼包名称：'.$mailInfo['gift_name'].'，剩余的数量：'.$mailInfo['remain_gifts'].'个，请及时添加。';
        $yunying_emailSubject = '剩余礼包比例预警';
    
        $emailSubject['emailSubject'] = array($yunying_emailSubject, $dev_emailSubject,);
        $emailSubject['body'] = array($yunying_body, $dev_body);
    
    }
    
    return $emailSubject;
}

echo CRON_SUCCESS;
exit;