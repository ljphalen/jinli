<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * WeiXin_Msg_Center
 *
 * 消息中心.
 */
class WeiXin_Msg_Center
{
    const MSG_SHOW_GIFT_COUNT = 3;
    
	var $msg_signature;
	var $timestamp;
	var $nonce;
	var $pc;
	
	var $logFileName;

	/**
	 * @param $msg_signature
	 * @param $timestamp
	 * @param $nonce
	 */
	public function __construct($msg_signature, $timestamp, $nonce) {
		$logFileName = WeiXin_Config::getLogFileName();
		$this->msg_signature = $msg_signature;
		$this->timestamp = $timestamp;
		$this->nonce = $nonce;
		$this->pc = new WeiXin_Msg_Crypt(WeiXin_Config::getToken(), WeiXin_Config::ENCODING_AES_KEY, WeiXin_Config::getAppID());
	}

	/**
	 *
	 * @param $from_xml
	 * @param $result
	 * @return int
	 */
	public function decryptMsg($from_xml, &$result) {
		if ($from_xml == '') {
			return WeiXin_Msg_Error::$EmptyData;
		}
		$result = WeiXin_Service_Base::xmlToArray($from_xml);
		return WeiXin_Msg_Error::$OK;
// 		$msg = ''; 
// 		$result = array();
// 		$errCode = $this->pc->decryptMsg($this->msg_signature, $this->timestamp, $this->nonce, $from_xml, $msg);
// 		if ($errCode != 0) return $errCode;
// 		if ($msg != '') {
// 			$result = WeiXin_Service_Base::xmlToArray($msg);
// 		}
// 		return WeiXin_Msg_Error::$OK;
	}

	/**
	 * 同步处理
	 * @param $msg
	 */
	public function dispatch($msg) {
		//事件处理
		Common::log($msg, $this->logFileName);
		$msgType = $msg["MsgType"];
		if ($msgType == "text") {
		    $keyWord = Admin_Service_Keyword::match($msg['Content']);
		    $this->outputByKeyWord($msg, $keyWord);
		} else if ($msgType == "event") {
            $this->handlerEventMsg($msg);
		}
		exit("");
	}
	
	private function handlerEventMsg($msg) {
	    switch ($msg["Event"]) {
	        //点击菜单事件
	    	case "CLICK":
	    	    $keyWordId = intval($msg['EventKey']);
	    	    $keyWord = Admin_Service_Keyword::get($keyWordId);
	    	    $this->outputByKeyWord($msg, $keyWord);
	    	    break;
	    	    //用户关注事件
	    	case "subscribe":
                $this->subscribe($msg);
                break;
	    	case "unsubscribe":
	    	    $this->deleteUser($msg);
	    	    break;
	    }
	    exit('');
	}
	
	private function outputByKeyWord($msg, $keyWord) {
	    if ($keyWord) {
    	    switch ($keyWord['opt_type']){
    	    	case 1 :
    	    	    $this->replyImgText($msg, $keyWord['material_id']);
    	    	    break;
    	    	case 2 :
    	    	    $this->replySysEvent($msg, $keyWord['sys_event']);
    	    	    break;
        	    case 3 :
        	        $this->replyText($msg, $keyWord['text_content']);
        	        break;
    	    }
	    }
	}
	
	private function replyImgText($msg, $materialId) {
	    $items = Admin_Service_Material::getItems($materialId);
	    $news = array();
	    foreach ($items as $item) {
	        $newItem = array();
	        $newItem['title'] = $item['title'];
	        $newItem['description'] = $item['digest'];
	        $newItem['picurl'] = Common::getAttachPath().$item['image'];
	        $url = '';
	        if ($item['type'] == 2) {
	        	$url = html_entity_decode($item['content_url']);
	        } else if($item['type'] == 1) {
	            $url = Admin_Service_Gift::getGiftUrl($item['gift_bag_id'], $msg['FromUserName']);
	        }
	        $newItem['url'] = $url;
	        $news[] = $newItem;
	    }
	    if ($news) {
    	    $tpl = new WeiXin_Msg_Tpl();
    	    $xml = $tpl->getArtXML($msg, $news);
    	    Common::log($xml, $this->logFileName);
    	    exit($xml);
	    } 
	}
	
	private function replySysEvent($msg, $sysEventCode) {
	    if ($sysEventCode == 1) {
	        $this->replyACion($msg);
	    } else {
	        $this->replyMyGift($msg);
	    }
	}

	private function replyACion($msg) {
	    $openId = $msg['FromUserName'];
	    $user = Admin_Service_Weixinuser::getByOpenId($openId);
	    $tpl = new WeiXin_Msg_Tpl();
	    $xml = '';
	    if($user['is_binded'] && $user['binded_uuid']) {
	        $bindInfo = Admin_Service_Weixinuser::getBindInfo($user['binded_uuid']);
	        if ($bindInfo) {
	            $helpIndexs = $this->getHelpIndexs();
	        	$xml = $tpl->getACionTextXML($msg, $bindInfo, $helpIndexs);
	        } else {
	            $xml = $tpl->getTextXML($msg, "暂无数据!");
	        }
	    } else {
	        $xml = $tpl->getLoginTextXml($msg);
	    }
	    Common::log($xml, $this->logFileName);
	    exit($xml);
	}
	
	private function getHelpIndexs() {
	    $index = array();
	    for ($i = 1; $i<=4; $i++) {
	        if (Admin_Service_Keyword::match(strval($i))) {
    	        $index[] = strval($i);
	        }
	    }
	    return $index;
	}
	
	private function replyMyGift($msg) {
	    $openId = $msg['FromUserName'];
	    $user = Admin_Service_Weixinuser::getByOpenId($openId);
	    $tpl = new WeiXin_Msg_Tpl();
	    $xml = '';
	    if($user['is_binded'] && $user['binded_uuid']) {
	        $myGife = self::getUserGiftCode($openId, $user['binded_uuid']);
	        $myGiftListUrl = Admin_Service_Gift::getMyGiftListUrl($openId);
	        $xml = $tpl->getMyGiftTextXml($msg, $myGife, $myGiftListUrl);
	    } else {
	        $xml = $tpl->getLoginTextXml($msg);
	    }
	    Common::log($xml, $this->logFileName);
	    exit($xml);
	}
	
	private static function getUserGiftCode($openId, $uuid) {
	    $qureyParams = array(
	                    'owner_uuid' => $uuid,
	                    'status' => Admin_Service_GiftGrabLog::STATUS_SENDED
	    );
	    list($total, $giftCodes) = Admin_Service_GiftGrabLog::getList(1, self::MSG_SHOW_GIFT_COUNT, $qureyParams, array('update_time'=>'DESC'));
	    if (!$giftCodes) {
	    	return false;
	    }
	    
	    $result = array();
	    foreach ($giftCodes as $code) {
	        $gift = Admin_Service_Gift::getById($code['gift_bag_id']);
	        $result[] = array(
	                        'title' => $gift['title'],
	                        'code' => $code['code'],
	                        'url' => Admin_Service_Gift::getGiftUrl($gift['id'], $openId)
	        );
	    }
	    return $result;
	}
	
	/**
	 *关注消息
	 */
	private function subscribe($msg) {
		$user = new WeiXin_Server_User($msg['FromUserName']);
		$user_info = $user->syncInfo();
        $subscribeSet = Admin_Service_Automsg::getby(array('type' => 1));
        if (!$subscribeSet) {
        	exit("");
        }
        
        if($subscribeSet['opt_type'] == 1) {
            $this->replyImgText($msg, $subscribeSet['material_id']);
        } else if($subscribeSet['opt_type'] == 3) {
    		$this->replyText($msg, $subscribeSet['text_content']);
        }
	}
	
	private function replyText($msg, $content) {
	    $tpl = new WeiXin_Msg_Tpl();
	    $xml = $tpl->getTextXML($msg, $content);
	    Common::log($xml, $this->logFileName);
	    exit($xml);
	}
	
	private function deleteUser($msg) {
	    Admin_Service_Weixinuser::deleteByOpenId($msg['FromUserName']);
	}
	
	private function textTest($msg) {
	    $tpl = new WeiXin_Msg_Tpl();
	    $xml = $tpl->getTextXML($msg, $msg['Content']."\n无匹配结果!");
	    Common::log($xml, $this->logFileName);
	    exit($xml);
	}
	
}


?>
