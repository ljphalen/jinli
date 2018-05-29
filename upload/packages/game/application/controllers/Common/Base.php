<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Common_BaseController
 * @author rainkid
 *
 */
abstract class Common_BaseController extends Yaf_Controller_Abstract {
    public $actions = array(); 
    public $filter = array();
    
    /**
     * 
     * Enter description here ...
     */
    public function init() {
		$webroot = Common::getWebRoot();
		$sp = $this->getInput('sp');
		$item = explode('_', $sp);
		if($sp){
			//注册一个全局sp版本数据
			Yaf_Registry::set("apkVersion", $item[1]);
		}
		//注册参数
		$this->assign("apk_version", $item[1]);
		Util_Cookie::set("apk_version", $item[1], true, Common::getTime() + 30);
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
    	$this->assign("webroot", $webroot);
		$this->assign("staticPath", $staticroot . '/apps/');
		$this->assign("attachPath", Common::getAttachPath());
		$this->assign('token', Common::getToken());
		$this->assign('version', Common::getConfig('siteConfig', 'version'));
		//init actions
		foreach ($this->actions as $key=>$value) {
			$this->assign($key, $value);
		}
        if($this->checkAppVersion() >= 4){
    		//初始化过滤
    		$package = $this->getInput('client_pkg');
    		//访问客户端设备
    		$client = $this->getRequestClient($package);
    		//根据访问客户端设备初始化过滤游戏id
    		$this->filter = $this->getFilterGame($client);
    	}else{
    		//根据访问客户端设备初始化过滤游戏id
    		$this->filter = $this->getFilterGame();
    		
    	}
    	
    	$this->assign('client', $client);
    	$this->assign('client_pkg', $package);
    	//根据访问客户端设备初始化过滤游戏id
		
		if ($this->isAjax()) {
			Yaf_Dispatcher::getInstance()->disableView();
		}
    }

    /**
     * 
     * Enter description here ...
     * @param unknown_type $var
     * @param unknown_type $value
     */
    public function assign($var, $value) {
        $this->getView()->assign($var, $value);
    }
    
    /**
     * 请求客户端判断
     * @param unknown $package
     */
    public function getRequestClient($package){
    	//艾米游戏客户端请求
    	if ($package == 'com.android.amigame') return 'amigame';
    	//游戏大厅客户端请求
    	if ($package == 'gn.com.android.gamehall') return 'gamehall';
    	//其他浏览器或设备请求
    	return 'other';
    }

    /**
     * 根据访问客户端获取过滤游戏ID
     * @param unknown $client
     * @return Ambigous <multitype:, mixed>
     */
    public function getFilterGame($client = ''){
    	$filter = $cfilter = $config =  array();
    	//增加缓存处理
    	$cache = Common::getCache();
    	//艾米游戏过滤的游戏ID
    	if($client == 'amigame') {
    		$cfilter = $cache->get("-gf-". $client);
    		if ($cfilter ===false){
    			$config = Game_Service_Config::getValue('game_filter_amigame');
    			$filter = $config ? explode('|', $config) : '';
    			$cache->set("-gf-". $client, $filter, 5*60);
    			$cfilter = $filter;
    		}
    		return $cfilter;
    		
    	}
    	//游戏大厅过滤的游戏ID
    	if($client == 'gamehall') {
    		$cfilter = $cache->get("-gf-". $client);
    		if ($cfilter ===false){
    			$config = Game_Service_Config::getValue('game_filter_gamehall');
    			$filter = $config ? explode('|', $config) : '';
    			$cache->set("-gf-". $client, $filter, 5*60);
    			$cfilter = $filter;
    		}
    		return $cfilter;
    		
    	}
    	
    	//非游戏大厅跟艾米游戏要过滤的游戏ID
    	$cfilter = $cache->get("-gf-default");
    	if ($cfilter ===false){
    		$config = Game_Service_Config::getValue('game_filter_default');
    		$filter = $config ? explode('|', $config) : '';
    		$cache->set("-gf-default", $filter, 5*60);
    		$cfilter = $filter;
    	}
    	return $cfilter;
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
               		$return[$value[0]] = Util_Filter::post($value[0], $value[1]);
               } else {
					$return[$value] = Util_Filter::post($value);;
               }
            }
            return $return;
        }
        return null;
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
     * 请求是否是ajax
     */
    public function isAjax() {
        return $this->getRequest()->isXmlHttpRequest() || $this->getInput("callback");
    }
    
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params
	 */
	public function showMsg($code, $msg = '') {
		throw new Yaf_Exception($msg, $code);
	}
	/**
	 * 根据游戏大厅不同版本号返回不同值用于区别输出
	 * return int
	 * 返回值：
	 *        0  sp参数不存在
	 * 		  1  游戏大厅版本不存在或低于 1.4.8
	 *        2  游戏大厅版本大于等于1.4.8
	 *        3  游戏大厅版本大于等于1.5.1
	 *        4  游戏大厅版本大于等于1.5.2
	 */
	protected function checkAppVersion(){
		$flag = 0;
		$request = $this->getInput('sp');
		if($request){
			$sp = explode('_', $request);
			$version = $sp[1];
			if(empty($version)||(strnatcmp($version, '1.4.8') < 0))	{
				$flag = 1;
			}
			if (strnatcmp($version, '1.4.8') >= 0){
				$flag = 2;
			}
			if (strnatcmp($version, '1.5.1') >= 0){
				$flag = 3;
			}
			if (strnatcmp($version, '1.5.2') >= 0){
				$flag = 4;
			}
			if (strnatcmp($version, '1.5.3') >= 0){
				$flag = 5;
			}
			if (strnatcmp($version, '1.5.3') >= 0){
				$flag = 5;
			}
			if (strnatcmp($version, '1.5.4') >= 0){
				$flag = 6;
			}
			if (strnatcmp($version, '1.5.5') >= 0){
				$flag = 7;
			}
		}
		return $flag;
	}
}
