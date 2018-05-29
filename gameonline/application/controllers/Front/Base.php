<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Front_BaseController extends Common_BaseController {		
	private  $url = array('tjUrl' => '/Front/Index/tj/'); 
	public $nav='';
	public $tj_object = array('Index_index'=>'homepage',
			                  'News_index'=>'newslist',
							  'News_detail'=>'newsdetail',
							  'Category_index'=>'category',
							  'Game_detail'=>'gamedetail',
			                  'Android_index'=>'android',
			                  'About_index'=>'about',  
							  'About_contact'=>'contact',
							  'About_disclaimer'=>'disclaimer',
							  'About_fcm'=>'fcm',
							  'About_apply'=>'apply',
							  'About_questions'=>'questions',
							  'About_feedback'=>'feedback',
			                  'Search_index'=>'searchlist',
			                  'Search_detail'=>'isearch'
			                 );

	
	/**
	 * 
	 * Enter description here ...
	 */
	public function init() {
		parent::init();
		$webroot = Common::getWebRoot();
		//获取H5的webroot
		$h5_webroot = Yaf_Application::app()->getConfig()->webroot;
		$is_mobile = Common::checkMobileRequest();
		$action = $this->getInput('action');
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action_name =  $this->getRequest()->getActionName();
		//判断pc端还是移动端
		//新闻资讯跳转
		if( $is_mobile  && strtolower($module) == 'front' && strtolower($controller) == 'news' && strtolower($action_name) == 'detail' ){
			$id = $this->getInput('id');
			if($id){
				$this->redirect($h5_webroot.'/game/news/detail/?id='.$id.'&intersrc=pcweb');
				exit;
			}
		}
		//游戏详情跳转
		if( $is_mobile  && strtolower($module) == 'front' && (strtolower($controller) == 'game' || strtolower($controller) == 'search') && strtolower($action_name) == 'detail' ){
			$id = $this->getInput('id');
			if($id){
				$this->redirect($h5_webroot.'/game/index/detail/?id='.$id.'&intersrc=pcweb');
				exit;
			}
		}
		//其它跳转到首页
		if($is_mobile && !((strtoupper($action) == 'QR' || strtoupper($action) == 'JIA'))) {
			$this->redirect($h5_webroot.'/?intersrc=pcweb');
			exit;
		}

		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
		$this->assign("webroot", $webroot);
		$this->assign("staticroot", $staticroot);
		$this->assign("staticSysPath", $staticroot . '/sys');
		$this->assign("staticResPath", $staticroot . '/apps/game/web');

		
		$action = $this->getRequest()->getActionName();
		$path = $module.'/'. $controller;
		//导航条变色
		switch($path){
			case "Front/Index" :
				$nav = 1;
				break;
			case "Front/Category" :
			case "Front/Game" :	
				$nav = 2;
				break;
			case "Front/Android" :
				$nav = 4;
				break;
			case "Front/News" :
			case "Front/About" :
			case "Front/Error" :
				$nav = 0 ;
				break;
			case "Front/Search" :
				switch($action){
					case "detail":
						$nav = 2;
						break;
					default:
						$nav = 0;
				}	
				break;
			default:
				$nav = 1;
		}
		$this->assign('nav', $nav);	
				
		//取得配置各个值
		$ami_web_bbs = Game_Service_Config::getValue('ami_web_bbs');
		$this->assign('ami_web_bbs', $ami_web_bbs );
		$ami_web_index_share_text = Game_Service_Config::getValue('ami_web_index_share_text');
		$this->assign('ami_web_index_share_text', $ami_web_index_share_text );	
		$this->assign('h5_webroot', $h5_webroot);
		$ami_android_url = Game_Service_Config::getValue('ami_android_url');
		$this->assign('ami_android_url', $ami_android_url );

		//统计参数的初始化
		$tj_channel = $this->getPost('channel')?$this->getPost('channel'):'';
		//获取用户的cookie
		$cku = $this->getInput('cku');
		if (!$cku) $this->setSource();
		$source = $this->getSource();

		//未登录
		$user = '_null';
		$tj_cku = $source.$user;
		$controller  = ($controller?$controller:'Index');
		$action      = ($action?$action:'index'); 
		$tj_object   = $this->tj_object[$controller.'_'.$action];
		$tj_intersrc = $tj_object ;		
	
		$this->assign('tj_channel', $tj_channel);
		$this->assign('tj_cku', $tj_cku);		
		$this->assign('tj_object', $tj_object);
		$this->assign('tj_intersrc', $tj_intersrc);
		$this->assign('tjUrl', $webroot.$this->url['tjUrl']);

		//PV统计
		Common::getCache()->increment('front_pv');
		
		if ($this->getToday()) {
			Common::getCache()->increment('front_uv');
		}
	}
	
	public function getDowloadRank(){
		$cacheKey = "ami-r-d"; //艾米游戏右边栏下载最多排行帮
		$cache = Common::getCache ();
		$data = $cache->get($cacheKey);
		if ($data) {
			return $data;
		}else {
		    if($this->filter){
				list(, $down_games) = Client_Service_Rank::getMostGames(60,'',$this->filter);
			}else{
				list(, $down_games) = Client_Service_Rank::getMostGames(60,'');
			}
	
			$down_games_list = array();
			if ($down_games) {
				foreach($down_games as $key=>$value) {
					if( count($down_games_list) >= 10) break;
					$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value['GAME_ID']));
					if ($info) $down_games_list[] = $info;
			
				}
			}
			if(count($down_games_list)){
				$cache->set($cacheKey, $down_games_list, 1*3600);//缓存6个小时
			}
			return $down_games_list;
		}
	}
	
	
	public function setSource() {

		$sid = $this->getSource();
		if ($sid) {
			$string = sprintf("%s", $sid);
		} else {
			$uid = crc32(uniqid());
			$string = sprintf("%s", $uid);
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
	public function getToday() {
		$is_game_user = Util_Cookie::get('ISGAMEUSER', true);
		if ($is_game_user) return false;
		return Util_Cookie::set('ISGAMEUSER', 1, true, strtotime(date('Y-m-d 23:59:59')), '/', $this->getDomain());
	}
	
	/**
	 * 检查token
	 */
	protected function checkToken() {
		if (!$this->getRequest()->isPost()) return true;
		$post = $this->getRequest()->getPost();
		$result = Common::checkToken($post['token']);
		if (Common::isError($result)) $this->output(-1, $result['msg']);
		return true;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $code
	 * @param unknown_type $msg
	 * @param unknown_type $data
	 */
	public function output($code, $msg = '', $data = array()) {
		exit(json_encode(array(
			'success' => $code == 0 ? true : false ,
			'msg' => $msg,
			'data' => $data
		)));
	}
	
	/**
	 *
	 * @return boolean
	 */
	public function getDomain() {
		$domain = str_replace('http://','',Common::getWebRoot());
		if($number = strrpos($domain,':'))  $domain = Util_String::substr($domain, 0, $number);
		return $domain;
	}
}
