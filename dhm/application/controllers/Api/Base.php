<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Common_BaseController
 * @author rainkid
 *
 */
abstract class Api_BaseController extends Yaf_Controller_Abstract {
    
    /**
     * 
     * Enter description here ...
     */
    public function init() {
			Yaf_Dispatcher::getInstance()->disableView();
    }
    
    /**
     * 
     * 获取post参数 
     * @param string/array $var
     */
    public function getPost($var) {
        if(is_string($var)) return Util_Filter::post($var);
        $return = array();
        if (is_array($var)) {
            foreach ($var as $key=>$value) {
               if (is_array($value)) {
               		$return[$value] = Util_Filter::post($value[0], $value[1]);
               } else {
					$return[$value] = Util_Filter::post($value);;
               }
            }
            return $return;
        }
        return null;
    }
    
    public function setSource() {
    	$source = $this->getInput('source');
    	$from = $this->getInput('from');
    
    	if($from) $source = $from;
    
    	$sid = $this->getSource();
    
    	if ($sid) {
    		$sid_arr = explode('_', $sid);
    		$sid_arr[0] = $source;
    		$string = implode('_', $sid_arr);
    	} else {
    		$uid = crc32(uniqid());
    		$string = sprintf("%s_%s", $source, $uid);
    	}
    	return Util_Cookie::set('GAME-SOURCE', $string, false, strtotime("+30 day"), '/', $this->getDomain());
    }
    
    public function getSource() {
    	return Util_Cookie::get('GAME-SOURCE', false);
    }
    
    /**
     *
     * @return boolean
     */
    public function getDomain() {
    	$domain = str_replace('http://','',Yaf_Application::app()->getConfig()->webroot);
    	if($number = strrpos($domain,':'))  $domain = Util_String::substr($domain, 0, $number);
    	return $domain;
    }
    
    /**
     * 
     * 获取get参数
     * @param string $var
     */
    public function getInput($var) {
    	if(is_string($var)) return self::getVal($var);
    	if (is_array($var)) {
    		$return = array();
    		foreach ($var as $key=>$value) {
    			$return[$value] = self::getVal($value);
    		}
    		return $return;
    	}
    	return null;
    }
    
    /**
     * 
     * @param unknown_type $var
     * @return unknown|NULL
     */
    private static function getVal($var) {
    	$value = Util_Filter::post($var);
    	if(!is_null($value)) return $value;
    	$value = Util_Filter::get($var);
    	if(!is_null($value)) return $value;
    	return null;
    }
    
    /**
     *
     * Enter description here ...
     * @param unknown_type $code
     * @param unknown_type $msg
     * @param unknown_type $data
     */
    public function output($code, $msg = '', $data = array()) {
    	header("Content-type:text/json");
    	exit(json_encode(array(
    			'success' => $code == 0 ? true : false ,
    			'msg' => $msg,
    			'data' => $data
    	)));
    }
}
