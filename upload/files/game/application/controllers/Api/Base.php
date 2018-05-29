<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Common_BaseController
 * @author rainkid
 *
 */
abstract class Api_BaseController extends Yaf_Controller_Abstract {
    
    const PERPAGE = 10;
    
	public $filter = array();//过滤游戏大厅包
	private $sTime = 0;
    /**
     * 
     * Enter description here ...
     */
    public function init() {
    	$this->sTime = microtime(true);
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
    	
    	//注册一个全局sp版本数据
    	$request = $this->getInput('sp');
    	if($request){
    		$sp = explode('_', $request);
    		Yaf_Registry::set("apkVersion", $sp[1]);
    		Yaf_Registry::set("androidVersion", $sp[3]);
    	}
       
	   Yaf_Dispatcher::getInstance()->disableView();
    }
    
    public function cache($data, $name) {
    	if (!$this->cacheKey) return false;
    	if (!count($data) || !$name) return false;
    	$file = sprintf("%scache/APPC_%s.php", Common::getConfig('siteConfig', 'dataPath'), $this->cacheKey);
    	 
    	$new_version = crc32(json_encode($data));
    	 
    	//如果文件存在，且版本号没有变化，则不更新
    	if (!file_exists($file)) $this->saveCacheFile($file, $name, $data);
    	 
    	$files = include $file;
    	if (crc32(json_encode($files[$name])) !== $new_version) $this->saveCacheFile($file, $name, $data);
    	return false;
    }
    
    private function saveCacheFile($file, $cacheName, $cacheData) {
    	if(file_exists($file)) {
    		$files = include $file;
    		if (!is_array($files)) $files = array();
    		$files[$cacheName] = array_unique($cacheData);
    	}
    
    	return Util_File::savePhpData($file, $files);
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
    	$domain = str_replace('http://','', Common::getWebRoot());
    	if($number = strrpos($domain,':'))  $domain = Util_String::substr($domain, 0, $number);
    	return $domain;
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
			if (strnatcmp($version, '1.5.4') >= 0){
				$flag = 6;
			}
			if (strnatcmp($version, '1.5.5') >= 0){
				$flag = 7;
			}
			if (strnatcmp($version, '1.5.6') >= 0){
				$flag = 8;
			}
			if (strnatcmp($version, '1.5.7') >= 0){
				$flag = 9;
			}
			if (strnatcmp($version, '1.5.8') >= 0){
			    $flag = 10;
			}
			if (strnatcmp($version, '1.5.9') >= 0){
			    $flag = 11;
			}
		}
		return $flag;
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
    
    
    public function localOutput($code, $msg = '',$data = array(), $sign='GioneeGameHall') {
    	header("Content-type:text/json");
    	exit(json_encode(array(
    			'success' => !$code  ? true : false ,
    			'msg' => $msg,
    			'sign' => $sign,
    			'data' => $data
    	)));
    }
    
    /**
     * 带版本控制的数据输出
     */
    public function versionOutput($code, $msg = '', $data = array(), $version = 0 , $sign='GioneeGameHall'){
    	header("Content-type:text/json");
    	exit(json_encode(array(
    			'success' => $code == 0 ? true : false ,
    			'msg' => $msg,
    			'sign' => $sign,
    			'version' => $version,
    			'data' => $data
    	)));
    }
    
    /**
     * 
     * @param unknown_type $code
     * @param unknown_type $msg
     * @param unknown_type $data
     */
    public function clientOutput($data = array()) {
    	header("Content-type:text/json");
    	if(!$data) exit();
    	exit(json_encode($data));
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
    	$cache = Cache_Factory::getCache(Cache_Factory::ID_LOCAL_APCU);
    	//艾米游戏过滤的游戏ID
    	if($client == 'amigame') {
    		$cfilter = $cache->get(Util_CacheKey::GAME_FILTER . $client);
    		if ($cfilter ===false){
    			$config = Game_Service_Config::getValue('game_filter_amigame');
    			$filter = $config ? explode('|', $config) : '';
    			$cache->set(Util_CacheKey::GAME_FILTER . $client, $filter, 5*60);
    			$cfilter = $filter;
    		}
    		return $cfilter;
    		
    	}
    	//游戏大厅过滤的游戏ID
    	if($client == 'gamehall') {
    		$cfilter = $cache->get(Util_CacheKey::GAME_FILTER . $client);
    		if ($cfilter ===false){
    			$config = Game_Service_Config::getValue('game_filter_gamehall');
    			$filter = $config ? explode('|', $config) : '';
    			$cache->set(Util_CacheKey::GAME_FILTER . $client, $filter, 5*60);
    			$cfilter = $filter;
    		}
    		return $cfilter;
    		
    	}
    	
    	//非游戏大厅跟艾米游戏要过滤的游戏ID
    	$cfilter = $cache->get(Util_CacheKey::GAME_FILTER . 'default');
    	if ($cfilter ===false){
    		$config = Game_Service_Config::getValue('game_filter_default');
    		$filter = $config ? explode('|', $config) : '';
    		$cache->set(Util_CacheKey::GAME_FILTER . 'default', $filter, 5*60);
    		$cfilter = $filter;
    	}
    	return $cfilter;
    }
    
    public function __destruct() {
    	$date       = date('Ymd');
    	$eTime      = microtime(true);
    	$module     = $this->getRequest()->getModuleName();
    	$controller = $this->getRequest()->getControllerName();
    	$action     = $this->getRequest()->getActionName();
    	$name       = sprintf("%s_%s_%s", $module, $controller, $action);
        $duration = $eTime - $this->sTime;
        if ($duration < 1) {
            $duration = round($duration, 1);
        } else {
            $duration = round($duration);
        }
    	//监控接口请求次数跟处理时间
        $aMonth = 2592000;
        Cache_Factory::getCache()->hIncrBy('MON_KEY_NUM:' . $date, $name, 1, $aMonth);
        Cache_Factory::getCache()->hIncrBy('MON_KEY_TIME:' . $date . ':' . $name, $duration, 1, $aMonth);
    }
    
    public function checkOnline($uuid) {
        $sp = $this->getInput('sp');
        $imei = end(explode('_',$sp));
        $online = false;
        if ($imei && $uuid) {
            $online = Account_Service_User::checkOnline($uuid, $imei, 'uuid');
        }
        
        if (! $online) {
            header("Content-type:text/json");
            exit(json_encode(array(
                            'success' => true ,
                            'msg' => '未登录',
                            'sign' => 'GioneeGameHall',
                            'code' => '0',
            )));
        }
    }
    
    public function getPageInput() {
        $page = intval($this->getInput('page'));
        return $page < 1 ? 1 : $page;
    }
    
    public function hasNext($total, $page) {
        return (ceil((int) $total / self::PERPAGE) - ($page)) > 0 ? true : false;
    }
}
