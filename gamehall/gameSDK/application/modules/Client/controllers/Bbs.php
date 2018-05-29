<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class BbsController extends Client_BaseController {
	
	/**
	 * 论坛定制首页接口地址
	 * code: GameHall_uuid_pk  //组成格式
	 */
	public function indexAction(){
		if(ENV == 'product'){
			$url = "http://bbs.amigo.cn/forum.php?mod=forumdisplay&fid=150&fromapp=game";
		}else{
			$url = "http://t-bbs.amigo.cn/forum.php?mod=forumdisplay&fid=150&fromapp=game";
		}
		
		$code = $this->getInput('code');
		$token = '';
		if($code){
			$token = $this->_getAstoken($code);
		}
		//解密还原
		if($token) $url .= "&token=".$token;
		$this->redirect($url);
		exit;
	}

	/**
	 * 游戏详细论坛接口地址
	 */
	public function blockAction(){
		$gid = $this->getInput('id');
		$code = $this->getInput('code');
		
		//取得游戏的url
		$ret = Bbs_Service_Bbs::getBy(array('game_id'=>$gid));
		$url = $ret['url'];
		if($url){
			if(stripos($url,'?')){
				$url .= '&fromapp=game';
			}else{
				$url .= '?fromapp=game';
			}
		}
		//获取游戏对应的论坛地址
		$token = '';
		if($code){
			$token = $this->_getAstoken($code);
		}
		//解密还原
		if($token) $url .= "&token=".$token;
		$this->redirect($url);
		exit;
	}
	
	
	/**
	 * 游戏apk地址
	 */
    public function apkAction() {
    	$gid = $this->getInput('id');
    	$from = $this->getInput('from');
		$game = $this->_getGame($gid);
		$url = $game ? $game['link'] : 'http://game.gionee.com'; //非法数据跳入游戏主站
		//跳转地址
		if ($url) $this->redirect($url);
    }
  
    /**
     * 根据code获取联合登陆token
     * @param string $data
     * @return string
     */
    private function _getAstoken($code){
    	$token = '';
    	$plain = Util_Aes::decryptText($code);
    	$params = explode('_', $plain);
    	if($params[0] != 'GameHall') return $token;
    	$uuid = $params[1];
    	$pk = $params[2];
    	
    	//获取联合登陆授权信息。
    	if(!$uuid || !$pk) return $token;
    	//联合登陆
    	$config = Common::getConfig('accountConfig');
    	$a = $config['BBSAppId'];
    	$ret = Api_Gionee_Account::assoc($a, $uuid, $pk);
   		$token = $ret['as'] ? base64_encode(json_encode($ret['as'])) : '';
 		return $token;
    }
    
    /**
     * 获取游戏数据
     * @param int $gid
     * @return array 
     */
    private function _getGame($gid){
    	$data = Resource_Service_Games::getGameAllInfo(array('id'=>$gid));
    	return $data;
    }
}
