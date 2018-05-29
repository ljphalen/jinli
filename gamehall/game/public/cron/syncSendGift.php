<?php
   include 'common.php';
   //将1.5.8赠送的礼包同步到game_client_gift_log表中

   $page = 1;
   do {
       $params = $myGrabGiftLogs = array();
       $params['status'] = 1;
       list($total, $myGifts) = Client_Service_GiftActivityLog::getList($page, 100, $params);
       foreach($myGifts as $key=>$value){
           if($value){
               $myGrabGiftLogs[] =  array(
                       'id' => '',
                       'log_type' => Client_Service_Giftlog::SEND_GIFT_LOG,
                       'gift_id' => $value['gift_id'],
                       'game_id' => $value['game_id'],
                       'uname' => $value['uname'],
                       'imei'=>$value['imei'],
                       'imeicrc'=>$value['imeicrc'],
                       'activation_code'=>$value['activation_code'],
                       'create_time'=>$value['create_time'],
                       'status'=>$value['status'],
                       'send_order'=>$value['send_order'],
               );
           }
       }
       $ret_log = Client_Service_Giftlog::mutiGiftlog($myGrabGiftLogs);
       
       $page++;
   } while ($total>(($page -1) * 100));
    
   
    echo CRON_SUCCESS;
    exit;
