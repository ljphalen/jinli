<?php 

class IGtBaseTemplate{
	var $appId;
	var $appkey;
	var $pushInfo;
	
	function get_transparent() {
		$transparent = new Transparent();
		$transparent->set_id('');
		$transparent->set_messageId('');
		$transparent->set_taskId('');
		$transparent->set_action('pushmessage');
		$transparent->set_pushInfo($this->get_pushInfo());
		$transparent->set_appId($this->appId);
		$transparent->set_appKey($this->appkey);

		$actionChainList = $this->getActionChain();
		
		foreach($actionChainList as $index=>$actionChain){
			$transparent->add_actionChain();
			$transparent->set_actionChain($index,$actionChain);
		}
		return $transparent->SerializeToString();
	}

	function  get_transmissionContent() {
		return null;
	}
	
	function  get_pushType() {
		return null;
	}

	function get_actionChain() {
		return null;
	}

	function get_pushInfo() {
		if ($this->pushInfo==null) {	
			$this->pushInfo = new PushInfo();
			$this->pushInfo->set_actionKey('');
			$this->pushInfo->set_badge('');
			$this->pushInfo->set_message('');
			$this->pushInfo->set_sound('');
		}

		return $this->pushInfo;
	}

	function set_pushInfo($actionLocKey,$badge,$message,$sound,$payload,$locKey,$locArgs,$launchImage) {
		$this->pushInfo = new PushInfo();
		$this->pushInfo->set_actionLocKey($actionLocKey);
		$this->pushInfo->set_badge($badge);
		$this->pushInfo->set_message($message);
		if ($sound!=null) {
			$this->pushInfo->set_sound($sound);
		}
		if ($payload!=null) {
			$this->pushInfo->set_payload($payload);
		}
		if ($locKey!=null) {
			$this->pushInfo->set_locKey($locKey);
		}
		if ($locArgs!=null) {
			$this->pushInfo->set_locArgs($locArgs);
		}
		if ($launchImage!=null) {
			$this->pushInfo->set_launchImage($launchImage);
		}
		$isValidate = $this->validatePayload($locKey, $locArgs, $message, $actionLocKey, $launchImage, $badge, $sound, $payload);
		if (!$isValidate) {
			$payloadLen = $this->validatePayloadLength($locKey, $locArgs, $message, $actionLocKey, $launchImage, $badge, $sound, $payload);
			$errorMsg = "PushInfo length over limit: " . (string)$payloadLen . ". Allowed: 256.";
			echo $errorMsg;
			throw new Exception($errorMsg);
		}
	}
	
	function  set_appId($appId) {
		$this->appId = $appId;
	}

	function  set_appkey($appkey) {
		$this->appkey = $appkey;
	}
	
	function processPayload($locKey, $locArgs, $message, $actionLocKey, $launchImage, $badge, $sound, $payload) {
		$map = array();
		$apnsMap = array();
		$alertMap = array();
		
		if ($sound!=null && strlen($sound)>0) {
			$apnsMap["sound"] = $sound;
		} else {
			$apnsMap["sound"] = "default";
		}
		
		if ($launchImage!=null && strlen($launchImage)>0) {
			$alertMap["launch-image"] = $launchImage;
		}
		if ($locKey!=null && strlen($locKey)>0) {
			$alertMap["loc-key"] = $locKey;
			if ($locArgs!=null && strlen($locArgs)>0) {
				$alertMap["loc-args"] = explode(",",$locArgs);
			}
		} elseif ($message!=null && strlen($message)>0) {
			$alertMap["body"] = urlencode($message);
		}
		
		if ($actionLocKey!=null && strlen($actionLocKey)>0) {
			$alertMap["action-loc-key"] = $actionLocKey;
		}
		
		$apnsMap["badge"] = $this->toInt($badge,0);
		
		if ($payload!=null && strlen($payload)>0) {
			$map["payload"] = $payload;
		}
		$apnsMap["alert"] = $alertMap;
		$map["aps"] = $apnsMap;
		
		
		return $map;
	
	}
	
	function validatePayload($locKey, $locArgs, $message, $actionLocKey, $launchImage, $badge, $sound, $payload) {
		$map = $this->processPayload($locKey, $locArgs, $message, $actionLocKey, $launchImage, $badge, $sound, $payload);
		$json = urldecode(json_encode($map));
		if (strlen($json)>256) {
			return false;
		}
		return true;
	}
	
	function validatePayloadLength($locKey, $locArgs, $message, $actionLocKey, $launchImage, $badge, $sound, $payload) {
		$map = $this->processPayload($locKey, $locArgs, $message, $actionLocKey, $launchImage, $badge, $sound, $payload);
		$json = urldecode(json_encode($map));
		return strlen($json);
	}
	
	function toInt($str, $defaultValue)
	{
		if ($str==null || $str=="") {
			return defaultValue;
		}
		return intVal($str);
	}
	
	function abslength($str)
	{
		if(empty($str)){
			return 0;
		}
		if(function_exists('mb_strlen')){
			return mb_strlen($str,'utf-8');
		}
		else {
			preg_match_all("/./u", $str, $ar);
			return count($ar[0]);
		}
	}
	
	
}