<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * WeiXin_Msg_Center
 *
 * 消息中心.
 */
class WeiXin_Msg_Center
{
	var $msg_signature;
	var $timestamp;
	var $nonce;
	var $pc;

	/**
	 * @param $msg_signature
	 * @param $timestamp
	 * @param $nonce
	 */
	public function __construct($msg_signature, $timestamp, $nonce) {
		$this->msg_signature = $msg_signature;
		$this->timestamp = $timestamp;
		$this->nonce = $nonce;
		$this->pc = new WeiXin_Msg_Crypt(WeiXin_Config::TOKEN, WeiXin_Config::ENCODING_AES_KEY, WeiXin_Config::APPID);
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

		$msg = ''; 
		$result = array();
		$errCode = $this->pc->decryptMsg($this->msg_signature, $this->timestamp, $this->nonce, $from_xml, $msg);
		if ($errCode != 0) return $errCode;
		if ($msg != '') {
			$result = WeiXin_Service_Base::xmlToArray($msg);
		}
		return WeiXin_Msg_Error::$OK;
	}

	/**
	 * 同步处理
	 * @param $msg
	 */
	public function dispatch($msg) {
		//事件处理
		Common::log($msg, "weixin.log");
		if  ($msg["MsgType"] == "event") {
			switch ($msg["Event"]) {
				//点击菜单事件
				case "CLICK":
					$this->clieckEvent($msg);
					break;
				//用户关注事件
				case "subscribe":
					$this->Subscribe($msg);
					break;
			}
		} else {
		    if(strpos($msg['Content'], '@') !== false) {
		        $keyword = strrchr($msg['Content'], '@');
		        $keyword = str_replace('@', '', $keyword);
		        $items = $this->searchGoods($keyword);
		        $tpl = new WeiXin_Msg_Tpl();
		        if($items) {
		            $xml = $tpl->getArtXML($msg, $items);
		        } else {
		            $xml = $tpl->getTextXML($msg, "暂无搜索结果，<a href='http://dhm.miigou.com/?source=search'>点击查看更多内容</a>");
		        }
		        exit($xml);
		        
		    } else {
		        $this->customer($msg);
		    }
			
		}
		exit("");
	}
	
	
	
	/**
	 * 获取商品数据
	 * @param unknown $keyword
	 */
	public function searchGoods($keyword) {
	    if(!$keyword) return false;
	    $params = array('keyword'=>urlencode($keyword));
	    $url = 'http://dhm.miigou.com/api/goods/index';
	    $curl = new Util_Http_Curl($url);
	    $result = $curl->get($params);
	    $result = json_decode($result, true);
	    $data = $result['data']['list'];
	    
	    if(!$data) return false;
        $items = array();
        $staticroot = Yaf_Application::app()->getConfig()->staticroot;
        $items = array(0=>array(
                        'title'=>'更多"'.$keyword.'"搜索结果，请点击这里',
                        'descritpion'=>'暂无搜索结果，点击查看更多内容',
                        'picurl'=>$staticroot.'/apps/findjoy/assets/img/search.png',
                        'url'=>'http://dhm.miigou.com/goods/index/?keyword='.$keyword
        ));
        if(count($data) > 7) $data = array_slice($data, 0, 7);
        $data = Common::resetKey($data, 'id');
        foreach ($data as $key=>$value) {
            $items[$key]['title'] = $value['title'] .' ￥'. $value['min_price'] . '-' . $value['max_price'];
            $items[$key]['description'] = $value['title'];
            $items[$key]['picurl'] = $value['img'];
            $items[$key]['url'] = $value['link'];
        }
	    
	    return $items;
	}

	/**
	 *转客服接待
	 */
	public function customer($msg) {
		$tpl = new WeiXin_Msg_Tpl();
		$xml = $tpl->getCustomerXML($msg);
		Common::log($xml, "weixin.log");
		exit($xml);
	}

	/**
	 *关注消息
	 */
	public function Subscribe($msg) {
		$user = new WeiXin_Server_User();
		$user->openid = $msg['FromUserName'];
		$user_info = $user->getInfo();
		Common::log($user_info, "weixin.log");

		$tpl = new WeiXin_Msg_Tpl();
		$xml = $tpl->getTextXML($msg, "Hi~您终于来啦~
			感谢关注大红帽~我们提供港货正品香港原价直销，集所有正品港货于您的指尖，满足您个性化需求！更多惊喜，快来了解体验吧...", $items);
		Common::log($xml, "weixin.log");
		exit($xml);
	}

	public function clieckEvent($msg) {
		$tpl = new WeiXin_Msg_Tpl();
		$xml = "";
		if ($msg["EventKey"] == "FJ_ONE_KEY") {
			$xml = $tpl->getTextXML($msg, "如需了解商品详情信息，请回复 @商品名称（如@苹果）或<a href='http://dhm.miigou.com/?source=daimai'>戳这里</a>\n如需购买，请回复商品信息（名称规格数量等），我们确认库存后，将第一时间把商品添加至您的购物车并通知您");
		} else if ($msg["EventKey"] == "FJ_QUESTION") {
			$xml = $tpl->getTextXML($msg, "大红帽专注于提升跨境购物客的购物体验，是首家提供买手制的香港商品购物平台，线上选品，一站提货，让您再也不用各种店前排队等待。另外，大红帽还独家提供一键代买功能，超低的代买价格，为您创造便捷、个性化、高性价比的香港商品购物体验！\n问题咨询请直接在大红帽公众号下留言!大红帽在线服务时间：周一至周五9:00-18:00");
		} else if ($msg["EventKey"] == "FJ_SEARCH") {
		    $xml = $tpl->getTextXML($msg, "如需了解商品详情信息，请回复 @商品名称（如@苹果）");
		}
		Common::log($xml, "weixin.log");
		exit($xml);
	}

	/**
	 * @param $data
	 */
	public function send($data) {
		$ret = WeiXin_Service_Base::sendMsg($data);
		Common::log($ret, "weixin.log");
	}

}


?>
