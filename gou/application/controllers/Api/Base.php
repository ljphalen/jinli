<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Common_BaseController
 * @author rainkid
 *
 */
abstract class Api_BaseController extends Common_BaseController {
    
	public $cacheKey = '';
	public $t_bi = '';
	public $errors = array();

    /**
     * 
     * Enter description here ...
     */
    public function init() {
        Yaf_Dispatcher::getInstance()->disableView();

        $controller = $this->getRequest()->getControllerName();
        $action = $this->getRequest()->getActionName();
        if($controller == 'Fitting' && $action == 'goods') {
            $cid = $this->getInput("cid");
            Util_Cookie::set('FCID', $cid, true, Common::getTime()+86400, '/');
        }

        //生成唯一用户标识
       //Common::uniqueUser();
        Common::createUserUid();

        $this->t_bi = Util_Cookie::get('GOU-SOURCE', true);
    }
    
    public function cache($data, $name) {
    	if (!$this->cacheKey) return false;
    	if (!count($data) || !$name) return false;
    	$file = sprintf("%scache/APPC_%s.php", Common::getConfig('siteConfig', 'dataPath'), $this->cacheKey);
    	
    	$cacheData = array();
    	foreach($data as $key=>$value) {
    		$v = parse_url($value);
    		$cacheData[] = $v['path'];
    	}
    	$new_version = crc32(json_encode($cacheData));
    	
    	//如果文件存在，且版本号没有变化，则不更新
    	if (!file_exists($file)) $this->saveCacheFile($file, $name, $cacheData);
    	
    	$files = include $file;
    	if (crc32(json_encode($files[$name])) !== $new_version) $this->saveCacheFile($file, $name, $cacheData);
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
     * @param mixed $var
     * @return mixed|null
     */
    private static function getVal($var) {
    	$value = Util_Filter::post($var);
    	if(!is_null($value)) return $value;
    	$value = Util_Filter::get($var);
    	if(!is_null($value)) return $value;
    	return null;
    }
    

    /**
     * @param int $code
     * @param string $msg
     * @param array $data
     */
    public function output($code, $msg = '', $data = array()) {
            $callback = $this->getInput('callback');
            $out      = array(
                'success' => $code == 0 ? true : false,
                'msg'     => $msg,
                'data'    => $data
            );
            if ($callback) {
                header("Content-type:text/javascript");
                exit($callback . '(' . json_encode($out) . ')');
            } else {
                header("Content-type:text/json");
                exit(json_encode($out));
            }
    }
    

    /**
     * @param int $code
     * @param string $msg
     */
    public function emptyOutput($code, $msg = '') {
        header("Content-type:text/json");
        exit(json_encode(array(
        'success' => $code == 0 ? true : false ,
        'msg' => $msg
        )));
    }
    

    /**
     * @param int $code
     * @param string $msg
     * @param array $data
     */
    public function clientOutput($code, $msg = '', $data = array()) {
    	header("Content-type:text/json");
    	exit(json_encode(array(
    			'success' => $code == 0 ? true : false ,
    			'code' => $code,
    			'msg' => $msg,    			
    			'data' => $data    			
    	)));
    }

    /**
     * @param int $code
     * @param string $msg
     * @param array $data
     */
    public function standOutput($code, $msg = '', $data = array()) {
        header("Content-type:text/json");
        exit(json_encode(array(
            'success' => $code == 0 ? true : false ,
            'msg' => $msg,
            'data' => $data
        ), JSON_FORCE_OBJECT));
    }

    /**
     * output an error
     * @param string $err
     */
    protected function showError($err) {
    	list($code, $msg) = $this->errors[$err];
    	$this->clientOutput($code, $msg);
    }
    
    /**
     *
     * @param string $click_url
     * @return string
     */
    public function getTaobaokeUrl($click_url) {
    	$sid = Gou_Service_User::getUserSid();
    	return sprintf("%s&ttid=%s&sid=%s", $click_url, Common::getConfig('apiConfig', 'taobao_taobaoke_ttid'), $sid);
    }
    
    
    /**
     *
     */
    protected function checkSource() {
    	$source = $this->getInput("source");
    	$source = str_replace(" ", "+", $source);
    	if ($source) Gou_Service_Channel::cookieChannel($source);
    }
    
    /**
     *获取outer_code
     */
    public function getOuterCode() {
    	$uid = 0;
    	$channel_id = Gou_Service_Channel::getChannelId();
    	$user = Gou_Service_User::isLogin();
    	if ($user) $uid = $user['id'];
    	return sprintf("%sH%s", intval($channel_id), $uid);
    }
}
