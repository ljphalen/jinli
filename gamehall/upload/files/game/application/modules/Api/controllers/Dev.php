<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class DevController extends Api_BaseController {
    private $logFile = '';
    private $lockExpireTime = 10;
    const ACTION_ON = 0;
    const ACTION_OFF = 1;
    
    /**
     * 添加（上线）游戏
     * 开发平台收到的返回结果不是“ok”（包括超时），则会重发（最大3次）
     */
    public function onAction() {
    	$this->logFile = date('Y-m-d') . '_dev.on.log';
	    $this->_actionProc(self::ACTION_ON);
    }
    
    /**
     * 关闭（下线）游戏
     * 开发平台收到的返回结果不是“ok”（包括超时），则会重发（最大3次）
     */
    public function offAction() {
        $this->logFile = date('Y-m-d') . '_dev.off.log';
        $this->_actionProc(self::ACTION_OFF); 
    }
    
    /**
     * appid转game_id
     * @return Ambigous <>
     */
    public function convertAction() {
        $this->logFile = date('Y-m-d') . '_dev.convert.log';
    	$info = $this->getInput(array('id', 'sign'));
    	self::_checkInput($info);
    	//通过appid找到game_id
    	$game = Resource_Service_Games::getBy(array('appid'=>$info['id']));
    	if(!$game) exit;
    	echo $game['id'];
    }
    
    /**
     * 处理游戏上、下线相同的处理
     * @param $action   ACTION_ON | ACTION_OFF
     */
    private function _actionProc($action) {
        $info = $this->getInput(array('id', 'sign'));
        self::_checkInput($info);
         
        if (!self::_lock($info['id'])) {
            Common::log(array("cannot get key({$info['id']})'s lock!"), $this->logFile);
            return;
        }
        
        try {
            if ($action == self::ACTION_ON) {
                Dev_Service_Sync::setGameOn($info['id']);
            } else {
                Dev_Service_Sync::setGameOff($info['id']);
            }
        } catch (Exception $e) {
            Common::log(array("catch exception:{$e}"), $this->logFile);
        }
        
        self::_unlock($info['id']);
        Common::log(array("finish!"), $this->logFile);
    }

    /**
     * 检查请求参数是否合法
     * @param unknown $info
     */
    private function _checkInput($info) {
        Common::log(array("appid: {$info['id']}", "sign: {$info['sign']}"), $this->logFile);
        
        if(!$info['id']) $this->output(-1, 'error dev appid', array());
        $params = array("id"=>$info['id']);
        $rsa = new Util_Rsa();
        $ret = $rsa->verifySign($params, $info['sign'], Common::getConfig("siteConfig", "rsaPubFile"));
        if (!$ret) $this->output(-1, 'error token.', array());
    }
    
    /**
     * 上下线同一个游戏，使用同一个互斥锁
     * @param unknown $key
     * @return boolean 是否获得锁
     */
    private function _lock($key) {
        $cache = Cache_Factory::getCache();
        return $cache->lock("dev:".$key, $this->lockExpireTime);
    }
    
    private function _unlock($key) {
        $cache = Cache_Factory::getCache();
        $cache->unlock("dev:".$key);
    }
}