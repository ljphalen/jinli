<?php
   include 'common.php';
   //手动赠送A券发送邮件
   while($createTime =  json_decode(Common::getQueue()->pop('game_client_send_mail'),TRUE)){
        $params['create_time']  = $createTime;
        $reasons = Client_Service_SendTicketReason::getsBy($params);
        sendMail($reasons, $createTime);
   }
   
    function sendMail($reasons, $createTime){
	    if(!$reasons) return;
	    $emailSubject ="手动赠送A券明细";
	    if (Util_Environment::isOnline()) {
	        $mails = array(
	                'huyk@gionee.com',
	                'wyly@gionee.com',
	                'guohb@gionee.com',
	                'baiyong@gionee.com',
	        );
	    } else {
	        $mails = array(
	                //'yxdtxm@gionee.com',
	                'huyk@gionee.com',
	                'wangpeng@gionee.com',
	                'lichanghua@gionee.com',
	        );
	    }
	    $title = "【手动赠送A券明细】";
	    $body = "在 " .date("Y-m-d H:i:s",$createTime)." 手动赠送A券明细如下:"."<br><br>";
	    $body.= '<div class="table_list" style="padding: 7px 10px 9px;">';
	    $body.= '<table width="100%"  border="2" cellpadding="2" cellspacing="2" bordercolor="#add9c0" style="border-collapse:collapse;border-width:2px 2px 2px 2px;">';
	    $body.= '<tr>';
	    $body.= '<td>赠送账号</td>';
	    $body.= '<td>赠送UUID</td>';
	    $body.= '<td width="150">赠送原因</td>';
	    $body.= '<td>赠送时间</td>';
	    $body.= '<td>面额</td>';
	    $body.= '<td>状态</td>';
	    $body.= '<td>A券赠送人</td>';
	    $body.= '</tr>';
	    $count = 0;
	    foreach($reasons as $key=>$value){
	        $sendTicket = Client_Service_TicketTrade::getBy(array('aid'=>$value['aid']));
	        $userInfo = Account_Service_User::getUser(array('uuid'=>$sendTicket['uuid']));
	        $uname = substr_replace($userInfo['uname'],'*****',3,5);
	        $status = $sendTicket['status'] ? "成功" : "失败";
	        $count +=$sendTicket['denomination'];
	        $body.='<tr>';
	        $body.='<td>'.$uname.'</td>';
	        $body.='<td>'.$sendTicket['uuid'].'</td>';
	        $body.='<td>'.$value['reason'].'</td>';
	        $body.='<td>'.date('Y-m-d H:i:s', $sendTicket['consume_time']).'</td>';
	        $body.='<td>'.$sendTicket['denomination'].'</td>';
	        $body.='<td>'.$status.'</td>';
	        $body.='<td>'.$value['operator_name'].'</td>';
	        $body.= '</tr>';
	    }
	    $count = sprintf("%.2f", $count);
	    $body.= '<tr>';
	    $body.= '<td>合计:</td>';
	    $body.= '<td colspan="6" ><font color="#FF0000">'.$count.'</font> A券</td>';
	    $body.= '</tr>';
	    $body.= '</table>';
	    $body.= '</div>';
	    foreach($mails as $k=>$v){
	        Util_PHPMailer_SendMail::postEmail($v ,$title, $body);
	    }
	}
	
    echo CRON_SUCCESS;
    exit;
    
    
   
     
     